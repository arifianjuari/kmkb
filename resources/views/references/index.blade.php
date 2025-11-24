@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">{{ __('Referensi Pengetahuan') }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <form method="GET" action="{{ route('references.index') }}" class="flex flex-wrap items-center gap-2">
                <div>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                           placeholder="{{ __('Cari judul atau konten...') }}"
                           class="w-48 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <div>
                    <select name="status"
                            class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['status'] ?? 'all') === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                        class="inline-flex items-center px-3 py-2 rounded-md border border-transparent text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    {{ __('Filter') }}
                </button>
                @if($filters['search'] || ($filters['status'] ?? 'all') !== 'all')
                    <a href="{{ route('references.index') }}"
                       class="inline-flex items-center px-3 py-2 rounded-md border border-gray-300 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        {{ __('Reset') }}
                    </a>
                @endif
            </form>
            @can('create', App\Models\Reference::class)
                <a href="{{ route('references.create') }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    {{ __('Tambah Referensi') }}
                </a>
            @endcan
        </div>
    </div>

    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-6 py-6">
            @if($references->count())
                <div class="space-y-4">
                    @foreach($references as $reference)
                        <article class="border border-gray-200 rounded-lg p-5 hover:border-indigo-200 transition">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <a href="{{ route('references.show', $reference) }}" class="text-lg font-semibold text-gray-900 hover:text-indigo-600">
                                        {{ $reference->title }}
                                    </a>
                                    <div class="mt-1 text-sm text-gray-500">
                                        {{ __('Ditulis oleh') }} {{ $reference->author->name ?? '—' }} ·
                                        {{ optional($reference->published_at)->translatedFormat('d M Y H:i') ?? __('Belum dipublikasikan') }}
                                    </div>
                                    <div class="mt-2 flex items-center gap-2 flex-wrap text-xs font-medium">
                                        <span class="px-2 py-1 rounded-full {{ $reference->status === \App\Models\Reference::STATUS_PUBLISHED ? 'bg-green-100 text-green-800' : ($reference->status === \App\Models\Reference::STATUS_ARCHIVED ? 'bg-gray-100 text-gray-700' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ ucfirst($reference->status) }}
                                        </span>
                                        @if($reference->is_pinned)
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-indigo-100 text-indigo-800 rounded-full">
                                                {{ __('Disematkan') }}
                                            </span>
                                        @endif
                                        <span class="inline-flex items-center gap-1 text-gray-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-3.333 0-6.222 2-8 4 1.778 2 4.667 4 8 4s6.222-2 8-4c-1.778-2-4.667-4-8-4Zm0 0v.01M12 12v.01" />
                                            </svg>
                                            {{ $reference->view_count }} {{ __('kali dibaca') }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    @can('update', $reference)
                                        <a href="{{ route('references.edit', $reference) }}"
                                           class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                            {{ __('Edit') }}
                                        </a>
                                    @endcan
                                    @can('delete', $reference)
                                        <form action="{{ route('references.destroy', $reference) }}" method="POST"
                                              onsubmit="return confirm('{{ __('Hapus referensi ini?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center px-3 py-2 border border-red-200 rounded-md text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500">
                                                {{ __('Hapus') }}
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
                <div class="mt-6">
                    {{ $references->links() }}
                </div>
            @else
                <p class="text-gray-600 text-sm">{{ __('Belum ada referensi yang tersimpan.') }}</p>
            @endif
        </div>
    </div>
</div>
@endsection

