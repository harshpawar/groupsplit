<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-orange-600 to-pink-600 bg-clip-text text-transparent leading-tight">{{ $bill->title }}</h2>
                <p class="text-sm text-gray-700 font-medium mt-2"><span class="inline-block bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold mr-2">{{ $bill->group->name }}</span><span class="inline-block bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-bold">Total: ₹{{ number_format($bill->total_amount, 2) }}</span></p>
            </div>
            <a href="{{ route('groups.show', $bill->group) }}" class="inline-flex items-center rounded-lg border-2 border-blue-500 px-5 py-2.5 text-base font-bold text-blue-600 hover:bg-blue-50 transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Group
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash-messages />

            @if ($bill->description)
                <div class="bg-gradient-to-r from-yellow-50 to-orange-50 overflow-hidden shadow-md sm:rounded-lg border-l-4 border-orange-500">
                    <div class="p-6 text-base text-gray-800 font-medium">{{ $bill->description }}</div>
                </div>
            @endif

            @foreach ($bill->splits as $split)
                @php
                    $canPay = ! $isAdmin && $split->belongsToUser(auth()->user());
                    $memberName = $split->groupMember->displayName();
                @endphp

                <div class="bg-gradient-to-br from-white to-blue-50 overflow-hidden shadow-md sm:rounded-lg border-2 border-blue-200">
                    <div class="p-6 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <h3 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">{{ $memberName }}</h3>
                                <div class="flex gap-2 mt-2 flex-wrap">
                                    <span class="inline-block bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-bold">Share: ₹{{ number_format($split->share_amount, 2) }}</span>
                                    <span class="inline-block bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-bold">Approved: ₹{{ number_format($split->approved_amount, 2) }}</span>
                                    <span class="inline-block bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-sm font-bold">Remaining: ₹{{ $split->remainingAmount() }}</span>
                                </div>
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                <x-split-status-badge :status="$split->status" />
                                @if ($isAdmin)
                                    <div class="text-xs font-bold text-gray-600">Change Status:</div>
                                    <div class="flex flex-wrap gap-1 justify-end">
                                        @foreach (['pending' => 'Pending', 'partially_paid' => 'Partial', 'approved' => 'Approved', 'settled' => 'Settled', 'rejected' => 'Reject', 'overpaid' => 'Overpaid'] as $statusValue => $statusLabel)
                                            <form method="POST" action="{{ route('splits.update-status', $split) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="{{ $statusValue }}">
                                                <button type="submit" class="px-2 py-1 rounded text-xs font-bold bg-gray-200 text-gray-800 hover:bg-gray-300 transition-all @if($split->status->value === $statusValue) bg-blue-500 text-white hover:bg-blue-600 @endif">
                                                    {{ $statusLabel }}
                                                </button>
                                            </form>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if ($canPay || $isAdmin)
                            <form method="POST" action="{{ route('payments.store', $split) }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 border-t pt-4">
                                @csrf
                                @if ($isAdmin)
                                    <div class="sm:col-span-3 bg-blue-50 border-l-4 border-blue-500 p-3 rounded">
                                        <p class="text-sm font-semibold text-blue-900">💼 Admin Payment: Recording payment for {{ $memberName }}</p>
                                    </div>
                                @endif
                                <div>
                                    <x-input-label for="amount-{{ $split->id }}" value="Amount Paid (₹)" />
                                    <x-text-input id="amount-{{ $split->id }}" name="amount" type="number" step="0.01" min="0.01" class="mt-1 block w-full" required />
                                </div>
                                <div class="sm:col-span-2">
                                    <x-input-label for="note-{{ $split->id }}" value="Note (optional)" />
                                    <x-text-input id="note-{{ $split->id }}" name="note" type="text" class="mt-1 block w-full" />
                                </div>
                                <div class="sm:col-span-3">
                                    <x-primary-button>{{ $isAdmin ? '✅ Approve & Record Payment' : 'Mark as Paid' }}</x-primary-button>
                                </div>
                            </form>
                        @endif

                        @if ($split->payments->isNotEmpty())
                            <div class="border-t pt-4">
                                <h4 class="font-medium text-gray-900">Payment History</h4>
                                <div class="mt-3 space-y-4">
                                    @foreach ($split->payments->sortByDesc('created_at') as $payment)
                                        <div class="rounded-lg border p-4">
                                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                                                <div>
                                                    <p class="font-medium text-gray-900">₹{{ number_format($payment->amount, 2) }} by {{ $payment->user->name }}</p>
                                                    <p class="text-sm text-gray-600">Submitted {{ $payment->marked_paid_at?->format('M d, Y h:i A') }}</p>
                                                    @if ($payment->note)
                                                        <p class="text-sm text-gray-600 mt-1">{{ $payment->note }}</p>
                                                    @endif
                                                    <p class="text-sm mt-1">
                                                        Status:
                                                        <span class="font-medium">{{ $payment->status->label() }}</span>
                                                    </p>
                                                </div>

                                                @if ($isAdmin && $payment->status === \App\Enums\PaymentStatus::Pending)
                                                    <div class="flex flex-wrap gap-2">
                                                        <form method="POST" action="{{ route('payments.approve', $payment) }}">
                                                            @csrf
                                                            <button type="submit" class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-700 border-2 border-green-800 rounded-lg font-bold text-base text-white shadow-md hover:shadow-lg hover:from-green-700 hover:to-emerald-800 transition-all transform active:scale-95">
                                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                                </svg>
                                                                Approve
                                                            </button>
                                                        </form>

                                                        <form method="POST" action="{{ route('payments.reject', $payment) }}" class="flex flex-col gap-2">
                                                            @csrf
                                                            <input type="text" name="rejection_reason" placeholder="Rejection reason" class="rounded-lg border-2 border-gray-400 bg-white text-gray-900 px-4 py-2 text-sm focus:border-red-500 focus:ring-2 focus:ring-red-300" required>
                                                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-3 text-sm font-bold text-white hover:bg-red-700 transition-all shadow-md hover:shadow-lg">
                                                                Reject
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endif
                                            </div>

                                            @if ($payment->approvals->isNotEmpty())
                                                <div class="mt-4 border-t pt-3">
                                                    <p class="text-sm font-medium text-gray-900">Approval History</p>
                                                    <ul class="mt-2 space-y-2">
                                                        @foreach ($payment->approvals as $approval)
                                                            <li class="text-sm text-gray-700">
                                                                <span class="font-medium">{{ $approval->action->label() }}</span>
                                                                by {{ $approval->reviewer->name }}
                                                                on {{ $approval->created_at->format('M d, Y h:i A') }}
                                                                @if ($approval->rejection_reason)
                                                                    · Reason: {{ $approval->rejection_reason }}
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            @if ($isAdmin)
                <form method="POST" action="{{ route('bills.destroy', $bill) }}" onsubmit="return confirm('Delete this bill?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm text-red-600 hover:text-red-800">Delete Bill</button>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
