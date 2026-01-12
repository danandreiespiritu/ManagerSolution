<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bank Account Summary</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html, body { font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, 'Helvetica Neue', Arial, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', sans-serif; }
    </style>
</head>
<body>
@include('user.components.navbar')
<div class="flex min-h-screen bg-gray-50">
    @include('user.components.sidebar')
    <div class="flex-1 flex flex-col p-6">
        <section class="bg-white border border-gray-200 rounded-md shadow-sm max-w-2xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-semibold text-gray-900">Bank Account Summary</h2>
                    <span class="inline-flex items-center justify-center h-5 w-5 text-gray-400 border border-gray-200 rounded-full text-xs" title="Generate a summary of movements for a bank account within a date range">?</span>
                </div>
                <p class="mt-1 text-xs text-gray-500">Choose a bank account (or leave blank for all), set an inclusive date range, and optionally add a comparative period.</p>
            </div>

            <form action="{{ route('reports.financial.bank-account-summary.update', $report) }}" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                @method('PUT')
                @if ($errors->any())
                    <div class="p-3 rounded border border-red-200 bg-red-50 text-sm text-red-700">
                        <ul class="list-disc ml-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <!-- Bank account -->
                <div class="space-y-1">
                    <label class="block text-sm text-gray-700">Bank account <span class="text-gray-500 text-xs font-normal">(optional)</span></label>
                    @php $hasAccounts = isset($bankAccounts) && $bankAccounts && count($bankAccounts) > 0; @endphp
                    <select name="bank_account_id" class="w-full max-w-md border border-gray-300 rounded-md px-3 py-2 bg-white text-sm text-gray-900 disabled:bg-gray-100 disabled:text-gray-500" @if(!$hasAccounts) disabled @endif>
                        @if($hasAccounts)
                            <option value="" {{ $report->bank_account_id ? '' : 'selected' }}>All accounts</option>
                            @foreach($bankAccounts as $acct)
                                <option value="{{ $acct->id }}" {{ (int)$report->bank_account_id === (int)$acct->id ? 'selected' : '' }}>{{ $acct->name ?? ('Account #'.$acct->id) }}</option>
                            @endforeach
                        @else
                            <option selected>Suspense</option>
                        @endif
                    </select>
                    <p class="text-xs text-gray-500">Leave blank to include all bank and cash accounts.</p>
                </div>

                <!-- Date range + comparative -->
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                    <div class="sm:col-span-4">
                        <label class="block text-sm text-gray-700">From</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                <i class="fas fa-calendar"></i>
                            </span>
                            <input type="date" name="from" value="{{ old('from', optional($report->from_date)->format('Y-m-d')) }}" class="w-full border border-gray-300 rounded-md pl-9 pr-3 py-2 text-sm">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Report period is inclusive.</p>
                    </div>
                    <div class="sm:col-span-4">
                        <label class="block text-sm text-gray-700">To</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                <i class="fas fa-calendar"></i>
                            </span>
                            <input type="date" name="to" value="{{ old('to', optional($report->to_date)->format('Y-m-d')) }}" class="w-full border border-gray-300 rounded-md pl-9 pr-3 py-2 text-sm">
                        </div>
                    </div>
                    <div class="sm:col-span-4" id="comparativeWrapper">
                        @php $hasCmp = !empty($report->comparative_from) || !empty($report->comparative_to); @endphp
                        @if($hasCmp)
                            <label class="block text-sm text-gray-700">Comparative</label>
                            <div class="grid grid-cols-1 gap-2" id="comparativeContainer">
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">From</label>
                                    <div class="relative">
                                        <span class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400"><i class="fas fa-calendar"></i></span>
                                        <input type="date" name="comparative_from" value="{{ optional($report->comparative_from)->format('Y-m-d') }}" class="w-full border border-gray-300 rounded-md pl-9 pr-3 py-2 text-sm" title="Comparative - From">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">To</label>
                                    <div class="relative">
                                        <span class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400"><i class="fas fa-calendar"></i></span>
                                        <input type="date" name="comparative_to" value="{{ optional($report->comparative_to)->format('Y-m-d') }}" class="w-full border border-gray-300 rounded-md pl-9 pr-3 py-2 text-sm" title="Comparative - To">
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500">Leave both blank to skip comparative.</p>
                                <div class="flex gap-2">
                                    <button type="button" id="cmpRemove" class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white text-sm text-gray-900 hover:bg-gray-50">Remove comparative</button>
                                </div>
                            </div>
                        @else
                            <label class="block text-sm text-transparent">Comparative</label>
                            <button type="button" name="comparative" class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white text-sm text-gray-900 hover:bg-gray-50">Add comparative column</button>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="pt-2 flex items-center gap-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md">
                        <i class="fas fa-save mr-2"></i> Save Changes
                    </button>
                    <button type="button" id="resetFormBtn" class="inline-flex items-center px-4 py-2 border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-md">
                        <i class="fas fa-undo mr-2"></i> Reset
                    </button>
                    <a href="{{ route('reports') }}" class="ml-auto inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">
                        Back to Reports
                    </a>
                </div>
            </form>
            <!-- Hidden state for JS to detect if comparative is initially open -->
          <div id="cmpState"
              data-open="{{ (!empty($report->comparative_from) || !empty($report->comparative_to)) ? '1' : '0' }}"
              data-cmp-from="{{ optional($report->comparative_from)->format('Y-m-d') }}"
              data-cmp-to="{{ optional($report->comparative_to)->format('Y-m-d') }}"
              style="display:none;"></div>
        </section>
    </div>
</div>
</body>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const fromInput = form.querySelector('input[name="from"]');
    const toInput = form.querySelector('input[name="to"]');
    const bankSelect = form.querySelector('select[name="bank_account_id"]');
    const comparativeWrapper = form.querySelector('#comparativeWrapper');
    const resetBtn = document.getElementById('resetFormBtn');
    let comparativeBtn = comparativeWrapper.querySelector('button[name="comparative"]');

    function showError(input, message) {
        clearError(input);
        const err = document.createElement('p');
        err.className = 'mt-1 text-xs text-red-600 error-text';
        err.textContent = message;
        input.parentNode.appendChild(err);
        input.classList.add('border-red-400');
    }
    function clearError(input) {
        const existing = input.parentNode.querySelector('.error-text');
        if (existing) existing.remove();
        input.classList.remove('border-red-400');
    }
    function validateDates() {
        clearError(fromInput);
        clearError(toInput);
        if (!fromInput.value || !toInput.value) return true;
        const fromDate = new Date(fromInput.value);
        const toDate = new Date(toInput.value);
        if (fromDate > toDate) {
            showError(toInput, '"To" date must be the same or after the "From" date.');
            return false;
        }
        return true;
    }
    function validateBank() { return true; }

    form.addEventListener('submit', function(e) {
        const okDates = validateDates();
        const okBank = validateBank();
        if (!okDates || !okBank) {
            e.preventDefault();
            const firstErr = form.querySelector('.error-text');
            if (firstErr) firstErr.previousElementSibling?.focus();
        }
    });

    fromInput.addEventListener('change', validateDates);
    toInput.addEventListener('change', validateDates);
    bankSelect.addEventListener('change', function(){ clearError(bankSelect); });

    const cmpStateEl = document.getElementById('cmpState');
    let comparativeOpen = cmpStateEl && cmpStateEl.dataset.open === '1';
    let comparativeContainer = document.getElementById('comparativeContainer');
    const initialValues = {
        bank: bankSelect ? bankSelect.value : '',
        from: fromInput ? fromInput.value : '',
        to: toInput ? toInput.value : '',
        cmpFrom: cmpStateEl?.dataset.cmpFrom || '',
        cmpTo: cmpStateEl?.dataset.cmpTo || '',
        cmpOpen: comparativeOpen
    };

    function openComparative() {
        if (comparativeOpen) return;
        comparativeOpen = true;
        comparativeContainer = document.createElement('div');
        comparativeContainer.id = 'comparativeContainer';
        comparativeContainer.className = 'grid grid-cols-1 gap-2';
        comparativeContainer.innerHTML = `
            <div>
                <label class="block text-xs text-gray-600 mb-1">From</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400"><i class="fas fa-calendar"></i></span>
                    <input type="date" name="comparative_from" class="w-full border border-gray-300 rounded-md pl-9 pr-3 py-2 text-sm" title="Comparative - From">
                </div>
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">To</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400"><i class="fas fa-calendar"></i></span>
                    <input type="date" name="comparative_to" class="w-full border border-gray-300 rounded-md pl-9 pr-3 py-2 text-sm" title="Comparative - To">
                </div>
            </div>
            <p class="text-xs text-gray-500">Leave both blank to skip comparative.</p>
            <div class="flex gap-2">
                <button type="button" id="cmpRemove" class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white text-sm text-gray-900 hover:bg-gray-50">Remove comparative</button>
            </div>
        `;
        comparativeWrapper.innerHTML = '<label class="block text-sm text-gray-700">Comparative</label>';
        comparativeWrapper.appendChild(comparativeContainer);
        comparativeContainer.querySelector('#cmpRemove').addEventListener('click', closeComparative);
    }

    function closeComparative() {
        comparativeWrapper.innerHTML = `
            <label class="block text-sm text-transparent">Comparative</label>
            <button type="button" name="comparative" class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white text-sm text-gray-900 hover:bg-gray-50">Add comparative column</button>
        `;
        comparativeOpen = false;
        comparativeContainer = null;
        comparativeBtn = comparativeWrapper.querySelector('button[name="comparative"]');
        comparativeBtn.addEventListener('click', openComparative);
    }

    if (comparativeBtn) comparativeBtn.addEventListener('click', openComparative);

    function validateComparativeDates() {
        const cmpFrom = form.querySelector('input[name="comparative_from"]');
        const cmpTo = form.querySelector('input[name="comparative_to"]');
        if (!cmpFrom || !cmpTo) return true;
        clearError(cmpFrom); clearError(cmpTo);
        if (!cmpFrom.value && !cmpTo.value) return true;
        if (!cmpFrom.value || !cmpTo.value) {
            showError(cmpTo, 'Please provide both comparative dates.');
            return false;
        }
        const f = new Date(cmpFrom.value);
        const t = new Date(cmpTo.value);
        if (f > t) {
            showError(cmpTo, 'Comparative "To" date must be after or equal to "From".');
            return false;
        }
        return true;
    }

    form.addEventListener('submit', function(e) {
        if (!validateComparativeDates()) {
            e.preventDefault();
        }
    });

    // Reset button mirrors Create page behavior and restores initial state
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            // Clear errors
            form.querySelectorAll('.error-text').forEach(el => el.remove());
            form.querySelectorAll('.border-red-400').forEach(el => el.classList.remove('border-red-400'));
            // Reset field values
            if (bankSelect) bankSelect.value = initialValues.bank;
            if (fromInput) fromInput.value = initialValues.from;
            if (toInput) toInput.value = initialValues.to;

            // Reset comparative UI and values
            if (initialValues.cmpOpen) {
                // ensure open, then set values
                if (!comparativeOpen) openComparative();
                const cmpFrom = form.querySelector('input[name="comparative_from"]');
                const cmpTo = form.querySelector('input[name="comparative_to"]');
                if (cmpFrom) cmpFrom.value = initialValues.cmpFrom || '';
                if (cmpTo) cmpTo.value = initialValues.cmpTo || '';
            } else {
                // ensure closed
                if (comparativeOpen) closeComparative();
            }
        });
    }
});
</script>
</html>
