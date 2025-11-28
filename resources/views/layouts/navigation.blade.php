<nav x-data="{ 
        mobileMenuOpen: false,
        sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true'
     }" 
     @sidebar-toggle.window="sidebarCollapsed = $event.detail.collapsed"
     class="bg-white border-b border-gray-200 fixed top-0 right-0 z-30 h-16 transition-all duration-300 ease-in-out"
     :class="sidebarCollapsed ? 'left-0 lg:left-16' : 'left-0 lg:left-64'"
     style="will-change: left;">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
        <!-- Left: Mobile menu button only -->
        <div class="flex items-center space-x-3 sm:space-x-4 flex-1 min-w-0">
            <!-- Mobile menu button (hamburger) - Only visible on mobile -->
            <button @click="mobileMenuOpen = !mobileMenuOpen; $dispatch('toggle-sidebar')" 
                    class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-biru-dongker-700 flex-shrink-0">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <!-- Right: User menu & Notifications -->
        <div class="flex items-center space-x-4">
            @auth
                @php
                    $isAdmin = auth()->user()->hasRole(\App\Models\User::ROLE_ADMIN);
                @endphp
                
                <!-- Admin Menu (Admin only) -->
                @if($isAdmin)
                    <x-dropdown align="right" width="56">
                        <x-slot name="trigger">
                            <button class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-biru-dongker-700 relative">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="px-4 py-2 border-b border-gray-200">
                                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">System Administration</div>
                            </div>
                            <x-dropdown-link :href="route('users.index')" class="{{ request()->routeIs('users.*') ? 'bg-gray-100' : '' }}">
                                {{ __('Users') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('audit-logs.index')" class="{{ request()->routeIs('audit-logs.*') ? 'bg-gray-100' : '' }}">
                                {{ __('Audit Logs') }}
                            </x-dropdown-link>
                            <div class="border-t border-gray-200 my-1"></div>
                            <div class="px-4 py-2 text-xs text-gray-500 cursor-not-allowed">
                                {{ __('Roles & Permissions') }}
                            </div>
                            <div class="px-4 py-2 text-xs text-gray-500 cursor-not-allowed">
                                {{ __('API Tokens') }}
                            </div>
                            <div class="px-4 py-2 text-xs text-gray-500 cursor-not-allowed">
                                {{ __('System Settings') }}
                            </div>
                        </x-slot>
                    </x-dropdown>
                @endif

                <!-- Notifications (optional) -->
                <button class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-biru-dongker-700 relative">
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
                            <div class="h-8 w-8 rounded-full bg-biru-dongker-800 flex items-center justify-center text-white font-semibold">
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
