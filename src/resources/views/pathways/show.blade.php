@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>{{ __('Clinical Pathway Details') }}</h2>
                <div>
                    <a href="{{ route('pathways.index') }}" class="btn btn-secondary">{{ __('Back to List') }}</a>
                    <a href="{{ route('pathways.edit', $pathway) }}" class="btn btn-primary">{{ __('Edit') }}</a>
                    
                    @if(Auth::user()->hasRole('mutu') || Auth::user()->hasRole('admin'))
                        <a href="{{ route('pathways.builder', $pathway) }}" class="btn btn-warning">{{ __('Builder') }}</a>
                    @endif
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5>{{ __('Pathway Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <td>{{ $pathway->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Diagnosis Code') }}</th>
                                    <td>{{ $pathway->diagnosis_code }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Version') }}</th>
                                    <td>{{ $pathway->version }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th>{{ __('Effective Date') }}</th>
                                    <td>{{ $pathway->effective_date->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Status') }}</th>
                                    <td>
                                        @if($pathway->status == 'active')
                                            <span class="badge bg-success">{{ __('Active') }}</span>
                                        @elseif($pathway->status == 'draft')
                                            <span class="badge bg-warning">{{ __('Draft') }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Created By') }}</th>
                                    <td>{{ $pathway->creator->name }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><strong>{{ __('Description') }}</strong></label>
                        <p>{{ $pathway->description }}</p>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Pathway Steps') }}</h5>
                </div>
                <div class="card-body">
                    @if($pathway->steps->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('Day') }}</th>
                                        <th>{{ __('Activity') }}</th>
                                        <th>{{ __('Description') }}</th>
                                        <th>{{ __('Standard Cost') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pathway->steps as $step)
                                        <tr>
                                            <td>{{ $step->day }}</td>
                                            <td>{{ $step->activity }}</td>
                                            <td>{{ $step->description }}</td>
                                            <td>Rp{{ number_format($step->standard_cost, 2) }}</td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-primary">{{ __('Edit') }}</a>
                                                <form action="#" method="POST" class="d-inline">
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
                    
                    @if(Auth::user()->hasRole('mutu') || Auth::user()->hasRole('admin'))
                        <a href="#" class="btn btn-success">{{ __('Add Step') }}</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
