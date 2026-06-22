<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-semibold text-gray-900">Join Group</h1>
        <p class="mt-2 text-sm text-gray-600">You are joining <strong>{{ $group->name }}</strong></p>
    </div>

    <x-flash-messages />

    <form method="POST" action="{{ route('groups.join.store', $token) }}">
        @csrf
        <p class="text-sm text-gray-600 mb-4">Your account ({{ Auth::user()->email }}) will be linked using mobile <strong>{{ Auth::user()->mobile ?? 'not set' }}</strong>.</p>

        @if (! Auth::user()->mobile)
            <div class="mb-4 rounded-md bg-yellow-50 p-4 text-sm text-yellow-800">
                Add your mobile number in profile before joining.
                <a href="{{ route('profile.edit') }}" class="underline">Update profile</a>
            </div>
        @endif

        <button type="submit" class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700 w-full justify-center {{ ! Auth::user()->mobile ? 'opacity-50 cursor-not-allowed' : '' }}" {{ ! Auth::user()->mobile ? 'disabled' : '' }}>
            Join Group
        </button>
    </form>
</x-guest-layout>
