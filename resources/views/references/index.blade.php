@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">{{ __('Referensi Pengetahuan') }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <form method="GET" action="{{ route('references.index') }}" class="flex flex-wrap items-center gap-2">
                <div>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                           placeholder="{{ __('Cari judul...') }}"
                           class="w-48 rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm">
                </div>
                <div>
                    <select name="status"
                            class="rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm">
                        @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['status'] ?? 'all') === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="tag"
                            class="rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm">
                        <option value="">{{ __('Semua Tag') }}</option>
                        @foreach($tags as $tag)
                            <option value="{{ $tag->id }}" @selected(($filters['tag'] ?? null) == $tag->id)>
                                {{ $tag->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                        class="inline-flex items-center px-3 py-2 rounded-md border border-transparent text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700">
                    {{ __('Filter') }}
                </button>
                @if($filters['search'] || ($filters['status'] ?? 'all') !== 'all' || ($filters['tag'] ?? null))
                    <a href="{{ route('references.index') }}"
                       class="inline-flex items-center px-3 py-2 rounded-md border border-gray-300 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700">
                        {{ __('Reset') }}
                    </a>
                @endif
            </form>
            @can('create', App\Models\Reference::class)
                <a href="{{ route('references.create') }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-biru-dongker-800 hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700">
                    {{ __('Tambah Referensi') }}
                </a>
            @endcan
        </div>
    </div>

    <div class="bg-white shadow sm:rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Judul') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Tag') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Aksi') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($references as $reference)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    @if($reference->is_pinned)
                                        <span class="inline-flex items-center mr-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-biru-dongker-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                                            </svg>
                                        </span>
                                    @endif
                                    <a href="{{ route('references.show', $reference) }}" 
                                       class="text-biru-dongker-800 hover:text-biru-dongker-950 hover:underline transition-colors">
                                        {{ $reference->title }}
                                    </a>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @if($reference->tags && $reference->tags->count() > 0)
                                        @foreach($reference->tags as $tag)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" 
                                                  style="background-color: {{ $tag->color }}20; color: {{ $tag->color }};">
                                                {{ $tag->name }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-xs text-gray-400">{{ __('Tidak ada tag') }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('references.export-pdf', $reference) }}"
                                       target="_blank"
                                       rel="noopener"
                                       class="inline-flex items-center p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-md transition"
                                       title="{{ __('PDF') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 3h8l4 4v14H7a2 2 0 01-2-2V5a2 2 0 012-2z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h1.5a1.5 1.5 0 000-3H9v3zm3-3v3m0-1.5h.75A1.25 1.25 0 0014 10.25v0A1.25 1.25 0 0012.75 9H12m3 0v4h1.5" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('references.show', $reference) }}"
                                       class="inline-flex items-center p-2 text-biru-dongker-800 hover:text-biru-dongker-950 hover:bg-biru-dongker-200 rounded-md transition"
                                       title="{{ __('Lihat') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    @can('update', $reference)
                                        <a href="{{ route('references.edit', $reference) }}"
                                           class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md transition"
                                           title="{{ __('Edit') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                    @endcan
                                    @can('delete', $reference)
                                        <form action="{{ route('references.destroy', $reference) }}" method="POST"
                                              onsubmit="return confirm('{{ __('Hapus referensi ini?') }}')"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-md transition"
                                                    title="{{ __('Hapus') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
                                {{ __('Belum ada referensi yang tersimpan.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
                            </div>
        
        @if($references->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                    {{ $references->links() }}
                </div>
            @endif
    </div>
</div>
@endsection
