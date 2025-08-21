@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>{{ __('Clinical Pathways') }}</h2>
                <a href="{{ route('pathways.create') }}" class="btn btn-primary">{{ __('Create New Pathway') }}</a>
            </div>
            
            <div class="card">
                <div class="card-body">
                    @if($pathways->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Diagnosis Code') }}</th>
                                        <th>{{ __('Version') }}</th>
                                        <th>{{ __('Effective Date') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Created By') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pathways as $pathway)
                                        <tr>
                                            <td>{{ $pathway->name }}</td>
                                            <td>{{ $pathway->diagnosis_code }}</td>
                                            <td>{{ $pathway->version }}</td>
                                            <td>{{ $pathway->effective_date->format('d M Y') }}</td>
                                            <td>
                                                @if($pathway->status == 'active')
                                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                                @elseif($pathway->status == 'draft')
                                                    <span class="badge bg-warning">{{ __('Draft') }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $pathway->creator->name }}</td>
                                            <td>
                                                <a href="{{ route('pathways.show', $pathway) }}" class="btn btn-sm btn-info">{{ __('View') }}</a>
                                                <a href="{{ route('pathways.edit', $pathway) }}" class="btn btn-sm btn-primary">{{ __('Edit') }}</a>
                                                
                                                @if(Auth::user()->hasRole('mutu') || Auth::user()->hasRole('admin'))
                                                    <a href="{{ route('pathways.builder', $pathway) }}" class="btn btn-sm btn-warning">{{ __('Builder') }}</a>
                                                @endif
                                                
                                                <form action="{{ route('pathways.destroy', $pathway) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('Are you sure you want to delete this pathway?') }}')">{{ __('Delete') }}</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        {{ $pathways->links() }}
                    @else
                        <p>{{ __('No clinical pathways found.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
