<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Add Business</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        html,
        body {
            font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, 'Helvetica Neue', Arial, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Navbar: responsive with user menu -->
    <nav class="bg-slate-100 fixed top-0 left-0 right-0 z-50 shadow-lg 
" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-3">
                            <x-logo class="h-8 w-8" />
                            <span class="font-semibold text-lg text-black">Manager Solution</span>
                        </a>
                    </div>
                </div>

                <div class="flex items-center">
                    @auth
                    <!-- Profile Dropdown -->
                    <div class="relative" id="profileDropdownWrapper">

                        <button id="profileDropdownBtn"
                            class="flex items-center gap-2 px-4 py-2 rounded-lg transition-all duration-200">

                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 
                    flex items-center justify-center text-white font-semibold">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>

                            <span class="text-sm text-black hidden sm:inline">{{ Auth::user()->name }}</span>

                            <svg id="profileDropdownArrow"
                                class="w-4 h-4 text-gray-300 transition-transform duration-200"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="profileDropdownMenu"
                            class="hidden absolute right-0 mt-2 w-56 rounded-lg 
                bg-[#1a1a1a] border border-white/10 shadow-xl
                overflow-hidden z-50 transition-all duration-200 origin-top">

                            <!-- User Info -->
                            <div class="px-4 py-3 border-b border-white/10">
                                <p class="text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-400">{{ Auth::user()->email }}</p>
                            </div>

                            <div class="py-2">
                                <!-- Profile -->
                                <a href="{{ route('profile.edit') }}"
                                    class="flex items-center gap-3 px-4 py-2 text-sm text-gray-300 
                      hover:bg-white/5 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Profile
                                </a>

                                <!-- Logout -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-full text-left flex items-center gap-3 px-4 py-2 text-sm text-gray-300 
                               hover:bg-white/5 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>


                    @else
                    <div class="hidden sm:flex sm:items-center sm:space-x-4">
                        <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-indigo-700">Login</a>
                        <a href="{{ route('register') }}" class="text-sm text-gray-600 hover:text-indigo-700">Register</a>
                    </div>
                    @endauth

                    <!-- Mobile menu button -->
                    <div class="-mr-2 flex sm:hidden">
                        <button type="button" id="mobile-menu-button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" aria-expanded="false">
                            <span class="sr-only">Open main menu</span>
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </nav>

        <main class="min-h-screen px-8 py-10 mt-15">
            <div class="max-w-6xl mx-auto">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h1 class="text-4xl font-extrabold text-black">Businesses</h1>
                        <p class="mt-2 text-black">Manage your business listings, add new entries, or remove existing ones.</p>
                    </div>
                    <div>
                        <button id="openAddBusiness" class="inline-flex items-center gap-2 px-5 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700">Add Business</button>
                    </div>
                </div>

                @if(session('success'))
                <div class="mb-4 inline-block rounded-md bg-green-50 border border-green-200 text-green-800 px-4 py-2">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                <div class="mb-4 rounded-md border border-red-200 bg-red-50 text-red-800 px-4 py-3">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="mt-6">
                    <div class="mb-4">
                        <input id="searchBusiness" type="text" placeholder="Search businesses..." class="w-full border-1 border-slate-300 rounded-lg px-4 py-2 focus:outline-none bg-white shadow-sm transition focus:ring-2 focus:ring-indigo-400">
                    </div>
                    @include('components.business-table', ['businesses' => $businesses ?? null])
                </div>
            </div>

            <!-- Add Business Modal -->
            <div id="addBusinessModal" class="fixed inset-0 z-50 hidden items-center justify-center">
                <div class="absolute inset-0 bg-black/40" id="addBusinessBackdrop"></div>
                <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md mx-4 z-10">
                    <div class="px-6 py-4 border-b">
                        <h3 class="text-lg font-semibold text-gray-900">Create Business</h3>
                    </div>
                    <form action="{{ route('business.store') }}" method="POST" class="px-6 py-6">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label for="business_name" class="block text-sm font-medium text-gray-700">Business Name <span class="text-red-500">*</span></label>
                                <input type="text" id="business_name" name="business_name" required autofocus value="{{ old('business_name') }}" placeholder="e.g. Acme Ltd"
                                    class="mt-1 block w-full rounded-md border-1 border-slate-300 shadow-sm px-3 py-2 focus:ring-1 focus:ring-indigo-500">
                                @error('business_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <input type="hidden" name="is_active" value="1">
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" id="closeAddBusiness" class="px-4 py-2 rounded-md border bg-white   border-1 border-slate-300 shadow-lg">Cancel</button>
                            <button type="submit" class="px-4 py-2 rounded-md bg-indigo-600 text-white shadow-lg">Create business</button>
                        </div>
                    </form>
                </div>
            </div>
            <style>
                [x-cloak] {
                    display: none !important;
                }
            </style>
            <script>
                (function() {
                    const openBtn = document.getElementById('openAddBusiness');
                    const closeBtn = document.getElementById('closeAddBusiness');
                    const modal = document.getElementById('addBusinessModal');
                    const backdrop = document.getElementById('addBusinessBackdrop');
                    const input = document.getElementById('business_name');

                    function openModal() {
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                        setTimeout(() => input && input.focus(), 50);
                    }

                    function closeModal() {
                        modal.classList.remove('flex');
                        modal.classList.add('hidden');
                    }

                    openBtn && openBtn.addEventListener('click', openModal);
                    closeBtn && closeBtn.addEventListener('click', closeModal);
                    backdrop && backdrop.addEventListener('click', closeModal);
                    document.addEventListener('keydown', function(e) {
                        if (e.key === 'Escape') closeModal();
                    });
                })();

                document.addEventListener("DOMContentLoaded", () => {
                    const btn = document.getElementById("profileDropdownBtn");
                    const menu = document.getElementById("profileDropdownMenu");
                    const arrow = document.getElementById("profileDropdownArrow");

                    btn.addEventListener("click", () => {
                        menu.classList.toggle("hidden");

                        // rotate arrow
                        arrow.classList.toggle("rotate-180");
                    });

                    // Close when clicking outside
                    document.addEventListener("click", (e) => {
                        if (!document.getElementById("profileDropdownWrapper").contains(e.target)) {
                            menu.classList.add("hidden");
                            arrow.classList.remove("rotate-180");
                        }
                    });
                });
            </script>
        </main>