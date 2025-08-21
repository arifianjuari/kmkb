@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>{{ __('Pathway Builder') }}: {{ $pathway->name }}</h2>
                <a href="{{ route('pathways.show', $pathway) }}" class="btn btn-secondary">{{ __('Back to Pathway') }}</a>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5>{{ __('Add New Step') }}</h5>
                </div>
                <div class="card-body">
                    <form id="step-form">
                        @csrf
                        <div class="row">
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="day" class="form-label">{{ __('Day') }}</label>
                                    <input type="number" class="form-control" id="day" name="day" min="1" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="activity" class="form-label">{{ __('Activity') }}</label>
                                    <input type="text" class="form-control" id="activity" name="activity" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="description" class="form-label">{{ __('Description') }}</label>
                                    <input type="text" class="form-control" id="description" name="description" required>
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="standard_cost" class="form-label">{{ __('Standard Cost') }}</label>
                                    <input type="number" class="form-control" id="standard_cost" name="standard_cost" step="0.01" min="0" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="cost_reference_id" class="form-label">{{ __('Cost Reference') }}</label>
                            <select class="form-control" id="cost_reference_id" name="cost_reference_id">
                                <option value="">{{ __('Select Cost Reference') }}</option>
                                @foreach($costReferences as $reference)
                                    <option value="{{ $reference->id }}">{{ $reference->item_name }} (Rp{{ number_format($reference->standard_cost, 2) }})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">{{ __('Add Step') }}</button>
                    </form>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Pathway Steps') }}</h5>
                </div>
                <div class="card-body">
                    @if($pathway->steps->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped" id="steps-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('Day') }}</th>
                                        <th>{{ __('Activity') }}</th>
                                        <th>{{ __('Description') }}</th>
                                        <th>{{ __('Standard Cost') }}</th>
                                        <th>{{ __('Cost Reference') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pathway->steps->sortBy('day') as $step)
                                        <tr data-step-id="{{ $step->id }}">
                                            <td contenteditable="true" data-field="day">{{ $step->day }}</td>
                                            <td contenteditable="true" data-field="activity">{{ $step->activity }}</td>
                                            <td contenteditable="true" data-field="description">{{ $step->description }}</td>
                                            <td contenteditable="true" data-field="standard_cost">{{ number_format($step->standard_cost, 2) }}</td>
                                            <td>
                                                <select class="cost-reference-select" data-step-id="{{ $step->id }}">
                                                    <option value="">{{ __('None') }}</option>
                                                    @foreach($costReferences as $reference)
                                                        <option value="{{ $reference->id }}" {{ $step->cost_reference_id == $reference->id ? 'selected' : '' }}>
                                                            {{ $reference->item_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-success save-step" data-step-id="{{ $step->id }}">{{ __('Save') }}</button>
                                                <form action="{{ route('pathways.steps.destroy', [$pathway, $step]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('Are you sure you want to delete this step?') }}')">{{ __('Delete') }}</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p>{{ __('No steps defined for this pathway yet.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
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
        
        // Handle saving edited steps
        document.querySelectorAll('.save-step').forEach(button => {
            button.addEventListener('click', function() {
                const stepId = this.getAttribute('data-step-id');
                const row = document.querySelector(`tr[data-step-id="${stepId}"]`);
                
                const data = {
                    day: row.querySelector('[data-field="day"]').textContent,
                    activity: row.querySelector('[data-field="activity"]').textContent,
                    description: row.querySelector('[data-field="description"]').textContent,
                    standard_cost: row.querySelector('[data-field="standard_cost"]').textContent
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
            select.addEventListener('change', function() {
                const stepId = this.getAttribute('data-step-id');
                const selectedOption = this.options[this.selectedIndex];
                
                if (selectedOption.value) {
                    // Update the standard cost field with the reference cost
                    const costCell = document.querySelector(`tr[data-step-id="${stepId}"] [data-field="standard_cost"]`);
                    const costText = selectedOption.text.match(/Rp([\d,]+\.\d{2})/);
                    if (costText) {
                        costCell.textContent = costText[1];
                    }
                }
            });
        });
    });
</script>
@endsection
