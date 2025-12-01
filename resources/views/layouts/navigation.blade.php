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
    </div>
</nav>
