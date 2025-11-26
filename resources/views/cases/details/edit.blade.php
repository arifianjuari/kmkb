@extends('layouts.app')

@section('title', 'Edit Case Detail')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-xl font-bold mb-4">Edit Case Detail for {{ $case->patient_name }}</h2>
    
    <form action="{{ route('cases.details.update', [$case, $detail]) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-4">
            <label class="inline-flex items-center">
                <input type="checkbox" id="custom_step_toggle" name="is_custom_step" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" value="1" {{ $detail->isCustomStep() ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-600">Edit as custom (non-standard) pathway step</span>
            </label>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div id="pathway_step_field">
                <label for="pathway_step_id" class="block text-sm font-medium text-gray-700">Pathway Step</label>
                <select name="pathway_step_id" id="pathway_step_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">Select a step</option>
                    @foreach($steps as $step)
                        <option value="{{ $step->id }}" 
                                data-description="{{ $step->description }}" 
                                data-quantity="{{ $step->quantity ?? 1 }}" 
                                data-estimated-cost="{{ $step->estimated_cost ?? 0 }}" 
                                data-cost-reference-id="{{ $step->cost_reference_id ?? '' }}"
                                data-service-code="{{ $step->costReference ? $step->costReference->service_code : '' }}"
                                {{ (old('pathway_step_id') ?? $detail->pathway_step_id) == $step->id ? 'selected' : '' }}>
                            Day {{ $step->step_order }}. {{ $step->description }}
                        </option>
                    @endforeach
                </select>
                @error('pathway_step_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="service_item" class="block text-sm font-medium text-gray-700">Service Item</label>
                <input type="text" name="service_item" id="service_item" value="{{ old('service_item') ?? $detail->service_item }}" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                @error('service_item')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="service_code" class="block text-sm font-medium text-gray-700">Service Code</label>
                <input type="text" name="service_code" id="service_code" value="{{ old('service_code') ?? $detail->service_code }}" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                @error('service_code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="pending" {{ (old('status') ?? $detail->status) == 'pending' ? 'selected' : '' }}>Belum Dilakukan</option>
                    <option value="completed" {{ (old('status') ?? $detail->status) == 'completed' ? 'selected' : '' }} selected>Sudah Dilakukan</option>
                    <option value="skipped" {{ (old('status') ?? $detail->status) == 'skipped' ? 'selected' : '' }}>Dilewati</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="performed" class="block text-sm font-medium text-gray-700">Performed</label>
                <input type="checkbox" name="performed" id="performed" value="1" {{ (old('performed') ?? $detail->performed) ? 'checked' : '' }} class="mt-1 rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                <span class="ml-2 text-sm text-gray-600">Mark as performed</span>
                @error('performed')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                <input type="number" name="quantity" id="quantity" value="{{ old('quantity') ?? $detail->quantity }}" min="1"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" step="1">
                @error('quantity')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="actual_cost" class="block text-sm font-medium text-gray-700">Actual Cost</label>
                <input type="number" name="actual_cost" id="actual_cost" value="{{ old('actual_cost') ?? $detail->actual_cost }}" min="0"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" step="1000">
                @error('actual_cost')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="service_date" class="block text-sm font-medium text-gray-700">Service Date</label>
                <input type="date" name="service_date" id="service_date" value="{{ old('service_date') ?? ($detail->service_date ? \Carbon\Carbon::parse($detail->service_date)->format('Y-m-d') : date('Y-m-d')) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                @error('service_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="mt-6 flex items-center justify-end space-x-3">
            <a href="{{ route('cases.show', $case) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Cancel
            </a>
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Update Case Detail
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pathwayStepSelect = document.getElementById('pathway_step_id');
        const serviceItemInput = document.getElementById('service_item');
        const serviceCodeInput = document.getElementById('service_code');
        const quantityInput = document.getElementById('quantity');
        const actualCostInput = document.getElementById('actual_cost');
        const customStepToggle = document.getElementById('custom_step_toggle');
        const pathwayStepField = document.getElementById('pathway_step_field');
        
        // Unit cost version from case
        const caseUnitCostVersion = @json($case->unit_cost_version ?? null);
        
        // Function to fetch unit cost from API
        async function fetchUnitCost(serviceId, version = null) {
            if (!serviceId) return null;
            
            try {
                const url = new URL('/api/unit-cost', window.location.origin);
                url.searchParams.append('serviceId', serviceId);
                if (version) {
                    url.searchParams.append('version', version);
                } else if (caseUnitCostVersion) {
                    url.searchParams.append('version', caseUnitCostVersion);
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
                const actualCostInput = document.getElementById('actual_cost');
                if (actualCostInput && actualCostInput.parentElement) {
                    actualCostInput.parentElement.appendChild(warningDiv);
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
        
        // Function to toggle between standard and custom steps
        function toggleCustomStep() {
            if (customStepToggle.checked) {
                // Hide pathway step selection
                pathwayStepField.style.display = 'none';
                // Clear pathway step selection
                pathwayStepSelect.value = '';
                // Make service item editable and required
                serviceItemInput.readOnly = false;
                serviceItemInput.required = true;
            } else {
                // Show pathway step selection
                pathwayStepField.style.display = 'block';
                // Make service item readonly and not required
                serviceItemInput.readOnly = true;
                serviceItemInput.required = false;
            }
        }
        
        // Add event listener to the toggle
        customStepToggle.addEventListener('change', toggleCustomStep);
        
        pathwayStepSelect.addEventListener('change', async function() {
            const selectedOption = this.options[this.selectedIndex];
            
            if (selectedOption.value === '') {
                serviceItemInput.value = '';
                serviceCodeInput.value = '';
                quantityInput.value = '';
                actualCostInput.value = '';
                serviceItemInput.readOnly = !customStepToggle.checked;
                serviceCodeInput.readOnly = !customStepToggle.checked;
                quantityInput.readOnly = !customStepToggle.checked;
                actualCostInput.readOnly = !customStepToggle.checked;
                hideUnitCostWarning();
            } else {
                const description = selectedOption.getAttribute('data-description');
                const quantity = selectedOption.getAttribute('data-quantity');
                const estimatedCost = selectedOption.getAttribute('data-estimated-cost');
                const serviceCode = selectedOption.getAttribute('data-service-code');
                const costReferenceId = selectedOption.getAttribute('data-cost-reference-id');
                
                serviceItemInput.value = description || '';
                serviceCodeInput.value = serviceCode || '';
                quantityInput.value = quantity || 1;
                
                // Fetch unit cost from API if cost reference ID is available
                if (costReferenceId) {
                    hideUnitCostWarning();
                    const unitCostData = await fetchUnitCost(costReferenceId);
                    
                    if (unitCostData) {
                        if (unitCostData.fallback_used) {
                            // Using standard cost as fallback
                            actualCostInput.value = unitCostData.unit_cost || estimatedCost || 0;
                            showUnitCostWarning('Belum ada unit cost resmi – menggunakan Standard Cost dari Cost References');
                        } else {
                            // Using unit cost
                            const qty = parseFloat(quantityInput.value) || 1;
                            actualCostInput.value = Math.round((unitCostData.unit_cost || 0) * qty);
                            hideUnitCostWarning();
                        }
                    } else {
                        // Fallback to estimated cost if API fails
                        actualCostInput.value = estimatedCost || 0;
                        hideUnitCostWarning();
                    }
                } else {
                    // No cost reference, use estimated cost
                actualCostInput.value = estimatedCost || 0;
                    hideUnitCostWarning();
                }
                
                // Make fields editable after selecting a pathway step
                serviceItemInput.readOnly = false;
                serviceCodeInput.readOnly = false;
                quantityInput.readOnly = false;
                actualCostInput.readOnly = false;
            }
        });
        
        // Update actual cost when quantity changes
        if (quantityInput && actualCostInput) {
            let lastUnitCost = null;
            
            quantityInput.addEventListener('change', async function() {
                const selectedOption = pathwayStepSelect.options[pathwayStepSelect.selectedIndex];
                const costReferenceId = selectedOption ? selectedOption.getAttribute('data-cost-reference-id') : null;
                
                if (costReferenceId && lastUnitCost === null) {
                    // Fetch unit cost if not already stored
                    const unitCostData = await fetchUnitCost(costReferenceId);
                    if (unitCostData && !unitCostData.fallback_used) {
                        lastUnitCost = unitCostData.unit_cost;
                    }
                }
                
                if (lastUnitCost !== null) {
                    const qty = parseFloat(this.value) || 1;
                    actualCostInput.value = Math.round(lastUnitCost * qty);
                }
            });
        }
        
        // Initialize on page load
        toggleCustomStep();
        
        // Trigger change event on page load if there's an old value
        if (pathwayStepSelect.value !== '') {
            pathwayStepSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection
