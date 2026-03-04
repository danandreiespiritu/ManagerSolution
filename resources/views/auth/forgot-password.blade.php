<x-guest-layout>
    <body class="font-inter">
        <div class="min-h-screen bg-indigo-50 flex items-center justify-center p-4 relative">

            <!-- Decorative Gradient Blobs -->
         
            <!-- Container -->
            <div class="relative w-full max-w-md">
                
                <!-- Branding -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl shadow-lg mb-4">
                        <x-logo class="h-16 w-16"/>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Forgot Password</h1>
                    <p class="text-gray-600">Enter your email to receive a reset link</p>
                </div>

                <!-- Card -->
                <div class="bg-indigo-100  rounded-2xl shadow-xl border border-white/20 p-8">

                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                        @csrf

                        <!-- Email Address -->
                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207">
                                        </path>
                                    </svg>
                                </span>

                                <input id="email"
                                       type="email"
                                       name="email"
                                       value="{{ old('email') }}"
                                       required
                                       autofocus
                                       placeholder="Enter your email"
                                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" />
                            </div>

                            @error('email')
                                <p class="text-sm text-red-600 flex items-center space-x-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <!-- Submit Button (WITH SEND SVG ICON) -->
                        <div class="mt-6 rounded-xl shadow-md hover:shadow-lg transition bg-indigo-500 hover:bg-indigo-600 focus-within:ring-2 focus-within:ring-blue-500">
                            <button type="submit"
                                class="relative w-full flex justify-center items-center py-3 px-4 text-white rounded-xl font-medium">

                              

                                Email Password Reset Link
                                  <!-- SEND Icon -->
                                <span class="absolute right-0 inset-y-0 flex items-center pr-5 ">
                                    <svg class="h-5 w-5 text-blue-200 group-hover:text-white rotate-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M22 2L11 13"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M22 2L15 22L11 13L2 9L22 2Z"></path>
                                    </svg>
                                </span>
                            </button>
                        </div>

                        <!-- Return to login -->
                        <div class="text-center">
                            <p class="text-sm text-gray-600">
                                Remember your password?
                                <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                    Back to Login
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Animations -->
            <style>
                @keyframes blob {
                    0% { transform: translate(0px, 0px) scale(1); }
                    33% { transform: translate(30px, -50px) scale(1.1); }
                    66% { transform: translate(-20px, 20px) scale(0.9); }
                    100% { transform: translate(0px, 0px) scale(1); }
                }
                .animate-blob {
                    animation: blob 7s infinite;
                }
                .animation-delay-2000 { animation-delay: 2s; }
                .animation-delay-4000 { animation-delay: 4s; }
                .font-inter { font-family: 'Inter', sans-serif; }
            </style>

        </div>
    </body>
</x-guest-layout>
