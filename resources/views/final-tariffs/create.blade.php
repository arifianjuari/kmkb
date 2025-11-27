@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Add New Final Tariff') }}</h2>
            <a href="{{ route('final-tariffs.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                {{ __('Back to List') }}
            </a>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Final Tariff Details') }}</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <form action="{{ route('final-tariffs.store') }}" method="POST" id="tariffForm">
                    @csrf
                    
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-12">
                        <div class="col-span-12 md:col-span-6">
                            <label for="cost_reference_id" class="block text-sm font-medium text-gray-700">{{ __('Service') }} <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <select id="cost_reference_id" name="cost_reference_id" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">{{ __('Select Service') }}</option>
                                    @foreach($costReferences as $cr)
                                        <option value="{{ $cr->id }}" {{ old('cost_reference_id', $prefill['cost_reference_id'] ?? '') == $cr->id ? 'selected' : '' }}>
                                            {{ $cr->service_code }} - {{ $cr->service_description }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('cost_reference_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="tariff_class_id" class="block text-sm font-medium text-gray-700">{{ __('Tariff Class') }}</label>
                            <div class="mt-1">
                                <select id="tariff_class_id" name="tariff_class_id" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">{{ __('No Class') }}</option>
                                    @foreach($tariffClasses as $tc)
                                        <option value="{{ $tc->id }}" {{ old('tariff_class_id') == $tc->id ? 'selected' : '' }}>{{ $tc->name }}</option>
                                    @endforeach
                                </select>
                                @error('tariff_class_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="unit_cost_calculation_id" class="block text-sm font-medium text-gray-700">{{ __('Unit Cost Calculation') }} <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <select id="unit_cost_calculation_id" name="unit_cost_calculation_id" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">{{ __('Select Unit Cost Calculation') }}</option>
                                </select>
                                @error('unit_cost_calculation_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Pilih unit cost calculation yang akan digunakan</p>
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="sk_number" class="block text-sm font-medium text-gray-700">{{ __('SK Number') }} <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <input type="text" id="sk_number" name="sk_number" value="{{ old('sk_number') }}" required maxlength="100" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                @error('sk_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Nomor SK/approval (contoh: SK/RS/2025/001)</p>
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="base_unit_cost" class="block text-sm font-medium text-gray-700">{{ __('Base Unit Cost') }} <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <input type="number" id="base_unit_cost" name="base_unit_cost" value="{{ old('base_unit_cost', $prefill['base_unit_cost'] ?? '') }}" step="0.01" min="0" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" onchange="calculateTariff()">
                                @error('base_unit_cost')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="margin_percentage" class="block text-sm font-medium text-gray-700">{{ __('Margin Percentage') }} <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <input type="number" id="margin_percentage" name="margin_percentage" value="{{ old('margin_percentage', ($prefill['margin_percentage'] ?? 0.20) * 100) }}" step="0.01" min="0" max="1000" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" onchange="calculateTariff()">
                                @error('margin_percentage')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Margin dalam persen (contoh: 20 untuk 20%)</p>
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="jasa_sarana" class="block text-sm font-medium text-gray-700">{{ __('Jasa Sarana') }}</label>
                            <div class="mt-1">
                                <input type="number" id="jasa_sarana" name="jasa_sarana" value="{{ old('jasa_sarana', $prefill['jasa_sarana'] ?? 0) }}" step="0.01" min="0" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" onchange="calculateTariff()">
                                @error('jasa_sarana')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="jasa_pelayanan" class="block text-sm font-medium text-gray-700">{{ __('Jasa Pelayanan') }}</label>
                            <div class="mt-1">
                                <input type="number" id="jasa_pelayanan" name="jasa_pelayanan" value="{{ old('jasa_pelayanan', $prefill['jasa_pelayanan'] ?? 0) }}" step="0.01" min="0" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" onchange="calculateTariff()">
                                @error('jasa_pelayanan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="final_tariff_price" class="block text-sm font-medium text-gray-700">{{ __('Final Tariff Price') }} <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <input type="number" id="final_tariff_price" name="final_tariff_price" value="{{ old('final_tariff_price', $prefill['final_tariff_price'] ?? '') }}" step="0.01" min="0" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                @error('final_tariff_price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Akan terhitung otomatis atau bisa diubah manual</p>
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="effective_date" class="block text-sm font-medium text-gray-700">{{ __('Effective Date') }} <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <input type="date" id="effective_date" name="effective_date" value="{{ old('effective_date', date('Y-m-d')) }}" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                @error('effective_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="expired_date" class="block text-sm font-medium text-gray-700">{{ __('Expired Date') }}</label>
                            <div class="mt-1">
                                <input type="date" id="expired_date" name="expired_date" value="{{ old('expired_date') }}" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                @error('expired_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Opsional - kosongkan jika tidak ada tanggal berakhir</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Save Final Tariff') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function calculateTariff() {
    const baseUnitCost = parseFloat(document.getElementById('base_unit_cost').value) || 0;
    const marginPercentage = (parseFloat(document.getElementById('margin_percentage').value) || 0) / 100;
    const jasaSarana = parseFloat(document.getElementById('jasa_sarana').value) || 0;
    const jasaPelayanan = parseFloat(document.getElementById('jasa_pelayanan').value) || 0;
    
    const marginAmount = baseUnitCost * marginPercentage;
    const finalPrice = baseUnitCost + marginAmount + jasaSarana + jasaPelayanan;
    
    document.getElementById('final_tariff_price').value = finalPrice.toFixed(2);
}

// Load unit cost calculations when service is selected
document.getElementById('cost_reference_id').addEventListener('change', function() {
    const serviceId = this.value;
    const unitCostSelect = document.getElementById('unit_cost_calculation_id');
    
    if (!serviceId) {
        unitCostSelect.innerHTML = '<option value="">Select Unit Cost Calculation</option>';
        return;
    }
    
    // Fetch unit cost calculations for this service
    fetch(`/api/unit-costs?cost_reference_id=${serviceId}`)
        .then(response => response.json())
        .then(data => {
            unitCostSelect.innerHTML = '<option value="">Select Unit Cost Calculation</option>';
            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = `${item.version_label} - ${item.period_month}/${item.period_year} (Rp ${parseFloat(item.total_unit_cost).toLocaleString('id-ID', {minimumFractionDigits: 2})})`;
                unitCostSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error:', error);
        });
});

// Load base unit cost when unit cost calculation is selected
document.getElementById('unit_cost_calculation_id').addEventListener('change', function() {
    const unitCostId = this.value;
    
    if (!unitCostId) {
        return;
    }
    
    // Fetch unit cost calculation details
    fetch(`/api/unit-costs/${unitCostId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('base_unit_cost').value = parseFloat(data.total_unit_cost).toFixed(2);
            calculateTariff();
        })
        .catch(error => {
            console.error('Error:', error);
        });
});

// Pre-fill if coming from simulation
@if(isset($prefill['unit_cost_calculation_id']) && $prefill['unit_cost_calculation_id'])
    document.addEventListener('DOMContentLoaded', function() {
        const unitCostId = {{ $prefill['unit_cost_calculation_id'] }};
        fetch(`/api/unit-costs/${unitCostId}`)
            .then(response => response.json())
            .then(data => {
                const option = document.createElement('option');
                option.value = data.id;
                option.textContent = `${data.version_label} - ${data.period_month}/${data.period_year} (Rp ${parseFloat(data.total_unit_cost).toLocaleString('id-ID', {minimumFractionDigits: 2})})`;
                option.selected = true;
                document.getElementById('unit_cost_calculation_id').appendChild(option);
            });
    });
@endif
</script>
@endsection

