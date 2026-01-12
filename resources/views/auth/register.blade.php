<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8 text-black">
            <div class="text-center mb-6">
                <div class="mx-auto inline-flex items-center justify-center w-12 h-12 bg-emerald-600 rounded-md mb-3 shadow-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0-2.21 1.79-4 4-4s4 1.79 4 4-1.79 4-4 4-4-1.79-4-4zM4 20v-1a4 4 0 014-4h0"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-semibold text-black">Create your account</h2>
                <p class="mt-1 text-sm text-black">Sign up to manage your accounting securely.</p>
            </div>

            @if ($errors->any())
                <div class="mb-4 rounded-md bg-red-50 border border-red-100 p-3">
                    <p class="text-sm font-semibold text-black">Please fix the following:</p>
                    <ul class="mt-2 text-sm text-black list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-black">Full name</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                        class="mt-1 block w-full rounded-md border-gray-300 bg-white px-3 py-2 shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-black" />
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-black">Email address</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 bg-white px-3 py-2 shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-black" />
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-black">Password</label>
                    <input id="password" name="password" type="password" minlength="8" required
                        class="mt-1 block w-full rounded-md border-gray-300 bg-white px-3 py-2 shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-black" />
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-black">Confirm password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" minlength="8" required
                        class="mt-1 block w-full rounded-md border-gray-300 bg-white px-3 py-2 shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-black" />
                </div>

                <div class="flex items-center">
                    <input id="terms" name="terms" type="checkbox" required class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded" />
                    <label for="terms" class="ml-2 text-sm text-black">I agree to the <a href="#" class="text-black hover:text-black">Terms</a> and <a href="#" class="text-black hover:text-black">Privacy Policy</a>.</label>
                </div>

                <div class="bg-blue-600 rounded-md shadow-sm hover:bg-blue-700 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Create account
                    </button>
                </div>

                <div class="text-center">
                    <p class="text-sm text-black">Already registered? <a href="{{ route('login') }}" class="font-medium text-black hover:text-black">Sign in</a></p>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
