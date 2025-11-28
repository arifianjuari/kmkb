<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>KMKB - Kendali Mutu Kendali Biaya</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
            }
            .glass-card {
                background: rgba(255, 255, 255, 0.9);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border: 1px solid rgba(255, 255, 255, 0.5);
            }
            .blob {
                position: absolute;
                filter: blur(80px);
                z-index: -1;
                opacity: 0.6;
                animation: float 10s ease-in-out infinite;
            }
            @keyframes float {
                0% { transform: translate(0, 0) scale(1); }
                33% { transform: translate(30px, -50px) scale(1.1); }
                66% { transform: translate(-20px, 20px) scale(0.9); }
                100% { transform: translate(0, 0) scale(1); }
            }
            @keyframes animate-blob {
                0%, 100% { transform: translate(0, 0) scale(1); }
                33% { transform: translate(30px, -50px) scale(1.1); }
                66% { transform: translate(-20px, 20px) scale(0.9); }
            }
            .animation-delay-2000 {
                animation-delay: 2s;
            }
            .animation-delay-4000 {
                animation-delay: 4s;
            }
            .animate-blob {
                animation: animate-blob 7s infinite;
            }
        </style>
    </head>
    <body class="antialiased text-slate-800 bg-slate-50 selection:bg-biru-dongker-700 selection:text-white">
        
        <!-- Navbar -->
        <nav class="fixed w-full z-50 top-0 transition-all duration-300 bg-white/80 backdrop-blur-md border-b border-slate-200/60">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <!-- Logo -->
                    <div class="flex items-center gap-2 cursor-pointer" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-biru-dongker-800 to-blue-500 flex items-center justify-center text-white font-bold">K</div>
                        <span class="text-xl font-bold tracking-tight text-slate-900">KMKB</span>
                    </div>
                    
                    <!-- Desktop Navigation -->
                    <div class="hidden md:flex items-center space-x-8">
                        <a href="#features" class="text-sm font-medium text-slate-600 hover:text-biru-dongker-800 transition-colors">Fitur</a>
                        <a href="#benefit" class="text-sm font-medium text-slate-600 hover:text-biru-dongker-800 transition-colors">Manfaat</a>
                        <a href="#about" class="text-sm font-medium text-slate-600 hover:text-biru-dongker-800 transition-colors">Tentang</a>
                    </div>

                    <!-- Right Side Actions -->
                    <div class="hidden md:flex items-center space-x-4">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="text-sm font-medium text-slate-700 hover:text-biru-dongker-800 transition-colors">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="text-sm font-medium text-slate-700 hover:text-biru-dongker-800 transition-colors">Log in</a>
                                <a href="{{ route('register') }}" class="btn btn-primary rounded-full">
                                    Get Started
                                </a>
                            @endauth
                        @endif
                    </div>

                    <!-- Mobile Menu Button -->
                    <div class="md:hidden">
                        <button id="mobile-menu-btn" class="text-slate-700 hover:text-biru-dongker-800 p-2">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-slate-100 absolute w-full shadow-xl">
                <div class="px-6 py-6 space-y-4">
                    <a href="#features" class="block text-base font-medium text-slate-700">Fitur</a>
                    <a href="#benefit" class="block text-base font-medium text-slate-700">Manfaat</a>
                    <hr class="border-slate-100">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="block text-base font-medium text-biru-dongker-800">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="block text-base font-medium text-slate-700">Log in</a>
                            <a href="{{ route('register') }}" class="block w-full px-6 py-3 text-center text-base font-medium text-white bg-biru-dongker-800 rounded-lg">Get Started</a>
                        @endauth
                    @endif
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative pt-24 pb-16 lg:pt-32 lg:pb-20 overflow-hidden bg-gradient-to-b from-white via-biru-dongker-200/20 to-white">
            <!-- Background Elements -->
            <div class="absolute top-0 right-0 -mt-20 -mr-20 w-96 h-96 bg-biru-dongker-700 rounded-full mix-blend-multiply filter blur-3xl opacity-25 animate-blob"></div>
            <div class="absolute top-0 left-0 -ml-20 w-72 h-72 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-25 animate-blob animation-delay-2000"></div>
            <div class="absolute -bottom-8 left-20 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-25 animate-blob animation-delay-4000"></div>

            <div class="max-w-7xl mx-auto px-6 lg:px-8 relative">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <!-- Hero Content -->
                    <div class="max-w-2xl">
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-gradient-to-r from-biru-dongker-300 to-blue-100 border border-biru-dongker-400 text-biru-dongker-900 text-xs font-bold uppercase tracking-wide mb-5">
                            <span class="relative flex h-2 w-2">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-biru-dongker-700 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-2 w-2 bg-biru-dongker-800"></span>
                            </span>
                            Sistem Manajemen Rumah Sakit Cerdas
                        </div>
                        <h1 class="text-5xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight text-slate-900 mb-5 leading-snug">
                            Kendali mutu, <br>
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-biru-dongker-800 via-biru-dongker-900 to-blue-600">kendali biaya.</span>
                        </h1>
                        <p class="text-lg text-slate-600 mb-8 leading-normal max-w-xl">
                            Platform terintegrasi untuk harmonisasi kualitas pelayanan medis dan efisiensi operasional melalui penerapan Clinical Pathway yang presisi.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center px-8 py-3.5 text-sm font-semibold text-white bg-gradient-to-r from-biru-dongker-800 to-biru-dongker-900 rounded-full shadow-xl shadow-biru-dongker-2000/30 hover:shadow-biru-dongker-800/50 hover:from-biru-dongker-900 hover:to-biru-dongker-950 transform hover:-translate-y-1 transition-all duration-200">
                                    Akses Dashboard
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-8 py-3.5 text-sm font-semibold text-white bg-gradient-to-r from-biru-dongker-800 to-biru-dongker-900 rounded-full shadow-xl shadow-biru-dongker-2000/30 hover:shadow-biru-dongker-800/50 hover:from-biru-dongker-900 hover:to-biru-dongker-950 transform hover:-translate-y-1 transition-all duration-200">
                                    Mulai Sekarang
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                </a>
                                <a href="#features" class="inline-flex items-center justify-center px-8 py-3.5 text-sm font-semibold text-biru-dongker-900 bg-white hover:bg-biru-dongker-200 border-2 border-biru-dongker-400 hover:border-biru-dongker-500 rounded-full shadow-sm transition-all duration-200">
                                    Pelajari Fitur
                                </a>
                            @endauth
                        </div>
                        
                        <div class="mt-8 flex items-center gap-4 text-sm text-slate-500">
                            <div class="flex -space-x-2">
                                <div class="w-8 h-8 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center text-xs font-bold">RS</div>
                                <div class="w-8 h-8 rounded-full bg-slate-300 border-2 border-white flex items-center justify-center text-xs font-bold">MD</div>
                                <div class="w-8 h-8 rounded-full bg-slate-400 border-2 border-white flex items-center justify-center text-xs font-bold">+</div>
                            </div>
                            <p>Dipercaya oleh 50+ Rumah Sakit</p>
                        </div>
                    </div>

                    <!-- Hero Visual (Dashboard Mockup) -->
                    <div class="relative lg:h-[640px] flex items-center justify-center perspective-1000">
                        <div class="relative w-full max-w-[600px] transform rotate-y-[-5deg] rotate-x-[2deg] hover:rotate-0 transition-transform duration-700 ease-out z-10">
                            <!-- Main Window -->
                            <div class="bg-white rounded-3xl shadow-2xl shadow-biru-dongker-2000/20 border-[6px] border-slate-900/10 overflow-hidden ring-4 ring-biru-dongker-300/50">
                                <!-- Window Header -->
                                <div class="bg-slate-50 border-b border-slate-100 px-4 py-3 flex items-center justify-between">
                                    <div class="flex gap-2">
                                        <div class="w-3 h-3 rounded-full bg-red-400"></div>
                                        <div class="w-3 h-3 rounded-full bg-biru-dongker-400"></div>
                                        <div class="w-3 h-3 rounded-full bg-green-400"></div>
                                    </div>
                                    <div class="text-xs font-medium text-slate-400">KMKB Dashboard</div>
                                    <div class="w-4"></div>
                                </div>

                                <!-- Dashboard Content -->
                                <div class="flex h-[400px]">
                                    <!-- Sidebar -->
                                    <div class="w-16 bg-slate-900 flex flex-col items-center py-6 gap-6 border-r border-slate-800">
                                        <div class="w-8 h-8 rounded bg-biru-dongker-700 flex items-center justify-center text-white font-bold text-xs">K</div>
                                        <div class="w-6 h-6 text-slate-400 hover:text-white cursor-pointer"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg></div>
                                        <div class="w-6 h-6 text-biru-dongker-600 cursor-pointer"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg></div>
                                        <div class="w-6 h-6 text-slate-400 hover:text-white cursor-pointer"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                                    </div>

                                    <!-- Main Area -->
                                    <div class="flex-1 bg-slate-50 p-6 overflow-hidden">
                                        <!-- Top Stats Row -->
                                        <div class="flex justify-between items-center mb-6">
                                            <h3 class="text-lg font-bold text-slate-800">Overview Kinerja</h3>
                                            <div class="flex gap-2 text-xs text-slate-500 bg-white px-2 py-1 rounded border border-slate-200">
                                                <span>Bulan Ini</span>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-3 gap-4 mb-6">
                                            <!-- Stat Card 1 -->
                                            <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                                                <div class="text-xs text-slate-500 mb-1">Pasien Rawat Inap</div>
                                                <div class="text-xl font-bold text-slate-800">842</div>
                                                <div class="text-xs text-emerald-600 flex items-center mt-1">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                                    +12%
                                                </div>
                                            </div>
                                            <!-- Stat Card 2 -->
                                            <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                                                <div class="text-xs text-slate-500 mb-1">Kepatuhan CP</div>
                                                <div class="text-xl font-bold text-slate-800">96.8%</div>
                                                <div class="text-xs text-emerald-600 flex items-center mt-1">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                                    +2.4%
                                                </div>
                                            </div>
                                            <!-- Stat Card 3 -->
                                            <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                                                <div class="text-xs text-slate-500 mb-1">Avg Cost Variance</div>
                                                <div class="text-xl font-bold text-slate-800">-4.2%</div>
                                                <div class="text-xs text-biru-dongker-800 flex items-center mt-1">
                                                    Efisiensi Tinggi
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Chart Area -->
                                        <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm h-40 relative">
                                            <div class="flex justify-between mb-4">
                                                <div class="text-sm font-semibold text-slate-700">Tren Klaim vs Biaya Riil</div>
                                            </div>
                                            <div class="flex items-end justify-between h-24 px-2 gap-3">
                                                <!-- Bar Group 1 -->
                                                <div class="w-full flex gap-1 items-end h-full">
                                                    <div class="w-full bg-biru-dongker-300 rounded-t h-[40%]"></div>
                                                    <div class="w-full bg-biru-dongker-700 rounded-t h-[30%]"></div>
                                                </div>
                                                <!-- Bar Group 2 -->
                                                <div class="w-full flex gap-1 items-end h-full">
                                                    <div class="w-full bg-biru-dongker-300 rounded-t h-[60%]"></div>
                                                    <div class="w-full bg-biru-dongker-700 rounded-t h-[45%]"></div>
                                                </div>
                                                <!-- Bar Group 3 -->
                                                <div class="w-full flex gap-1 items-end h-full">
                                                    <div class="w-full bg-biru-dongker-300 rounded-t h-[50%]"></div>
                                                    <div class="w-full bg-biru-dongker-700 rounded-t h-[40%]"></div>
                                                </div>
                                                <!-- Bar Group 4 -->
                                                <div class="w-full flex gap-1 items-end h-full">
                                                    <div class="w-full bg-biru-dongker-300 rounded-t h-[80%]"></div>
                                                    <div class="w-full bg-biru-dongker-700 rounded-t h-[65%]"></div>
                                                </div>
                                                <!-- Bar Group 5 -->
                                                <div class="w-full flex gap-1 items-end h-full">
                                                    <div class="w-full bg-biru-dongker-300 rounded-t h-[75%]"></div>
                                                    <div class="w-full bg-biru-dongker-700 rounded-t h-[60%]"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Floating Context Cards -->
                            <div class="absolute -right-12 top-20 glass-card p-5 rounded-2xl shadow-2xl shadow-green-500/20 border border-green-200/50 animate-[bounce_4s_infinite] z-20">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center text-white shadow-lg">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <div>
                                        <div class="text-xs font-medium text-slate-500 mb-1">Status Audit</div>
                                        <div class="text-base font-bold text-slate-900">Compliant</div>
                                    </div>
                                </div>
                            </div>

                             <div class="absolute -left-8 bottom-24 glass-card p-5 rounded-2xl shadow-2xl shadow-blue-500/20 border border-blue-200/50 animate-[bounce_5s_infinite] z-20">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-biru-dongker-800 flex items-center justify-center text-white shadow-lg">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    </div>
                                    <div>
                                        <div class="text-xs font-medium text-slate-500 mb-1">Efisiensi</div>
                                        <div class="text-base font-bold text-slate-900">Optimal</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-20 bg-gradient-to-b from-white via-biru-dongker-200/30 to-white relative">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(99,102,241,0.05),transparent_70%)]"></div>
            <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
                <div class="text-center max-w-3xl mx-auto mb-12">
                    <h2 class="text-xs font-bold text-biru-dongker-900 uppercase tracking-wider mb-3">Platform Features</h2>
                    <p class="mt-3 text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 leading-snug">Solusi Komprehensif untuk Rumah Sakit Modern</p>
                    <p class="mt-4 text-base text-slate-600 max-w-2xl mx-auto leading-normal">Platform terdepan yang mengintegrasikan manajemen kualitas dan biaya dalam satu ekosistem digital.</p>
                </div>
                
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Feature 1 -->
                    <div class="bg-white p-8 rounded-3xl shadow-xl shadow-slate-200/60 border-2 border-slate-100 hover:border-biru-dongker-500 transition-all hover:-translate-y-2 group">
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 text-white shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        </div>
                        <h3 class="text-xl font-extrabold text-slate-900 mb-3">Clinical Pathway</h3>
                        <p class="text-slate-600 leading-normal text-sm">Standardisasi pelayanan medis dengan panduan klinis digital yang interaktif dan mudah diikuti oleh tenaga medis.</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="bg-white p-8 rounded-3xl shadow-xl shadow-slate-200/60 border-2 border-slate-100 hover:border-emerald-300 transition-all hover:-translate-y-2 group">
                        <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl flex items-center justify-center mb-6 text-white shadow-lg shadow-emerald-500/30 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-extrabold text-slate-900 mb-3">Cost Control</h3>
                        <p class="text-slate-600 leading-normal text-sm">Monitoring real-time biaya perawatan per pasien dibandingkan dengan tarif INA-CBGs untuk mencegah kerugian.</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="bg-white p-8 rounded-3xl shadow-xl shadow-slate-200/60 border-2 border-slate-100 hover:border-biru-dongker-500 transition-all hover:-translate-y-2 group">
                        <div class="w-14 h-14 bg-gradient-to-br from-biru-dongker-800 to-biru-dongker-900 rounded-2xl flex items-center justify-center mb-6 text-white shadow-lg shadow-biru-dongker-2000/30 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <h3 class="text-xl font-extrabold text-slate-900 mb-3">Advanced Analytics</h3>
                        <p class="text-slate-600 leading-normal text-sm">Dashboard visual yang menyajikan insight mendalam untuk pengambilan keputusan strategis manajemen.</p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="bg-white p-8 rounded-3xl shadow-xl shadow-slate-200/60 border-2 border-slate-100 hover:border-purple-300 transition-all hover:-translate-y-2 group">
                        <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6 text-white shadow-lg shadow-purple-500/30 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-extrabold text-slate-900 mb-3">Automated Compliance</h3>
                        <p class="text-slate-600 leading-normal text-sm">Sistem otomatis yang memastikan setiap tindakan medis mematuhi standar akreditasi dan regulasi.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats/Proof Section -->
        <section class="py-16 bg-gradient-to-br from-slate-900 via-biru-dongker-950 to-slate-900 text-white relative overflow-hidden border-t border-biru-dongker-900/50">
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 opacity-30">
                <div class="absolute -top-[50%] -left-[50%] w-[200%] h-[200%] bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-biru-dongker-900 via-slate-900 to-slate-900 animate-spin-slow"></div>
            </div>
            <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
                <div class="grid md:grid-cols-3 gap-12 text-center">
                    <div>
                        <div class="text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-biru-dongker-600 to-blue-400 mb-2">30%</div>
                        <div class="text-base font-medium text-slate-300">Peningkatan Efisiensi Biaya</div>
                    </div>
                    <div>
                        <div class="text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-green-400 mb-2">98%</div>
                        <div class="text-base font-medium text-slate-300">Kepatuhan Clinical Pathway</div>
                    </div>
                    <div>
                        <div class="text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-biru-dongker-600 mb-2">< 1%</div>
                        <div class="text-base font-medium text-slate-300">Resiko Audit Medis</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 bg-gradient-to-b from-white to-slate-50 border-t border-slate-200">
            <div class="max-w-5xl mx-auto px-6 lg:px-8">
                <div class="bg-gradient-to-r from-biru-dongker-800 via-biru-dongker-900 to-blue-600 rounded-3xl p-8 md:p-12 text-center text-white relative overflow-hidden shadow-2xl shadow-biru-dongker-2000/40">
                    <!-- Decorative circles -->
                    <div class="absolute top-0 left-0 w-64 h-64 bg-white opacity-10 rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl"></div>
                    <div class="absolute bottom-0 right-0 w-64 h-64 bg-white opacity-10 rounded-full translate-x-1/2 translate-y-1/2 blur-3xl"></div>
                    
                    <h2 class="text-3xl md:text-4xl font-extrabold mb-4 relative z-10 leading-snug">
                        Siap Transformasi Layanan Kesehatan Anda?
                    </h2>
                    <p class="text-biru-dongker-300 text-lg mb-8 max-w-2xl mx-auto relative z-10 leading-normal">
                        Bergabunglah dengan jaringan rumah sakit modern yang telah mengimplementasikan KMKB untuk pelayanan yang lebih baik.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center relative z-10">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center px-8 py-3.5 text-sm font-bold text-biru-dongker-900 bg-white hover:bg-biru-dongker-200 rounded-full shadow-xl border-none transition-all duration-200 transform hover:-translate-y-1">
                                Kembali ke Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-8 py-3.5 text-sm font-bold text-biru-dongker-900 bg-white hover:bg-biru-dongker-200 rounded-full shadow-xl border-none transition-all duration-200 transform hover:-translate-y-1">
                                Mulai Sekarang
                            </a>
                            <a href="#" class="inline-flex items-center justify-center px-8 py-3.5 text-sm font-semibold text-white bg-transparent border-2 border-white/40 hover:bg-white/10 rounded-full transition-all duration-200">
                                Hubungi Tim Sales
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gradient-to-br from-slate-950 via-slate-900 to-biru-dongker-950 border-t-2 border-biru-dongker-900/50 pt-16 pb-10 text-slate-400">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
                    <div class="col-span-1 lg:col-span-1">
                        <div class="flex items-center gap-2 mb-5">
                            <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-biru-dongker-800 to-blue-500 flex items-center justify-center text-white font-bold text-base">K</div>
                            <span class="text-xl font-bold text-white">KMKB</span>
                        </div>
                        <p class="text-sm leading-normal text-slate-400 max-w-xs">
                            Platform kendali mutu dan biaya berbasis clinical pathway terdepan untuk rumah sakit Indonesia.
                        </p>
                    </div>
                    
                    <div>
                        <h4 class="font-bold text-white mb-5 text-sm">Produk</h4>
                        <ul class="space-y-2.5 text-sm">
                            <li><a href="#" class="text-slate-400 hover:text-biru-dongker-600 transition-colors inline-block">Fitur Utama</a></li>
                            <li><a href="#" class="text-slate-400 hover:text-biru-dongker-600 transition-colors inline-block">Studi Kasus</a></li>
                            <li><a href="#" class="text-slate-400 hover:text-biru-dongker-600 transition-colors inline-block">Harga</a></li>
                            <li><a href="#" class="text-slate-400 hover:text-biru-dongker-600 transition-colors inline-block">Enterprise</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 class="font-bold text-white mb-5 text-sm">Perusahaan</h4>
                        <ul class="space-y-2.5 text-sm">
                            <li><a href="#" class="text-slate-400 hover:text-biru-dongker-600 transition-colors inline-block">Tentang Kami</a></li>
                            <li><a href="#" class="text-slate-400 hover:text-biru-dongker-600 transition-colors inline-block">Karir</a></li>
                            <li><a href="#" class="text-slate-400 hover:text-biru-dongker-600 transition-colors inline-block">Blog</a></li>
                            <li><a href="#" class="text-slate-400 hover:text-biru-dongker-600 transition-colors inline-block">Kontak</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 class="font-bold text-white mb-5 text-sm">Legal</h4>
                        <ul class="space-y-2.5 text-sm">
                            <li><a href="#" class="text-slate-400 hover:text-biru-dongker-600 transition-colors inline-block">Kebijakan Privasi</a></li>
                            <li><a href="#" class="text-slate-400 hover:text-biru-dongker-600 transition-colors inline-block">Syarat & Ketentuan</a></li>
                            <li><a href="#" class="text-slate-400 hover:text-biru-dongker-600 transition-colors inline-block">Keamanan Data</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="pt-6 border-t border-slate-800/80 flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-sm text-slate-500">&copy; {{ date('Y') }} KMKB System. All rights reserved.</p>
                    <div class="flex gap-6">
                        <a href="#" class="text-slate-500 hover:text-biru-dongker-600 transition-colors p-2 hover:bg-slate-800 rounded-lg">
                            <span class="sr-only">Twitter</span>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"></path></svg>
                        </a>
                        <a href="#" class="text-slate-500 hover:text-biru-dongker-600 transition-colors p-2 hover:bg-slate-800 rounded-lg">
                            <span class="sr-only">LinkedIn</span>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" clip-rule="evenodd"></path></svg>
                        </a>
                    </div>
                </div>
            </div>
        </footer>

        <script>
            // Mobile menu toggle
            const btn = document.getElementById('mobile-menu-btn');
            const menu = document.getElementById('mobile-menu');

            btn.addEventListener('click', () => {
                menu.classList.toggle('hidden');
            });
        </script>
    </body>
</html>
