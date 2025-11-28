<div class="space-y-6">
    <!-- Loading State -->
    <div x-show="loading" class="text-center py-8">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-biru-dongker-600"></div>
        <p class="mt-2 text-gray-600 dark:text-gray-400">Memuat data...</p>
    </div>

    <!-- KPI Summary Tiles -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6" x-show="!loading && overview.kpis && overview.kpis.length > 0">
        <template x-for="kpi in overview.kpis" :key="kpi.id">
            <div 
                @click="kpi.action && (activeTab = kpi.action)"
                class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 cursor-pointer hover:shadow-lg transition-shadow"
                :class="kpi.action ? 'hover:ring-2 hover:ring-biru-dongker-500' : ''">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400" x-text="kpi.label"></p>
                        <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white" x-text="kpi.value"></p>
                        <p x-show="kpi.change" class="mt-1 text-xs" 
                           :class="kpi.change >= 0 ? 'text-green-600' : 'text-red-600'"
                           x-text="(kpi.change >= 0 ? '+' : '') + kpi.change + '%'"></p>
                    </div>
                    <div class="p-3 rounded-full" :class="kpi.color">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="kpi.icon"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Empty State -->
    <div x-show="!loading && (!overview.kpis || overview.kpis.length === 0)" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Tidak ada data</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Jalankan seeder untuk menghasilkan data dashboard.</p>
        <div class="mt-6">
            <code class="px-3 py-2 bg-gray-100 dark:bg-gray-800 rounded text-sm">php artisan db:seed --class=DashboardDataSeeder</code>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" x-show="!loading">
        <!-- Chart: Total Cost vs INA-CBG -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Total Cost vs INA-CBG (JKN)</h3>
            <div class="h-64">
                <canvas id="overviewCostChart"></canvas>
            </div>
        </div>

        <!-- Chart: Pathway Compliance vs LOS -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pathway Compliance vs LOS</h3>
            <div class="h-64">
                <canvas id="overviewComplianceChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Mini Table: Top 5 Pathway / Layanan Kritis -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">5 Pathway / Layanan Kritis</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Pathway / Layanan</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Compliance %</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Avg Cost/Case</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Selisih vs INA-CBG</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="(pathway, index) in overview.topPathways" :key="index">
                        <tr 
                            @click="filters.pathway = pathway.id; activeTab = 'pathway_mutu'"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200" x-text="pathway.name"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200" x-text="pathway.compliance + '%'"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200" x-text="'Rp ' + formatNumber(pathway.avgCost)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm" 
                                :class="pathway.selisih >= 0 ? 'text-red-600' : 'text-green-600'"
                                x-text="'Rp ' + formatNumber(pathway.selisih)"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold"
                                      :class="pathway.status === 'hijau' ? 'bg-green-100 text-green-800' : 
                                              pathway.status === 'kuning' ? 'bg-yellow-100 text-yellow-800' : 
                                              'bg-red-100 text-red-800'"
                                      x-text="pathway.status"></span>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="overview.topPathways.length === 0">
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Tidak ada data</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

