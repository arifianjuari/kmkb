@extends('layouts.app')

@section('title', 'Add Case Detail')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-xl font-bold mb-4">Add Case Detail for {{ $case->patient_name }}</h2>
    
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        {{ session('error') }}
                    </h3>
                    @if($errors->any())
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
    
    @if($errors->any() && !session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        Mohon periksa kembali data yang diisi:
                    </h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    <form action="{{ route('cases.details.store', $case) }}" method="POST">
        @csrf
        
        <div class="mb-4">
            <label class="inline-flex items-center">
                <input type="checkbox" id="custom_step_toggle" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" checked>
                <span class="ml-2 text-sm text-gray-600">Add custom (non-standard) pathway step</span>
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
                                {{ old('pathway_step_id') == $step->id ? 'selected' : '' }}>
                            Day {{ $step->step_order }}. {{ $step->description }}
                        </option>
                    @endforeach
                </select>
                @error('pathway_step_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="service_item" class="block text-sm font-medium text-gray-700">Service Item <span class="text-red-500">*</span></label>
                <div class="mt-1">
                    <input type="text" name="service_item" id="service_item" value="{{ old('service_item') }}" 
                        list="serviceItemList" 
                        placeholder="{{ __('Type to search service code or enter manually') }}" 
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <datalist id="serviceItemList">
                        @foreach($costReferences as $reference)
                            <option value="{{ $reference->service_code }}">{{ $reference->service_description }}</option>
                        @endforeach
                    </datalist>
                </div>
                @error('service_item')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="service_code" class="block text-sm font-medium text-gray-700">Service Code</label>
                <input type="text" name="service_code" id="service_code" value="{{ old('service_code') }}" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                @error('service_code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Belum Dilakukan</option>
                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }} selected>Sudah Dilakukan</option>
                    <option value="skipped" {{ old('status') == 'skipped' ? 'selected' : '' }}>Dilewati</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="performed" class="block text-sm font-medium text-gray-700">Performed</label>
                <input type="checkbox" name="performed" id="performed" value="1" {{ old('performed', true) ? 'checked' : '' }} class="mt-1 rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                <span class="ml-2 text-sm text-gray-600">Mark as performed</span>
                @error('performed')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                <input type="number" name="quantity" id="quantity" value="{{ old('quantity', 1) }}" min="1"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" step="1">
                @error('quantity')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="actual_cost" class="block text-sm font-medium text-gray-700">Actual Cost <span class="text-red-500">*</span></label>
                <input type="number" name="actual_cost" id="actual_cost" value="{{ old('actual_cost') }}" min="0" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" step="1000">
                @error('actual_cost')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="service_date" class="block text-sm font-medium text-gray-700">Service Date</label>
                <input type="date" name="service_date" id="service_date" value="{{ old('service_date') ?? date('Y-m-d') }}"
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
                Add Case Detail
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
        
        // Function to toggle between standard and custom steps
        function toggleCustomStep() {
            if (customStepToggle.checked) {
                // Hide pathway step selection
                pathwayStepField.style.display = 'none';
                // Clear pathway step selection
                pathwayStepSelect.value = '';
                // Make service item editable and required (autocomplete will still work)
                serviceItemInput.readOnly = false;
                serviceItemInput.required = true;
                // Clear other fields
                serviceCodeInput.value = '';
                quantityInput.value = '1'; // Set default quantity to 1
                actualCostInput.value = '';
            } else {
                // Show pathway step selection
                pathwayStepField.style.display = 'block';
                // Make service item readonly again
                serviceItemInput.readOnly = true;
                serviceItemInput.required = false;
                // Clear other fields
                serviceCodeInput.value = '';
                quantityInput.value = '1'; // Set default quantity to 1
                actualCostInput.value = '';
            }
        }
        
        // Add event listener to the toggle
        customStepToggle.addEventListener('change', toggleCustomStep);
        
        pathwayStepSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            
            if (selectedOption.value === '') {
                serviceItemInput.value = '';
                serviceCodeInput.value = '';
                quantityInput.value = customStepToggle.checked ? '1' : ''; // Set default to 1 if custom step
                actualCostInput.value = '';
                serviceItemInput.readOnly = !customStepToggle.checked; // Only readonly if not custom
                serviceCodeInput.readOnly = !customStepToggle.checked; // Only readonly if not custom
            } else {
                const description = selectedOption.getAttribute('data-description');
                const quantity = selectedOption.getAttribute('data-quantity');
                const estimatedCost = selectedOption.getAttribute('data-estimated-cost');
                const serviceCode = selectedOption.getAttribute('data-service-code');
                
                serviceItemInput.value = description || '';
                serviceCodeInput.value = serviceCode || '';
                quantityInput.value = quantity || '1';
                actualCostInput.value = estimatedCost || 0;
                
                // Make fields editable after selecting a pathway step
                serviceItemInput.readOnly = false;
                serviceCodeInput.readOnly = false;
            }
        });
        
        // Initialize on page load
        toggleCustomStep();
        
        // Trigger change event on page load if there's an old value
        if (pathwayStepSelect.value !== '') {
            pathwayStepSelect.dispatchEvent(new Event('change'));
        }
        
        // Service Item autocomplete from Cost References using datalist (like pathways builder)
        // Build a lookup map: service_code -> { id, cost, desc }
        const costRefMap = {
@foreach($costReferences as $reference)
            "{{ $reference->service_code }}": { id: {{ $reference->id }}, cost: "{{ number_format($reference->standard_cost, 0, '.', '') }}", desc: @json($reference->service_description) },
@endforeach
        };
        
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
        
        // Auto-fill Service Code and Actual Cost when Service Item is selected from datalist
        async function applyServiceItemSelection() {
            const inputValue = (serviceItemInput.value || '').trim();
            const ref = costRefMap[inputValue]; // Try to find by service_code
            const serviceCodeInput = document.getElementById('service_code');
            const actualCostInput = document.getElementById('actual_cost');
            
            if (ref) {
                // If exact match found in cost references (user selected service_code from datalist)
                // The input currently contains service_code, but service_item field should contain description
                // So we update service_item to description, and fill service_code and actual_cost
                
                // Update Service Item field to show description (for form submission)
                // Use setTimeout to ensure this happens after the datalist value is set
                setTimeout(() => {
                    serviceItemInput.value = ref.desc || inputValue;
                }, 0);
                
                // Auto-fill service code immediately
                if (serviceCodeInput) {
                    serviceCodeInput.value = inputValue; // This is the service_code from datalist
                }
                
                // Fetch unit cost from API
                if (ref.id) {
                    hideUnitCostWarning();
                    const unitCostData = await fetchUnitCost(ref.id);
                    
                    if (unitCostData) {
                        if (unitCostData.fallback_used) {
                            // Using standard cost as fallback
                            if (actualCostInput) {
                                const costValue = unitCostData.unit_cost || parseFloat(ref.cost) || 0;
                                actualCostInput.value = costValue > 0 ? Math.round(costValue) : '';
                            }
                            showUnitCostWarning('Belum ada unit cost resmi – menggunakan Standard Cost dari Cost References');
                        } else {
                            // Using unit cost
                            if (actualCostInput) {
                                actualCostInput.value = unitCostData.unit_cost > 0 ? Math.round(unitCostData.unit_cost) : '';
                            }
                            hideUnitCostWarning();
                        }
                    } else {
                        // Fallback to standard cost if API fails
                        if (actualCostInput) {
                            const costValue = parseFloat(ref.cost) || 0;
                            actualCostInput.value = costValue > 0 ? costValue : '';
                        }
                        hideUnitCostWarning();
                    }
                } else {
                    // No cost reference ID, use standard cost
                if (actualCostInput) {
                    const costValue = parseFloat(ref.cost) || 0;
                    actualCostInput.value = costValue > 0 ? costValue : '';
                    }
                    hideUnitCostWarning();
                }
            } else {
                // If not found in map, allow free text input
                // User can type service description manually
                // Service code and actual cost can be filled manually or left empty
                // Don't clear fields if user is typing custom text
                if (inputValue === '') {
                    if (serviceCodeInput) serviceCodeInput.value = '';
                    if (actualCostInput) actualCostInput.value = '';
                    hideUnitCostWarning();
                }
            }
        }
        
        // Update pathway step change handler to fetch unit cost
        const originalPathwayStepHandler = pathwayStepSelect.onchange;
        pathwayStepSelect.addEventListener('change', async function() {
            const selectedOption = this.options[this.selectedIndex];
            
            if (selectedOption.value === '') {
                serviceItemInput.value = '';
                serviceCodeInput.value = '';
                quantityInput.value = customStepToggle.checked ? '1' : '';
                actualCostInput.value = '';
                serviceItemInput.readOnly = !customStepToggle.checked;
                serviceCodeInput.readOnly = !customStepToggle.checked;
                hideUnitCostWarning();
            } else {
                const description = selectedOption.getAttribute('data-description');
                const quantity = selectedOption.getAttribute('data-quantity');
                const estimatedCost = selectedOption.getAttribute('data-estimated-cost');
                const serviceCode = selectedOption.getAttribute('data-service-code');
                const costReferenceId = selectedOption.getAttribute('data-cost-reference-id');
                
                serviceItemInput.value = description || '';
                serviceCodeInput.value = serviceCode || '';
                quantityInput.value = quantity || '1';
                
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
            }
        });
        
        // Listen for input and change events (like pathways builder)
        if (serviceItemInput) {
            serviceItemInput.addEventListener('change', () => { 
                applyServiceItemSelection(); 
            });
            serviceItemInput.addEventListener('input', function() {
                // Apply quickly but not on every keystroke heavy logic
                applyServiceItemSelection();
            });
        }
        
        // Update actual cost when quantity changes (multiply by unit cost)
        if (quantityInput && actualCostInput) {
            let lastUnitCost = null;
            
            // Store unit cost when it's fetched
            const storeUnitCost = (cost) => {
                lastUnitCost = cost;
            };
            
            // Update applyServiceItemSelection to store unit cost
            const originalApply = applyServiceItemSelection;
            applyServiceItemSelection = async function() {
                await originalApply();
                // Try to extract unit cost from actual cost (divide by quantity)
                const qty = parseFloat(quantityInput.value) || 1;
                const actualCost = parseFloat(actualCostInput.value) || 0;
                if (actualCost > 0 && qty > 0) {
                    storeUnitCost(actualCost / qty);
                }
            };
            
            quantityInput.addEventListener('change', function() {
                if (lastUnitCost !== null) {
                    const qty = parseFloat(this.value) || 1;
                    actualCostInput.value = Math.round(lastUnitCost * qty);
                }
            });
        }
    });
</script>
@endsection
