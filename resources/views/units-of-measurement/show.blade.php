@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">{{ __('Unit of Measurement Details') }}</h2>
        <div class="flex items-center space-x-2">
            @if(!auth()->user()?->isObserver())
            <a href="{{ route('units-of-measurement.edit', $unit) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900">
                {{ __('Edit') }}
            </a>
            @endif
            <a href="{{ route('units-of-measurement.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                {{ __('Back to List') }}
            </a>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Code') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $unit->code }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Name') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $unit->name }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Symbol') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $unit->symbol ?? '-' }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Category') }}</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            {{ $unit->category_label }}
                        </span>
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Context') }}</dt>
                    <dd class="mt-1">
                        @php
                            $contextColors = [
                                'allocation' => 'bg-blue-100 text-blue-800',
                                'service' => 'bg-green-100 text-green-800',
                                'both' => 'bg-purple-100 text-purple-800',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $contextColors[$unit->context] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $unit->context_label }}
                        </span>
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Status') }}</dt>
                    <dd class="mt-1">
                        @if($unit->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ __('Active') }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ __('Inactive') }}
                            </span>
                        @endif
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Created At') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $unit->created_at->format('d M Y H:i') }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Updated At') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $unit->updated_at->format('d M Y H:i') }}</dd>
                </div>
            </dl>
        </div>
    </div>

    {{-- Usage Statistics --}}
    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Allocation Drivers --}}
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Used in Allocation Drivers') }}</h3>
                @if($unit->allocationDrivers->count() > 0)
                    <ul class="divide-y divide-gray-200">
                        @foreach($unit->allocationDrivers->take(10) as $driver)
                            <li class="py-2">
                                <a href="{{ route('allocation-drivers.show', $driver) }}" class="text-sm text-biru-dongker-800 hover:underline">
                                    {{ $driver->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    @if($unit->allocationDrivers->count() > 10)
                        <p class="mt-2 text-xs text-gray-500">... dan {{ $unit->allocationDrivers->count() - 10 }} lainnya</p>
                    @endif
                @else
                    <p class="text-sm text-gray-500">{{ __('No allocation drivers using this unit.') }}</p>
                @endif
            </div>
        </div>

        {{-- Cost References --}}
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Used in Cost References') }}</h3>
                @if($unit->costReferences->count() > 0)
                    <ul class="divide-y divide-gray-200">
                        @foreach($unit->costReferences->take(10) as $ref)
                            <li class="py-2">
                                <a href="{{ route('cost-references.show', $ref) }}" class="text-sm text-biru-dongker-800 hover:underline">
                                    {{ $ref->service_description }}
                                </a>
                                <span class="text-xs text-gray-500">({{ $ref->service_code }})</span>
                            </li>
                        @endforeach
                    </ul>
                    @if($unit->costReferences->count() > 10)
                        <p class="mt-2 text-xs text-gray-500">... dan {{ $unit->costReferences->count() - 10 }} lainnya</p>
                    @endif
                @else
                    <p class="text-sm text-gray-500">{{ __('No cost references using this unit.') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
