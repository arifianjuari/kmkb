@extends('layouts.app')

@section('content')
<div x-data="dashboardData()" x-init="init()" class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Tabs Navigation -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="flex -mb-px" aria-label="Tabs">
                    <button 
                        @click="activeTab = 'overview'"
                        :class="activeTab === 'overview' ? 'border-biru-dongker-500 text-biru-dongker-600 dark:text-biru-dongker-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                        class="whitespace-nowrap py-5 px-8 border-b-2 font-semibold text-base">
                        Overview
                    </button>
                    <button 
                        @click="activeTab = 'biaya_tarif'"
                        :class="activeTab === 'biaya_tarif' ? 'border-biru-dongker-500 text-biru-dongker-600 dark:text-biru-dongker-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                        class="whitespace-nowrap py-5 px-8 border-b-2 font-semibold text-base">
                        Biaya & Tarif
                    </button>
                    <button 
                        @click="activeTab = 'pathway_mutu'"
                        :class="activeTab === 'pathway_mutu' ? 'border-biru-dongker-500 text-biru-dongker-600 dark:text-biru-dongker-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                        class="whitespace-nowrap py-5 px-8 border-b-2 font-semibold text-base">
                        Pathway & Mutu
                    </button>
                    <button 
                        @click="activeTab = 'variance_jkn'"
                        :class="activeTab === 'variance_jkn' ? 'border-biru-dongker-500 text-biru-dongker-600 dark:text-biru-dongker-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                        class="whitespace-nowrap py-5 px-8 border-b-2 font-semibold text-base">
                        Case Variance & JKN
                    </button>
                    @can('viewCostingStatus')
                    <button 
                        @click="activeTab = 'data_proses'"
                        :class="activeTab === 'data_proses' ? 'border-biru-dongker-500 text-biru-dongker-600 dark:text-biru-dongker-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                        class="whitespace-nowrap py-5 px-8 border-b-2 font-semibold text-base">
                        Data & Proses
                    </button>
                    @endcan
                </nav>
            </div>
        </div>

        <!-- Global Filter Bar -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Periode -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Periode</label>
                    <select x-model="filters.period" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-biru-dongker-500 focus:ring-biru-dongker-500">
                        <template x-for="period in availablePeriods" :key="period.value">
                            <option :value="period.value" x-text="period.label"></option>
                        </template>
                    </select>
                </div>

                <!-- Payer Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payer Type</label>
                    <select x-model="filters.payerType" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-biru-dongker-500 focus:ring-biru-dongker-500">
                        <option value="all">All</option>
                        <option value="jkn">JKN</option>
                        <option value="non_jkn">Non-JKN</option>
                    </select>
                </div>

                <!-- Kelas Perawatan (Opsional) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kelas Perawatan</label>
                    <select x-model="filters.kelasRawat" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-biru-dongker-500 focus:ring-biru-dongker-500">
                        <option value="all">All</option>
                        <option value="VIP">VIP</option>
                        <option value="I">I</option>
                        <option value="II">II</option>
                        <option value="III">III</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-end gap-2">
                    <button @click="applyFilters()" class="btn-primary flex-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Apply
                    </button>
                    <button @click="resetFilters()" class="btn-secondary">
                        Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="space-y-6">
            <!-- Tab 1: Overview -->
            <div x-show="activeTab === 'overview'" x-cloak>
                @include('dashboard.tabs.overview')
            </div>

            <!-- Tab 2: Biaya & Tarif -->
            <div x-show="activeTab === 'biaya_tarif'" x-cloak>
                @include('dashboard.tabs.biaya-tarif')
            </div>

            <!-- Tab 3: Pathway & Mutu -->
            <div x-show="activeTab === 'pathway_mutu'" x-cloak>
                @include('dashboard.tabs.pathway-mutu')
            </div>

            <!-- Tab 4: Case Variance & JKN -->
            <div x-show="activeTab === 'variance_jkn'" x-cloak>
                @include('dashboard.tabs.variance-jkn')
            </div>

            <!-- Tab 5: Data & Proses -->
            @can('viewCostingStatus')
            <div x-show="activeTab === 'data_proses'" x-cloak>
                @include('dashboard.tabs.data-proses')
            </div>
            @endcan
        </div>
    </div>
</div>

@push('scripts')
<script>
function dashboardData() {
    return {
        activeTab: 'overview',
        filters: {
            period: '{{ date("Y-m") }}',
            payerType: 'all',
            kelasRawat: 'all',
            pathway: 'all'
        },
        availablePeriods: [],
        loading: false,
        
        // Data untuk setiap tab
        overview: {
            kpis: [],
            costVsCbg: null,
            complianceVsLos: null,
            topPathways: []
        },
        biayaTarif: {
            topCostCenters: { labels: [], data: [] },
            unitCostTrend: { labels: [], datasets: [] },
            tarifVsUnitCost: [],
            unitCostVsCbg: [],
            availableServices: [],
            tarifFilter: 'all',
            selectedServices: [] // Layanan yang dipilih untuk trend chart
        },
        pathwayMutu: {
            compliance: { labels: [], data: [] },
            los: { labels: [], losStandard: [], losActual: [] },
            summary: [],
            nonCompliantSteps: [],
            availablePathways: []
        },
        varianceJkn: {
            distribution: null,
            kpis: [],
            topCases: [],
            byPathway: []
        },
        dataProses: {
            status: {},
            checks: [],
            logs: []
        },

        init() {
            this.generatePeriods();
            this.loadTabData();
            
            // Watch for tab changes
            this.$watch('activeTab', () => {
                this.loadTabData();
            });
        },

        generatePeriods() {
            const periods = [];
            const now = new Date();
            for (let i = 11; i >= 0; i--) {
                const date = new Date(now.getFullYear(), now.getMonth() - i, 1);
                const value = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
                const label = date.toLocaleDateString('id-ID', { year: 'numeric', month: 'long' });
                periods.push({ value, label });
            }
            this.availablePeriods = periods;
        },

        async loadTabData() {
            this.loading = true;
            try {
                switch(this.activeTab) {
                    case 'overview':
                        await this.loadOverview();
                        break;
                    case 'biaya_tarif':
                        await this.loadBiayaTarif();
                        break;
                    case 'pathway_mutu':
                        await this.loadPathwayMutu();
                        break;
                    case 'variance_jkn':
                        await this.loadVarianceJkn();
                        break;
                    case 'data_proses':
                        await this.loadDataProses();
                        break;
                }
            } catch (error) {
                console.error('Error loading tab data:', error);
            } finally {
                this.loading = false;
            }
        },

        async loadOverview() {
            const params = new URLSearchParams(this.filters);
            try {
                const response = await fetch(`/api/dashboard/overview?${params}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin'
                });
                
                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({ error: 'Unknown error' }));
                    console.error('API Error:', errorData);
                    alert('Gagal memuat data: ' + (errorData.error || response.statusText));
                    return;
                }
                
                const data = await response.json();
                console.log('Overview data loaded:', data);
                this.overview = data;
                this.$nextTick(() => {
                    this.renderOverviewCharts();
                });
            } catch (error) {
                console.error('Error loading overview:', error);
                alert('Terjadi error saat memuat data dashboard');
            }
        },

        async loadBiayaTarif() {
            const params = new URLSearchParams(this.filters);
            if (this.biayaTarif && this.biayaTarif.tarifFilter) {
                params.append('tarifFilter', this.biayaTarif.tarifFilter);
            }
            // Preserve selectedServices saat reload
            const selectedServices = this.biayaTarif.selectedServices || [];
            try {
                const response = await fetch(`/api/dashboard/biaya-tarif?${params}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin'
                });
                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({ error: 'Unknown error' }));
                    console.error('API Error:', errorData);
                    throw new Error(errorData.error || 'Failed to load data');
                }
                const data = await response.json();
                console.log('Biaya Tarif data loaded:', data);
                this.biayaTarif = data;
                this.biayaTarif.selectedServices = selectedServices; // Restore selected services
                this.$nextTick(() => {
                    this.renderBiayaTarifCharts();
                });
            } catch (error) {
                console.error('Error loading biaya tarif:', error);
                alert('Gagal memuat data Biaya & Tarif: ' + error.message);
            }
        },

        async updateUnitCostTrend() {
            // Update hanya chart unit cost trend berdasarkan layanan yang dipilih
            if (!this.biayaTarif.selectedServices || this.biayaTarif.selectedServices.length === 0) {
                alert('Pilih minimal satu layanan untuk melihat trend');
                return;
            }

            const params = new URLSearchParams(this.filters);
            // Kirim selected services sebagai parameter
            this.biayaTarif.selectedServices.forEach(serviceId => {
                params.append('services[]', serviceId);
            });

            try {
                console.log('Updating unit cost trend with services:', this.biayaTarif.selectedServices);
                const response = await fetch(`/api/dashboard/biaya-tarif?${params}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin'
                });
                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({ error: 'Unknown error' }));
                    console.error('API Error:', errorData);
                    throw new Error(errorData.error || 'Failed to load trend data');
                }
                const data = await response.json();
                console.log('Unit cost trend data received:', data);
                console.log('Unit cost trend datasets:', data.unitCostTrend?.datasets);
                
                // Update hanya unit cost trend data, preserve data lainnya
                this.biayaTarif.unitCostTrend = data.unitCostTrend;
                
                // Render hanya unit cost trend chart setelah data di-update
                await this.$nextTick();
                setTimeout(() => {
                    console.log('Rendering updated unit cost trend chart');
                    this.renderUnitCostTrendChart();
                }, 150);
            } catch (error) {
                console.error('Error updating unit cost trend:', error);
                alert('Gagal memuat trend unit cost: ' + error.message);
            }
        },

        async loadPathwayMutu() {
            const params = new URLSearchParams(this.filters);
            // Hanya kirim pathway jika bukan 'all' atau null
            if (this.filters.pathway && this.filters.pathway !== 'all') {
                params.append('pathway', this.filters.pathway);
            }
            try {
                const response = await fetch(`/api/dashboard/pathway-mutu?${params}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin'
                });
                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({ error: 'Unknown error' }));
                    console.error('API Error:', errorData);
                    throw new Error(errorData.error || 'Failed to load data');
                }
                const data = await response.json();
                console.log('Pathway Mutu data loaded:', data);
                console.log('Compliance data:', data.compliance);
                console.log('LOS data:', data.los);
                console.log('Summary data:', data.summary);
                console.log('Non-compliant steps:', data.nonCompliantSteps);
                this.pathwayMutu = data;
                // Pastikan filter pathway tetap sinkron
                if (!this.filters.pathway || this.filters.pathway === null) {
                    this.filters.pathway = 'all';
                }
                this.$nextTick(() => {
                    this.renderPathwayMutuCharts();
                });
            } catch (error) {
                console.error('Error loading pathway mutu:', error);
                alert('Gagal memuat data Pathway & Mutu: ' + error.message);
            }
        },

        async loadVarianceJkn() {
            const params = new URLSearchParams(this.filters);
            try {
                const response = await fetch(`/api/dashboard/variance-jkn?${params}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin'
                });
                if (!response.ok) throw new Error('Failed to load data');
                const data = await response.json();
                this.varianceJkn = data;
                this.$nextTick(() => {
                    this.renderVarianceJknCharts();
                });
            } catch (error) {
                console.error('Error loading variance JKN:', error);
            }
        },

        async loadDataProses() {
            try {
                const response = await fetch('/api/dashboard/data-proses', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin'
                });
                if (!response.ok) throw new Error('Failed to load data');
                const data = await response.json();
                this.dataProses = data;
            } catch (error) {
                console.error('Error loading data proses:', error);
            }
        },

        applyFilters() {
            this.loadTabData();
        },

        resetFilters() {
            this.filters = {
                period: '{{ date("Y-m") }}',
                payerType: 'all',
                kelasRawat: 'all',
                pathway: null
            };
            this.loadTabData();
        },

        renderOverviewCharts() {
            // Chart 1: Cost vs INA-CBG
            const costChartEl = document.getElementById('overviewCostChart');
            if (this.overview.costVsCbg && costChartEl && this.overview.costVsCbg.labels && this.overview.costVsCbg.labels.length > 0) {
                const ctx = costChartEl.getContext('2d');
                if (this.overview.costVsCbg.chart) {
                    this.overview.costVsCbg.chart.destroy();
                }
                this.overview.costVsCbg.chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: this.overview.costVsCbg.labels,
                        datasets: [
                            {
                                label: 'Actual Cost',
                                data: this.overview.costVsCbg.actualCost,
                                borderColor: 'rgb(59, 130, 246)',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.4
                            },
                            {
                                label: 'INA-CBG Claim',
                                data: this.overview.costVsCbg.inaCbgClaim,
                                borderColor: 'rgb(239, 68, 68)',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                tension: 0.4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'top' },
                            title: { display: true, text: 'Total Cost vs INA-CBG (JKN)' }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Chart 2: Compliance vs LOS
            const complianceChartEl = document.getElementById('overviewComplianceChart');
            if (this.overview.complianceVsLos && complianceChartEl && this.overview.complianceVsLos.labels && this.overview.complianceVsLos.labels.length > 0) {
                const ctx = complianceChartEl.getContext('2d');
                if (this.overview.complianceVsLos.chart) {
                    this.overview.complianceVsLos.chart.destroy();
                }
                this.overview.complianceVsLos.chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: this.overview.complianceVsLos.labels,
                        datasets: [
                            {
                                label: 'Compliance %',
                                data: this.overview.complianceVsLos.compliance,
                                backgroundColor: 'rgba(34, 197, 94, 0.6)',
                                borderColor: 'rgb(34, 197, 94)',
                                yAxisID: 'y'
                            },
                            {
                                label: 'LOS Standar',
                                data: this.overview.complianceVsLos.losStandard,
                                type: 'line',
                                borderColor: 'rgb(59, 130, 246)',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                yAxisID: 'y1'
                            },
                            {
                                label: 'LOS Actual',
                                data: this.overview.complianceVsLos.losActual,
                                type: 'line',
                                borderColor: 'rgb(239, 68, 68)',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                yAxisID: 'y1'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'top' },
                            title: { display: true, text: 'Pathway Compliance vs LOS' }
                        },
                        scales: {
                            y: {
                                type: 'linear',
                                position: 'left',
                                max: 100,
                                title: { display: true, text: 'Compliance %' }
                            },
                            y1: {
                                type: 'linear',
                                position: 'right',
                                title: { display: true, text: 'LOS (days)' },
                                grid: { drawOnChartArea: false }
                            }
                        }
                    }
                });
            }
        },

        renderBiayaTarifCharts() {
            // Top Cost Centers
            const topCostCentersEl = document.getElementById('topCostCentersChart');
            if (this.biayaTarif.topCostCenters && topCostCentersEl && 
                this.biayaTarif.topCostCenters.labels && this.biayaTarif.topCostCenters.labels.length > 0) {
                // Destroy chart lama jika ada
                if (this.biayaTarif.topCostCenters.chart) {
                    this.biayaTarif.topCostCenters.chart.destroy();
                    this.biayaTarif.topCostCenters.chart = null;
                }
                const ctx = topCostCentersEl.getContext('2d');
                this.biayaTarif.topCostCenters.chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: this.biayaTarif.topCostCenters.labels,
                        datasets: [{
                            label: 'Total Cost',
                            data: this.biayaTarif.topCostCenters.data,
                            backgroundColor: 'rgba(59, 130, 246, 0.6)',
                            borderColor: 'rgb(59, 130, 246)'
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            title: { display: true, text: 'Top Cost Centers - Post Allocation' }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Unit Cost Trend
            const unitCostTrendEl = document.getElementById('unitCostTrendChart');
            if (this.biayaTarif.unitCostTrend && unitCostTrendEl) {
                // Destroy chart lama jika ada
                if (this.biayaTarif.unitCostTrend.chart) {
                    this.biayaTarif.unitCostTrend.chart.destroy();
                    this.biayaTarif.unitCostTrend.chart = null;
                }
                
                // Cek apakah ada data untuk di-render
                if (this.biayaTarif.unitCostTrend.labels && this.biayaTarif.unitCostTrend.labels.length > 0 &&
                    this.biayaTarif.unitCostTrend.datasets && this.biayaTarif.unitCostTrend.datasets.length > 0) {
                    const ctx = unitCostTrendEl.getContext('2d');
                    const colors = ['rgb(59, 130, 246)', 'rgb(239, 68, 68)', 'rgb(34, 197, 94)', 'rgb(234, 179, 8)', 'rgb(168, 85, 247)'];
                    console.log('Rendering unit cost trend chart with:', {
                        labels: this.biayaTarif.unitCostTrend.labels,
                        datasets: this.biayaTarif.unitCostTrend.datasets
                    });
                    this.biayaTarif.unitCostTrend.chart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: this.biayaTarif.unitCostTrend.labels,
                            datasets: this.biayaTarif.unitCostTrend.datasets.map((ds, i) => ({
                                label: ds.label,
                                data: ds.data,
                                borderColor: colors[i % colors.length],
                                backgroundColor: colors[i % colors.length].replace('rgb', 'rgba').replace(')', ', 0.1)'),
                                tension: 0.4
                            }))
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'top' },
                                title: { display: true, text: 'Unit Cost Trend - Layanan Kunci' }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return 'Rp ' + (value / 1000).toFixed(0) + 'K';
                                        }
                                    }
                                }
                            }
                        }
                    });
                    console.log('Unit cost trend chart rendered successfully');
                } else {
                    console.warn('Unit cost trend chart not rendered. Missing data:', {
                        hasLabels: !!(this.biayaTarif.unitCostTrend?.labels?.length),
                        hasDatasets: !!(this.biayaTarif.unitCostTrend?.datasets?.length),
                        labelsLength: this.biayaTarif.unitCostTrend?.labels?.length || 0,
                        datasetsLength: this.biayaTarif.unitCostTrend?.datasets?.length || 0
                    });
                }
            }
        },

        async renderUnitCostTrendChart() {
            // Method khusus untuk me-render hanya unit cost trend chart
            const unitCostTrendEl = document.getElementById('unitCostTrendChart');
            if (!unitCostTrendEl) {
                console.warn('Unit cost trend chart element not found');
                return;
            }

            // Destroy chart lama jika ada - cek semua kemungkinan
            if (this.biayaTarif.unitCostTrend && this.biayaTarif.unitCostTrend.chart) {
                console.log('Destroying old unit cost trend chart, chart ID:', this.biayaTarif.unitCostTrend.chart.id);
                try {
                    this.biayaTarif.unitCostTrend.chart.destroy();
                } catch (e) {
                    console.warn('Error destroying chart:', e);
                }
                this.biayaTarif.unitCostTrend.chart = null;
            }
            
            // Juga cek apakah ada chart yang terdaftar di Chart.js registry
            const existingChart = Chart.getChart(unitCostTrendEl);
            if (existingChart) {
                console.log('Found existing chart in registry, destroying it, chart ID:', existingChart.id);
                try {
                    existingChart.destroy();
                } catch (e) {
                    console.warn('Error destroying chart from registry:', e);
                }
            }
            
            // Pastikan chart benar-benar dihapus dengan delay kecil
            await new Promise(resolve => setTimeout(resolve, 50));
            
            // Cek apakah ada data untuk di-render
            if (this.biayaTarif.unitCostTrend && 
                this.biayaTarif.unitCostTrend.labels && this.biayaTarif.unitCostTrend.labels.length > 0 &&
                this.biayaTarif.unitCostTrend.datasets && this.biayaTarif.unitCostTrend.datasets.length > 0) {
                const ctx = unitCostTrendEl.getContext('2d');
                const colors = ['rgb(59, 130, 246)', 'rgb(239, 68, 68)', 'rgb(34, 197, 94)', 'rgb(234, 179, 8)', 'rgb(168, 85, 247)'];
                console.log('Rendering unit cost trend chart with:', {
                    labels: this.biayaTarif.unitCostTrend.labels,
                    datasets: this.biayaTarif.unitCostTrend.datasets
                });
                this.biayaTarif.unitCostTrend.chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: this.biayaTarif.unitCostTrend.labels,
                        datasets: this.biayaTarif.unitCostTrend.datasets.map((ds, i) => ({
                            label: ds.label,
                            data: ds.data,
                            borderColor: colors[i % colors.length],
                            backgroundColor: colors[i % colors.length].replace('rgb', 'rgba').replace(')', ', 0.1)'),
                            tension: 0.4
                        }))
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'top' },
                            title: { display: true, text: 'Unit Cost Trend - Layanan Kunci' }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + (value / 1000).toFixed(0) + 'K';
                                    }
                                }
                            }
                        }
                    }
                });
                console.log('Unit cost trend chart rendered successfully');
            } else {
                console.warn('Unit cost trend chart not rendered. Missing data:', {
                    hasLabels: !!(this.biayaTarif.unitCostTrend?.labels?.length),
                    hasDatasets: !!(this.biayaTarif.unitCostTrend?.datasets?.length),
                    labelsLength: this.biayaTarif.unitCostTrend?.labels?.length || 0,
                    datasetsLength: this.biayaTarif.unitCostTrend?.datasets?.length || 0
                });
            }
        },

        renderPathwayMutuCharts() {
            console.log('Rendering Pathway Mutu charts...', this.pathwayMutu);
            
            // Compliance Chart
            const complianceChartEl = document.getElementById('pathwayComplianceChart');
            console.log('Compliance chart element:', complianceChartEl);
            console.log('Compliance data:', this.pathwayMutu.compliance);
            
            if (this.pathwayMutu.compliance && complianceChartEl && 
                this.pathwayMutu.compliance.labels && this.pathwayMutu.compliance.labels.length > 0) {
                console.log('Rendering compliance chart with labels:', this.pathwayMutu.compliance.labels);
                // Destroy chart lama jika ada
                if (this.pathwayMutu.compliance.chart) {
                    this.pathwayMutu.compliance.chart.destroy();
                    this.pathwayMutu.compliance.chart = null;
                }
                // Juga cek registry
                const existingChart = Chart.getChart(complianceChartEl);
                if (existingChart) {
                    existingChart.destroy();
                }
                const ctx = complianceChartEl.getContext('2d');
                this.pathwayMutu.compliance.chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: this.pathwayMutu.compliance.labels,
                        datasets: [{
                            label: 'Compliance %',
                            data: this.pathwayMutu.compliance.data,
                            backgroundColor: this.pathwayMutu.compliance.data.map(v => 
                                v >= 80 ? 'rgba(34, 197, 94, 0.6)' : 
                                v >= 50 ? 'rgba(234, 179, 8, 0.6)' : 
                                'rgba(239, 68, 68, 0.6)'
                            ),
                            borderColor: this.pathwayMutu.compliance.data.map(v => 
                                v >= 80 ? 'rgb(34, 197, 94)' : 
                                v >= 50 ? 'rgb(234, 179, 8)' : 
                                'rgb(239, 68, 68)'
                            )
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            title: { display: true, text: 'Pathway Compliance per Pathway' }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                title: { display: true, text: 'Compliance %' }
                            }
                        }
                    }
                });
                console.log('Compliance chart rendered successfully');
            } else {
                console.warn('Compliance chart not rendered. Missing:', {
                    hasCompliance: !!this.pathwayMutu.compliance,
                    hasElement: !!complianceChartEl,
                    hasLabels: !!(this.pathwayMutu.compliance?.labels?.length),
                    labelsLength: this.pathwayMutu.compliance?.labels?.length || 0
                });
            }

            // LOS Chart
            const losChartEl = document.getElementById('pathwayLosChart');
            console.log('LOS chart element:', losChartEl);
            console.log('LOS data:', this.pathwayMutu.los);
            
            if (this.pathwayMutu.los && losChartEl && 
                this.pathwayMutu.los.labels && this.pathwayMutu.los.labels.length > 0) {
                console.log('Rendering LOS chart with labels:', this.pathwayMutu.los.labels);
                // Destroy chart lama jika ada
                if (this.pathwayMutu.los.chart) {
                    this.pathwayMutu.los.chart.destroy();
                    this.pathwayMutu.los.chart = null;
                }
                // Juga cek registry
                const existingChart = Chart.getChart(losChartEl);
                if (existingChart) {
                    existingChart.destroy();
                }
                const ctx = losChartEl.getContext('2d');
                this.pathwayMutu.los.chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: this.pathwayMutu.los.labels,
                        datasets: [
                            {
                                label: 'LOS Standar',
                                data: this.pathwayMutu.los.losStandard,
                                backgroundColor: 'rgba(59, 130, 246, 0.6)',
                                borderColor: 'rgb(59, 130, 246)'
                            },
                            {
                                label: 'LOS Actual',
                                data: this.pathwayMutu.los.losActual,
                                backgroundColor: 'rgba(239, 68, 68, 0.6)',
                                borderColor: 'rgb(239, 68, 68)'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'top' },
                            title: { display: true, text: 'LOS Actual vs LOS Standar per Pathway' }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: { display: true, text: 'LOS (days)' }
                            }
                        }
                    }
                });
                console.log('LOS chart rendered successfully');
            } else {
                console.warn('LOS chart not rendered. Missing:', {
                    hasLos: !!this.pathwayMutu.los,
                    hasElement: !!losChartEl,
                    hasLabels: !!(this.pathwayMutu.los?.labels?.length),
                    labelsLength: this.pathwayMutu.los?.labels?.length || 0
                });
            }
        },

        formatNumber(num) {
            if (!num) return '0';
            return new Intl.NumberFormat('id-ID').format(num);
        },

        renderVarianceJknCharts() {
            // Distribution Chart
            if (this.varianceJkn.distribution && document.getElementById('varianceDistributionChart')) {
                const ctx = document.getElementById('varianceDistributionChart').getContext('2d');
                if (this.varianceJkn.distribution.chart) {
                    this.varianceJkn.distribution.chart.destroy();
                }
                this.varianceJkn.distribution.chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: this.varianceJkn.distribution.labels,
                        datasets: [{
                            label: 'Jumlah Kasus',
                            data: this.varianceJkn.distribution.data,
                            backgroundColor: this.varianceJkn.distribution.data.map((v, i) => {
                                if (i === 0) return 'rgba(239, 68, 68, 0.6)'; // < -20%
                                if (i === 1) return 'rgba(234, 179, 8, 0.6)'; // -20 to 0
                                if (i === 2) return 'rgba(34, 197, 94, 0.6)'; // 0 to 20
                                if (i === 3) return 'rgba(234, 179, 8, 0.6)'; // 20 to 50
                                return 'rgba(239, 68, 68, 0.6)'; // > 50
                            }),
                            borderColor: this.varianceJkn.distribution.data.map((v, i) => {
                                if (i === 0) return 'rgb(239, 68, 68)';
                                if (i === 1) return 'rgb(234, 179, 8)';
                                if (i === 2) return 'rgb(34, 197, 94)';
                                if (i === 3) return 'rgb(234, 179, 8)';
                                return 'rgb(239, 68, 68)';
                            })
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            title: { display: true, text: 'Distribusi Variance Cost per Case' }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: { display: true, text: 'Jumlah Kasus' }
                            }
                        }
                    }
                });
            }
        }
    }
}
</script>
@endpush
@endsection

