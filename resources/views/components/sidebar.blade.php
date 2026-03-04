<aside x-data="{ open: $store.sidebar.open, hoverOpen: false }"
    :class="open ? 'w-64' : 'w-20'"
    class="fixed inset-y-0 left-0 bg-slate-600 shadow-lg border-r border-white/5 z-40 flex flex-col transition-all duration-300">

    <!-- TOP SECTION -->
    <div class="h-16 flex items-center px-3 border-b border-white/10 relative">
        <!-- LOGO WHEN SIDEBAR IS SMALL -->
        <!-- LOGO WHEN SIDEBAR IS SMALL -->
        <div class="flex items-center gap-3 relative justify-center w-full"
            @mouseenter="if (!open) hoverOpen = true"
            @mouseleave="hoverOpen = false"
            @click="if (!open) { open = true; $store.sidebar.open = true }"
            x-show="!open">

            <!-- Logo fades out on hover -->
            <x-logo class="w-8 h-8 border-2 border-white rounded-lg"
                x-show="!hoverOpen"
                x-transition:enter="transition-opacity duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                x-cloak />

            <!-- SVG fades in on hover -->
            <svg x-show="hoverOpen"
                x-transition:enter="transition-opacity duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute left-3 top-1/2 -translate-y-1/2"
                width="26" height="26" viewBox="0 0 24 24" fill="none"
                stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="2" width="20" height="20" rx="4"></rect>
                <line x1="9" y1="2" x2="9" y2="22"></line>
                <polyline points="13 8 17 12 13 16"></polyline>
            </svg>
            <!-- OPEN SIDEBAR TEXT -->
            <span
                x-show="hoverOpen"
                x-transition:enter="transition-all duration-200"
                x-transition:enter-start="opacity-0 translate-x-2"
                x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition-all duration-150"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 translate-x-2"
                class="absolute left-12 top-1/2 -translate-y-1/2
           text-xs font-semibold tracking-wide
           text-white bg-black/100 px-2 py-1 rounded-md
           whitespace-nowrap pointer-events-none">
                Open Sidebar
            </span>

        </div>



        <!-- LOGO + TITLE WHEN SIDEBAR IS OPEN -->
        <div x-show="open" class="flex items-center gap-3 w-full relative">
            <x-logo class="w-8 h-8 border-2 border-white rounded-lg" />
            <a href="{{ url('dashboard') }}"><span class="text-white font-semibold text-md">Manager Solution</span></a>
            <button @click="
                open = false;
                $store.sidebar.open = false;
                customersOpen = false;
                suppliersOpen = false
            "
                class="absolute right-3 top-1/2 -translate-y-1/2 p-1 rounded-md hover:bg-white/10 text-gray-300 hover:text-white">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="2" width="20" height="20" rx="4"></rect>
                    <line x1="15" y1="2" x2="15" y2="22"></line>
                    <polyline points="11 8 7 12 11 16"></polyline>
                </svg>
            </button>
        </div>
    </div>

    <!-- NAVIGATION -->
    <nav class="flex-1 px-3 py-4 space-y-1 text-sm overflow-auto scrollbar-hide scrollbar-thin scrollbar-thumb-rounded-full scrollbar-thumb-gray-400 scrollbar-track-transparent">
        @php $currentBizId = session('current_business_id'); @endphp

        {{-- SUMMARY --}}
        @if (Route::has('dashboard'))
        <a href="{{ $currentBizId && Route::has('business.summary') ? route('business.summary', $currentBizId) : route('dashboard') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors
                  {{ request()->routeIs('dashboard') || request()->routeIs('business.summary') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M9 21V9h6v12" />
            </svg>
            <span x-show="open" x-transition>Summary</span>
        </a>
        @endif

        {{-- JOURNAL ENTRY --}}
        @if (Route::has('journal.index'))
        <a href="{{ route('journal.index') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors
                  {{ request()->routeIs('journal.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" />
            </svg>
            <span x-show="open" x-transition>Journal Entry</span>
        </a>
        @endif

        {{-- ACCOUNTING PERIOD --}}
        @if (Route::has('accountingperiod.index'))
        <a href="{{ route('accountingperiod.index') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors
                  {{ request()->routeIs('accountingperiod.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" />
            </svg>
            <span x-show="open" x-transition>Accounting Period</span>
        </a>
        @endif

        {{-- CHART OF ACCOUNTS --}}
        @if (Route::has('chartofaccountIndex'))
        <a href="{{ route('chartofaccountIndex') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors
                  {{ request()->routeIs('chartofaccountIndex') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <span x-show="open" x-transition>Chart of Accounts</span>
        </a>
        @endif

        {{-- REPORTS --}}
        @if (Route::has('reports.index'))
        <a href="{{ route('reports.index') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors
                  {{ request()->routeIs('reports.*') || request()->routeIs('reportfilters.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6m4 4H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2z" />
            </svg>
            <span x-show="open" x-transition>Reports</span>
        </a>
        @endif

        {{-- CUSTOMERS DROPDOWN --}}
        <div x-data="{ customersOpen: {{ request()->routeIs('customers.*') || request()->routeIs('customercreditnotes.*') || request()->routeIs('payments.*') || request()->routeIs('invoicepayments.*') ? 'true' : 'false' }} }">
            <button @click="if (!open) { open = true; $store.sidebar.open = true } customersOpen = !customersOpen"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-lg transition-colors text-left
                    {{ request()->routeIs('customers.*') || request()->routeIs('customercreditnotes.*') || request()->routeIs('payments.*') || request()->routeIs('invoicepayments.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5V4H2v16h5" />
                </svg>
                <span x-show="open" x-transition>Customers</span>
                <svg x-show="open" class="w-5 h-5 ml-auto transition-transform" :class="customersOpen ? '-rotate-90' : 'rotate-90'" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <div x-show="customersOpen" x-collapse x-transition class="mt-1 space-y-1 pl-6">
                {{-- CUSTOMER SUBMENU ITEMS --}}
                @if (Route::has('customers.index'))
                <a href="{{ route('customers.index') }}" class="flex items-center gap-3 px-2 py-2 rounded-lg transition {{ request()->routeIs('customers.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5V4H2v16h5" />
                    </svg>
                    <span x-show="open" x-transition>Customer List</span>
                </a>
                @endif
                @if (Route::has('customercreditnotes.index'))
                <a href="{{ route('customercreditnotes.index') }}" class="flex items-center gap-3 px-2 py-2 rounded-lg transition {{ request()->routeIs('customercreditnotes.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l2 2 4-4M7 3h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z" />
                    </svg>
                    <span x-show="open" x-transition>Customer Credit Notes</span>
                </a>
                @endif
                @if (Route::has('payments.index'))
                <a href="{{ route('payments.index') }}" class="flex items-center gap-3 px-2 py-2 rounded-lg transition {{ request()->routeIs('payments.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-3.866 0-7 1.79-7 4s3.134 4 7 4 7-1.79 7-4-3.134-4-7-4z" />
                    </svg>
                    <span x-show="open" x-transition>Payments</span>
                </a>
                @endif
                {{-- Invoice Allocations --}}
                @if (Route::has('invoicepayments.index'))
                <a href="{{ route('invoicepayments.index') }}"
                    class="flex items-center gap-3 px-2 py-2 rounded-lg transition
                   {{ request()->routeIs('invoicepayments.*') 
                       ? 'bg-white/10 text-white'
                       : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">

                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z" />
                    </svg>

                    <span x-show="open" x-transition>Invoice Allocations</span>
                </a>
                @endif

            </div>
        </div>

        {{-- SUPPLIERS DROPDOWN --}}
        <div x-data="{ suppliersOpen: {{ request()->routeIs('suppliers.*') || request()->routeIs('supplierbills.*') || request()->routeIs('supplierpayments.*') || request()->routeIs('supplierbillpayments.*') || request()->routeIs('suppliercreditnotes.*') || request()->routeIs('supplierdebitnotes.*') ? 'true' : 'false' }} }">
            <button @click="if (!open) { open = true; $store.sidebar.open = true } suppliersOpen = !suppliersOpen"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-lg transition-colors text-left
                    {{ request()->routeIs('suppliers.*') || request()->routeIs('supplierbills.*') || request()->routeIs('supplierpayments.*') || request()->routeIs('supplierbillpayments.*') || request()->routeIs('suppliercreditnotes.*') || request()->routeIs('supplierdebitnotes.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16 11c1.657 0 3-1.343 3-3S17.657 5 16 5s-3 1.343-3 3 1.343 3 3 3zM8 11
                             c1.657 0 3-1.343 3-3S9.657 5 8 5 5 6.343 5 8s1.343 3 3 3zM8 13c-2.67 0-8 1.34-8 4v2h16v-2
                             c0-2.66-5.33-4-8-4zM16 13v6h6v-2c0-2.66-5.33-4-6-4z" />
                </svg>
                <span x-show="open" x-transition>Suppliers</span>
                <svg x-show="open" class="w-5 h-5 ml-auto transition-transform" :class="suppliersOpen ? '-rotate-90' : 'rotate-90'" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <div x-show="suppliersOpen" x-collapse x-transition class="mt-1 space-y-1 pl-6">
                {{-- SUPPLIER SUBMENU ITEMS --}}
                @if (Route::has('suppliers.index'))
                <a href="{{ route('suppliers.index') }}" class="flex items-center gap-3 px-2 py-2 rounded-lg transition {{ request()->routeIs('suppliers.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11c1.657 0 3-1.343 3-3S17.657 5 16 5s-3 1.343-3 3 1.343 3 3 3zM8 11c1.657 0 3-1.343 3-3S9.657 5 8 5 5 6.343 5 8s1.343 3 3 3zM8 13c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4zM16 13v6h6v-2c0-2.66-5.33-4-6-4z" />
                    </svg>
                    <span x-show="open" x-transition>Supplier List</span>
                </a>
                @endif

                @if (Route::has('supplierbills.index'))
                <a href="{{ route('supplierbills.index') }}"
                    class="flex items-center gap-3 px-2 py-2 rounded-lg transition
                {{ request()->routeIs('supplierbills.*')
                    ? 'bg-white/10 text-white'
                    : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">

                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12h6M9 16h6M8 6h8l2 4H6l2-4z" />
                    </svg>

                    <span x-show="open" x-transition>Supplier Bills</span>
                </a>
                @endif

                {{-- Supplier Payments --}}
                @if (Route::has('supplierpayments.index'))
                <a href="{{ route('supplierpayments.index') }}"
                    class="flex items-center gap-3 px-2 py-2 rounded-lg transition
                {{ request()->routeIs('supplierpayments.*')
                    ? 'bg-white/10 text-white'
                    : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">

                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 8v4l3 3" />
                    </svg>

                    <span x-show="open" x-transition>Supplier Payments</span>
                </a>
                @endif

                {{-- Bill Allocations --}}
                @if (Route::has('supplierbillpayments.index'))
                <a href="{{ route('supplierbillpayments.index') }}"
                    class="flex items-center gap-3 px-2 py-2 rounded-lg transition
                {{ request()->routeIs('supplierbillpayments.*')
                    ? 'bg-white/10 text-white'
                    : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">

                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 8c-1.1 0-2 .9-2 2v4h4v-4c0-1.1-.9-2-2-2z" />
                    </svg>

                    <span x-show="open" x-transition>Bill Allocations</span>
                </a>
                @endif

                {{-- Supplier Credit Notes --}}
                @if (Route::has('suppliercreditnotes.index'))
                <a href="{{ route('suppliercreditnotes.index') }}"
                    class="flex items-center gap-3 px-2 py-2 rounded-lg transition
                {{ request()->routeIs('suppliercreditnotes.*')
                    ? 'bg-white/10 text-white'
                    : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">

                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 8c-1.1 0-2 .9-2 2v4h4v-4c0-1.1-.9-2-2-2z" />
                    </svg>

                    <span x-show="open" x-transition>Supplier Credit Notes</span>
                </a>
                @endif

                {{-- Supplier Debit Notes --}}
                @if (Route::has('supplierdebitnotes.index'))
                <a href="{{ route('supplierdebitnotes.index') }}"
                    class="flex items-center gap-3 px-2 py-2 rounded-lg transition
                {{ request()->routeIs('supplierdebitnotes.*')
                    ? 'bg-white/10 text-white'
                    : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">

                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 8v8m4-4H8" />
                    </svg>

                    <span x-show="open" x-transition>Supplier Debit Notes</span>
                </a>
                @endif
                {{-- Repeat similar structure for supplierbills, supplierpayments, supplierbillpayments, suppliercreditnotes, supplierdebitnotes --}}
            </div>
        </div>
    </nav>
</aside>