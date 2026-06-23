<?php

use App\Http\Controllers\BillController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\JoinGroupController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('groups.index')
        : view('welcome');
});

Route::get('/join/{token}', [JoinGroupController::class, 'show'])->name('groups.join');
Route::post('/join/{token}', [JoinGroupController::class, 'store'])->middleware('auth')->name('groups.join.store');

Route::get('/dashboard', function () {
    return redirect()->route('groups.index');
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
