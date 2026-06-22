<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Group</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <x-flash-messages />

                    <form method="POST" action="{{ route('groups.update', $group) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="name" value="Group Name" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $group->name)" required />
                        </div>

                        <div>
                            <x-input-label for="mobile_numbers" value="Add More Mobile Numbers" />
                            <textarea id="mobile_numbers" name="mobile_numbers" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Optional: add new members">{{ old('mobile_numbers') }}</textarea>
                        </div>

                        <div class="flex items-center gap-3">
                            <x-primary-button>Save Changes</x-primary-button>
                            <a href="{{ route('groups.show', $group) }}" class="text-sm text-gray-600 hover:text-gray-900">Back</a>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('groups.destroy', $group) }}" class="mt-8 border-t pt-6" onsubmit="return confirm('Delete this group and all bills?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">Delete Group</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
