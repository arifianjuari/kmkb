@extends('layouts.app')

@section('title', __('Pengaturan Integrasi SIMRS'))

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-semibold text-gray-900 mb-6">{{ __('Pengaturan Integrasi SIMRS') }}</h1>

    @php
        $status = $simrsConnectionStatus ?? app(\App\Services\SimrsService::class)->connectionStatus(true);
    @endphp

    <div class="bg-white shadow sm:rounded-lg divide-y divide-gray-200">
        <div class="px-4 py-5 sm:px-6">
            <h2 class="text-lg font-medium text-gray-900">{{ __('Status koneksi') }}</h2>
            <p class="mt-1 text-sm text-gray-500">{{ __('Read-only ke database Khanza (MySQL). Butuh jaringan RS atau VPN.') }}</p>
        </div>
        <dl class="px-4 py-5 sm:px-6 grid grid-cols-1 gap-4 sm:grid-cols-2 text-sm">
            <div>
                <dt class="font-medium text-gray-500">Configured</dt>
                <dd class="mt-1 {{ $status['configured'] ? 'text-green-700' : 'text-red-700' }}">
                    {{ $status['configured'] ? __('Ya') : __('Tidak') }}
                </dd>
            </div>
            <div>
                <dt class="font-medium text-gray-500">Available</dt>
                <dd class="mt-1 {{ $status['available'] ? 'text-green-700' : 'text-amber-700' }}">
                    {{ $status['available'] ? __('Ya') : __('Tidak') }}
                </dd>
            </div>
            <div>
                <dt class="font-medium text-gray-500">{{ __('Koneksi Laravel') }}</dt>
                <dd class="mt-1 font-mono text-gray-900">{{ $status['connection'] ?? 'simrs' }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="font-medium text-gray-500">{{ __('Pesan') }}</dt>
                <dd class="mt-1 text-gray-900">{{ $status['message'] }}</dd>
            </div>
        </dl>
        <div class="px-4 py-5 sm:px-6 text-sm text-gray-600 space-y-2">
            <p><strong>HOSPITAL_DB_ENABLED=true</strong> — gunakan <code class="text-xs bg-gray-100 px-1 rounded">HOSPITAL_DB_*</code> (produksi RS).</p>
            <p><strong>HOSPITAL_DB_ENABLED=false</strong> — gunakan <code class="text-xs bg-gray-100 px-1 rounded">SIMRS_DB_*</code> (lokal / legacy).</p>
            <p>Setelah ubah <code class="text-xs bg-gray-100 px-1 rounded">.env</code>: <code class="text-xs bg-gray-100 px-1 rounded">php artisan config:clear</code></p>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('simrs.master-barang') }}" class="text-biru-dongker-800 hover:underline text-sm font-medium">
            {{ __('Buka data master barang SIMRS') }} →
        </a>
    </div>
</div>
@endsection
