@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">{{ __('Laporan Kontribusi Depresiasi') }}</h2>
        <div class="flex items-center space-x-2">
            <a href="{{ route('fixed-assets.dashboard') }}" class="btn-secondary">{{ __('Dashboard') }}</a>
            <a href="{{ route('fixed-assets.depreciation') }}" class="btn-primary">{{ __('Kalkulator Depresiasi') }}</a>
        </div>
    </div>

    <!-- Period Filter -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('fixed-assets.depreciation-report') }}" class="flex items-center space-x-4">
            <div class="flex items-center space-x-2">
                <label class="text-sm font-medium text-gray-700">Periode:</label>
                <select name="period_month" class="py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm">
                    @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $periodMonth == $m ? 'selected' : '' }}>
                        {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                    </option>
                    @endfor
                </select>
                <select name="period_year" class="py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm">
                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                    <option value="{{ $y }}" {{ $periodYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="btn-primary">Tampilkan</button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-500">Total Depresiasi</p>
            <p class="text-2xl font-semibold text-gray-900">Rp {{ number_format($totalDepreciation, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-500">Total GL Expenses</p>
            <p class="text-2xl font-semibold text-gray-900">Rp {{ number_format($totalGlExpenses, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-500">Kontribusi Depresiasi</p>
            <p class="text-2xl font-semibold {{ $depreciationPercentage > 0 ? 'text-blue-600' : 'text-gray-900' }}">{{ number_format($depreciationPercentage, 2) }}%</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Depreciation by Cost Center -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Depresiasi per Cost Center</h3>
            @if($depreciationByCostCenter->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Cost Center</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500">Aset</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($depreciationByCostCenter as $item)
                        <tr>
                            <td class="px-3 py-2 text-sm text-gray-900">{{ $item['cost_center']->name ?? '-' }}</td>
                            <td class="px-3 py-2 text-sm text-gray-500 text-center">{{ $item['asset_count'] }}</td>
                            <td class="px-3 py-2 text-sm text-gray-900 text-right">Rp {{ number_format($item['total_depreciation'], 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td class="px-3 py-2 text-sm font-medium text-gray-900" colspan="2">Total</td>
                            <td class="px-3 py-2 text-sm font-medium text-gray-900 text-right">Rp {{ number_format($totalDepreciation, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @else
            <p class="text-gray-500 text-sm">Tidak ada data depresiasi untuk periode ini.</p>
            @endif
        </div>

        <!-- Depreciation by Jenis BMN -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Depresiasi per Jenis BMN</h3>
            @if($depreciationByJenisBmn->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Jenis BMN</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500">Aset</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($depreciationByJenisBmn as $item)
                        <tr>
                            <td class="px-3 py-2 text-sm text-gray-900">{{ $item['jenis_bmn'] }}</td>
                            <td class="px-3 py-2 text-sm text-gray-500 text-center">{{ $item['count'] }}</td>
                            <td class="px-3 py-2 text-sm text-gray-900 text-right">Rp {{ number_format($item['total'], 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-gray-500 text-sm">Tidak ada data depresiasi untuk periode ini.</p>
            @endif
        </div>
    </div>

    <!-- Info Box -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
        <div class="flex">
            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Tentang Kontribusi Depresiasi</h3>
                <p class="text-sm text-blue-700 mt-1">
                    Persentase kontribusi depresiasi menunjukkan berapa persen dari total biaya GL (GL Expenses) yang berasal dari biaya penyusutan aset tetap. 
                    Nilai ini penting untuk memahami struktur biaya dan pengaruhnya terhadap perhitungan Unit Cost.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
