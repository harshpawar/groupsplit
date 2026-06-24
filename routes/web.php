<?php

use App\Enums\PaymentStatus;
use App\Http\Controllers\BillController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\JoinGroupController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Models\BillSplit;
use App\Models\Group;
use App\Models\Payment;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('groups.index')
        : view('welcome');
});

Route::get('/join/{token}', [JoinGroupController::class, 'show'])->name('groups.join');
Route::post('/join/{token}', [JoinGroupController::class, 'store'])->middleware('auth')->name('groups.join.store');

Route::get('/dashboard', function () {
    $user = auth()->user();

    $groupIds = $user->administeredGroups()->pluck('id')
        ->merge($user->groupMemberships()->pluck('group_id'))
        ->filter()
        ->unique();

    $groups = Group::whereIn('id', $groupIds)
        ->with(['members.user', 'bills.splits'])
        ->withCount('bills')
        ->get();

    $userSplits = BillSplit::where('user_id', $user->id)
        ->with(['bill.group', 'payments'])
        ->get();

    $remainingToSettle = $userSplits->sum(fn ($split) => max(0, (float) $split->share_amount - (float) $split->approved_amount));
    $totalPaid = $userSplits->sum(fn ($split) => (float) $split->approved_amount);
    $pendingSplits = $userSplits->filter(fn ($split) => $split->status->value !== 'settled')->count();
    $settledSplits = $userSplits->count() - $pendingSplits;
    $pendingPayments = Payment::where('user_id', $user->id)->where('status', PaymentStatus::Pending)->count();
    $unsettledGroups = $groups->filter(fn ($group) => ! $group->isSettled())->count();
    $settledGroups = $groups->count() - $unsettledGroups;
    $totalBills = $groups->sum('bills_count');
    $totalMembers = $groups->sum(fn ($group) => $group->members->count());
    $needsAttentionGroups = $groups->filter(fn ($group) => ! $group->isSettled())->take(4);

    return view('dashboard', compact(
        'groups',
        'userSplits',
        'remainingToSettle',
        'totalPaid',
        'pendingSplits',
        'settledSplits',
        'pendingPayments',
        'unsettledGroups',
        'settledGroups',
        'totalBills',
        'totalMembers',
        'needsAttentionGroups',
    ));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('groups/search-members', [GroupController::class, 'searchMembers'])->name('groups.search-members');
    Route::resource('groups', GroupController::class);
    Route::get('groups/{group}/bills/create', [BillController::class, 'create'])->name('groups.bills.create');
    Route::post('groups/{group}/bills', [BillController::class, 'store'])->name('groups.bills.store');
    Route::get('bills/{bill}', [BillController::class, 'show'])->name('bills.show');
    Route::delete('bills/{bill}', [BillController::class, 'destroy'])->name('bills.destroy');

    Route::post('splits/{billSplit}/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::post('payments/{payment}/approve', [PaymentController::class, 'approve'])->name('payments.approve');
    Route::post('payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
    Route::patch('splits/{billSplit}/status', [BillController::class, 'updateSplitStatus'])->name('splits.update-status');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
