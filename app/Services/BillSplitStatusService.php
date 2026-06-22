<?php

namespace App\Services;

use App\Enums\ApprovalAction;
use App\Enums\PaymentStatus;
use App\Enums\SplitStatus;
use App\Models\BillSplit;
use App\Models\Payment;
use App\Models\PaymentApproval;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class BillSplitStatusService
{
    public function recalculate(BillSplit $billSplit): void
    {
        $approvedTotal = (float) $billSplit->payments()
            ->where('status', PaymentStatus::Approved)
            ->sum('amount');

        $shareAmount = (float) $billSplit->share_amount;
        $hasPending = $billSplit->payments()->where('status', PaymentStatus::Pending)->exists();
        $latestRejected = $billSplit->payments()
            ->where('status', PaymentStatus::Rejected)
            ->latest()
            ->first();

        $status = SplitStatus::Pending;

        if ($approvedTotal > $shareAmount) {
            $status = SplitStatus::Overpaid;
        } elseif (abs($approvedTotal - $shareAmount) < 0.01 && $approvedTotal > 0) {
            $status = SplitStatus::Settled;
        } elseif ($approvedTotal > 0 && $approvedTotal < $shareAmount) {
            $status = SplitStatus::PartiallyPaid;
        } elseif ($approvedTotal > 0) {
            $status = SplitStatus::Approved;
        } elseif ($latestRejected && ! $hasPending) {
            $status = SplitStatus::Rejected;
        }

        $billSplit->update([
            'approved_amount' => number_format($approvedTotal, 2, '.', ''),
            'status' => $status,
        ]);
    }

    public function approvePayment(Payment $payment, User $reviewer): void
    {
        $this->assertAdminCanReview($payment, $reviewer);

        DB::transaction(function () use ($payment, $reviewer) {
            $payment->update(['status' => PaymentStatus::Approved]);

            PaymentApproval::create([
                'payment_id' => $payment->id,
                'reviewed_by' => $reviewer->id,
                'action' => ApprovalAction::Approved,
            ]);

            $this->recalculate($payment->billSplit);
        });
    }

    public function rejectPayment(Payment $payment, User $reviewer, ?string $reason): void
    {
        $this->assertAdminCanReview($payment, $reviewer);

        DB::transaction(function () use ($payment, $reviewer, $reason) {
            $payment->update(['status' => PaymentStatus::Rejected]);

            PaymentApproval::create([
                'payment_id' => $payment->id,
                'reviewed_by' => $reviewer->id,
                'action' => ApprovalAction::Rejected,
                'rejection_reason' => $reason,
            ]);

            $this->recalculate($payment->billSplit);
        });
    }

    private function assertAdminCanReview(Payment $payment, User $reviewer): void
    {
        $group = $payment->billSplit->bill->group;

        if (! $group->isAdmin($reviewer)) {
            throw new InvalidArgumentException('Only the group admin can review payments.');
        }

        if ($payment->status !== PaymentStatus::Pending) {
            throw new InvalidArgumentException('Only pending payments can be reviewed.');
        }
    }
}
