<tr>
    <td class="px-4 py-2 whitespace-nowrap text-sm">
        <div class="flex space-x-2">
            <a href="{{ route('cases.details.edit', [$case, $detail]) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" title="{{ __('Edit') }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            </a>

            <form id="delete-form-{{ $detail->id }}" action="{{ route('cases.details.delete', [$case, $detail]) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="button" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" onclick="confirmDelete({{ $detail->id }}, '{{ __('Are you sure you want to delete this case detail?') }}')" title="{{ __('Delete') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </form>
        </div>
    </td>
    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
        @if($detail->isCustomStep())
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                Custom
            </span>
        @else
            {{ $detail->pathwayStep->step_order }}
        @endif
    </td>
    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
        @if($detail->isCustomStep())
            <strong>{{ $detail->service_item }}</strong>
        @else
            {{ $detail->pathwayStep->description }}
        @endif
    </td>
    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300" data-field="quantity" data-id="{{ $detail->id }}" contenteditable="true">{{ $detail->quantity }}</td>
    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
        @if($detail->isCustomStep())
            <span class="text-gray-500 dark:text-gray-400">{{ __('N/A') }}</span>
        @else
            {{ $detail->pathwayStep->quantity ?? 0 }}
        @endif
    </td>
    <td class="px-4 py-2 whitespace-nowrap text-sm">
        @if($detail->actual_cost !== null)
            @php
                $standardCost = $detail->pathwayStep->costReference->standard_cost ?? 0;
                $standardCostTotal = $standardCost * $detail->quantity;
                $actualCostTotal = $detail->actual_cost * $detail->quantity;
                $variance = $standardCostTotal - $actualCostTotal;
            @endphp
            <span class="{{ $variance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} font-semibold">
                Rp{{ number_format($variance, 0, ',', '.') }}
            </span>
        @else
            <span class="text-gray-500 dark:text-gray-400">{{ __('N/A') }}</span>
        @endif
    </td>
    <td class="px-4 py-2 whitespace-nowrap text-sm" data-field="performed" data-id="{{ $detail->id }}">
        <input type="checkbox" class="inline-edit-checkbox" {{ $detail->performed ? 'checked' : '' }} data-original-value="{{ $detail->performed ? '1' : '0' }}">
    </td>
    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300" data-field="actual_cost" data-id="{{ $detail->id }}" contenteditable="true">{{ $detail->actual_cost !== null ? 'Rp' . number_format($detail->actual_cost, 0, ',', '.') : '' }}</td>
    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">Rp{{ number_format($detail->actual_cost * $detail->quantity, 0, ',', '.') }}</td>
    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
        Rp{{ number_format($detail->pathwayStep->costReference->standard_cost ?? 0, 0, ',', '.') }}
    </td>
    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
        @if($detail->isCustomStep())
            <span class="text-gray-500 dark:text-gray-400">{{ __('N/A') }}</span>
        @else
            @php
                $pathwayStandardCost = $detail->pathwayStep->costReference->standard_cost ?? 0;
                $pathwayQuantity = $detail->pathwayStep->quantity ?? 0;
                $pathwayStandardCostTotal = $pathwayStandardCost * $pathwayQuantity;
            @endphp
            Rp{{ number_format($pathwayStandardCostTotal, 0, ',', '.') }}
        @endif
    </td>
    <td class="px-4 py-2 whitespace-nowrap text-sm" data-field="status" data-id="{{ $detail->id }}">
        <select class="inline-edit-select" data-original-value="{{ $detail->status }}">
            <option value="pending" {{ $detail->status === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
            <option value="completed" {{ $detail->status === 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
            <option value="skipped" {{ $detail->status === 'skipped' ? 'selected' : '' }}>{{ __('Skipped') }}</option>
        </select>
    </td>
</tr>

