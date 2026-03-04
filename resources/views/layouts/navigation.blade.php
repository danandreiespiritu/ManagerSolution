<nav x-data="{ open: false, showLogoutModal: false }" class="fixed top-0 left-0 right-0 md:right-0 p-2
             bg-gray-100 text-black
             border-b border-white/5 shadow-lg
             z-30"
             :class="$store.sidebar.open ? 'md:left-64' : 'md:left-18'">

    <div class="h-full flex items-center justify-between ">

        <!-- Left -->
        <div class="px-4">
            <h1 class="text-lg font-semibold ">
                Hey, {{ Auth::user()->name }}
            </h1>
            <p class="text-xs text-gray-400">
                {{ now()->format('l, F d, Y') }}
            </p>
        </div>

        <!-- Right -->
        <div class="flex items-center gap-4">
            <!-- Profile Dropdown -->
                <div x-data="{ profileOpen: false }" class="relative">
                <button @click="profileOpen = !profileOpen" 
                        class="flex items-center gap-2 px-2 py-2 rounded-lg 
                               transition-all duration-200">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 
                                flex items-center justify-center text-white
                         font-semibold">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <span class="text-sm hidden sm:inline">{{ Auth::user()->name }}</span>
                    <svg class="w-4 h-4  transition-transform" 
                         :class="{'rotate-180': profileOpen}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="profileOpen" 
                     @click.away="profileOpen = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     x-cloak
                     class="absolute right-0 mt-2 w-56 rounded-lg 
                            bg-[#1a1a1a] border border-white/10 
                            shadow-xl overflow-hidden z-50">
                    
                    <div class="px-4 py-3 border-b border-white/10">
                        <p class="text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-400">{{ Auth::user()->email }}</p>
                    </div>

                    <div class="py-2">
                        <a href="{{ route('dashboard') }}" 
                           class="flex items-center gap-3 px-4 py-2 text-sm text-gray-300 
                                  hover:bg-white/5 transition-colors">
                            <svg  class="w-4 h-4" xmlns="http://www.w3.org/2000/svg"  viewBox="0 -960 960 960" fill="#CCCCCC"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h240v720H200Zm320 0v-360h320v280q0 33-23.5 56.5T760-120H520Zm0-440v-280h240q33 0 56.5 23.5T840-760v200H520Z"/></svg>
                            Dashboard
                        </a>
                        <a href="{{ route('profile.edit') }}" 
                           class="flex items-center gap-3 px-4 py-2 text-sm text-gray-300 
                                  hover:bg-white/5 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Profile
                        </a>
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" 
                                    class="w-full flex items-center gap-3 px-4 py-2 text-sm text-gray-300 
                                           hover:bg-white/5 transition-colors text-left">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu Button -->
            <!-- <button @click="open = !open" class="sm:hidden text-gray-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button> -->
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div x-show="open" 
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-1"
         x-cloak
         class="sm:hidden absolute top-full left-0 right-0 
                bg-[#0b0b0b] border-b border-white/5 shadow-lg">
        
        <div class="px-4 py-3 border-b border-white/10">
            <p class="text-sm font-medium text-white">{{ Auth::user()->name }}</p>
            <p class="text-xs text-gray-400">{{ Auth::user()->email }}</p>
        </div>

        <div class="py-2">
            <a href="{{ route('dashboard') }}" 
               class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 
                      {{ request()->routeIs('dashboard') ? 'bg-white/5 text-white' : '' }}">
                Dashboard
            </a>
            <a href="{{ route('profile.edit') }}" 
               class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5">
                Profile
            </a>
            
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" 
                        class="w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-white/5">
                    Log Out
                </button>
            </form>
        </div>
    </div>
</nav>

<style>
    [x-cloak] { display: none !important; }
</style>
