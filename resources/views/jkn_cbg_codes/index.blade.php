@extends('layouts.app')

@section('content')
<section class="mx-auto py-6 sm:px-6 lg:px-8">
    <div>
        <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
            <div class="flex items-center gap-3 flex-shrink-0">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white whitespace-nowrap">{{ __('JKN CBG Codes') }}</h2>
                <button
                    type="button"
                    class="flex-shrink-0 text-xs font-semibold text-biru-dongker-800 border border-biru-dongker-400 rounded-full w-5 h-5 flex items-center justify-center hover:bg-biru-dongker-200 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700 transition-colors dark:text-biru-dongker-600 dark:border-biru-dongker-800 dark:hover:bg-biru-dongker-900"
                    onclick="const p = document.getElementById('jkn-cbg-codes-help'); if (p) { p.classList.toggle('hidden'); }"
                    aria-label="{{ __('What is JKN CBG Code?') }}"
                    title="{{ __('What is JKN CBG Code?') }}"
                >
                    i
                </button>
            </div>
            <a href="{{ route('jkn-cbg-codes.create') }}" class="btn-primary">
                {{ __('Add New CBG Code') }}
            </a>
        </div>
        <div id="jkn-cbg-codes-help" class="mb-4 hidden text-xs text-gray-700 bg-biru-dongker-200 border border-biru-dongker-300 rounded-md p-3 dark:bg-biru-dongker-900 dark:border-biru-dongker-900 dark:text-biru-dongker-300">
            <p class="mb-2">
                <span class="font-semibold">JKN CBG Code</span> adalah master data kode INA-CBG (JKN) dan informasi dasarnya.
            </p>
            <div class="mb-2">
                <p class="font-semibold mb-1">Isi utama:</p>
                <ul class="list-disc list-inside space-y-1 ml-2">
                    <li>Kode CBG</li>
                    <li>Deskripsi CBG</li>
                    <li>Tarif paket INA-CBG per kelas / rumah sakit</li>
                    <li>(Opsional) jenis kasus, severity, dll</li>
                </ul>
            </div>
            <div>
                <p class="font-semibold mb-1">Peran di sistem:</p>
                <p class="ml-2">Menjadi referensi untuk:</p>
                <ul class="list-disc list-inside space-y-1 ml-4">
                    <li>Tarif Comparison (tarif RS vs INA-CBG)</li>
                    <li>Analisis Case Variance dan Pathway Performance berdasarkan CBG</li>
                </ul>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative dark:bg-green-800 dark:text-green-100" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <div class="bg-white shadow overflow-hidden sm:rounded-lg dark:bg-gray-800">
            <div class="px-4 py-5 sm:p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                    {{ __('Code') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                    {{ __('Name') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                    {{ __('Service Type') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                    {{ __('Tariff') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                    {{ __('Status') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                    {{ __('Actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($cbgCodes as $cbgCode)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $cbgCode->code }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ $cbgCode->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ $cbgCode->service_type ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        Rp {{ number_format($cbgCode->tariff, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        @if($cbgCode->is_active)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                                {{ __('Active') }}
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                                                {{ __('Inactive') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('jkn-cbg-codes.edit', $cbgCode) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-biru-dongker-800 hover:bg-biru-dongker-200 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700 dark:border-gray-600 dark:text-biru-dongker-600 dark:hover:bg-biru-dongker-900" title="{{ __('Edit') }}" aria-label="{{ __('Edit') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25Zm2.92 2.83H5v-.92l9.06-9.06.92.92L5.92 20.08ZM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83Z"/>
                                                </svg>
                                            </a>
                                            <form action="{{ route('jkn-cbg-codes.destroy', $cbgCode) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this CBG code?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-red-700 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 dark:border-gray-600 dark:text-red-400 dark:hover:bg-red-900" title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                        <path d="M9 3h6a1 1 0 0 1 1 1v1h4v2H4V5h4V4a1 1 0 0 1 1-1Zm-3 6h12l-1 11a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L6 9Zm3 2v8h2v-8H9Zm4 0v8h2v-8h-2Z"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 text-center">
                                        {{ __('No CBG codes found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $cbgCodes->links() }}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
