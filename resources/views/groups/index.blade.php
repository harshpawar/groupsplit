<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-bold text-3xl bg-gradient-to-r from-purple-600 via-blue-600 to-cyan-600 bg-clip-text text-transparent leading-tight">My Groups</h2>
            <a href="{{ route('groups.create') }}" class="inline-flex items-center justify-center rounded-lg bg-green-600 hover:bg-green-700 px-8 py-3 text-base font-bold text-white shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-200 border-2 border-green-800">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span class="whitespace-nowrap">Create Group</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6" style="margin-top: 1rem;">
            <x-flash-messages />

            @forelse ($groups as $group)
                <div class="bg-white overflow-hidden shadow-md sm:rounded-lg hover:shadow-xl transition-all border-l-4 border-gradient-to-b from-purple-500 to-blue-500">
                    <div class="p-6 bg-gradient-to-br from-white via-white to-blue-50">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">{{ $group->name }}</h3>
                                    @if ($group->isSettled())
                                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-4 py-2 text-sm font-bold text-emerald-700 shadow-md">
                                            <svg class="w-5 h-5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            ✓ Settled
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-amber-100 px-4 py-2 text-sm font-bold text-amber-800 shadow-md">
                            <svg class="w-5 h-5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            ⚠ {{ $group->unsettledSplitsCount() }} Pending
                        </span>
                    @endif
                </div>
                <p class="text-sm text-gray-700 font-medium">
                    <span class="inline-block bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold mr-2">Admin: {{ $group->admin->name }}</span>
                    <span class="inline-block bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs font-bold mr-2">{{ $group->members->count() }} members</span>
                    <span class="inline-block bg-pink-100 text-pink-700 px-3 py-1 rounded-full text-xs font-bold">{{ $group->bills_count }} bills</span>
                </p>
                
                @if ($group->bills_count > 0)
                    <div class="mt-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-gray-700">Settlement Progress</span>
                            <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded">{{ $group->settledPercentage() }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden shadow-sm">
                            <div class="bg-gradient-to-r from-green-400 to-emerald-600 h-3 rounded-full transition-all" style="width: {{ $group->settledPercentage() }}%"></div>
                        </div>
                    </div>
                @endif
            </div>
            <a href="{{ route('groups.show', $group) }}" class="flex-shrink-0 inline-flex justify-center items-center rounded-lg bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 text-lg font-bold shadow-lg hover:shadow-2xl hover:scale-105 transition-all border-2 border-blue-800">
                Open Group
                <svg class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>
</div>
            @empty
                <div class="bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 overflow-hidden shadow-lg sm:rounded-lg border-2 border-purple-200">
                    <div class="p-16 text-center">
                        <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 shadow-lg mb-6">
                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <h3 class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">No groups yet</h3>
                        <p class="mt-4 text-lg text-gray-600 mb-10 max-w-md mx-auto">
                            Create a new group to start splitting bills with your friends, family, or roommates. You can invite members by mobile number or QR code.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <a href="{{ route('groups.create') }}" class="inline-flex items-center justify-center rounded-lg bg-green-600 hover:bg-green-700 px-10 py-4 text-lg font-bold text-white shadow-lg hover:shadow-2xl hover:scale-110 transition-all duration-200 border-2 border-green-800">
                                <svg class="w-7 h-7 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Create Your First Group
                            </a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Floating Action Button (Mobile) -->
    <div class="fixed bottom-6 right-6 sm:hidden z-50">
        <a href="{{ route('groups.create') }}" class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 text-white shadow-xl hover:shadow-2xl hover:scale-125 transition-all duration-200 active:scale-95 animate-bounce">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
        </a>
    </div>
</x-app-layout>
