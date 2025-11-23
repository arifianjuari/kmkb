<nav x-data="{ 
        mobileMenuOpen: false,
        sidebarCollapsed: window.sidebarCollapsed || false
     }" 
     x-init="
        // Sync with body's sidebarCollapsed state via sidebar-toggle event
        window.addEventListener('sidebar-toggle', (e) => {
            sidebarCollapsed = e.detail.collapsed;
            window.sidebarCollapsed = e.detail.collapsed;
        });
     "
     class="bg-white border-b border-gray-200 fixed top-0 right-0 z-30 h-16 transition-all duration-300"
     :class="sidebarCollapsed ? 'left-0 lg:left-16' : 'left-0 lg:left-64'">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
        <!-- Left: Mobile menu button only -->
        <div class="flex items-center space-x-3 sm:space-x-4 flex-1 min-w-0">
            <!-- Mobile menu button (hamburger) - Only visible on mobile -->
            <button @click="mobileMenuOpen = !mobileMenuOpen; $dispatch('toggle-sidebar')" 
                    class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 flex-shrink-0">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <!-- Right: User menu & Notifications -->
        <div class="flex items-center space-x-4">
            @auth
                <!-- Notifications (optional) -->
                <button class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 relative">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <!-- Badge for notifications count (optional) -->
                    <!-- <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-400 ring-2 ring-white"></span> -->
                </button>

                <!-- User Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center space-x-3 text-sm focus:outline-none">
                            <div class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center text-white font-semibold">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="hidden md:block text-left">
                                <div class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</div>
                                <div class="text-xs text-gray-500">{{ auth()->user()->email }}</div>
                            </div>
                            <svg class="hidden md:block h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            @else
                <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900">Login</a>
            @endauth
        </div>
    </div>
</nav>
