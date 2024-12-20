<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Marcopolo') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite('resources/css/app.css')

    <!-- Scripts -->
    @vite('resources/js/app.js')
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-base-200 dark:bg-gray-900">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
        @endisset

        <!-- Page Content -->
        <main>
            @hasSection('content')
            @yield('content')
            @else
            {{ $slot }}
            @endif
        </main>
    </div>
    <div class="bg-base-200">
        <div class="mx-auto sm:px-8 lg:px-10">
            <x-footer></x-footer>
        </div>
    </div>
</body>

</html>