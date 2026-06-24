<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Dashboard Overview</h2>
                <p class="mt-1 text-sm text-gray-600">Track your balances, group activity, and what still needs attention.</p>
            </div>
            <a href="{{ route('groups.index') }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                View all groups
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 p-8 text-white shadow-xl">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-blue-200">Welcome back</p>
                        <h3 class="mt-2 text-3xl font-bold">{{ auth()->user()->name }}</h3>
                        <p class="mt-3 max-w-2xl text-sm text-blue-100 sm:text-base">
                            Here is a quick snapshot of your balances, outstanding splits, and the groups that still need your attention.
                        </p>
                    </div>
                    <div class="rounded-2xl border border-white/20 bg-white/10 px-5 py-4 backdrop-blur-sm">
                        <p class="text-sm text-blue-100">Participating groups</p>
                        <p class="mt-1 text-3xl font-bold">{{ $groups->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 shadow-sm">
                    <p class="text-sm font-semibold text-amber-700">Remaining to settle</p>
                    <p class="mt-3 text-3xl font-bold text-amber-900">{{ number_format($remainingToSettle, 2) }}</p>
                    <p class="mt-2 text-sm text-amber-700">Open balance across your splits</p>
                </div>
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6 shadow-sm">
                    <p class="text-sm font-semibold text-emerald-700">Total paid</p>
                    <p class="mt-3 text-3xl font-bold text-emerald-900">{{ number_format($totalPaid, 2) }}</p>
                    <p class="mt-2 text-sm text-emerald-700">Amount already settled</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-semibold text-slate-700">Unsettled groups</p>
                    <p class="mt-3 text-3xl font-bold text-slate-900">{{ $unsettledGroups }}</p>
                    <p class="mt-2 text-sm text-slate-500">Groups that still have pending splits</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-semibold text-slate-700">Pending approvals</p>
                    <p class="mt-3 text-3xl font-bold text-slate-900">{{ $pendingPayments }}</p>
                    <p class="mt-2 text-sm text-slate-500">Payments waiting for review</p>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-[1.3fr_0.7fr]">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-5 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Group activity</h3>
                            <p class="text-sm text-slate-500">Your participation snapshot across all groups</p>
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-3">
                        <div class="rounded-xl bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">Total bills</p>
                            <p class="mt-2 text-2xl font-bold text-slate-900">{{ $totalBills }}</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">Pending splits</p>
                            <p class="mt-2 text-2xl font-bold text-slate-900">{{ $pendingSplits }}</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">Members across groups</p>
                            <p class="mt-2 text-2xl font-bold text-slate-900">{{ $totalMembers }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900">Status summary</h3>
                    <div class="mt-5 space-y-4">
                        <div class="flex items-center justify-between rounded-xl bg-emerald-50 px-4 py-3">
                            <span class="text-sm font-medium text-emerald-700">Settled groups</span>
                            <span class="text-lg font-bold text-emerald-900">{{ $settledGroups }}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-xl bg-amber-50 px-4 py-3">
                            <span class="text-sm font-medium text-amber-700">Needs attention</span>
                            <span class="text-lg font-bold text-amber-900">{{ $unsettledGroups }}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-xl bg-sky-50 px-4 py-3">
                            <span class="text-sm font-medium text-sky-700">Settled splits</span>
                            <span class="text-lg font-bold text-sky-900">{{ $settledSplits }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Groups that need attention</h3>
                        <p class="text-sm text-slate-500">These groups still have unsettled balances or pending activity.</p>
                    </div>
                    <a href="{{ route('groups.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Open groups</a>
                </div>

                @if ($needsAttentionGroups->isNotEmpty())
                    <div class="space-y-3">
                        @foreach ($needsAttentionGroups as $group)
                            <div class="flex flex-col gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $group->name }}</p>
                                    <p class="text-sm text-slate-500">{{ $group->unsettledSplitsCount() }} pending split(s) • {{ $group->bills_count }} bill(s)</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="rounded-full bg-amber-100 px-3 py-1 text-sm font-semibold text-amber-700">Needs review</span>
                                    <a href="{{ route('groups.show', $group) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Open</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-xl border border-dashed border-emerald-200 bg-emerald-50 p-5 text-sm text-emerald-700">
                        Everything looks settled right now. You are all caught up.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
