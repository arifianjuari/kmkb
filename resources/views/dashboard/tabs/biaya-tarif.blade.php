<div class="space-y-6">
    <!-- Loading State -->
    <div x-show="loading" class="text-center py-8">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-biru-dongker-600"></div>
        <p class="mt-2 text-gray-600 dark:text-gray-400">Memuat data...</p>
    </div>

    <!-- Filter Layanan -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4" x-show="!loading">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pilih Layanan untuk Trend</label>
        <select x-model="biayaTarif.selectedServices" multiple class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white min-h-[120px]" size="5">
            <template x-for="service in biayaTarif.availableServices" :key="service.id">
                <option :value="service.id" x-text="service.name"></option>
            </template>
        </select>
        <div class="mt-2 flex gap-2">
            <button @click="updateUnitCostTrend()" class="btn-primary flex-1" :disabled="!biayaTarif.selectedServices || biayaTarif.selectedServices.length === 0">
                Update Chart
            </button>
            <button @click="biayaTarif.selectedServices = []" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600">
                Reset
            </button>
        </div>
        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
            <span x-show="biayaTarif.selectedServices && biayaTarif.selectedServices.length > 0">
                <span x-text="biayaTarif.selectedServices.length"></span> layanan dipilih
            </span>
            <span x-show="!biayaTarif.selectedServices || biayaTarif.selectedServices.length === 0">
                Belum ada layanan dipilih. Chart akan menampilkan top 5 layanan berdasarkan volume.
            </span>
        </p>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Chart: Top Cost Centers -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top Cost Centers - Post Allocation</h3>
            <div class="h-80">
                <canvas id="topCostCentersChart"></canvas>
            </div>
        </div>

        <!-- Chart: Unit Cost Trend -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Unit Cost Trend - Layanan Kunci</h3>
            <div class="h-80">
                <canvas id="unitCostTrendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Table: Tarif Internal vs Unit Cost (Non-JKN) -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tarif Internal vs Unit Cost (Non-JKN)</h3>
            <select x-model="biayaTarif.tarifFilter" @change="loadBiayaTarif()" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <option value="all">Semua</option>
                <option value="defisit">Defisit</option>
                <option value="surplus">Surplus</option>
            </select>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Nama Layanan</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Unit Cost</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Tarif Internal</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Margin</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Margin %</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="(item, index) in biayaTarif.tarifVsUnitCost" :key="index">
                        <tr>
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200" x-text="item.kode"></td>
                            <td class="px-6 py-2 text-sm text-gray-900 dark:text-gray-200" x-text="item.nama"></td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200" x-text="'Rp ' + formatNumber(item.unitCost)"></td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200" x-text="'Rp ' + formatNumber(item.tarifInternal)"></td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm" 
                                :class="item.margin >= 0 ? 'text-green-600' : 'text-red-600'"
                                x-text="'Rp ' + formatNumber(item.margin)"></td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm" 
                                :class="item.marginPercent >= 0 ? 'text-green-600' : 'text-red-600'"
                                x-text="item.marginPercent + '%'"></td>
                            <td class="px-6 py-2 whitespace-nowrap">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold"
                                      :class="item.status === 'Defisit' ? 'bg-red-100 text-red-800' : 
                                              item.status === 'Surplus' ? 'bg-green-100 text-green-800' : 
                                              'bg-gray-100 text-gray-800'"
                                      x-text="item.status"></span>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="biayaTarif.tarifVsUnitCost.length === 0">
                        <td colspan="7" class="px-6 py-2 text-center text-gray-500 dark:text-gray-400">Tidak ada data</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Table: Unit Cost vs INA-CBG (JKN) -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Unit Cost vs INA-CBG (JKN)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Pathway / Paket</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Avg Unit Cost/Case</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Avg INA-CBG/Case</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Selisih (Rp)</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Selisih %</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Volume</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="(item, index) in biayaTarif.unitCostVsCbg" :key="index">
                        <tr>
                            <td class="px-6 py-2 text-sm text-gray-900 dark:text-gray-200" x-text="item.pathway"></td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200" x-text="'Rp ' + formatNumber(item.avgUnitCost)"></td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200" x-text="'Rp ' + formatNumber(item.avgCbg)"></td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm" 
                                :class="item.selisih >= 0 ? 'text-red-600' : 'text-green-600'"
                                x-text="'Rp ' + formatNumber(item.selisih)"></td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm" 
                                :class="item.selisihPercent >= 0 ? 'text-red-600' : 'text-green-600'"
                                x-text="item.selisihPercent + '%'"></td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200" x-text="item.volume"></td>
                        </tr>
                    </template>
                    <tr x-show="biayaTarif.unitCostVsCbg.length === 0">
                        <td colspan="6" class="px-6 py-2 text-center text-gray-500 dark:text-gray-400">Tidak ada data</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

