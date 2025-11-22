<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
        <div class="min-h-screen">
            <header>
                @include('layouts.navigation')
                <!-- Spacer to offset fixed navbar height -->
                <div class="h-16"></div>

                <!-- Page Heading -->
                @if (isset($header))
                    <div class="bg-white shadow dark:bg-gray-800">
                        <div class="mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </div>
                @endif
            </header>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <strong class="font-bold">Success!</strong>
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                </div>
            @endif
            
            @if(session('error'))
                <div class="mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                </div>
            @endif
            
            <!-- Page Content -->
            <main class="mx-auto py-6 px-4 sm:px-6 lg:px-8">
                @isset($slot)
                    {{ $slot }}
                @else
                    @yield('content')
                @endisset
            </main>
            
            {{-- Per-view scripts --}}
            @yield('scripts')
            @stack('scripts')
        </div>
    </body>
</html>
