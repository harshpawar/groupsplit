<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('groups.show', $group) }}" class="text-gray-600 hover:text-blue-600 transition-colors">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h2 class="font-bold text-3xl bg-gradient-to-r from-orange-600 to-pink-600 bg-clip-text text-transparent">Add Bill · {{ $group->name }}</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <x-flash-messages />

                    <form method="POST" action="{{ route('groups.bills.store', $group) }}" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="title" value="Bill Title" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required />
                        </div>

                        <div>
                            <x-input-label for="total_amount" value="Total Amount (₹)" />
                            <x-text-input id="total_amount" name="total_amount" type="number" step="0.01" min="0.01" class="mt-1 block w-full" :value="old('total_amount')" required />
                            <p class="mt-2 text-sm text-gray-600">Amount will be split equally among {{ $group->members()->count() }} members.</p>
                        </div>

                        <div>
                            <x-input-label for="description" value="Description (optional)" />
                            <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                        </div>

                        <div class="flex items-center gap-3">
                            <x-primary-button>Add Bill</x-primary-button>
                            <a href="{{ route('groups.show', $group) }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
