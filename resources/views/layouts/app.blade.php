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
        @stack('styles')
    </head>
    <body class="font-sans antialiased bg-gray-100 dark:bg-gray-900" 
          x-data="{ 
              sidebarOpen: false, 
              sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true'
          }"
          @toggle-sidebar.window="sidebarOpen = !sidebarOpen"
          @sidebar-toggle.window="sidebarCollapsed = $event.detail.collapsed">
        <div class="flex h-screen overflow-hidden">
            <!-- Sidebar -->
            <div class="hidden lg:block">
                <x-sidebar />
            </div>
            
            <!-- Mobile Sidebar Overlay - Only visible on mobile (hidden on desktop via CSS) -->
            <div
                 x-show="sidebarOpen"
                 x-cloak
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="sidebarOpen = false"
                 class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-30"></div>

            <!-- Mobile Sidebar - Only visible on mobile (hidden on desktop via CSS) -->
            <div
                 x-show="sidebarOpen"
                 x-cloak
                 x-transition:enter="transition ease-in-out duration-300 transform"
                 x-transition:enter-start="-translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in-out duration-300 transform"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="-translate-x-full"
                 class="lg:hidden fixed inset-y-0 left-0 z-40">
                <x-sidebar />
            </div>

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col overflow-hidden transition-all duration-300 ease-in-out" 
                 :class="sidebarCollapsed ? 'lg:ml-16' : 'lg:ml-64'"
                 style="will-change: margin-left;">
                <!-- Mobile menu button (floating) - Only visible on mobile when sidebar is closed -->
                <button @click="sidebarOpen = true" 
                        class="lg:hidden fixed top-4 left-4 z-50 p-3 rounded-md bg-white shadow-lg text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-biru-dongker-700"
                        x-show="!sidebarOpen">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <!-- Main Content -->
                <main class="flex-1 overflow-y-auto bg-gray-50">
                    <!-- Page Heading -->
                    @if (isset($header))
                        <div class="bg-white shadow dark:bg-gray-800">
                            <div class="mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </div>
                    @endif

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
                    <div class="py-3 px-4 sm:px-6 lg:px-8">
                        @isset($slot)
                            {{ $slot }}
                        @else
                            @yield('content')
                        @endisset
                    </div>
                </main>
            </div>
        </div>
        
        {{-- Per-view scripts --}}
        @yield('scripts')
        @stack('scripts')
    </body>
</html>
