<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('groups.index') }}" class="text-gray-600 hover:text-blue-600 transition-colors">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h2 class="font-bold text-3xl bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent leading-tight">Create New Group</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gradient-to-br from-white to-blue-50 overflow-hidden shadow-lg sm:rounded-lg border-2 border-blue-200">
                <div class="p-8">
                    <x-flash-messages />

                    <form method="POST" action="{{ route('groups.store') }}" class="space-y-8">
                        @csrf

                        <div>
                            <x-input-label for="name" value="Group Name" />
                            <p class="text-sm text-gray-500 mt-1 mb-3">Give your group a memorable name (e.g., "Summer Trip 2024", "Apartment Expenses")</p>
                            <x-text-input 
                                id="name" 
                                name="name" 
                                type="text" 
                                class="mt-1 block w-full border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-blue-500 focus:ring-offset-2" 
                                placeholder="Enter group name"
                                :value="old('name')" 
                                required 
                            />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <x-input-label for="search_query" value="Add Members" />
                                <span class="text-xs text-gray-500">Search by name or mobile</span>
                            </div>
                            
                            <div class="space-y-3">
                                <div class="relative">
                                    <input 
                                        id="search_query" 
                                        type="text" 
                                        placeholder="Search member by name or mobile number..."
                                        class="w-full rounded-lg border-2 border-gray-300 bg-white text-gray-900 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-300 shadow-sm transition"
                                        autocomplete="off"
                                    />
                                    <div id="search_results" class="absolute top-full left-0 right-0 mt-1 bg-white border-2 border-gray-300 rounded-lg shadow-lg z-10 hidden max-h-60 overflow-y-auto">
                                    </div>
                                </div>

                                <div id="selected_members" class="space-y-2">
                                </div>
                            </div>

                            <p class="text-sm text-gray-500 mt-3 mb-3">Or add members by their mobile numbers (one per line or comma-separated)</p>
                            <textarea 
                                id="mobile_numbers" 
                                name="mobile_numbers" 
                                rows="6" 
                                class="mt-1 block w-full rounded-lg border-2 border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 focus:ring-offset-2" 
                                placeholder="9876543210&#10;9123456780&#10;9999999999"
                                required
                            >{{ old('mobile_numbers') }}</textarea>
                            <x-input-error :messages="$errors->get('mobile_numbers')" class="mt-2" />
                            <p class="mt-3 text-sm text-gray-600">
                                <strong class="text-blue-600">💡 Tips:</strong> Enter mobile numbers without country code. Members will be notified and can join the group by scanning a QR code or registering with the same mobile number.
                            </p>
                        </div>

                        <div class="flex items-center gap-4 pt-8 border-t-2 border-blue-200">
                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-green-500 to-emerald-600 px-8 py-4 text-lg font-bold text-white shadow-lg hover:shadow-2xl hover:scale-105 transition-all">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Create Group
                            </button>
                            <a href="{{ route('groups.index') }}" class="px-8 py-4 text-lg font-bold text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-all">Cancel</a>
                        </div>
                    </form>

                    <script>
                        const searchInput = document.getElementById('search_query');
                        const searchResults = document.getElementById('search_results');
                        const selectedMembers = document.getElementById('selected_members');
                        const mobileNumbersField = document.getElementById('mobile_numbers');
                        let selectedUsers = {};

                        searchInput.addEventListener('input', async (e) => {
                            const query = e.target.value.trim();
                            
                            if (query.length < 2) {
                                searchResults.classList.add('hidden');
                                return;
                            }

                            try {
                                const response = await fetch(`{{ route('groups.search-members') }}?q=${encodeURIComponent(query)}`);
                                const data = await response.json();
                                
                                if (data.users.length === 0) {
                                    searchResults.innerHTML = '<div class="p-3 text-gray-500 text-sm">No users found</div>';
                                    searchResults.classList.remove('hidden');
                                    return;
                                }

                                searchResults.innerHTML = data.users
                                    .filter(user => !selectedUsers[user.id])
                                    .map(user => `
                                        <button type="button" 
                                            class="w-full text-left px-4 py-3 hover:bg-blue-50 border-b last:border-b-0 transition-colors flex justify-between items-center"
                                            onclick="addMember(${user.id}, '${user.mobile.replace(/'/g, "\\'")}', '${user.name.replace(/'/g, "\\'")}'); event.preventDefault();">
                                            <div>
                                                <div class="font-medium text-gray-900">${user.name}</div>
                                                <div class="text-sm text-gray-600">${user.mobile}</div>
                                            </div>
                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                        </button>
                                    `)
                                    .join('');
                                searchResults.classList.remove('hidden');
                            } catch (error) {
                                console.error('Search error:', error);
                            }
                        });

                        document.addEventListener('click', (e) => {
                            if (!e.target.closest('#search_query') && !e.target.closest('#search_results')) {
                                searchResults.classList.add('hidden');
                            }
                        });

                        function addMember(userId, mobile, name) {
                            selectedUsers[userId] = { mobile, name };
                            searchInput.value = '';
                            searchResults.classList.add('hidden');
                            updateSelectedMembers();
                        }

                        function removeMember(userId) {
                            delete selectedUsers[userId];
                            updateSelectedMembers();
                        }

                        function updateSelectedMembers() {
                            if (Object.keys(selectedUsers).length === 0) {
                                selectedMembers.innerHTML = '';
                                return;
                            }

                            selectedMembers.innerHTML = `
                                <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-4">
                                    <p class="text-sm font-bold text-blue-900 mb-3">Selected Members:</p>
                                    <div class="space-y-2">
                                        ${Object.entries(selectedUsers)
                                            .map(([userId, { name, mobile }]) => `
                                                <div class="flex items-center justify-between bg-white p-2 rounded border-l-4 border-blue-600">
                                                    <div>
                                                        <div class="font-medium text-gray-900">${name}</div>
                                                        <div class="text-sm text-gray-600">${mobile}</div>
                                                    </div>
                                                    <button type="button" 
                                                        class="text-red-600 hover:text-red-700 font-bold text-lg"
                                                        onclick="removeMember(${userId}); event.preventDefault();">
                                                        ✕
                                                    </button>
                                                </div>
                                            `)
                                            .join('')}
                                    </div>
                                </div>
                            `;

                            const mobiles = Object.values(selectedUsers)
                                .map(u => u.mobile)
                                .join('\n');
                            
                            // if (mobileNumbersField.value.trim()) {
                            //     mobileNumbersField.value = mobileNumbersField.value.trim() + '\n' + mobiles;
                            // } else {
                                mobileNumbersField.value = mobiles;
                            // }
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
