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

    <body class="bg-gray-800">
        <!-- Navbar: responsive with user menu -->
        <nav class="bg-gradient-to-r from-[#0b0b0b] to-[#141414]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('chartofaccountIndex') }}" class="inline-flex items-center gap-3">
                                <svg class="h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" />
                                </svg>
                                <span class="font-semibold text-lg text-white">Manager Solution</span>
                            </a>
                        </div>
                        <div class="hidden sm:-my-px sm:ml-6 sm:flex sm:space-x-8">
                            <a href="{{ url('dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-white hover:border-indigo-300 hover:text-indigo-700">Dashboard</a>
                            <a href="{{ route('chartofaccountIndex') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-white hover:border-indigo-300 hover:text-indigo-700">Chart of Accounts</a>
                        </div>
                    </div>

                    <div class="flex items-center">
                        @auth
                            <div class="hidden sm:flex sm:items-center sm:space-x-4">
                                <div class="text-sm text-white">{{ auth()->user()?->name }}</div>
                                <div class="relative">
                                    <button id="user-menu-button" type="button" class="flex items-center gap-2 rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500" aria-expanded="false" aria-haspopup="true">
                                        <span class="inline-flex h-8 w-8 rounded-full bg-indigo-100 items-center justify-center text-indigo-700">{{ strtoupper(substr(auth()->user()?->name ?? 'U',0,1)) }}</span>
                                    </button>
                                    <div id="user-menu" class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 py-1" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button">
                                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50" role="menuitem">Profile</a>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-gray-50">Logout</button>
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

            <main class="min-h-screen px-8 py-10">
                <div class="max-w-6xl mx-auto">
                    <div class="flex items-start justify-between mb-6">
                        <div>
                            <h1 class="text-4xl font-extrabold text-white">Businesses</h1>
                            <p class="mt-2 text-white">Manage your business listings, add new entries, or remove existing ones.</p>
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
                            <input id="searchBusiness" type="text" placeholder="Search businesses..." class="w-full border rounded-lg px-4 py-2 focus:outline-none bg-white shadow-sm transition border-gray-200 focus:ring-2 focus:ring-indigo-400">
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
                                           class="mt-1 block w-full rounded-md border-gray-200 shadow-sm px-3 py-2 focus:ring-1 focus:ring-indigo-500">
                                    @error('business_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <input type="hidden" name="is_active" value="1">
                            </div>

                            <div class="mt-6 flex justify-end gap-3">
                                <button type="button" id="closeAddBusiness" class="px-4 py-2 rounded-md border bg-white border-gray-200">Cancel</button>
                                <button type="submit" class="px-4 py-2 rounded-md bg-indigo-600 text-white">Create business</button>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                    (function(){
                        const openBtn = document.getElementById('openAddBusiness');
                        const closeBtn = document.getElementById('closeAddBusiness');
                        const modal = document.getElementById('addBusinessModal');
                        const backdrop = document.getElementById('addBusinessBackdrop');
                        const input = document.getElementById('business_name');

                        function openModal(){
                            modal.classList.remove('hidden');
                            modal.classList.add('flex');
                            setTimeout(()=> input && input.focus(), 50);
                        }
                        function closeModal(){
                            modal.classList.remove('flex');
                            modal.classList.add('hidden');
                        }

                        openBtn && openBtn.addEventListener('click', openModal);
                        closeBtn && closeBtn.addEventListener('click', closeModal);
                        backdrop && backdrop.addEventListener('click', closeModal);
                        document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeModal(); });
                    })();
                </script>
            </main>