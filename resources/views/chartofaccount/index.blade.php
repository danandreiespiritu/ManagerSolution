<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Page header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Chart of Accounts</h1>
                <p class="text-sm text-gray-600 mt-1">Manage your account structure and categories</p>
            </div>

            <!-- Two column layout -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Left column: Balance Sheet --}}
                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow">
                    <div class="px-6 py-4 flex items-center justify-between border-b border-gray-200 bg-gray-50">
                        <h3 class="text-gray-900 font-semibold">Balance Sheet</h3>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('BlGroupCreate') }}" class="text-sm bg-green-100 text-green-700 px-3 py-1 rounded border border-green-300 hover:bg-green-200">New Group</a>
                            <a href="{{ route('BlAccountCreate') }}" class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded border border-blue-300 hover:bg-blue-200">New Account</a>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        @php
                            $leftSections = $sections['balance_sheet'] ?? [
                                ['name' => 'Assets', 'accounts' => [['name'=>'Accounts Receivable']]],
                                ['name' => 'Liabilities', 'accounts' => [['name'=>'Accounts Payable'], ['name'=>'Accrued Expenses'], ['name'=>'Notes Payable']]],
                                ['name' => 'Equity', 'accounts' => [['name'=>"Owner's Equity"], ['name'=>'Retained Earnings'], ['name'=>'123']]],
                            ];
                        @endphp

                        @foreach($leftSections as $group)
                            <div class="bg-gray-50 border border-gray-200 rounded">
                                <div class="px-4 py-3 bg-gray-100 border-b border-gray-200">
                                    <div class="text-xs text-gray-600">Name</div>
                                    <div class="font-medium text-gray-900">{{ $group['name'] }}</div>
                                </div>
                                <div>
                                    @if (count($group['accounts']) === 0)
                                        <div class="bg-white p-3 text-gray-600">No accounts</div>
                                    @else
                                    @foreach($group['accounts'] as $acct)
                                        <div class="flex items-center justify-between px-4 py-3 border-b last:border-b-0 bg-white hover:bg-gray-50">
                                            <div class="flex items-center gap-3 justify-between w-full">
                                                <div class="text-gray-900">{{ $acct['name'] }}</div>
                                                <div>
                                                    <form method="GET" action="{{ route('BlAccountEdit', $acct['id']) }}" class="inline">
                                                        <button class="text-xs px-3 py-1 border rounded-md bg-blue-100 text-blue-700 hover:bg-blue-200">Edit</button>
                                                    </form>
                                                    <form method="POST" action="{{ route('BlAccountDestroy', $acct['id']) }}" id="delete-form-{{ $acct['id'] }}" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" data-form-id="delete-form-{{ $acct['id'] }}" class="js-delete-button text-xs px-3 py-1 border rounded-md bg-red-100 text-red-600 border-red-300 hover:bg-red-200">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Right column: Profit and Loss --}}
                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow">
                    <div class="px-6 py-4 flex items-center justify-between border-b border-gray-200 bg-gray-50">
                        <h3 class="text-gray-900 font-semibold">Profit and Loss Statement</h3>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('PlGroupCreate') }}" class="text-sm bg-green-100 text-green-700 px-3 py-1 rounded border border-green-300 hover:bg-green-200">New Group</a>
                            <a href="{{ route('PlAccountCreate') }}" class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded border border-blue-300 hover:bg-blue-200">New Account</a>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        @php
                            $rightSections = $sections['profit_and_loss'] ?? [
                                ['name'=>'123','accounts'=>[]],
                                ['name'=>'123124','accounts'=>[]],
                                ['name'=>'3123a','accounts'=>[]],
                                ['name'=>'Expenses','accounts'=>[['name'=>'Motor Vehicle Expenses'],['name'=>'Printing and Stationery'],['name'=>'Rent'],['name'=>'Repairs and Maintenance'],['name'=>'Salaries and Wages'],['name'=>'Depreciation']]],
                            ];
                        @endphp

                        @foreach($rightSections as $group)
                            <div class="bg-gray-50 border border-gray-200 rounded">
                                <div class="px-4 py-3 bg-gray-100 border-b border-gray-200">
                                    <div class="text-xs text-gray-600">Name</div>
                                    <div class="font-medium text-gray-900">{{ $group['name'] }}</div>
                                </div>
                                <div>
                                    @if(count($group['accounts']) === 0)
                                        <div class="bg-white p-3 text-gray-600">No accounts</div>
                                    @else
                                        @foreach($group['accounts'] as $acct)
                                            <div class="flex items-center justify-between px-4 py-3 border-b last:border-b-0 bg-white hover:bg-gray-50">
                                                <div class="flex items-center gap-3 justify-between w-full">
                                                    <div class="text-gray-900">{{ $acct['name'] }}</div>
                                                    <div>
                                                        <form method="GET" action="{{ route('PlAccountEdit', $acct['id']) }}" class="inline">
                                                            <button class="text-xs px-3 py-1 border rounded-md bg-blue-100 text-blue-700 hover:bg-blue-200">Edit</button>
                                                        </form>
                                                        <form method="POST" action="{{ route('PlAccountDestroy', $acct['id']) }}" id="delete-form-{{ $acct['id'] }}" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" data-form-id="delete-form-{{ $acct['id'] }}" class="js-delete-button text-xs px-3 py-1 border rounded-md bg-red-100 text-red-600 border-red-300 hover:bg-red-200">Delete</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

<!-- Delete confirmation modal -->
<div id="delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div id="delete-modal-overlay" class="absolute inset-0 bg-black/50"></div>
    <div class="relative bg-white rounded-lg w-full max-w-md mx-4 p-6 text-left">
        <h3 class="text-lg font-medium text-gray-900">Confirm deletion</h3>
        <p class="mt-2 text-sm text-gray-700">Are you sure you want to delete this account? This action cannot be undone.</p>
        <div class="mt-4 flex justify-end gap-2">
            <button id="delete-cancel" class="px-4 py-2 bg-gray-200 text-gray-900 rounded hover:bg-gray-300">Cancel</button>
            <button id="delete-confirm" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let currentFormId = null;
    const modal = document.getElementById('delete-modal');
    const overlay = document.getElementById('delete-modal-overlay');
    const btnConfirm = document.getElementById('delete-confirm');
    const btnCancel = document.getElementById('delete-cancel');

    function openModal(formId) {
        currentFormId = formId;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        currentFormId = null;
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    document.querySelectorAll('.js-delete-button').forEach(function(btn){
        btn.addEventListener('click', function(){
            const fid = btn.getAttribute('data-form-id');
            if (fid) openModal(fid);
        });
    });

    btnCancel.addEventListener('click', function(){ closeModal(); });
    overlay.addEventListener('click', function(){ closeModal(); });

    btnConfirm.addEventListener('click', function(){
        if (!currentFormId) return closeModal();
        const f = document.getElementById(currentFormId);
        if (f) f.submit();
        else closeModal();
    });

    document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeModal(); });
});
</script>
