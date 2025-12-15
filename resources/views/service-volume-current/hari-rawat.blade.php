@extends('layouts.app')

@section('title', 'Service Volume (Current) - Hari Rawat')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="bg-white shadow rounded-lg p-6">
        <form method="GET" class="grid gap-6 md:grid-cols-3">
            <div>
                <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                <select id="year" name="year" class="w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700">
                    @foreach($availableYears as $yearOption)
                        <option value="{{ $yearOption }}" {{ (int) $year === (int) $yearOption ? 'selected' : '' }}>{{ $yearOption }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="bangsal" class="block text-sm font-medium text-gray-700 mb-1">Bangsal</label>
                <select id="bangsal" name="bangsal[]" multiple class="w-full h-36 rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700">
                    @foreach($bangsalOptions as $option)
                        <option value="{{ $option->kd_bangsal }}" {{ in_array($option->kd_bangsal, $bangsal ?? []) ? 'selected' : '' }}>
                            {{ $option->kd_bangsal }} - {{ $option->nm_bangsal }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">Gunakan Ctrl/Command untuk memilih lebih dari satu.</p>
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="inline-flex items-center justify-center w-full md:w-auto px-4 py-2 bg-biru-dongker-800 text-white rounded-md shadow hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                    Terapkan Filter
                </button>
                <button type="submit"
                        formaction="{{ route('svc-current.hari-rawat.export') }}"
                        class="inline-flex items-center justify-center w-full md:w-auto px-4 py-2 bg-emerald-600 text-white rounded-md shadow hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                    Export Excel
                </button>
            </div>
        </form>
    </div>

    @if($errorMessage)
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            {{ $errorMessage }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    {{-- Summary Cards --}}
    @if(count($hariRawatData) > 0)
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="rounded-lg shadow p-4 text-white" style="background: linear-gradient(to bottom right, #3b82f6, #2563eb);">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-80">Total Hari Rawat</p>
                    <p class="text-2xl font-bold">{{ number_format($grandTotals['total_hari'], 0, ',', '.') }}</p>
                </div>
                <svg class="w-10 h-10 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>
        <div class="rounded-lg shadow p-4 text-white" style="background: linear-gradient(to bottom right, #22c55e, #16a34a);">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-80">Jumlah Pasien</p>
                    <p class="text-2xl font-bold">{{ number_format($grandTotals['jumlah_pasien'], 0, ',', '.') }}</p>
                </div>
                <svg class="w-10 h-10 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
        </div>
        <div class="rounded-lg shadow p-4 text-white" style="background: linear-gradient(to bottom right, #a855f7, #9333ea);">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-80">Rata-rata LOS</p>
                    <p class="text-2xl font-bold">{{ number_format($grandTotals['avg_los'], 2, ',', '.') }} hari</p>
                </div>
                <svg class="w-10 h-10 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
        </div>
        <div class="rounded-lg shadow p-4 text-white" style="background: linear-gradient(to bottom right, #f97316, #ea580c);">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-80">Jumlah Kelas</p>
                    <p class="text-2xl font-bold">{{ collect($hariRawatData)->pluck('kelas')->unique()->count() }}</p>
                </div>
                <svg class="w-10 h-10 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Rekap Hari Rawat (Dikelompokkan per Kelas)</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Tahun {{ $year }}
                        @if(!empty($bangsal))
                            • Bangsal {{ implode(', ', $bangsal) }}
                        @endif
                    </p>
                </div>
                <p class="text-sm text-gray-500">Sumber data: SIMRS (kamar_inap)</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-700">Bangsal</th>
                        @foreach(['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'] as $bulan)
                            <th scope="col" class="px-3 py-3 text-center font-semibold text-gray-700">{{ $bulan }}</th>
                        @endforeach
                        <th scope="col" class="px-4 py-3 text-center font-semibold text-gray-700 bg-blue-50">Total Hari</th>
                        <th scope="col" class="px-4 py-3 text-center font-semibold text-gray-700 bg-green-50">Pasien</th>
                        <th scope="col" class="px-4 py-3 text-center font-semibold text-gray-700 bg-purple-50">Avg LOS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        // Group data by kelas
                        $groupedData = collect($hariRawatData)->groupBy('kelas');
                    @endphp
                    
                    @forelse($groupedData as $kelas => $items)
                        @php
                            // Calculate subtotals for this group
                            $groupTotals = [
                                'jan' => $items->sum('jan'),
                                'feb' => $items->sum('feb'),
                                'mar' => $items->sum('mar'),
                                'apr' => $items->sum('apr'),
                                'may' => $items->sum('may'),
                                'jun' => $items->sum('jun'),
                                'jul' => $items->sum('jul'),
                                'aug' => $items->sum('aug'),
                                'sep' => $items->sum('sep'),
                                'oct' => $items->sum('oct'),
                                'nov' => $items->sum('nov'),
                                'dec' => $items->sum('dec'),
                                'total_hari' => $items->sum('total_hari'),
                                'jumlah_pasien' => $items->sum('jumlah_pasien'),
                            ];
                            $groupTotals['avg_los'] = $groupTotals['jumlah_pasien'] > 0 
                                ? round($groupTotals['total_hari'] / $groupTotals['jumlah_pasien'], 2) 
                                : 0;
                        @endphp
                        
                        {{-- Group Header --}}
                        <tr class="bg-indigo-50">
                            <td colspan="16" class="px-4 py-3">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-indigo-900">
                                        <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        Kelas: {{ $kelas ?: 'Tidak Diketahui' }}
                                    </span>
                                    <span class="text-sm text-indigo-700">{{ $items->count() }} bangsal • {{ $items->sum('jumlah_pasien') }} pasien • Avg LOS: {{ number_format($groupTotals['avg_los'], 2) }} hari</span>
                                </div>
                            </td>
                        </tr>
                        
                        {{-- Data Rows for this group --}}
                        @foreach($items as $row)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-900">{{ $row['bangsal'] }}</td>
                                <td class="px-3 py-3 text-center">{{ $row['jan'] }}</td>
                                <td class="px-3 py-3 text-center">{{ $row['feb'] }}</td>
                                <td class="px-3 py-3 text-center">{{ $row['mar'] }}</td>
                                <td class="px-3 py-3 text-center">{{ $row['apr'] }}</td>
                                <td class="px-3 py-3 text-center">{{ $row['may'] }}</td>
                                <td class="px-3 py-3 text-center">{{ $row['jun'] }}</td>
                                <td class="px-3 py-3 text-center">{{ $row['jul'] }}</td>
                                <td class="px-3 py-3 text-center">{{ $row['aug'] }}</td>
                                <td class="px-3 py-3 text-center">{{ $row['sep'] }}</td>
                                <td class="px-3 py-3 text-center">{{ $row['oct'] }}</td>
                                <td class="px-3 py-3 text-center">{{ $row['nov'] }}</td>
                                <td class="px-3 py-3 text-center">{{ $row['dec'] }}</td>
                                <td class="px-4 py-3 text-center font-semibold bg-blue-50">{{ $row['total_hari'] }}</td>
                                <td class="px-4 py-3 text-center font-semibold bg-green-50">{{ $row['jumlah_pasien'] }}</td>
                                <td class="px-4 py-3 text-center font-semibold bg-purple-50">{{ number_format($row['avg_los'], 2) }}</td>
                            </tr>
                        @endforeach
                        
                        {{-- Group Subtotal --}}
                        <tr class="bg-indigo-100 font-semibold">
                            <td class="px-4 py-2 text-indigo-900">Subtotal Kelas {{ $kelas ?: 'Tidak Diketahui' }}</td>
                            <td class="px-3 py-2 text-center text-indigo-900">{{ $groupTotals['jan'] }}</td>
                            <td class="px-3 py-2 text-center text-indigo-900">{{ $groupTotals['feb'] }}</td>
                            <td class="px-3 py-2 text-center text-indigo-900">{{ $groupTotals['mar'] }}</td>
                            <td class="px-3 py-2 text-center text-indigo-900">{{ $groupTotals['apr'] }}</td>
                            <td class="px-3 py-2 text-center text-indigo-900">{{ $groupTotals['may'] }}</td>
                            <td class="px-3 py-2 text-center text-indigo-900">{{ $groupTotals['jun'] }}</td>
                            <td class="px-3 py-2 text-center text-indigo-900">{{ $groupTotals['jul'] }}</td>
                            <td class="px-3 py-2 text-center text-indigo-900">{{ $groupTotals['aug'] }}</td>
                            <td class="px-3 py-2 text-center text-indigo-900">{{ $groupTotals['sep'] }}</td>
                            <td class="px-3 py-2 text-center text-indigo-900">{{ $groupTotals['oct'] }}</td>
                            <td class="px-3 py-2 text-center text-indigo-900">{{ $groupTotals['nov'] }}</td>
                            <td class="px-3 py-2 text-center text-indigo-900">{{ $groupTotals['dec'] }}</td>
                            <td class="px-4 py-2 text-center text-indigo-900 bg-indigo-200">{{ $groupTotals['total_hari'] }}</td>
                            <td class="px-4 py-2 text-center text-indigo-900 bg-indigo-200">{{ $groupTotals['jumlah_pasien'] }}</td>
                            <td class="px-4 py-2 text-center text-indigo-900 bg-indigo-200">{{ number_format($groupTotals['avg_los'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="16" class="px-4 py-6 text-center text-gray-500">Belum ada data hari rawat untuk filter yang dipilih.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if(count($hariRawatData) > 0)
                    <tfoot class="bg-gray-100">
                        <tr class="font-bold">
                            <td class="px-4 py-3 text-gray-900">GRAND TOTAL</td>
                            <td class="px-3 py-3 text-center">{{ $grandTotals['jan'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $grandTotals['feb'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $grandTotals['mar'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $grandTotals['apr'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $grandTotals['may'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $grandTotals['jun'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $grandTotals['jul'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $grandTotals['aug'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $grandTotals['sep'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $grandTotals['oct'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $grandTotals['nov'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $grandTotals['dec'] }}</td>
                            <td class="px-4 py-3 text-center bg-blue-100">{{ $grandTotals['total_hari'] }}</td>
                            <td class="px-4 py-3 text-center bg-green-100">{{ $grandTotals['jumlah_pasien'] }}</td>
                            <td class="px-4 py-3 text-center bg-purple-100">{{ number_format($grandTotals['avg_los'], 2) }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
