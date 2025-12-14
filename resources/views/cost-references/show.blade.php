@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Cost Catalogue Item Details') }}</h2>
            <div>
                <a href="{{ route('cost-references.edit', $costReference) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                    {{ __('Edit') }}
                </a>
                <a href="{{ route('cost-references.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700 ml-2">
                    {{ __('Back to List') }}
                </a>
            </div>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Reference Information') }}</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Service Code') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $costReference->service_code }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Unit') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $costReference->unit }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Service Description') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $costReference->service_description }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Standard Cost (Rp)') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 text-right">{{ number_format($costReference->standard_cost, 0, ',', '.') }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Source') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $costReference->source == 'internal' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($costReference->source) }}
                            </span>
                        </dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Category') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @php
                                $categoryLabels = [
                                    'barang' => 'Barang/BMHP',
                                    'tindakan_rj' => 'Tindakan Rawat Jalan',
                                    'tindakan_ri' => 'Tindakan Rawat Inap',
                                    'laboratorium' => 'Laboratorium',
                                    'radiologi' => 'Radiologi',
                                    'operasi' => 'Operasi',
                                    'kamar' => 'Kamar',
                                ];
                            @endphp
                            @if($costReference->category)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-800">
                                    {{ $categoryLabels[$costReference->category] ?? $costReference->category }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Created At') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $costReference->created_at->format('d M Y, H:i') }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Last Updated') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $costReference->updated_at->format('d M Y, H:i') }}</dd>
                    </div>
                </dl>
            </div>
        </div>
        
        <div class="mt-6 flex justify-end">
            <form action="{{ route('cost-references.destroy', $costReference) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" onclick="return confirm('{{ __('Are you sure you want to delete this cost catalogue item?') }}')">
                    {{ __('Delete Cost Catalogue Item') }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
