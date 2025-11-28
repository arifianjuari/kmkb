@extends('layouts.app')

@section('content')
<div class="mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between gap-3 mb-4 flex-wrap">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('Clinical Pathway Details') }}</h2>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('pathways.index') }}" class="btn btn-outline">{{ __('Back to List') }}</a>
            <a href="{{ route('pathways.edit', $pathway) }}" class="btn btn-primary">{{ __('Edit') }}</a>
            @auth
            @if(auth()->user()?->hasRole('mutu') || auth()->user()?->hasRole('admin'))
                <a href="{{ route('pathways.builder', $pathway) }}" class="btn btn-warning">{{ __('Builder') }}</a>
                <form action="{{ route('pathways.duplicate', $pathway) }}" method="POST" class="contents">
                    @csrf
                    <button type="submit" class="btn btn-secondary">{{ __('Duplicate') }}</button>
                </form>
                <form action="{{ route('pathways.version', $pathway) }}" method="POST" class="flex items-center gap-2">
                    @csrf
                    <label for="bump" class="sr-only">{{ __('Version bump') }}</label>
                    <select id="bump" name="bump" class="py-2 px-2 min-w-[140px] border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 bg-white text-gray-900">
                        <option value="patch">{{ __('Patch') }}</option>
                        <option value="minor">{{ __('Minor') }}</option>
                        <option value="major">{{ __('Major') }}</option>
                    </select>
                    <button type="submit" class="btn btn-success">{{ __('New Version') }}</button>
                </form>
                <a href="{{ route('pathways.export-docx', $pathway) }}" class="btn btn-primary">{{ __('Export DOCX') }}</a>
                <a href="{{ route('pathways.export-pdf', $pathway) }}" class="btn btn-secondary">{{ __('Export PDF') }}</a>
            @endif
            @endauth
        </div>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h5 class="text-base font-semibold text-gray-900">{{ __('Pathway Information') }}</h5>
            <div class="text-xl font-semibold text-gray-900">
                {{ $pathway->diagnosis_code }}.{{ $pathway->name }}
            </div>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <table class="min-w-full">
                        <tr>
                            <th class="py-2 pr-4 text-left text-sm font-medium text-gray-500 align-top">{{ __('Version') }}</th>
                            <td class="py-2 text-sm text-gray-900">{{ $pathway->version }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 pr-4 text-left text-sm font-medium text-gray-500 align-top">{{ __('Description') }}</th>
                            <td class="py-2 text-sm text-gray-900">{{ $pathway->description }}</td>
                        </tr>
                    </table>
                </div>
                <div>
                    <table class="min-w-full">
                        <tr>
                            <th class="py-2 pr-4 text-left text-sm font-medium text-gray-500">{{ __('Effective Date') }}</th>
                            <td class="py-2 text-sm text-gray-900">{{ $pathway->effective_date->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 pr-4 text-left text-sm font-medium text-gray-500">{{ __('Status') }}</th>
                            <td class="py-2">
                                @if($pathway->status == 'active')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">{{ __('Active') }}</span>
                                @elseif($pathway->status == 'draft')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">{{ __('Draft') }}</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">{{ __('Inactive') }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="py-2 pr-4 text-left text-sm font-medium text-gray-500">{{ __('Created By') }}</th>
                            <td class="py-2 text-sm text-gray-900">{{ $pathway->creator->name }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden" x-data="{ groupBy: 'category' }">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h5 class="text-base font-semibold text-gray-900">{{ __('Pathway Steps') }}</h5>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-600">{{ __('Group by') }}:</span>
                <div class="flex items-center gap-2">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="groupBy" value="category" x-model="groupBy" class="mr-2 text-biru-dongker-800 focus:ring-biru-dongker-700">
                        <span class="text-sm text-gray-700">{{ __('Category') }}</span>
                    </label>
                    <label class="flex items-center cursor-pointer ml-4">
                        <input type="radio" name="groupBy" value="day" x-model="groupBy" class="mr-2 text-biru-dongker-800 focus:ring-biru-dongker-700">
                        <span class="text-sm text-gray-700">{{ __('Day') }}</span>
                    </label>
                </div>
            </div>
        </div>
        <div class="p-6">
            @if($pathway->steps->count() > 0)
                @php
                    // Define category order based on the enum values
                    $categoryOrder = [
                        'Administrasi',
                        'Penilaian dan Pemantauan Medis',
                        'Penilaian dan Pemantauan Keperawatan',
                        'Pemeriksaan Penunjang Medik',
                        'Tindakan Medis',
                        'Tindakan Keperawatan',
                        'Medikasi',
                        'BHP',
                        'Nutrisi',
                        'Kegiatan',
                        'Konsultasi dan Komunikasi Tim',
                        'Konseling Psikososial',
                        'Pendidikan dan Komunikasi dengan Pasien/Keluarga',
                        'Kriteria KRS',
                    ];
                    
                    // Group steps by category
                    $stepsByCategory = $pathway->steps->groupBy('category');
                    
                    // Group steps by day (step_order)
                    $stepsByDay = $pathway->steps->groupBy('step_order')->sortKeys();
                    
                    // Calculate total cost
                    $totalCost = $pathway->steps->sum(function($step) {
                        return ($step->estimated_cost ?? 0) * $step->quantity;
                    });
                @endphp

                <!-- Group by Category -->
                <div x-show="groupBy === 'category'" class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Day') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Activity') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Description') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Criteria') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Standard Cost') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Full Standard Cost') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($categoryOrder as $category)
                                @if($stepsByCategory->has($category) && $stepsByCategory[$category]->count() > 0)
                                    <tr class="bg-gray-50">
                                        <td colspan="6" class="px-6 py-3">
                                            <h6 class="text-sm font-semibold text-gray-700">{{ $category }}</h6>
                                        </td>
                                    </tr>
                                    @foreach($stepsByCategory[$category]->sortBy('step_order') as $step)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $step->step_order }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <div class="flex items-center gap-2">
                                                    <span>{{ $step->service_code }}</span>
                                                    @if(method_exists($step, 'isConditional') ? $step->isConditional() : (!empty(trim($step->criteria ?? ''))))
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">{{ __('Conditional') }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ $step->description }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ $step->criteria }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp{{ number_format($step->estimated_cost, 0, ',', '.') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp{{ number_format(($step->estimated_cost ?? 0) * $step->quantity, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach

                            @if($stepsByCategory->has(null) && $stepsByCategory[null]->count() > 0)
                                <tr class="bg-gray-50">
                                    <td colspan="6" class="px-6 py-3">
                                        <h6 class="text-sm font-semibold text-gray-700">{{ __('Other') }}</h6>
                                    </td>
                                </tr>
                                @foreach($stepsByCategory[null]->sortBy('step_order') as $step)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $step->step_order }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="flex items-center gap-2">
                                            <span>{{ $step->service_code }}</span>
                                            @if(method_exists($step, 'isConditional') ? $step->isConditional() : (!empty(trim($step->criteria ?? ''))))
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">{{ __('Conditional') }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $step->description }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $step->criteria }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp{{ number_format($step->estimated_cost, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp{{ number_format(($step->estimated_cost ?? 0) * $step->quantity, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Group by Day -->
                <div x-show="groupBy === 'day'" class="overflow-x-auto" style="display: none;">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Day') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Activity') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Description') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Criteria') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Standard Cost') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Full Standard Cost') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($stepsByDay as $day => $steps)
                                <tr class="bg-gray-50">
                                    <td colspan="6" class="px-6 py-3">
                                        <h6 class="text-sm font-semibold text-gray-700">{{ __('Day') }} {{ $day }}</h6>
                                    </td>
                                </tr>
                                @foreach($steps->sortBy('display_order') as $step)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $step->step_order }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div class="flex items-center gap-2">
                                                <span>{{ $step->service_code }}</span>
                                                @if(method_exists($step, 'isConditional') ? $step->isConditional() : (!empty(trim($step->criteria ?? ''))))
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">{{ __('Conditional') }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $step->description }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $step->criteria }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp{{ number_format($step->estimated_cost, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp{{ number_format(($step->estimated_cost ?? 0) * $step->quantity, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Total Cost Summary -->
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <div class="flex justify-end">
                        <div class="text-right">
                            <span class="text-sm font-semibold text-gray-900">{{ __('Total Standard Cost') }}: </span>
                            <span class="text-sm font-bold text-gray-900">Rp{{ number_format($totalCost, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Recalculate Summary Section -->
                @auth
                @if(auth()->user()?->hasRole('mutu') || auth()->user()?->hasRole('admin'))
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">{{ __('Recalculate Pathway Summary') }}</h4>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <form id="recalculate-summary-form" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="recalc_version" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Unit Cost Version') }}</label>
                                    <select id="recalc_version" name="version" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                        <option value="">{{ __('Use pathway default') }}</option>
                                        @foreach($versions ?? [] as $version)
                                            <option value="{{ $version }}" {{ $pathway->unit_cost_version == $version ? 'selected' : '' }}>{{ $version }}</option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">{{ __('Pilih versi unit cost untuk perhitungan. Kosongkan untuk menggunakan versi default pathway.') }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Mode') }}</label>
                                    <div class="space-y-2">
                                        <label class="flex items-center">
                                            <input type="radio" name="mode" value="simulation" checked class="mr-2 text-biru-dongker-800 focus:ring-biru-dongker-700">
                                            <span class="text-sm text-gray-700">{{ __('Simulasi') }}</span>
                                            <span class="ml-2 text-xs text-gray-500">(Preview tanpa update data)</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="mode" value="rebaseline" class="mr-2 text-biru-dongker-800 focus:ring-biru-dongker-700">
                                            <span class="text-sm text-gray-700">{{ __('Re-baseline') }}</span>
                                            <span class="ml-2 text-xs text-gray-500">(Update semua step dengan unit cost baru)</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-4">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-biru-dongker-800 text-white text-sm font-medium rounded-md shadow hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                                    {{ __('Recalculate') }}
                                </button>
                                <div id="recalc-loading" class="hidden text-sm text-gray-600">
                                    <span class="inline-block animate-spin mr-2">⏳</span>
                                    {{ __('Calculating...') }}
                                </div>
                            </div>
                            
                            <div id="recalc-result" class="hidden mt-4 p-4 bg-white border border-gray-200 rounded-lg">
                                <h5 class="text-sm font-semibold text-gray-900 mb-2">{{ __('Hasil Perhitungan') }}</h5>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-700">{{ __('Estimated Total Cost') }}:</span>
                                        <span class="font-semibold text-gray-900" id="recalc-total-cost">Rp0</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-700">{{ __('Estimated Total Tariff') }}:</span>
                                        <span class="font-semibold text-gray-900" id="recalc-total-tariff">Rp0</span>
                                    </div>
                                    <div class="mt-2 pt-2 border-t border-gray-200">
                                        <p class="text-xs text-gray-600" id="recalc-message"></p>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @endif
                @endauth
            @else
                <p class="text-gray-600">{{ __('No steps defined for this pathway yet.') }}</p>
            @endif

            @auth
            @if(auth()->user()?->hasRole('mutu') || auth()->user()?->hasRole('admin'))
                <a href="#" class="inline-flex items-center mt-4 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md shadow hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">{{ __('Add Step') }}</a>
            @endif
            @endauth
        </div>
    </div>
</div>

@auth
@if(auth()->user()?->hasRole('mutu') || auth()->user()?->hasRole('admin'))
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const recalcForm = document.getElementById('recalculate-summary-form');
        if (!recalcForm) return;
        
        recalcForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const version = formData.get('version') || '';
            const mode = formData.get('mode') || 'simulation';
            
            const loadingDiv = document.getElementById('recalc-loading');
            const resultDiv = document.getElementById('recalc-result');
            const totalCostSpan = document.getElementById('recalc-total-cost');
            const totalTariffSpan = document.getElementById('recalc-total-tariff');
            const messageP = document.getElementById('recalc-message');
            
            // Show loading
            loadingDiv.classList.remove('hidden');
            resultDiv.classList.add('hidden');
            
            try {
                const url = '{{ route("pathways.recalculate-summary", $pathway) }}';
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        version: version || null,
                        mode: mode
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Format numbers
                    const formatCurrency = (num) => {
                        return 'Rp' + new Intl.NumberFormat('id-ID').format(Math.round(num || 0));
                    };
                    
                    totalCostSpan.textContent = formatCurrency(data.estimated_total_cost);
                    totalTariffSpan.textContent = formatCurrency(data.estimated_total_tariff);
                    messageP.textContent = data.message || 'Perhitungan berhasil.';
                    
                    resultDiv.classList.remove('hidden');
                    
                    // If rebaseline mode, reload page to show updated data
                    if (mode === 'rebaseline') {
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    }
                } else {
                    alert('Error: ' + (data.message || 'Failed to recalculate summary'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menghitung ulang summary.');
            } finally {
                loadingDiv.classList.add('hidden');
            }
        });
    });
</script>
@endsection
@endif
@endauth
@endsection
