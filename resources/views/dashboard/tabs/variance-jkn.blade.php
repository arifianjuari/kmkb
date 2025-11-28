<div class="space-y-6" x-data="{ varianceType: 'actual_vs_inacbg' }">
    <!-- Filter Spesifik -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipe Variance</label>
                <select x-model="varianceType" @change="loadVarianceJkn()" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="actual_vs_pathway">Actual vs Pathway</option>
                    <option value="actual_vs_inacbg">Actual vs INA-CBG</option>
                </select>
            </div>
        </div>
    </div>

    <!-- KPI Mini Tiles -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <template x-for="kpi in varianceJkn.kpis" :key="kpi.id">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400" x-text="kpi.label"></p>
                        <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white" x-text="kpi.value"></p>
                    </div>
                    <div class="p-3 rounded-full" :class="kpi.color">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Chart: Distribusi Variance -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Distribusi Variance Cost per Case</h3>
        <div class="h-80">
            <canvas id="varianceDistributionChart"></canvas>
        </div>
    </div>

    <!-- Table: Top 10 Kasus Defisit Terbesar -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Top 10 Kasus Defisit Terbesar (Actual vs INA-CBG)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Case ID</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Pathway/Diagnosis</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Actual Cost</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">INA-CBG</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Selisih</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">LOS</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="(item, index) in varianceJkn.topCases" :key="index">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200" x-text="item.caseId"></td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200" x-text="item.pathway"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200" x-text="item.kelas"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200" x-text="'Rp ' + formatNumber(item.actualCost)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200" x-text="'Rp ' + formatNumber(item.inaCbg)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600" x-text="'Rp ' + formatNumber(item.selisih) + ' (' + item.selisihPercent + '%)'"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200" x-text="item.los + ' hari'"></td>
                        </tr>
                    </template>
                    <tr x-show="varianceJkn.topCases.length === 0">
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Tidak ada data</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Table: Pathway/Diagnosis dengan Variance Tinggi -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Pathway/Diagnosis dengan Variance Tinggi (Agregat)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Pathway/Diagnosis</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Jumlah Kasus</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Avg Variance (Rp)</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Avg Variance %</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">% Kasus > Threshold</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="(item, index) in varianceJkn.byPathway" :key="index">
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200" x-text="item.pathway"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200" x-text="item.jumlahKasus"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm" 
                                :class="item.avgVariance >= 0 ? 'text-red-600' : 'text-green-600'"
                                x-text="'Rp ' + formatNumber(item.avgVariance)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm" 
                                :class="item.avgVariancePercent >= 0 ? 'text-red-600' : 'text-green-600'"
                                x-text="item.avgVariancePercent + '%'"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200" x-text="item.percentAboveThreshold + '%'"></td>
                        </tr>
                    </template>
                    <tr x-show="varianceJkn.byPathway.length === 0">
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Tidak ada data</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

