<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Models\BillSplit;
use App\Models\Payment;
use App\Services\BillSplitStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class PaymentController extends Controller
{
    public function __construct(private BillSplitStatusService $statusService) {}

    public function store(Request $request, BillSplit $billSplit): RedirectResponse
    {
        $billSplit->load('bill.group', 'groupMember');

        $this->authorizeSplitAccess($billSplit);

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $user = Auth::user();
        $isAdmin = $billSplit->bill->group->isAdmin($user);

        $payment = Payment::create([
            'bill_split_id' => $billSplit->id,
            'user_id' => $user->id,
            'amount' => $validated['amount'],
            'status' => $isAdmin ? PaymentStatus::Approved : PaymentStatus::Pending,
            'note' => $validated['note'] ?? null,
            'marked_paid_at' => now(),
        ]);

        // If admin made the payment, create approval record directly
        if ($isAdmin) {
            $payment->approvals()->create([
                'reviewed_by' => $user->id,
                'action' => \App\Enums\ApprovalAction::Approved,
            ]);
        }

        $this->statusService->recalculate($billSplit);

        $message = $isAdmin 
            ? 'Payment recorded and approved successfully.' 
            : 'Payment submitted for admin approval.';

        return back()->with('success', $message);
    }

    public function approve(Payment $payment): RedirectResponse
    {
        try {
            $this->statusService->approvePayment($payment, Auth::user());
        } catch (InvalidArgumentException $exception) {
            return back()->withErrors(['payment' => $exception->getMessage()]);
        }

        return back()->with('success', 'Payment approved.');
    }

    public function reject(Request $request, Payment $payment): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        try {
            $this->statusService->rejectPayment($payment, Auth::user(), $validated['rejection_reason']);
        } catch (InvalidArgumentException $exception) {
            return back()->withErrors(['payment' => $exception->getMessage()]);
        }

        return back()->with('success', 'Payment rejected.');
    }

    private function authorizeSplitAccess(BillSplit $billSplit): void
    {
        $user = Auth::user();
        $group = $billSplit->bill->group;

        if ($group->isAdmin($user)) {
            return;
        }

        if (! $billSplit->belongsToUser($user)) {
            abort(403);
        }
    }
}
