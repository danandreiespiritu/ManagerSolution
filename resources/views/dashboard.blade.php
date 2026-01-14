<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Businesses</title>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <style>
        html, body {
            font-family: -apple-system, BlinkMacSystemFont, "Inter", "Segoe UI", Roboto, sans-serif;
        }

        /* Smooth modal transition */
        .modal-enter {
            opacity: 0;
            transform: scale(0.95);
        }
        .modal-enter-active {
            transition: all 200ms ease-out;
            opacity: 1;
            transform: scale(1);
        }
        .modal-leave {
            opacity: 1;
            transform: scale(1);
        }
        .modal-leave-active {
            transition: all 150ms ease-in;
            opacity: 0;
            transform: scale(0.95);
        }
    </style>
</head>

<body class="bg-gradient-to-br from-gray-100 to-gray-200 text-gray-900">

    <!-- NAVBAR -->
    <nav class="backdrop-blur bg-white/70 shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">

            <a href="{{ route('dashboard') }}" class="text-xl font-semibold tracking-tight text-gray-900">
                Manager Solution
            </a>

            <div class="flex items-center gap-4">
                @auth
                    <span class="text-sm font-medium text-gray-800">{{ auth()->user()?->name }}</span>

                    <button id="user-menu-button"
                        class="flex items-center justify-center h-9 w-9 rounded-full bg-indigo-100 text-indigo-700 font-semibold hover:ring-2 hover:ring-indigo-400 transition">
                        {{ strtoupper(substr(auth()->user()?->name, 0, 1)) }}
                    </button>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-indigo-600">Login</a>
                    <a href="{{ route('register') }}" class="text-sm text-gray-600 hover:text-indigo-600">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- PAGE CONTENT -->
    <main class="max-w-6xl mx-auto px-6 py-10">

        <!-- Header section -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold text-gray-900">Businesses</h1>
                <p class="text-gray-600 mt-1">
                    Manage, and add new businesses.
                </p>
            </div>

            <button id="openAddBusiness"
                class="px-5 py-2 rounded-xl bg-blue-600 text-white shadow hover:bg-blue-700 active:scale-95 transition">
                + Add Business
            </button>
        </div>

        <!-- FLASH MESSAGES -->
        @if(session('success'))
            <div class="mb-4 bg-green-100 text-green-800 border border-green-300 px-4 py-2 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 bg-red-100 text-red-800 border border-red-300 px-4 py-2 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @include('components.business-table', ['businesses' => $businesses ?? null])

    </main>

    <!-- MODAL -->
    <div id="addBusinessModal" class="fixed inset-0 z-50 hidden items-center justify-center">
        <div id="addBusinessBackdrop" class="absolute inset-0 bg-black/30 backdrop-blur-sm"></div>

        <div id="modalContent" class="relative bg-white rounded-2xl shadow-lg w-full max-w-md p-6 modal-enter">
            <h2 class="text-xl font-semibold mb-4">Add New Business</h2>

            <form method="POST" action="{{ route('business.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="text-sm font-medium text-gray-700">Business Name</label>
                    <input type="text" name="business_name" id="business_name" required
                        class="w-full mt-1 px-3 py-2 rounded-lg border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Acme Corporation">
                </div>

                <input type="hidden" name="is_active" value="1">

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" id="closeAddBusiness"
                        class="px-4 py-2 rounded-lg border border-gray-300 bg-white hover:bg-gray-100 transition">
                        Cancel
                    </button>

                    <button type="submit"
                        class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition">
                        Create
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JS: MODAL CONTROL -->
    <script>
        const openBtn = document.getElementById('openAddBusiness');
        const closeBtn = document.getElementById('closeAddBusiness');
        const modal = document.getElementById('addBusinessModal');
        const backdrop = document.getElementById('addBusinessBackdrop');
        const modalContent = document.getElementById('modalContent');

        function openModal() {
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            modalContent.classList.add('modal-enter-active');
        }

        function closeModal() {
            modalContent.classList.add('modal-leave-active');

            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');

                modalContent.classList.remove('modal-leave-active');
            }, 150);
        }

        openBtn.addEventListener('click', openModal);
        closeBtn.addEventListener('click', closeModal);
        backdrop.addEventListener('click', closeModal);

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeModal();
        });
    </script>

</body>
</html>
