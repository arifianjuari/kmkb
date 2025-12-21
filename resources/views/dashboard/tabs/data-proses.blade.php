<div class="space-y-6">
    <!-- Cards Status Data -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- GL & Expenses -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">GL & Expenses</h3>
                <span class="px-2 py-1 rounded-full text-xs font-semibold"
                      :class="dataProses.status.gl?.status === 'OK' ? 'bg-green-100 text-green-800' : 
                              dataProses.status.gl?.status === 'Warning' ? 'bg-yellow-100 text-yellow-800' : 
                              'bg-red-100 text-red-800'"
                      x-text="dataProses.status.gl?.status || 'Unknown'"></span>
            </div>
            <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                <p><span class="font-medium">Import Terakhir:</span> <span x-text="dataProses.status.gl?.lastImport || '-'"></span></p>
                <p><span class="font-medium">Periode Terakhir:</span> <span x-text="dataProses.status.gl?.lastPeriod || '-'"></span></p>
            </div>
        </div>

        <!-- Allocation -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Allocation</h3>
                <span class="px-2 py-1 rounded-full text-xs font-semibold"
                      :class="dataProses.status.allocation?.status === 'OK' ? 'bg-green-100 text-green-800' : 
                              dataProses.status.allocation?.status === 'Warning' ? 'bg-yellow-100 text-yellow-800' : 
                              'bg-red-100 text-red-800'"
                      x-text="dataProses.status.allocation?.status || 'Unknown'"></span>
            </div>
            <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                <p><span class="font-medium">Run Terakhir:</span> <span x-text="dataProses.status.allocation?.lastRun || '-'"></span></p>
                <p><span class="font-medium">Periode Selesai:</span> <span x-text="dataProses.status.allocation?.completedPeriod || '-'"></span></p>
                <p><span class="font-medium">Driver:</span> <span x-text="dataProses.status.allocation?.driver || '-'"></span></p>
            </div>
        </div>

        <!-- Unit Cost -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Unit Cost</h3>
                <span class="px-2 py-1 rounded-full text-xs font-semibold"
                      :class="dataProses.status.unitCost?.status === 'OK' ? 'bg-green-100 text-green-800' : 
                              dataProses.status.unitCost?.status === 'Warning' ? 'bg-yellow-100 text-yellow-800' : 
                              'bg-red-100 text-red-800'"
                      x-text="dataProses.status.unitCost?.status || 'Unknown'"></span>
            </div>
            <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                <p><span class="font-medium">Versi Aktif:</span> <span x-text="dataProses.status.unitCost?.activeVersion || '-'"></span></p>
                <p><span class="font-medium">Tanggal Perhitungan:</span> <span x-text="dataProses.status.unitCost?.calculationDate || '-'"></span></p>
            </div>
        </div>

        <!-- Tarif Internal -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tarif Internal</h3>
                <span class="px-2 py-1 rounded-full text-xs font-semibold"
                      :class="dataProses.status.tarif?.status === 'OK' ? 'bg-green-100 text-green-800' : 
                              dataProses.status.tarif?.status === 'Warning' ? 'bg-yellow-100 text-yellow-800' : 
                              'bg-red-100 text-red-800'"
                      x-text="dataProses.status.tarif?.status || 'Unknown'"></span>
            </div>
            <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                <p><span class="font-medium">Update Terakhir:</span> <span x-text="dataProses.status.tarif?.lastUpdate || '-'"></span></p>
                <p><span class="font-medium">Layanan Aktif:</span> <span x-text="dataProses.status.tarif?.activeServices || '-'"></span></p>
            </div>
        </div>
    </div>

    <!-- Table: Data Quality / Pre-Allocation Check -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Data Quality / Pre-Allocation Check</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Nama Check</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Jumlah Temuan</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="(check, index) in dataProses.checks" :key="index">
                        <tr>
                            <td class="px-6 py-2 text-sm text-gray-900 dark:text-gray-200" x-text="check.name"></td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200" x-text="check.count"></td>
                            <td class="px-6 py-2 whitespace-nowrap">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold"
                                      :class="check.status === 'OK' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
                                      x-text="check.status"></span>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="dataProses.checks.length === 0">
                        <td colspan="3" class="px-6 py-2 text-center text-gray-500 dark:text-gray-400">Tidak ada data</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Timeline Proses (Opsional) -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Timeline Proses</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <template x-for="(log, index) in dataProses.logs" :key="index">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="h-2 w-2 rounded-full mt-2"
                                 :class="log.status === 'success' ? 'bg-green-500' : 
                                         log.status === 'warning' ? 'bg-yellow-500' : 
                                         'bg-red-500'"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900 dark:text-white" x-text="log.activity"></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="log.timestamp"></p>
                        </div>
                        <div>
                            <span class="px-2 py-1 rounded-full text-xs font-semibold"
                                  :class="log.status === 'success' ? 'bg-green-100 text-green-800' : 
                                          log.status === 'warning' ? 'bg-yellow-100 text-yellow-800' : 
                                          'bg-red-100 text-red-800'"
                                  x-text="log.status"></span>
                        </div>
                    </div>
                </template>
                <p x-show="dataProses.logs.length === 0" class="text-gray-500 dark:text-gray-400 text-center">Tidak ada log</p>
            </div>
        </div>
    </div>
</div>

