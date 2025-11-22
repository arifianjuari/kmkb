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
                    <select id="bump" name="bump" class="py-2 px-2 min-w-[140px] border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 bg-white text-gray-900">
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
        <div class="px-6 py-4 border-b border-gray-200">
            <h5 class="text-base font-semibold text-gray-900">{{ __('Pathway Information') }}</h5>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <table class="min-w-full">
                        <tr>
                            <th class="py-2 pr-4 text-left text-sm font-medium text-gray-500">{{ __('Name') }}</th>
                            <td class="py-2 text-sm text-gray-900">{{ $pathway->name }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 pr-4 text-left text-sm font-medium text-gray-500">{{ __('Diagnosis Code') }}</th>
                            <td class="py-2 text-sm text-gray-900">{{ $pathway->diagnosis_code }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 pr-4 text-left text-sm font-medium text-gray-500">{{ __('Version') }}</th>
                            <td class="py-2 text-sm text-gray-900">{{ $pathway->version }}</td>
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

            <div class="mt-4">
                <label class="block text-sm font-semibold text-gray-900">{{ __('Description') }}</label>
                <p class="mt-1 text-gray-700">{{ $pathway->description }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h5 class="text-base font-semibold text-gray-900">{{ __('Pathway Steps') }}</h5>
        </div>
        <div class="p-6">
            @if($pathway->steps->count() > 0)
                <div class="overflow-x-auto">
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
                            @foreach($pathway->steps->sortBy('step_order') as $step)
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
                        </tbody>
                        <tfoot class="bg-gray-50 font-semibold">
                            <tr>
                                <td colspan="5" class="px-6 py-3 text-right text-sm text-gray-900">{{ __('Total Standard Cost') }}:</td>
                                <td class="px-6 py-3 text-sm text-gray-900">
                                    @php
                                        $totalCost = $pathway->steps->sum(function($step) {
                                            return ($step->estimated_cost ?? 0) * $step->quantity;
                                        });
                                    @endphp
                                    Rp{{ number_format($totalCost, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
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
@endsection
