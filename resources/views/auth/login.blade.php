<x-guest-layout>

    <body class="font-inter">
        <div class="min-h-screen bg-indigo-50 flex items-center justify-center p-4">
            <!-- Background decorative elements -->
          

            <div class="relative w-full max-w-md">
                <!-- Logo/Brand section -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl mb-4 shadow-lg">
                        <x-logo class="h-16 w-16" />
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Welcome Back</h1>
                    <p class="text-gray-600">Sign in to your account to continue</p>
                </div>

                <!-- Login form card -->
              <div class="bg-indigo-100 rounded-2xl shadow-md border-8 border-indigo-700 p-8">

                    @if ($errors->has('email') || $errors->has('password'))
                    <div class="rounded-xl border border-red-200 bg-red-50 p-4 flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-red-800">Invalid email or password. Please try again.</p>
                        </div>
                    </div>
                    @endif

                    <form action="{{ route('login') }}" method="POST" class="space-y-6 ">
                        @csrf

                        <!-- Email Field -->
                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                    </svg>
                                </div>
                                <input type="email" name="email" id="email" value="{{ old('email') }}"
                                    class="shadow-xl block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                                    placeholder="Enter your email" autocomplete="email" required />
                            </div>
                            @error('email')
                            <p class="text-sm text-red-600 flex items-center space-x-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>{{ $message }}</span>
                            </p>
                            @enderror
                        </div>

                        <!-- Password Field -->
                        <div class="space-y-2">
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <input type="password" name="password" id="password"
                                    class="shadow-xl block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                                    placeholder="Enter your password" autocomplete="current-password" required />

                                <button type="button" onclick="togglePassword('password', 'togglePasswordIcon')"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500">
                                    <svg id="togglePasswordIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2 12s4-8 10-8 10 8 10 8-4 8-10 8-10-8-10-8z" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                            <p class="text-sm text-red-600 flex items-center space-x-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>{{ $message }}</span>
                            </p>
                            @enderror
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
                            </div>
                            <div>
                                <a href="{{ route('password.request') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500 transition duration-150 ease-in-out">
                                    Forgot your password?
                                </a>
                            </div>
                        </div>

                            <!-- Submit Button -->
                        <div class="mt-6 bg-indigo-500 font-bold rounded-xl shadow-sm hover:bg-indigo-600 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                            <button type="submit" class="group relative w-full flex text-white justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm  bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">

                                <!-- RIGHT ICON USING YOUR PROVIDED SVG -->
                                <span class="absolute right-0 inset-y-0 flex items-center pr-3">

                                    <svg class="h-5 w-5" fill="white" viewBox="0 0 89.63 122.88">
                                        <path d="M33.27,68.66H7.15a7.23,7.23,0,0,1,0-14.45H33.27l-8.48-9.46a7.25,7.25,0,0,1,.5-10.16,7.07,7.07,0,0,1,10.06.5L54.62,56.61a7.25,7.25,0,0,1-.06,9.72L35.35,87.78a7.07,7.07,0,0,1-10.06.5,7.25,7.25,0,0,1-.5-10.16l8.48-9.46Zm16.25,54.08a7.22,7.22,0,1,1-2.83-14.17l3.39-.67c16.33-3.24,25.1-5.09,25.1-27.69V40.63c0-21-9.34-22.76-24.8-25.65l-3.63-.68A7.21,7.21,0,1,1,49.46.13L53,.81c22.82,4.26,36.6,6.84,36.6,39.82V80.21c0,34.43-12.84,37.11-36.74,41.85l-3.37.68Z" />
                                     </svg>

                                </span>

                               SIGN IN

                            </button>
                        </div>



                        <!-- Sign up link -->
                        <div class="text-center">
                            <p class="text-sm text-gray-600">
                                Don't have an account yet?
                                <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-500 transition duration-150 ease-in-out">
                                    Create one now
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Add some custom CSS for animations -->
        <style>
            @keyframes blob {
                0% {
                    transform: translate(0px, 0px) scale(1);
                }

                33% {
                    transform: translate(30px, -50px) scale(1.1);
                }

                66% {
                    transform: translate(-20px, 20px) scale(0.9);
                }

                100% {
                    transform: translate(0px, 0px) scale(1);
                }
            }

            .animate-blob {
                animation: blob 7s infinite;
            }

            .animation-delay-2000 {
                animation-delay: 2s;
            }

            .animation-delay-4000 {
                animation-delay: 4s;
            }

            .font-inter {
                font-family: 'Inter', sans-serif;
            }
        </style>

         <script>
                function togglePassword(fieldId, iconId) {
                    const input = document.getElementById(fieldId);
                    const icon = document.getElementById(iconId);

                    if (input.type === "password") {
                        input.type = "text";
                        icon.innerHTML = `
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.875 18.825A10.05 10.05 0 0112 19c-6 0-10-7-10-7a21.8 21.8 0 015.44-5.44m3.78-.78A9.96 9.96 0 0112 5c6 0 10 7 10 7a21.6 21.6 0 01-4.185 4.868M9.88 9.88a3 3 0 104.24 4.24"/>
                        <line x1="3" y1="3" x2="21" y2="21" stroke="currentColor" stroke-width="2"/>
                    `;
                    } else {
                        input.type = "password";
                        icon.innerHTML = `
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2 12s4-8 10-8 10 8 10 8-4 8-10 8-10-8-10-8z" />
                    `;
                    }
                }
            </script>
    </body>
</x-guest-layout>