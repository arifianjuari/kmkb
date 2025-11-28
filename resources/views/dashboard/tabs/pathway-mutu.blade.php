<div class="space-y-6">
    <!-- Loading State -->
    <div x-show="loading" class="text-center py-8">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-biru-dongker-600"></div>
        <p class="mt-2 text-gray-600 dark:text-gray-400">Memuat data...</p>
    </div>

    <!-- Filter Spesifik -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4" x-show="!loading">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pathway</label>
                <select x-model="filters.pathway" @change="loadPathwayMutu()" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="all">All</option>
                    <template x-for="pathway in pathwayMutu.availablePathways" :key="pathway.id">
                        <option :value="pathway.id" x-text="pathway.name"></option>
                    </template>
                </select>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Chart: Pathway Compliance -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pathway Compliance per Pathway</h3>
            <div class="h-80">
                <canvas id="pathwayComplianceChart"></canvas>
            </div>
        </div>

        <!-- Chart: LOS Actual vs Standar -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">LOS Actual vs LOS Standar per Pathway</h3>
            <div class="h-80">
                <canvas id="pathwayLosChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Table: Ringkasan Pathway -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Ringkasan Pathway</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Pathway</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Jumlah Kasus</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Compliance %</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">LOS Standar</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">LOS Actual</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Avg Cost/Case</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Status LOS</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="(item, index) in pathwayMutu.summary" :key="index">
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200" x-text="item.pathway"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200" x-text="item.jumlahKasus"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200" x-text="item.compliance + '%'"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200" x-text="item.losStandar + ' hari'"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200" x-text="item.losActual + ' hari'"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200" x-text="'Rp ' + formatNumber(item.avgCost)"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold"
                                      :class="item.statusLos === 'Over' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'"
                                      x-text="item.statusLos"></span>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="pathwayMutu.summary.length === 0">
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Tidak ada data</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Table: Top Non-compliant Steps -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Top Non-compliant Steps</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Pathway</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Nama Step</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">% Ketidakpatuhan</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Dampak</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="(item, index) in pathwayMutu.nonCompliantSteps" :key="index">
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200" x-text="item.pathway"></td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200" x-text="item.stepName"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600" x-text="item.nonCompliancePercent + '%'"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800" x-text="item.dampak || '-'"></span>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="pathwayMutu.nonCompliantSteps.length === 0">
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Tidak ada data</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

