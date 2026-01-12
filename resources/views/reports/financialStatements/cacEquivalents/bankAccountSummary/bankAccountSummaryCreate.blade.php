<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bank Account Summary</title>
        <!-- Fonts & Icons -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Tailwind -->
        <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            html,
            body {
                font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, 'Helvetica Neue', Arial, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', sans-serif;
            }
        </style>
</head>

<body>
    @include('user.components.navbar')
    <div class="flex min-h-screen bg-gray-50">
        <!-- Sidebar -->
        @include('user.components.sidebar')

        <div class="flex-1 flex flex-col p-6">
            <section class="bg-white border border-gray-200 rounded-lg shadow-lg max-w-4xl mx-auto">
            <div class="px-8 py-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-3">
                    <i class="fas fa-university text-blue-600"></i>
                    Bank Account Summary
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">Generate comprehensive bank account reports with optional comparative analysis</p>
                </div>
                <div class="flex items-center justify-center h-10 w-10 bg-blue-100 text-blue-600 rounded-full">
                    <i class="fas fa-chart-bar"></i>
                </div>
                </div>
            </div>

            <form action="{{ route('reports.financial.bank-account-summary.generate') }}" method="POST" class="px-8 py-6 space-y-6">
                @csrf
                @if ($errors->any())
                <div class="p-4 rounded-lg border border-red-200 bg-red-50 text-sm text-red-700">
                    <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-exclamation-circle"></i>
                    <strong>Please fix the following errors:</strong>
                    </div>
                    <ul class="list-disc ml-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                    </ul>
                </div>
                @endif

                <!-- Bank account selection -->
                <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-3">
                    <i class="fas fa-piggy-bank text-gray-500"></i>
                    Bank Account Selection
                    <span class="text-gray-500 text-xs font-normal">(optional)</span>
                </label>
                @php $hasAccounts = isset($bankAccounts) && $bankAccounts && count($bankAccounts) > 0; @endphp
                <div class="relative">
                    <i class="fas fa-university absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <select name="bank_account_id" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg bg-white text-sm text-gray-900 disabled:bg-gray-100 disabled:text-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" @if(!$hasAccounts) disabled @endif>
                    @if($hasAccounts)
                        <option value="">All accounts</option>
                        @foreach($bankAccounts as $acct)
                        <option value="{{ $acct->id }}">{{ $acct->name ?? ('Account #'.$acct->id) }}</option>
                        @endforeach
                    @else
                        <option selected>Suspense</option>
                    @endif
                    </select>
                </div>
                <p class="mt-2 text-xs text-gray-500 flex items-center gap-1">
                    <i class="fas fa-info-circle"></i>
                    Leave blank to include all bank and cash accounts in the report
                </p>
                </div>

                <!-- Date range configuration -->
                <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                <h3 class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-4">
                    <i class="fas fa-calendar-alt text-gray-500"></i>
                    Report Period
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                    <div class="md:col-span-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                    <div class="relative">
                        <i class="fas fa-calendar absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="date" name="from" value="{{ old('from', \Illuminate\Support\Carbon::now()->format('Y-m-d')) }}" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Start of report period (inclusive)</p>
                    </div>
                    <div class="md:col-span-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                    <div class="relative">
                        <i class="fas fa-calendar absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="date" name="to" value="{{ old('to', \Illuminate\Support\Carbon::now()->format('Y-m-d')) }}" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">End of report period (inclusive)</p>
                    </div>
                    <div class="md:col-span-4" id="comparativeWrapper">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Comparative Analysis</label>
                    <button type="button" name="comparative" class="w-full border-2 border-dashed border-gray-300 rounded-lg px-4 py-3 bg-white text-sm text-gray-600 hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-plus-circle"></i>
                        Add Comparative Period
                    </button>
                    <p class="mt-1 text-xs text-gray-500">Optional comparison period</p>
                    </div>
                </div>
                </div>

                <!-- Action buttons -->
                <div class="flex flex-col sm:flex-row items-center gap-4 pt-4 border-t border-gray-200">
                <div class="flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                    <i class="fas fa-chart-line mr-2"></i> 
                    Generate Report
                    </button>
                    <button type="button" id="resetFormBtn" class="inline-flex items-center px-6 py-3 border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-refresh mr-2"></i> 
                    Reset Form
                    </button>
                </div>
                <a href="{{ route('reports') }}" class="sm:ml-auto inline-flex items-center px-4 py-2 text-sm text-gray-600 hover:text-gray-900 transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Reports
                </a>
                </div>
            </form>
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
    let comparativeBtn = comparativeWrapper.querySelector('button[name="comparative"]');

    // helper to show inline error under an input
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

    // validate date range: from <= to
    function validateDates() {
        clearError(fromInput);
        clearError(toInput);
        if (!fromInput.value || !toInput.value) return true; // HTML date required can be used if desired
        const fromDate = new Date(fromInput.value);
        const toDate = new Date(toInput.value);
        if (fromDate > toDate) {
            showError(toInput, '"To" date must be the same or after the "From" date.');
            return false;
        }
        return true;
    }

    // validate bank selection if select is enabled
    function validateBank() { return true; }

    // handle form submit
    form.addEventListener('submit', function(e) {
        // clear generic errors
        const okDates = validateDates();
        const okBank = validateBank();
        if (!okDates || !okBank) {
            e.preventDefault();
            // focus first invalid
            const firstErr = form.querySelector('.error-text');
            if (firstErr) firstErr.previousElementSibling?.focus();
        }
    });

    // live validate when date changes
    fromInput.addEventListener('change', validateDates);
    toInput.addEventListener('change', validateDates);
    bankSelect.addEventListener('change', function(){ clearError(bankSelect); });

    // Comparative toggle: adds/removes comparative date inputs in the 3rd column
    let comparativeOpen = false;
    let comparativeContainer = null;

    function openComparative() {
        if (comparativeOpen) return;
        comparativeOpen = true;
        comparativeContainer = document.createElement('div');
        comparativeContainer.className = 'space-y-2';
        comparativeContainer.innerHTML = `
            <label class="block text-sm text-gray-700">Comparative</label>
            <div class="grid grid-cols-1 gap-2">
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
            </div>
        `;
        comparativeWrapper.innerHTML = '';
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

    // Bind initial button
    comparativeBtn.addEventListener('click', openComparative);

    // accessibility: set initial aria attributes
    if (comparativeBtn) comparativeBtn.setAttribute('aria-pressed', 'false');

    // Add validation: comparative dates must be both filled and to >= from when present
    function validateComparativeDates() {
        const cmpFrom = form.querySelector('input[name="comparative_from"]');
        const cmpTo = form.querySelector('input[name="comparative_to"]');
        if (!cmpFrom || !cmpTo) return true; // not open
        // Clear previous
        clearError(cmpFrom); clearError(cmpTo);
        if (!cmpFrom.value && !cmpTo.value) return true; // optional, both empty OK
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

    // Reset button clears inputs, errors, and closes comparative
    const resetBtn = document.getElementById('resetFormBtn');
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            // Clear errors
            form.querySelectorAll('.error-text').forEach(el => el.remove());
            form.querySelectorAll('.border-red-400').forEach(el => el.classList.remove('border-red-400'));
            // Reset form to initial defaults
            form.reset();
            // Close comparative if open
            if (comparativeOpen) closeComparative();
        });
    }
});
</script>

</html>