@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
            <div class="flex items-center gap-3 flex-shrink-0">
                <h2 class="text-2xl font-bold text-gray-900 whitespace-nowrap">{{ __('Pathway Builder') }}</h2>
                <button
                    type="button"
                    class="flex-shrink-0 text-xs font-semibold text-biru-dongker-800 border border-biru-dongker-400 rounded-full w-5 h-5 flex items-center justify-center hover:bg-biru-dongker-200 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700 transition-colors"
                    onclick="const p = document.getElementById('builder-help'); if (p) { p.classList.toggle('hidden'); }"
                    aria-label="{{ __('What is Pathway Builder?') }}"
                    title="{{ __('What is Pathway Builder?') }}"
                >
                    i
                </button>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('pathways.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900">
                    {{ __('Create New Pathway') }}
                </a>
                <a href="{{ route('pathways.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    {{ __('Back to Repository') }}
                </a>
            </div>
        </div>

        <div id="builder-help" class="mb-4 hidden text-xs text-gray-700 bg-biru-dongker-200 border border-biru-dongker-300 rounded-md p-3">
            <p class="mb-2">
                <span class="font-semibold">Pathway Builder</span> adalah tool untuk membangun dan mengedit langkah-langkah (steps) dalam clinical pathway. Pilih pathway dari daftar di bawah untuk mulai membangun atau mengedit steps-nya.
            </p>
            <ul class="list-disc list-inside space-y-1 ml-2">
                <li>Klik <strong>Open Builder</strong> pada pathway yang ingin diedit</li>
                <li>Tambahkan, edit, atau hapus steps sesuai kebutuhan</li>
                <li>Link steps ke cost references untuk perhitungan biaya otomatis</li>
                <li>Set urutan dan kategori untuk setiap step</li>
            </ul>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <form method="GET" action="{{ route('pathways.builder.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                        <div>
                            <label for="q" class="block text-sm font-medium text-gray-700">{{ __('Search') }}</label>
                            <input type="text" id="q" name="q" value="{{ $q }}" placeholder="{{ __('Name, diagnosis code, version...') }}" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                        </div>
                        
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">{{ __('Status') }}</label>
                            <select id="status" name="status" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                                <option value="all" {{ $status == 'all' ? 'selected' : '' }}>{{ __('All Status') }}</option>
                                <option value="draft" {{ $status == 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                                <option value="review" {{ $status == 'review' ? 'selected' : '' }}>{{ __('Review') }}</option>
                                <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                                <option value="archived" {{ $status == 'archived' ? 'selected' : '' }}>{{ __('Archived') }}</option>
                            </select>
                        </div>
                        
                        <div class="flex items-end">
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900">
                                {{ __('Filter') }}
                            </button>
                        </div>
                        
                        @if($q || $status != 'all')
                            <div class="flex items-end">
                                <a href="{{ route('pathways.builder.index') }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    {{ __('Clear') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Pathways List -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Select Pathway to Build') }}</h3>
                
                @if($pathways->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Pathway Name') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Diagnosis Code') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Version') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Steps') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Created By') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($pathways as $pathway)
                                    <tr>
                                        <td class="px-6 py-4 text-sm">
                                            <div class="font-medium text-gray-900">{{ $pathway->name }}</div>
                                            @if($pathway->description)
                                                <div class="text-gray-500 text-xs mt-1">{{ Str::limit($pathway->description, 50) }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $pathway->diagnosis_code ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $pathway->version ?? '1.0.0' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                            {{ number_format($pathway->steps_count, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $pathway->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $pathway->status === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                                                {{ $pathway->status === 'review' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $pathway->status === 'archived' ? 'bg-red-100 text-red-800' : '' }}
                                            ">
                                                {{ ucfirst($pathway->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $pathway->creator->name ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('pathways.show', $pathway) }}" class="text-biru-dongker-800 hover:text-biru-dongker-950" title="{{ __('View') }}">
                                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>
                                                <a href="{{ route('pathways.builder', $pathway) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-biru-dongker-800 hover:bg-biru-dongker-900" title="{{ __('Open Builder') }}">
                                                    {{ __('Open Builder') }}
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $pathways->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No pathways found') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ __('Get started by creating a new pathway.') }}
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('pathways.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-biru-dongker-800 hover:bg-biru-dongker-900">
                                {{ __('Create New Pathway') }}
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

