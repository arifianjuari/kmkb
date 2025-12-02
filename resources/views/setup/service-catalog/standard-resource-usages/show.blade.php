@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Detail Standard Resource Usage</h2>
            <div class="flex items-center space-x-2">
                <a href="{{ route('standard-resource-usages.edit', $standardResourceUsage) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                    Edit
                </a>
                <a href="{{ route('standard-resource-usages.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                    Kembali ke Daftar
                </a>
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Informasi Standard Resource Usage</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Service (Tindakan/Pemeriksaan)</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <div class="font-medium">
                                {{ $standardResourceUsage->service->service_description ?? $standardResourceUsage->service_name ?? '-' }}
                            </div>
                            <div class="text-xs text-gray-500">
                                Kode: {{ $standardResourceUsage->service->service_code ?? $standardResourceUsage->service_code ?? '-' }}
                            </div>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">BMHP</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <div class="font-medium">{{ $standardResourceUsage->bmhp->service_description ?? '-' }}</div>
                            <div class="text-xs text-gray-500">Kode: {{ $standardResourceUsage->bmhp->service_code ?? '-' }}</div>
                            @if($standardResourceUsage->bmhp)
                                <div class="text-xs text-gray-500 mt-1">
                                    Harga: Rp {{ number_format($standardResourceUsage->bmhp->purchase_price ?? $standardResourceUsage->bmhp->standard_cost ?? 0, 0, ',', '.') }}
                                </div>
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Quantity</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ number_format($standardResourceUsage->quantity, 2, ',', '.') }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Unit</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $standardResourceUsage->unit }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Total Cost</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold text-biru-dongker-800">
                            Rp {{ number_format($standardResourceUsage->getTotalCost(), 0, ',', '.') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            @if($standardResourceUsage->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Tidak Aktif
                                </span>
                            @endif
                        </dd>
                    </div>

                    @if($standardResourceUsage->notes)
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Catatan</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $standardResourceUsage->notes }}</dd>
                    </div>
                    @endif

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Dibuat Oleh</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $standardResourceUsage->creator->name ?? '-' }}
                            @if($standardResourceUsage->created_at)
                                <div class="text-xs text-gray-500">{{ $standardResourceUsage->created_at->format('d/m/Y H:i') }}</div>
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Diperbarui Oleh</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $standardResourceUsage->updater->name ?? '-' }}
                            @if($standardResourceUsage->updated_at)
                                <div class="text-xs text-gray-500">{{ $standardResourceUsage->updated_at->format('d/m/Y H:i') }}</div>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection

