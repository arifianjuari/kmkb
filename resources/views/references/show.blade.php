@extends('layouts.app')

@section('content')
@php
use Illuminate\Support\Facades\Storage;
@endphp
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8 space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
                <h1 class="text-3xl font-semibold text-gray-900">{{ $reference->title }}</h1>
                <p class="mt-2 text-sm text-gray-500">
                    {{ __('Ditulis oleh') }} {{ $reference->author->name ?? '—' }}
                    · {{ optional($reference->published_at)->translatedFormat('d M Y H:i') ?? __('Belum dipublikasikan') }}
                </p>
            </div>
            <a href="{{ route('references.index') }}"
               class="ml-4 inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Kembali') }}
            </a>
        </div>

        <div class="mt-4 flex flex-wrap items-center gap-2 text-xs font-medium">
            <span class="px-2 py-1 rounded-full {{ $reference->status === \App\Models\Reference::STATUS_PUBLISHED ? 'bg-green-100 text-green-800' : ($reference->status === \App\Models\Reference::STATUS_ARCHIVED ? 'bg-gray-100 text-gray-700' : 'bg-yellow-100 text-yellow-800') }}">
                {{ ucfirst($reference->status) }}
            </span>
            @if($reference->is_pinned)
                <span class="px-2 py-1 rounded-full bg-indigo-100 text-indigo-800">
                    {{ __('Disematkan') }}
                </span>
            @endif
            <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                {{ $reference->view_count }} {{ __('kali dibaca') }}
            </span>
            @if($reference->tags && $reference->tags->count() > 0)
                @foreach($reference->tags as $tag)
                    <span class="px-2 py-1 rounded-full text-xs font-medium" 
                          style="background-color: {{ $tag->color }}20; color: {{ $tag->color }};">
                        {{ $tag->name }}
                    </span>
                @endforeach
            @endif
        </div>
    </div>

    <!-- Image Section -->
    @if($reference->image_path)
        @php
            $imagePath = $reference->image_path;
            $imageUrl = storage_url($imagePath);
            $disk = uploads_disk();
            $existsInDisk = Storage::disk($disk)->exists($imagePath);
            $existsInPublic = Storage::disk('public')->exists($imagePath);
        @endphp
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            @if($existsInDisk || $existsInPublic)
                <img src="{{ $imageUrl }}" 
                     alt="{{ $reference->title }}"
                     class="w-full h-auto object-cover max-h-[600px] object-center">
            @else
                <div class="p-8 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="mt-2 text-sm">Gambar tidak ditemukan</p>
                    <p class="text-xs text-gray-400 mt-1">Path: {{ $imagePath }}</p>
                    @if(config('app.debug'))
                        <p class="text-xs text-gray-400 mt-1">Disk: {{ $disk }}</p>
                        <p class="text-xs text-gray-400">URL: {{ $imageUrl }}</p>
                    @endif
                </div>
            @endif
        </div>
    @endif

    <!-- Content Section -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
        <div class="markdown-content">
            @php
                // Convert single line breaks to <br> tags for better readability
                $content = $reference->content ?? '';
                // Replace single newlines (not followed by another newline) with <br>
                $content = preg_replace('/(?<!\n)\n(?!\n)/', "  \n", $content);
                echo \Illuminate\Support\Str::markdown(
                    $content,
                    [
                        'html_input' => 'allow',
                        'allow_unsafe_links' => false,
                    ]
                );
            @endphp
        </div>
    </div>
</div>

@push('styles')
<style>
.markdown-content {
    line-height: 1.75;
    color: #374151;
}

.markdown-content h1 {
    font-size: 2.25em;
    font-weight: 800;
    margin-top: 0;
    margin-bottom: 0.8888889em;
    line-height: 1.1111111;
}

.markdown-content h2 {
    font-size: 1.5em;
    font-weight: 700;
    margin-top: 2em;
    margin-bottom: 1em;
    line-height: 1.3333333;
}

.markdown-content h3 {
    font-size: 1.25em;
    font-weight: 600;
    margin-top: 1.6em;
    margin-bottom: 0.6em;
    line-height: 1.6;
}

.markdown-content h4 {
    font-weight: 600;
    margin-top: 1.5em;
    margin-bottom: 0.5em;
    line-height: 1.5;
}

.markdown-content p {
    margin-top: 0.75em;
    margin-bottom: 0.75em;
}

.markdown-content p:first-child {
    margin-top: 0;
}

.markdown-content p:last-child {
    margin-bottom: 0;
}

.markdown-content ul,
.markdown-content ol {
    margin-top: 1.25em;
    margin-bottom: 1.25em;
    padding-left: 1.625em;
}

.markdown-content ul {
    list-style-type: disc;
}

.markdown-content ol {
    list-style-type: decimal;
}

.markdown-content li {
    margin-top: 0.5em;
    margin-bottom: 0.5em;
}

.markdown-content li > p {
    margin-top: 0.75em;
    margin-bottom: 0.75em;
}

.markdown-content strong {
    font-weight: 600;
    color: #111827;
}

.markdown-content a {
    color: #2563eb;
    text-decoration: underline;
    font-weight: 500;
}

.markdown-content a:hover {
    color: #1d4ed8;
}

.markdown-content code {
    font-size: 0.875em;
    font-weight: 600;
    color: #111827;
    background-color: #f3f4f6;
    padding: 0.125em 0.25em;
    border-radius: 0.25rem;
}

.markdown-content pre {
    background-color: #1f2937;
    color: #f9fafb;
    overflow-x: auto;
    font-size: 0.875em;
    line-height: 1.7142857;
    margin-top: 1.7142857em;
    margin-bottom: 1.7142857em;
    border-radius: 0.375rem;
    padding: 0.8571429em 1.1428571em;
}

.markdown-content pre code {
    background-color: transparent;
    border-width: 0;
    border-radius: 0;
    padding: 0;
    font-weight: 400;
    color: inherit;
    font-size: inherit;
    font-family: inherit;
    line-height: inherit;
}

.markdown-content blockquote {
    font-weight: 500;
    font-style: italic;
    color: #111827;
    border-left-width: 0.25rem;
    border-left-color: #e5e7eb;
    quotes: "\201C""\201D""\2018""\2019";
    margin-top: 1.6em;
    margin-bottom: 1.6em;
    padding-left: 1em;
}

.markdown-content hr {
    border-color: #e5e7eb;
    border-top-width: 1px;
    margin-top: 3em;
    margin-bottom: 3em;
}

.markdown-content table {
    width: 100%;
    table-layout: auto;
    text-align: left;
    margin-top: 2em;
    margin-bottom: 2em;
    font-size: 0.875em;
    line-height: 1.7142857;
}

.markdown-content thead {
    border-bottom-width: 1px;
    border-bottom-color: #d1d5db;
}

.markdown-content thead th {
    color: #111827;
    font-weight: 600;
    vertical-align: bottom;
    padding-right: 0.5714286em;
    padding-bottom: 0.5714286em;
    padding-left: 0.5714286em;
}

.markdown-content tbody tr {
    border-bottom-width: 1px;
    border-bottom-color: #e5e7eb;
}

.markdown-content tbody td {
    vertical-align: baseline;
    padding-top: 0.5714286em;
    padding-right: 0.5714286em;
    padding-bottom: 0.5714286em;
    padding-left: 0.5714286em;
}

.markdown-content img {
    margin-top: 2em;
    margin-bottom: 2em;
    max-width: 100%;
    height: auto;
    border-radius: 0.5rem;
}
</style>
@endpush
@endsection

