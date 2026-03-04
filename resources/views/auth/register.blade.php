<x-guest-layout>

    <body class="font-inter">
        <div class="min-h-screen bg-indigo-50 flex items-center justify-center p-4 relative">

           

            <!-- Register Card Container -->
            <div class="relative w-full max-w-md ">
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl shadow-lg mb-4">
                        <x-logo class="h-16 w-16" />
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Create Your Account</h1>
                    <p class="text-gray-600">Register to manage your accounting securely</p>
                </div>

              <div class="bg-indigo-100 rounded-3xl border-4 border-blue-600 px-8 py-4  overflow-hidden">

                    @if ($errors->any())
                    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4">
                        <p class="text-sm font-bold text-red-700">Please fix the following:</p>
                        <ul class="mt-2 text-sm text-red-600 list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}" class="space-y-5  ">
                        @csrf

                        <!-- Full Name -->
                        <div class="space-y-2 mt-3">
                            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>

                            <div class="relative">
                                <!-- Icon -->
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5 text-gray-400"
                                        viewBox="0 -960 960 960" fill="currentColor">
                                        <path d="M234-276q51-39 114-61.5T480-360q69 0 132 22.5T726-276q35-41 54.5-93T800-480q0-133-93.5-226.5T480-800q-133 0-226.5 93.5T160-480q0 59 19.5 111t54.5 93Zm246-164q-59 0-99.5-40.5T340-580q0-59 40.5-99.5T480-720q59 0 99.5 40.5T620-580q0 59-40.5 99.5T480-440Zm0 360q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Z" />
                                    </svg>
                                </div>

                                <!-- Input -->
                                <input id="name" name="name" type="text"
                                    value="{{ old('name') }}"
                                    placeholder="Enter your full name"
                                    class="shadow-lg block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl bg-white
                      placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                    required autofocus />
                            </div>
                        </div>

                        <!-- Email Address -->
                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                    </svg>
                                </div>
                                <input type="email" name="email" id="email" value="{{ old('email') }}"
                                    class="shadow-lg block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
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

                        <!-- Password -->
                        <div class="space-y-2">
                            <label for="password" class="text-sm font-medium text-gray-700">Password</label>

                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <input id="password" name="password" type="password" minlength="8" placeholder="Enter password"
                                    class=" shadow-lg pl-10 pr-3 block w-full px-3 py-3 border border-gray-300 rounded-xl bg-white placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:outline-none" required />

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
                        </div>

                        <!-- Confirm Password -->
                        <div class="space-y-2">
                            <label for="password_confirmation" class="text-sm font-medium text-gray-700">Confirm Password</label>

                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <input id="password_confirmation" name="password_confirmation" type="password" minlength="8"
                                    placeholder="Confirm password"
                                    class=" shadow-lg pl-10 pr-3 block w-full px-3 py-3 border border-gray-300 rounded-xl bg-white placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:outline-none" required />

                                <button type="button" onclick="togglePassword('password_confirmation', 'toggleConfirmIcon')"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500">
                                    <svg id="toggleConfirmIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2 12s4-8 10-8 10 8 10 8-4 8-10 8-10-8-10-8z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Terms -->
                        <div class="flex items-center">
                            <input id="terms" name="terms" type="checkbox"
                                class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" required />
                            <label for="terms" class="ml-2 text-sm text-gray-700">
                                I agree to the <a href="#" class="text-blue-600 hover:text-blue-500">Terms</a> and <a href="#" class="text-blue-600 hover:text-blue-500">Privacy Policy</a>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-6 bg-indigo-500  font-bold rounded-xl shadow-sm hover:bg-indigo-600 focus-within:ring-2 focus-within:ring-blue-500">
                            <button type="submit"
                                class="group relative w-full flex justify-center py-3 px-4 text-white rounded-xl ">

                                <span class="absolute right-0 inset-y-0 flex items-center pr-3">
                                    <svg class="h-5 w-5" fill="white" viewBox="0 0 89.63 122.88">
                                        <path d="M33.27,68.66H7.15a7.23,7.23,0,0,1,0-14.45H33.27l-8.48-9.46a7.25,7.25,0,0,1,.5-10.16,7.07,7.07,0,0,1,10.06.5L54.62,56.61a7.25,7.25,0,0,1-.06,9.72L35.35,87.78a7.07,7.07,0,0,1-10.06.5,7.25,7.25,0,0,1-.5-10.16l8.48-9.46Zm16.25,54.08a7.22,7.22,0,1,1-2.83-14.17l3.39-.67c16.33-3.24,25.1-5.09,25.1-27.69V40.63c0-21-9.34-22.76-24.8-25.65l-3.63-.68A7.21,7.21,0,1,1,49.46.13L53,.81c22.82,4.26,36.6,6.84,36.6,39.82V80.21c0,34.43-12.84,37.11-36.74,41.85l-3.37.68Z" />
                                    </svg>
                                </span>

                               CREATE  ACCOUNT
                            </button>
                        </div>

                        <!-- Already Registered -->
                        <div class="text-center mb-1">
                            <p class="text-sm text-gray-700">
                                Already registered?
                                <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                    Sign in
                                </a>
                            </p>
                        </div>

                    </form>
                </div>
            </div>

            <!-- Animations -->
            <style>
                @keyframes blob {
                    0% {
                        transform: translate(0, 0) scale(1);
                    }

                    33% {
                        transform: translate(30px, -50px) scale(1.1);
                    }

                    66% {
                        transform: translate(-20px, 20px) scale(0.9);
                    }

                    100% {
                        transform: translate(0, 0) scale(1);
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

            <!-- Password Toggle Script -->
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
        </div>
    </body>
</x-guest-layout>