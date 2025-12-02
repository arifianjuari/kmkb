@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Detail RVU</h2>
            <div class="flex items-center space-x-2">
                <a href="{{ route('rvu-values.edit', $rvuValue) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                    Edit
                </a>
                <a href="{{ route('rvu-values.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                    Kembali ke Daftar
                </a>
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Informasi RVU</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Cost Reference</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $rvuValue->costReference->service_description ?? '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Cost Center</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $rvuValue->costCenter->building_name ?? '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Periode</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $rvuValue->period_year }}{{ $rvuValue->period_month ? '/' . str_pad($rvuValue->period_month, 2, '0', STR_PAD_LEFT) : ' (Tahunan)' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Waktu (menit)</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $rvuValue->time_factor }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Profesionalisme</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $rvuValue->professionalism_factor }} - {{ $rvuValue->professionalism_label }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Tingkat Kesulitan</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $rvuValue->difficulty_factor }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Faktor Normalisasi</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ number_format($rvuValue->normalization_factor, 4, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">RVU Value</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">{{ number_format($rvuValue->rvu_value, 4, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            @if($rvuValue->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Tidak Aktif</span>
                            @endif
                        </dd>
                    </div>
                    @if($rvuValue->notes)
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Catatan</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $rvuValue->notes }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection

