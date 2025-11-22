<nav x-data="{ open: false, scrolled: false }" @scroll.window="scrolled = window.scrollY > 0" class="bg-white border-b border-gray-100 fixed inset-x-0 top-0 z-50" :class="{ 'shadow-sm': scrolled }">
    <!-- Primary Navigation Menu -->
    <div class="mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        @php
                            $logoPath = hospital('logo_path');
                            $isAbsoluteUrl = $logoPath && (Str::startsWith($logoPath, ['http://', 'https://']));
                            $normalizedPath = $logoPath;
                            if ($logoPath && (Str::startsWith($logoPath, '/storage/') || Str::startsWith($logoPath, 'storage/'))) {
                                $normalizedPath = ltrim(Str::after($logoPath, '/storage/'), '/');
                            }
                        @endphp
                        @if($isAbsoluteUrl || ($normalizedPath && Storage::disk('public')->exists($normalizedPath)))
                            <img src="{{ $isAbsoluteUrl ? $logoPath : Storage::disk('public')->url($normalizedPath) }}" alt="{{ hospital('name') }}" class="block h-9 w-auto" />
                        @else
                            <x-hospital-avatar name="{{ hospital('name') }}" color="{{ hospital('theme_color') }}" size="9" class="block" />
                        @endif
                    </a>
                </div>

                <!-- Hospital Name -->
                <div class="flex items-center ms-3 flex-shrink-0">
                    <div class="text-base sm:text-lg font-semibold text-gray-800 truncate max-w-[30vw] sm:max-w-xs">{{ hospital('name') ?? config('app.name', 'Laravel') }}</div>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    @if(!auth()->user()?->isSuperadmin())
                        <x-nav-link :href="route('cases.index')" :active="request()->routeIs('cases.*')">
                            {{ __('Cases') }}
                        </x-nav-link>
                        <x-nav-link :href="route('pathways.index')" :active="request()->routeIs('pathways.*')">
                            {{ __('Pathways') }}
                        </x-nav-link>
                        <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                            {{ __('Reports') }}
                        </x-nav-link>
                    @endif
                    @auth
                        @if(auth()->user()?->isSuperadmin())
                            <x-nav-link :href="route('hospitals.index')" :active="request()->routeIs('hospitals.*')">
                                {{ __('Hospitals') }}
                            </x-nav-link>
                            <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                                {{ __('Users') }}
                            </x-nav-link>
                        @elseif(auth()->user()?->hasRole(\App\Models\User::ROLE_ADMIN))
                            <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                                {{ __('Users') }}
                            </x-nav-link>
                            <x-nav-link :href="route('audit-logs.index')" :active="request()->routeIs('audit-logs.*')">
                                {{ __('Audit Logs') }}
                            </x-nav-link>
                            <x-nav-link :href="route('cost-references.index')" :active="request()->routeIs('cost-references.*')">
                                {{ __('Cost References') }}
                            </x-nav-link>
                            <x-nav-link :href="route('jkn-cbg-codes.index')" :active="request()->routeIs('jkn-cbg-codes.*')">
                                {{ __('JKN CBG Codes') }}
                            </x-nav-link>
                            <x-dropdown class="inline-flex items-center sm:-my-px" align="right" width="48">
                                <x-slot name="trigger">
                                    @php $simrsActive = request()->routeIs('simrs.*'); @endphp
                                    <div role="button" class="{{ $simrsActive
                                        ? 'inline-flex items-center px-1 pt-1 border-b-2 border-indigo-400 text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out'
                                        : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out' }}">
                                        <span>{{ __('Data SIM RS') }}</span>
                                        <svg class="ms-1 fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link :href="route('simrs.master-barang')" :active="request()->routeIs('simrs.master-barang')">
                                        {{ __('Master Barang') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('simrs.tindakan-rawat-jalan')" :active="request()->routeIs('simrs.tindakan-rawat-jalan')">
                                        {{ __('Tindakan Rawat Jalan') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('simrs.tindakan-rawat-inap')" :active="request()->routeIs('simrs.tindakan-rawat-inap')">
                                        {{ __('Tindakan Rawat Inap') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('simrs.laboratorium')" :active="request()->routeIs('simrs.laboratorium')">
                                        {{ __('Laboratorium') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('simrs.radiologi')" :active="request()->routeIs('simrs.radiologi')">
                                        {{ __('Radiologi') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('simrs.operasi')" :active="request()->routeIs('simrs.operasi')">
                                        {{ __('Operasi') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('simrs.kamar')" :active="request()->routeIs('simrs.kamar')">
                                        {{ __('Kamar') }}
                                    </x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown -->
            @auth
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ auth()->user()?->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
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
            </div>
            @endauth

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            @if(!auth()->user()?->isSuperadmin())
                <x-responsive-nav-link :href="route('cases.index')" :active="request()->routeIs('cases.*')">
                    {{ __('Cases') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('pathways.index')" :active="request()->routeIs('pathways.*')">
                    {{ __('Pathways') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                    {{ __('Reports') }}
                </x-responsive-nav-link>
            @endif
            @auth
                @if(auth()->user()?->isSuperadmin())
                    <x-responsive-nav-link :href="route('hospitals.index')" :active="request()->routeIs('hospitals.*')">
                        {{ __('Hospitals') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                        {{ __('Users') }}
                    </x-responsive-nav-link>
                @elseif(auth()->user()?->hasRole(\App\Models\User::ROLE_ADMIN))
                    <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                        {{ __('Users') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('audit-logs.index')" :active="request()->routeIs('audit-logs.*')">
                        {{ __('Audit Logs') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('cost-references.index')" :active="request()->routeIs('cost-references.*')">
                        {{ __('Cost References') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('jkn-cbg-codes.index')" :active="request()->routeIs('jkn-cbg-codes.*')">
                        {{ __('JKN CBG Codes') }}
                    </x-responsive-nav-link>
                    <div class="pt-2 pb-3 space-y-1">
                        <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Data SIM RS') }}</div>
                        <x-responsive-nav-link :href="route('simrs.master-barang')" :active="request()->routeIs('simrs.master-barang')" class="pl-8">
                            {{ __('Master Barang') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('simrs.tindakan-rawat-jalan')" :active="request()->routeIs('simrs.tindakan-rawat-jalan')" class="pl-8">
                            {{ __('Tindakan Rawat Jalan') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('simrs.tindakan-rawat-inap')" :active="request()->routeIs('simrs.tindakan-rawat-inap')" class="pl-8">
                            {{ __('Tindakan Rawat Inap') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('simrs.laboratorium')" :active="request()->routeIs('simrs.laboratorium')" class="pl-8">
                            {{ __('Laboratorium') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('simrs.radiologi')" :active="request()->routeIs('simrs.radiologi')" class="pl-8">
                            {{ __('Radiologi') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('simrs.operasi')" :active="request()->routeIs('simrs.operasi')" class="pl-8">
                            {{ __('Operasi') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('simrs.kamar')" :active="request()->routeIs('simrs.kamar')" class="pl-8">
                            {{ __('Kamar') }}
                        </x-responsive-nav-link>
                    </div>
                @endif
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        @auth
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ auth()->user()?->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ auth()->user()?->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
        @endauth
    </div>
</nav>

