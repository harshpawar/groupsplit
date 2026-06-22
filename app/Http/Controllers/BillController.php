<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillSplit;
use App\Models\Group;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BillController extends Controller
{
    public function create(Group $group): View
    {
        $this->authorizeGroupAdmin($group);

        return view('bills.create', compact('group'));
    }

    public function store(Request $request, Group $group): RedirectResponse
    {
        $this->authorizeGroupAdmin($group);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'total_amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $members = $group->members()->get();

        if ($members->isEmpty()) {
            return back()->withErrors(['total_amount' => 'Add at least one member before creating a bill.'])->withInput();
        }

        $shareAmount = round((float) $validated['total_amount'] / $members->count(), 2);
        $allocated = $shareAmount * ($members->count() - 1);
        $lastShare = round((float) $validated['total_amount'] - $allocated, 2);

        $bill = Bill::create([
            'group_id' => $group->id,
            'created_by' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'total_amount' => $validated['total_amount'],
        ]);

        foreach ($members->values() as $index => $member) {
            BillSplit::create([
                'bill_id' => $bill->id,
                'group_member_id' => $member->id,
                'user_id' => $member->user_id,
                'share_amount' => $index === $members->count() - 1 ? $lastShare : $shareAmount,
            ]);
        }

        return redirect()->route('groups.show', $group)->with('success', 'Bill added and split among members.');
    }

    public function show(Bill $bill): View
    {
        $bill->load([
            'group.admin',
            'group.members.user',
            'splits.groupMember.user',
            'splits.payments.user',
            'splits.payments.approvals.reviewer',
        ]);

        $this->authorizeGroupAccess($bill->group);

        $isAdmin = $bill->group->isAdmin(Auth::user());

        return view('bills.show', compact('bill', 'isAdmin'));
    }

    public function destroy(Bill $bill): RedirectResponse
    {
        $this->authorizeGroupAdmin($bill->group);

        $group = $bill->group;
        $bill->delete();

        return redirect()->route('groups.show', $group)->with('success', 'Bill deleted.');
    }

    public function updateSplitStatus(Request $request, BillSplit $billSplit): RedirectResponse
    {
        $this->authorizeGroupAdmin($billSplit->bill->group);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:pending,partially_paid,approved,rejected,overpaid,settled'],
        ]);

        $billSplit->update([
            'status' => $validated['status'],
        ]);

        $bill = $billSplit->bill;

        return redirect()->route('bills.show', $bill)->with('success', 'Split status updated successfully.');
    }

    private function authorizeGroupAccess(Group $group): void
    {
        $user = Auth::user();

        if (! $group->isAdmin($user) && ! $group->hasMember($user)) {
            abort(403);
        }
    }

    private function authorizeGroupAdmin(Group $group): void
    {
        if (! $group->isAdmin(Auth::user())) {
            abort(403);
        }
    }
}
