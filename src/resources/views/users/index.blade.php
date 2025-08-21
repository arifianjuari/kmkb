@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>{{ __('Users') }}</h2>
            <a href="{{ route('users.create') }}" class="btn btn-primary">{{ __('Create New User') }}</a>
        </div>
        
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('users.index') }}">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="name" class="form-label">{{ __('Name') }}</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ request('name') }}">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="email" class="form-label">{{ __('Email') }}</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ request('email') }}">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="role" class="form-label">{{ __('Role') }}</label>
                                <select class="form-control" id="role" name="role">
                                    <option value="">{{ __('All Roles') }}</option>
                                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                                    <option value="mutu" {{ request('role') == 'mutu' ? 'selected' : '' }}>{{ __('Mutu') }}</option>
                                    <option value="klaim" {{ request('role') == 'klaim' ? 'selected' : '' }}>{{ __('Klaim') }}</option>
                                    <option value="manajemen" {{ request('role') == 'manajemen' ? 'selected' : '' }}>{{ __('Manajemen') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary me-2">{{ __('Filter') }}</button>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">{{ __('Clear') }}</a>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                @if($users->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Role') }}</th>
                                    <th>{{ __('Department') }}</th>
                                    <th>{{ __('Created At') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @if($user->hasRole('admin'))
                                                <span class="badge bg-danger">{{ __('Admin') }}</span>
                                            @elseif($user->hasRole('mutu'))
                                                <span class="badge bg-primary">{{ __('Mutu') }}</span>
                                            @elseif($user->hasRole('klaim'))
                                                <span class="badge bg-success">{{ __('Klaim') }}</span>
                                            @elseif($user->hasRole('manajemen'))
                                                <span class="badge bg-warning">{{ __('Manajemen') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $user->department }}</td>
                                        <td>{{ $user->created_at->format('d M Y') }}</td>
                                        <td>
                                            <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-info">{{ __('View') }}</a>
                                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-primary">{{ __('Edit') }}</a>
                                            
                                            @if($user->id != Auth::id())
                                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('Are you sure you want to delete this user?') }}')">{{ __('Delete') }}</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    {{ $users->links() }}
                @else
                    <p>{{ __('No users found.') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
</div>
@endsection
