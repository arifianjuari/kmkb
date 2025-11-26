@extends('layouts.app')

@section('content')
<div class="mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Pathway Builder') }}: {{ $pathway->name }}</h2>
            <a href="{{ route('pathways.show', $pathway) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Back to Pathway') }}
            </a>
        </div>
        
        <div class="mb-6 md:grid md:grid-cols-12 md:gap-6">
            <!-- Add New Step (2/3) -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg md:col-span-8">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Add New Step') }}</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <form id="step-form" method="POST" action="{{ route('pathways.steps.store', $pathway) }}">
                        @csrf
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-12">
                            <div class="sm:col-span-2">
                                <label for="day" class="block text-sm font-medium text-gray-700">{{ __('Day') }}</label>
                                <div class="mt-1">
                                    <input type="number" id="day" name="day" min="1" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>
                            
                            <div class="sm:col-span-4">
                                <label for="category" class="block text-sm font-medium text-gray-700">{{ __('Category') }}</label>
                                <div class="mt-1">
                                    <select id="category" name="category" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">{{ __('Select Category') }}</option>
                                        <option value="Administrasi">Administrasi</option>
                                        <option value="Penilaian dan Pemantauan Medis">Penilaian dan Pemantauan Medis</option>
                                        <option value="Penilaian dan Pemantauan Keperawatan">Penilaian dan Pemantauan Keperawatan</option>
                                        <option value="Pemeriksaan Penunjang Medik">Pemeriksaan Penunjang Medik</option>
                                        <option value="Tindakan Medis">Tindakan Medis</option>
                                        <option value="Tindakan Keperawatan">Tindakan Keperawatan</option>
                                        <option value="Medikasi">Medikasi</option>
                                        <option value="BHP">BHP</option>
                                        <option value="Nutrisi">Nutrisi</option>
                                        <option value="Kegiatan">Kegiatan</option>
                                        <option value="Konsultasi dan Komunikasi Tim">Konsultasi dan Komunikasi Tim</option>
                                        <option value="Konseling Psikososial">Konseling Psikososial</option>
                                        <option value="Pendidikan dan Komunikasi dengan Pasien/Keluarga">Pendidikan dan Komunikasi dengan Pasien/Keluarga</option>
                                        <option value="Kriteria KRS">Kriteria KRS</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="sm:col-span-5">
                                <label for="activity" class="block text-sm font-medium text-gray-700">{{ __('Activity') }}</label>
                                <div class="mt-1">
                                    <input type="text" id="activity" name="activity" list="activityList" placeholder="{{ __('Type to search service code') }}" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <datalist id="activityList">
                                        @foreach($costReferences as $reference)
                                            <option value="{{ $reference->service_code }}">{{ $reference->service_description }}</option>
                                        @endforeach
                                    </datalist>
                                    <input type="hidden" id="cost_reference_id" name="cost_reference_id" value="">
                                    <div id="no-match-add-ref" class="mt-2 hidden text-left">
                                        <button type="button" id="open-add-ref" class="text-xs text-indigo-600 hover:text-indigo-800">
                                            {{ __('Tidak menemukan hasil? Tambah referensi biaya baru') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="sm:col-span-5">
                                <label for="description" class="block text-sm font-medium text-gray-700">{{ __('Description') }}</label>
                                <div class="mt-1">
                                    <input type="text" id="description" name="description" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>

                            <div class="sm:col-span-5">
                                <label for="criteria" class="block text-sm font-medium text-gray-700">{{ __('Keterangan (opsional)') }}</label>
                                <div class="mt-1">
                                    <input type="text" id="criteria" name="criteria" placeholder="e.g., if age > 60 then ..." class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>
                            
                            <div class="sm:col-span-3">
                                <label for="standard_cost" class="block text-sm font-medium text-gray-700">{{ __('Standard Cost') }}</label>
                                <div class="mt-1">
                                    <input type="number" id="standard_cost" name="standard_cost" step="1" min="0" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="quantity" class="block text-sm font-medium text-gray-700">{{ __('Quantity') }}</label>
                                <div class="mt-1">
                                    <input type="number" id="quantity" name="quantity" step="1" min="1" value="1" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="total_cost_display" class="block text-sm font-medium text-gray-700">{{ __('Total Cost') }}</label>
                                <div class="mt-1">
                                    <input type="text" id="total_cost_display" readonly value="0" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-700 focus:outline-none">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Cost Reference selection moved: handled by Activity dropdown above (which writes cost_reference_id, description, and standard_cost) -->
                        
                        <div class="mt-6">
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('Add Step') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Bulk Import (1/3) -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg mt-6 md:mt-0 md:col-span-4">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Bulk Import Steps') }}</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('pathways.steps.template', $pathway) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Download Excel Template') }}
                        </a>
                    </div>
                    <form class="mt-4" method="POST" action="{{ route('pathways.steps.import', $pathway) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-3">
                            <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('Upload File') }}
                            </button>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">{{ __('Kolom: day, activity, description, criteria, standard_cost, quantity, cost_reference_id. Total cost akan dihitung otomatis (standard_cost x quantity). Anda dapat mengisi dari template Excel lalu upload file Excel/CSV.') }}</p>
                    </form>
                </div>
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Pathway Steps') }}</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                @if($pathway->steps->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10"></th>
                                    <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Day') }}</th>
                                    <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Category') }}</th>
                                    <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Service Code') }}</th>
                                    <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Description') }}</th>
                                    <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Criteria') }}</th>
                                    <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Standard Cost') }}</th>
                                    <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Quantity') }}</th>
                                    <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Total Cost') }}</th>
                                    <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                    <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Cost Reference') }}</th>
                                </tr>
                            </thead>
                            <tbody id="steps-tbody" class="bg-white divide-y divide-gray-200">
                                @php
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
                                    $orderMap = array_flip($categoryOrder);
                                    $stepsSorted = $pathway->steps->sortBy(function ($step) use ($orderMap) {
                                        $idx = $orderMap[$step->category] ?? 999;
                                        $disp = (int) ($step->display_order ?? $step->step_order ?? 0);
                                        return sprintf('%03d-%05d', $idx, $disp);
                                    });
                                @endphp
                                @foreach($stepsSorted as $step)
                                    <tr data-step-id="{{ $step->id }}">
                                        <td class="px-2 py-2 text-gray-400 cursor-move drag-handle" title="Drag to reorder">☰</td>
                                        <td contenteditable="true" data-field="day" class="px-6 py-2 whitespace-nowrap text-xs text-gray-900">{{ $step->step_order }}</td>
                                        <td contenteditable="true" data-field="category" class="px-6 py-2 whitespace-nowrap text-xs text-gray-900">{{ $step->category }}</td>
                                        <td contenteditable="true" data-field="activity" class="px-6 py-2 whitespace-nowrap text-xs text-gray-900">{{ $step->service_code }}</td>
                                        <td contenteditable="true" data-field="description" class="px-6 py-2 text-xs text-gray-900">{{ $step->description }}</td>
                                        <td contenteditable="true" data-field="criteria" class="px-6 py-2 text-xs text-gray-900">{{ $step->criteria }}</td>
                                        <td contenteditable="true" data-field="standard_cost" class="px-6 py-2 whitespace-nowrap text-xs text-gray-900">{{ number_format($step->estimated_cost, 0, ',', '.') }}</td>
                                        <td contenteditable="true" data-field="quantity" class="px-6 py-2 whitespace-nowrap text-xs text-gray-900">{{ number_format($step->quantity, 0, ',', '.') }}</td>
                                        <td data-field="total_cost" class="px-6 py-2 whitespace-nowrap text-xs text-gray-900">{{ number_format($step->total_cost, 0, ',', '.') }}</td>
                                        <td class="px-6 py-2 whitespace-nowrap text-xs">
                                            <button class="save-step inline-flex items-center p-1.5 border border-transparent rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" data-step-id="{{ $step->id }}" aria-label="{{ __('Save') }}" title="{{ __('Save') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4" aria-hidden="true">
                                                    <path d="M17 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7z" />
                                                    <path d="M17 3v4H7V3" />
                                                    <path d="M7 21v-8h10v8" />
                                                </svg>
                                                <span class="sr-only">{{ __('Save') }}</span>
                                            </button>
                                            <form action="{{ route('pathways.steps.destroy', [$pathway, $step]) }}" method="POST" class="inline ml-2">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center p-1.5 border border-transparent rounded text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" onclick="return confirm('{{ __('Are you sure you want to delete this step?') }}')" aria-label="{{ __('Delete') }}" title="{{ __('Delete') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4" aria-hidden="true">
                                                        <path d="M6 7h12" />
                                                        <path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2" />
                                                        <path d="M18 7v12a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V7" />
                                                        <path d="M10 12v6" />
                                                        <path d="M14 12v6" />
                                                    </svg>
                                                    <span class="sr-only">{{ __('Delete') }}</span>
                                                </button>
                                            </form>
                                        </td>
                                        <td class="px-6 py-2 whitespace-nowrap text-xs text-gray-900">
                                            <select class="cost-reference-select py-1 px-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" data-step-id="{{ $step->id }}">
                                                <option value="">{{ __('None') }}</option>
                                                @foreach($costReferences as $reference)
                                                    <option value="{{ $reference->id }}" data-cost="{{ number_format($reference->standard_cost, 0, '.', '') }}" data-code="{{ $reference->service_code }}" data-desc="{{ $reference->service_description }}" {{ $step->cost_reference_id == $reference->id ? 'selected' : '' }}>
                                                        {{ $reference->service_description }} (Rp{{ number_format($reference->standard_cost, 0, ',', '.') }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-600">{{ __('No steps defined for this pathway yet.') }}</p>
                @endif
                
                <!-- Total Cost Display -->
                <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex justify-between items-center">
                        <h4 class="text-lg font-medium text-gray-900">{{ __('Total Pathway Cost') }}</h4>
                        <div class="text-2xl font-bold text-indigo-600" id="total-pathway-cost">
                            Rp{{ number_format($pathway->steps->sum('total_cost'), 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Cost Reference Modal -->
<div id="add-ref-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div class="sm:flex sm:items-start">
                <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">{{ __('Tambah Referensi Biaya') }}</h3>
                    <div class="mt-4">
                        <form id="add-ref-form">
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">{{ __('Service Name') }}</label>
                                    <input type="text" id="ref_service_code" required class="mt-1 py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">{{ __('Description') }}</label>
                                    <input type="text" id="ref_service_description" required class="mt-1 py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">{{ __('Standard Cost') }}</label>
                                    <input type="number" id="ref_standard_cost" step="1" min="0" value="0" required class="mt-1 py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ __('Unit') }}</label>
                                        <input type="text" id="ref_unit" placeholder="{{ __('e.g. kali / tindakan') }}" required class="mt-1 py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ __('Source') }}</label>
                                        <input type="text" id="ref_source" placeholder="{{ __('e.g. internal') }}" required class="mt-1 py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>
                                <p id="add-ref-error" class="text-sm text-red-600 hidden"></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-6 sm:flex sm:flex-row-reverse">
                <button type="button" id="save-add-ref" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">{{ __('Simpan') }}</button>
                <button type="button" id="cancel-add-ref" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">{{ __('Batal') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Helpers
        function parseNum(val, fallback = 0) {
            if (typeof val !== 'string') val = String(val ?? '');
            // remove common thousand separators and spaces
            const clean = val.replace(/[.,\s]/g, '').trim();
            const n = parseFloat(clean);
            return isNaN(n) ? fallback : n;
        }
        function toIntString(n) { return String(Math.round(parseFloat(n || 0))); }
        const idFormatter = new Intl.NumberFormat('id-ID');
        function formatInt(n) { return idFormatter.format(Math.round(parseFloat(n || 0))); }

        // Handle form submission for adding new steps
        document.getElementById('step-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('{{ route('pathways.steps.store', $pathway) }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload the page to show the new step
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while adding the step.');
            });
        });

        // Build a lookup map: service_code -> { id, cost, desc }
        const costRefMap = {
@foreach($costReferences as $reference)
            "{{ $reference->service_code }}": { id: {{ $reference->id }}, cost: "{{ number_format($reference->standard_cost, 0, '.', '') }}", desc: @json($reference->service_description) },
@endforeach
        };

        // Auto-fill fields when selecting/typing Activity (service_code) in create form
        const activityInput = document.getElementById('activity');
        const hiddenCostId = document.getElementById('cost_reference_id');
        const addRefPrompt = document.getElementById('no-match-add-ref');
        const openAddRefBtn = document.getElementById('open-add-ref');
        const addRefModal = document.getElementById('add-ref-modal');
        const addRefForm = document.getElementById('add-ref-form');
        const saveAddRefBtn = document.getElementById('save-add-ref');
        const cancelAddRefBtn = document.getElementById('cancel-add-ref');
        const refCode = document.getElementById('ref_service_code');
        const refDesc = document.getElementById('ref_service_description');
        const refCost = document.getElementById('ref_standard_cost');
        const refUnit = document.getElementById('ref_unit');
        const refSource = document.getElementById('ref_source');
        const addRefError = document.getElementById('add-ref-error');
        
        // Unit cost version from pathway
        const pathwayUnitCostVersion = @json($pathway->unit_cost_version ?? null);
        
        // Function to fetch unit cost from API
        async function fetchUnitCost(serviceId, version = null) {
            if (!serviceId) return null;
            
            try {
                const url = new URL('/api/unit-cost', window.location.origin);
                url.searchParams.append('serviceId', serviceId);
                if (version) {
                    url.searchParams.append('version', version);
                } else if (pathwayUnitCostVersion) {
                    url.searchParams.append('version', pathwayUnitCostVersion);
                }
                
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (!response.ok) {
                    throw new Error('Failed to fetch unit cost');
                }
                
                const data = await response.json();
                return data;
            } catch (error) {
                console.error('Error fetching unit cost:', error);
                return null;
            }
        }
        
        // Function to show unit cost warning
        function showUnitCostWarning(message) {
            let warningDiv = document.getElementById('unit-cost-warning');
            if (!warningDiv) {
                warningDiv = document.createElement('div');
                warningDiv.id = 'unit-cost-warning';
                warningDiv.className = 'mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded text-sm text-yellow-800';
                const stdCostInput = document.getElementById('standard_cost');
                if (stdCostInput && stdCostInput.parentElement) {
                    stdCostInput.parentElement.appendChild(warningDiv);
                }
            }
            warningDiv.textContent = message;
            warningDiv.classList.remove('hidden');
        }
        
        // Function to hide unit cost warning
        function hideUnitCostWarning() {
            const warningDiv = document.getElementById('unit-cost-warning');
            if (warningDiv) {
                warningDiv.classList.add('hidden');
            }
        }
        
        async function applyActivitySelection() {
            const code = (activityInput.value || '').trim();
            const ref = costRefMap[code];
            const stdCostInput = document.getElementById('standard_cost');
            const descInput = document.getElementById('description');
            
            if (ref) {
                if (hiddenCostId) hiddenCostId.value = ref.id || '';
                if (descInput) descInput.value = ref.desc || '';
                if (addRefPrompt) addRefPrompt.classList.add('hidden');
                
                // Fetch unit cost from API
                if (ref.id) {
                    hideUnitCostWarning();
                    const unitCostData = await fetchUnitCost(ref.id);
                    
                    if (unitCostData) {
                        if (unitCostData.fallback_used) {
                            // Using standard cost as fallback
                            if (stdCostInput) stdCostInput.value = unitCostData.unit_cost || ref.cost || '';
                            showUnitCostWarning('Belum ada unit cost resmi – menggunakan Standard Cost dari Cost References');
                        } else {
                            // Using unit cost
                            if (stdCostInput) stdCostInput.value = Math.round(unitCostData.unit_cost) || ref.cost || '';
                            hideUnitCostWarning();
                        }
                    } else {
                        // Fallback to standard cost if API fails
                        if (stdCostInput) stdCostInput.value = ref.cost || '';
                    }
                } else {
                    // No cost reference ID, use standard cost
                    if (stdCostInput) stdCostInput.value = ref.cost || '';
                    hideUnitCostWarning();
                }
            } else {
                if (hiddenCostId) hiddenCostId.value = '';
                // Only clear when the field is blank to avoid wiping custom entries while typing
                if (code === '') {
                    if (stdCostInput) stdCostInput.value = '';
                    if (descInput) descInput.value = '';
                    hideUnitCostWarning();
                }
            }
            recalcCreateTotal();
        }

        function checkActivityMatches() {
            if (!activityInput || !addRefPrompt) return;
            const q = (activityInput.value || '').trim().toLowerCase();
            if (!q) { addRefPrompt.classList.add('hidden'); return; }
            const matches = Object.entries(costRefMap).filter(([code, ref]) => {
                return code.toLowerCase().includes(q) || String(ref.desc || '').toLowerCase().includes(q);
            });
            const exact = !!costRefMap[activityInput.value.trim()];
            if (matches.length === 0 && !exact) {
                addRefPrompt.classList.remove('hidden');
            } else {
                addRefPrompt.classList.add('hidden');
            }
        }
        if (activityInput) {
            activityInput.addEventListener('change', () => { applyActivitySelection(); checkActivityMatches(); });
            activityInput.addEventListener('input', function() {
                // Debounced-ish: apply quickly but not on every keystroke heavy logic
                applyActivitySelection();
                checkActivityMatches();
            });
        }

        function openAddRefModal(prefillCode = '') {
            if (!addRefModal) return;
            addRefError.classList.add('hidden');
            addRefError.textContent = '';
            refCode.value = prefillCode || (activityInput?.value || '');
            refDesc.value = '';
            refCost.value = '0';
            refUnit.value = '';
            refSource.value = '';
            addRefModal.classList.remove('hidden');
        }
        function closeAddRefModal() {
            if (!addRefModal) return;
            addRefModal.classList.add('hidden');
        }
        if (openAddRefBtn) openAddRefBtn.addEventListener('click', () => openAddRefModal(activityInput?.value || ''));
        if (cancelAddRefBtn) cancelAddRefBtn.addEventListener('click', closeAddRefModal);
        if (addRefModal) addRefModal.addEventListener('click', (e) => { if (e.target === addRefModal) closeAddRefModal(); });

        async function submitAddRef() {
            addRefError.classList.add('hidden');
            addRefError.textContent = '';
            try {
                const payload = {
                    service_code: refCode.value.trim(),
                    service_description: refDesc.value.trim(),
                    standard_cost: parseNum(refCost.value, 0),
                    unit: refUnit.value.trim(),
                    source: refSource.value.trim(),
                };
                const resp = await fetch('{{ route('cost-references.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const data = await resp.json();
                if (!resp.ok || !data?.success) {
                    const msg = data?.message || (data?.errors ? Object.values(data.errors).flat().join(', ') : 'Failed to create');
                    throw new Error(msg);
                }
                const created = data.data;
                // Update datalist
                const dl = document.getElementById('activityList');
                if (dl) {
                    const opt = document.createElement('option');
                    opt.value = created.service_code;
                    opt.textContent = created.service_description;
                    dl.appendChild(opt);
                }
                // Update lookup map
                costRefMap[created.service_code] = { id: created.id, cost: String(created.standard_cost || 0), desc: created.service_description };
                // Apply selection
                if (activityInput) activityInput.value = created.service_code;
                applyActivitySelection();
                checkActivityMatches();
                // Update cost reference selects in table rows
                document.querySelectorAll('.cost-reference-select').forEach(sel => {
                    const opt = document.createElement('option');
                    opt.value = created.id;
                    opt.dataset.cost = String(created.standard_cost || 0);
                    opt.dataset.code = created.service_code;
                    opt.dataset.desc = created.service_description;
                    opt.textContent = `${created.service_description} (Rp${formatInt(created.standard_cost || 0)})`;
                    sel.appendChild(opt);
                });
                closeAddRefModal();
            } catch (err) {
                addRefError.textContent = err.message || 'Terjadi kesalahan saat membuat referensi biaya.';
                addRefError.classList.remove('hidden');
            }
        }
        if (saveAddRefBtn) saveAddRefBtn.addEventListener('click', submitAddRef);

        // Live compute total in create form
        const stdCostInput = document.getElementById('standard_cost');
        const qtyInput = document.getElementById('quantity');
        const totalDisplay = document.getElementById('total_cost_display');
        function recalcCreateTotal() {
            if (!stdCostInput || !qtyInput || !totalDisplay) return;
            const cost = parseNum(stdCostInput.value, 0);
            const qty = parseNum(qtyInput.value, 1);
            totalDisplay.value = formatInt(cost * qty);
        }
        if (stdCostInput) stdCostInput.addEventListener('input', recalcCreateTotal);
        if (qtyInput) qtyInput.addEventListener('input', recalcCreateTotal);
        recalcCreateTotal();
        
        // Update total pathway cost on page load
        updateTotalPathwayCost();
        
        // Handle saving edited steps
        document.querySelectorAll('.save-step').forEach(button => {
            button.addEventListener('click', function() {
                const stepId = this.getAttribute('data-step-id');
                const row = document.querySelector(`tr[data-step-id="${stepId}"]`);
                
                const data = {
                    day: row.querySelector('[data-field="day"]').textContent,
                    category: row.querySelector('[data-field="category"]').textContent,
                    activity: row.querySelector('[data-field="activity"]').textContent,
                    description: row.querySelector('[data-field="description"]').textContent,
                    annotation: row.querySelector('[data-field="criteria"]').textContent,
                    standard_cost: row.querySelector('[data-field="standard_cost"]').textContent.replace(/[.,\s]/g, ''),
                    quantity: row.querySelector('[data-field="quantity"]').textContent.replace(/[.,\s]/g, '')
                };
                
                // Get cost reference from select
                const costReferenceSelect = row.querySelector('.cost-reference-select');
                data.cost_reference_id = costReferenceSelect.value;
                
                fetch(`/pathways/{{ $pathway->id }}/steps/${stepId}`, {
                    method: 'PUT',
                    body: JSON.stringify(data),
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Step updated successfully');
                        // Update total pathway cost
                        updateTotalPathwayCost();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating the step.');
                });
            });
        });
        
        // Handle cost reference changes
        document.querySelectorAll('.cost-reference-select').forEach(select => {
            select.addEventListener('change', async function() {
                const stepId = this.getAttribute('data-step-id');
                const selectedOption = this.options[this.selectedIndex];
                const selectedValue = this.value;
                
                if (selectedOption && selectedOption.dataset) {
                    const costCell = document.querySelector(`tr[data-step-id="${stepId}"] [data-field="standard_cost"]`);
                    const activityCell = document.querySelector(`tr[data-step-id="${stepId}"] [data-field="activity"]`);
                    const descCell = document.querySelector(`tr[data-step-id="${stepId}"] [data-field="description"]`);
                    const row = document.querySelector(`tr[data-step-id="${stepId}"]`);
                    
                    // Update activity and description from selected option
                    if (activityCell && selectedOption.dataset.code) {
                        activityCell.textContent = selectedOption.dataset.code;
                    }
                    if (descCell && selectedOption.dataset.desc) {
                        descCell.textContent = selectedOption.dataset.desc;
                    }
                    
                    // Fetch unit cost from API if cost reference is selected
                    if (selectedValue && costCell) {
                        const unitCostData = await fetchUnitCost(selectedValue);
                        
                        if (unitCostData) {
                            if (unitCostData.fallback_used) {
                                // Using standard cost as fallback
                                costCell.textContent = formatInt(unitCostData.unit_cost || selectedOption.dataset.cost || 0);
                                // Show warning in console or could add visual indicator
                                console.log('Using standard cost fallback for step ' + stepId);
                            } else {
                                // Using unit cost
                                costCell.textContent = formatInt(unitCostData.unit_cost || 0);
                            }
                        } else {
                            // Fallback to standard cost if API fails
                            if (selectedOption.dataset.cost) {
                                costCell.textContent = formatInt(selectedOption.dataset.cost);
                            }
                        }
                    } else if (selectedOption.dataset.cost && costCell) {
                        // No cost reference selected, use standard cost
                        costCell.textContent = formatInt(selectedOption.dataset.cost);
                    }
                    
                    if (row) recalcRowTotal(row);
                }
            });
        });

        // Bind live total recompute for table rows
        function recalcRowTotal(row) {
            const cost = parseNum(row.querySelector('[data-field="standard_cost"]').textContent, 0);
            const qty = parseNum(row.querySelector('[data-field="quantity"]').textContent, 1);
            const totalCell = row.querySelector('[data-field="total_cost"]');
            if (totalCell) totalCell.textContent = formatInt(cost * qty);
            // Update overall total
            updateTotalPathwayCost();
        }
        
        // Calculate and update total pathway cost
        function updateTotalPathwayCost() {
            let total = 0;
            document.querySelectorAll('[data-field="total_cost"]').forEach(cell => {
                total += parseNum(cell.textContent, 0);
            });
            const totalElement = document.getElementById('total-pathway-cost');
            if (totalElement) {
                totalElement.textContent = 'Rp' + formatInt(total);
            }
        }
        function bindRowListeners(row) {
            const costCell = row.querySelector('[data-field="standard_cost"]');
            const qtyCell = row.querySelector('[data-field="quantity"]');
            if (costCell) costCell.addEventListener('input', () => recalcRowTotal(row));
            if (qtyCell) qtyCell.addEventListener('input', () => recalcRowTotal(row));
            // format on blur for readability
            if (costCell) costCell.addEventListener('blur', () => { const v = parseNum(costCell.textContent, 0); costCell.textContent = v ? formatInt(v) : '0'; });
            if (qtyCell) qtyCell.addEventListener('blur', () => { const v = parseNum(qtyCell.textContent, 1); qtyCell.textContent = v ? formatInt(v) : '1'; });
            recalcRowTotal(row);
        }
        document.querySelectorAll('#steps-tbody tr').forEach(bindRowListeners);

        // Enable drag-and-drop reordering via SortableJS
        const tbody = document.getElementById('steps-tbody');
        if (tbody && window.Sortable) {
            new Sortable(tbody, {
                handle: '.drag-handle',
                animation: 150,
                ghostClass: 'bg-gray-50',
                onEnd: function() {
                    const order = Array.from(tbody.querySelectorAll('tr')).map((tr, idx) => ({
                        id: tr.getAttribute('data-step-id'),
                        position: idx + 1
                    }));
                    fetch('{{ route('pathways.steps.reorder', $pathway) }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ order })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            alert('Failed to save new order');
                        }
                    })
                    .catch(() => alert('Error saving new order'));
                }
            });
        }
    });
</script>
@endsection
