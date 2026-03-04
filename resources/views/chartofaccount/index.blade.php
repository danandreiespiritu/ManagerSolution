<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Chart of Accounts</h1>
                <p class="text-gray-600 mt-1 text-sm">Organize and manage your company’s account structure</p>
            </div>

            <!-- Two-column layout -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                <!-- BALANCE SHEET CARD -->
                <div class="bg-white border border-gray-200 shadow-md rounded-xl overflow-hidden">
                    
                    <!-- Card Header -->
                    <div class="px-6 py-4 flex items-center justify-between border-b bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">Balance Sheet</h3>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('BlGroupCreate') }}"
                                class="text-sm bg-green-100 hover:bg-green-200 text-green-700 px-3 py-1 rounded border border-green-300">
                                New Group
                            </a>
                            <a href="{{ route('BlAccountCreate') }}"
                                class="text-sm bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1 rounded border border-blue-300">
                                New Account
                            </a>
                            <button id="bulkDeleteBtnBl" class="text-sm bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1 rounded border border-red-300">Delete Selected</button>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="p-6 space-y-6">
                        @php
                            $leftSections = $sections['balance_sheet'] ?? [];
                        @endphp

                        @forelse ($leftSections as $group)
                            <div class="bg-gray-50 border border-gray-200 rounded-lg overflow-hidden">

                                <!-- Group Header -->
                                <div class="px-4 py-3 bg-gray-100 border-b">
                                    <div class="text-xs text-gray-600 uppercase tracking-wide">Group</div>
                                    <div class="font-semibold text-gray-900">{{ $group['name'] }}</div>
                                </div>

                                <!-- Accounts List -->
                                @if(empty($group['accounts']))
                                    <div class="bg-white p-3 text-gray-500 text-sm">No accounts in this group.</div>
                                @else
                                    @foreach($group['accounts'] as $acct)
                                            <div class="flex items-center justify-between px-4 py-3 border-b last:border-b-0 bg-white hover:bg-gray-50 transition">

                                                <div class="flex items-center gap-3">
                                                    <input type="checkbox" class="coa-checkbox" value="{{ $acct['id'] }}">
                                                    <div class="text-gray-900 text-sm font-medium">{{ $acct['name'] }}</div>
                                                </div>
                                            
                                                <!-- Actions -->
                                            <div class="flex gap-2">

                                                <a href="{{ route('BlAccountEdit', $acct['id']) }}"
                                                    class="px-3 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 text-xs rounded border border-blue-300 shadow-sm">
                                                    Edit
                                                </a>

                                                <form method="POST" action="{{ route('BlAccountDestroy', $acct['id']) }}" id="delete-form-{{ $acct['id'] }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        data-form-id="delete-form-{{ $acct['id'] }}"
                                                        class="js-delete-button px-3 py-1 bg-red-100 hover:bg-red-200 text-red-700 text-xs rounded border border-red-300 shadow-sm">
                                                        Delete
                                                    </button>
                                                </form>

                                            </div>
                                        </div>
                                    @endforeach
                                @endif

                            </div>
                        @empty
                            <div class="text-gray-500 text-sm">No groups found.</div>
                        @endforelse
                    </div>

                </div>

                <!-- PROFIT & LOSS CARD -->
                <div class="bg-white border border-gray-200 shadow-md rounded-xl overflow-hidden">

                    <!-- Card Header -->
                    <div class="px-6 py-4 flex items-center justify-between border-b bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">Profit and Loss Statement</h3>
                            <div class="flex items-center gap-2">
                            <a href="{{ route('PlGroupCreate') }}"
                                class="text-sm bg-green-100 hover:bg-green-200 text-green-700 px-3 py-1 rounded border border-green-300">
                                New Group
                            </a>
                            <a href="{{ route('PlAccountCreate') }}"
                                class="text-sm bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1 rounded border border-blue-300">
                                New Account
                            </a>
                            <button id="bulkDeleteBtnPl" class="text-sm bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1 rounded border border-red-300">Delete Selected</button>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="p-6 space-y-6">
                        @php
                            $rightSections = $sections['profit_and_loss'] ?? [];
                        @endphp

                        @forelse ($rightSections as $group)
                            <div class="bg-gray-50 border border-gray-200 rounded-lg overflow-hidden">

                                <!-- Group Header -->
                                <div class="px-4 py-3 bg-gray-100 border-b">
                                    <div class="text-xs text-gray-600 uppercase tracking-wide">Group</div>
                                    <div class="font-semibold text-gray-900">{{ $group['name'] }}</div>
                                </div>

                                <!-- Accounts List -->
                                @if(empty($group['accounts']))
                                    <div class="bg-white p-3 text-gray-500 text-sm">No accounts in this group.</div>
                                @else
                                    @foreach($group['accounts'] as $acct)
                                        <div class="flex items-center justify-between px-4 py-3 border-b last:border-b-0 bg-white hover:bg-gray-50 transition">

                                            <div class="flex items-center gap-3">
                                                <input type="checkbox" class="coa-checkbox" value="{{ $acct['id'] }}">
                                                <div class="text-gray-900 text-sm font-medium">{{ $acct['name'] }}</div>
                                            </div>

                                            <!-- Actions -->
                                            <div class="flex gap-2">

                                                <a href="{{ route('PlAccountEdit', $acct['id']) }}"
                                                    class="px-3 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 text-xs rounded border border-blue-300 shadow-sm">
                                                    Edit
                                                </a>

                                                <form method="POST" action="{{ route('PlAccountDestroy', $acct['id']) }}" id="delete-form-{{ $acct['id'] }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        data-form-id="delete-form-{{ $acct['id'] }}"
                                                        class="js-delete-button px-3 py-1 bg-red-100 hover:bg-red-200 text-red-700 text-xs rounded border border-red-300 shadow-sm">
                                                        Delete
                                                    </button>
                                                </form>

                                            </div>
                                        </div>
                                    @endforeach
                                @endif

                            </div>
                        @empty
                            <div class="text-gray-500 text-sm">No groups found.</div>
                        @endforelse
                    </div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div class="absolute inset-0 bg-black/50"></div>

    <div class="relative bg-white rounded-xl w-full max-w-md mx-4 p-6 shadow-xl">

        <h3 class="text-xl font-semibold text-gray-900 mb-2">Confirm deletion</h3>
        <p class="text-gray-700 text-sm mb-6">
            Are you sure you want to delete this account? This action cannot be undone.
        </p>

        <div class="flex justify-end gap-3">
            <button id="delete-cancel"
                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-gray-800">
                Cancel
            </button>
            <button id="delete-confirm"
                class="px-4 py-2 bg-red-600 hover:bg-red-700 rounded-lg text-white">
                Delete
            </button>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let currentFormId = null;
    const modal = document.getElementById('delete-modal');

    function openModal(id) {
        currentFormId = id;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        currentFormId = null;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.querySelectorAll('.js-delete-button').forEach(btn => {
        btn.addEventListener('click', () => openModal(btn.dataset.formId));
    });

    document.getElementById('delete-cancel').onclick = closeModal;
    modal.querySelector('div.bg-black\\/50').onclick = closeModal;

    document.getElementById('delete-confirm').onclick = () => {
        if (!currentFormId) return closeModal();
        document.getElementById(currentFormId).submit();
    };

    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
});
</script>
