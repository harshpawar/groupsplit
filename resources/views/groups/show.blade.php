<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-purple-600 via-blue-600 to-cyan-600 bg-clip-text text-transparent leading-tight">{{ $group->name }}</h2>
                @php
                    $isSettled = $group->isSettled();
                @endphp
                <div class="mt-2 flex items-center gap-2">
                    @if ($isSettled)
                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-4 py-1.5 text-sm font-bold text-emerald-700 shadow-sm">
                            <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            All Settled!
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-amber-100 px-4 py-1.5 text-sm font-bold text-amber-700 shadow-sm">
                            <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $group->unsettledSplitsCount() }} Pending
                        </span>
                    @endif
                </div>
            </div>
            @if ($isAdmin)
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('groups.bills.create', $group) }}" class="inline-flex items-center justify-center rounded-lg bg-green-600 hover:bg-green-700 px-8 py-4 text-base font-bold text-white shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-200 border-2 border-green-800">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Bill
                    </a>
                    <a href="{{ route('groups.edit', $group) }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 hover:bg-blue-700 px-8 py-4 text-base font-bold text-white shadow-lg hover:shadow-lg transition-all duration-200 border-2 border-blue-800">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Group
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash-messages />

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg border-t-4 border-blue-500">
                        <div class="p-6">
                            <h3 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Bills</h3>

                            @forelse ($group->bills as $bill)
                                <div class="mt-4 border-2 border-blue-200 rounded-lg p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 hover:shadow-md hover:border-blue-400 transition-all bg-gradient-to-r from-blue-50 to-purple-50">
                                    <div>
                                        <p class="font-bold text-lg text-gray-900">{{ $bill->title }}</p>
                                        <p class="text-sm text-gray-600 mt-1"><span class="font-semibold text-blue-600">₹{{ number_format($bill->total_amount, 2) }}</span> · <span class="bg-purple-100 text-purple-700 px-2 py-1 rounded text-xs font-semibold">{{ $bill->splits->count() }} splits</span></p>
                                    </div>
                                    <a href="{{ route('bills.show', $bill) }}" class="inline-flex justify-center rounded-lg bg-blue-500 text-white px-5 py-2.5 text-sm font-bold hover:bg-blue-600 shadow-md hover:shadow-lg transition-all">
                                        View Details
                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            @empty
                                <p class="mt-4 text-sm text-gray-600">No bills yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg border-t-4 border-purple-500">
                        <div class="p-6">
                            <h3 class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">Members</h3>
                            <ul class="mt-4 space-y-2">
                                @foreach ($group->members as $member)
                                    <li class="flex items-center justify-between text-sm p-3 rounded-lg bg-gradient-to-r from-purple-50 to-pink-50 hover:from-purple-100 hover:to-pink-100 transition-all">
                                        <span class="font-medium text-gray-900">{{ $member->displayName() }}</span>
                                        @if ($member->joined_at)
                                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                Joined
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
                                                </svg>
                                                Invited
                                            </span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    @if ($isAdmin)
                        <div class="bg-gradient-to-br from-indigo-500 to-blue-600 overflow-hidden shadow-lg sm:rounded-lg">
                            <div class="p-8 text-center">
                                <h3 class="text-2xl font-bold text-white">Invite via QR</h3>
                                <p class="mt-2 text-sm text-blue-100">Scan on mobile to join this group</p>
                                <div class="mx-auto mt-6 rounded-xl border-4 border-white p-3 inline-block bg-white shadow-xl">
                                    {!! $qrCode !!}
                                </div>
                                <p class="mt-4 text-xs text-blue-100 break-all font-mono">{{ $group->inviteUrl() }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
