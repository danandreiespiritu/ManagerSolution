<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Manager Solution</title>

    <!-- Tailwind -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- Alpine -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('sidebar', { open: true });
        });
    </script>

     
</head>

<body class=" bg-slate-100 min-h-screen font-sans">

    @include('components.sidebar')
    @include('layouts.navigation')

    <!-- Main content -->
    <main x-data class="pt-22 px-6 pb-10 transition-all duration-300" :class="$store.sidebar.open ? 'md:ml-64' : 'md:ml-20'">
        @isset($slot)
            {{ $slot }}
        @else
            @yield('content')
        @endisset
    </main>

</body>


</html>
