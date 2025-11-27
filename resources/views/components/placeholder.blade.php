@extends('layouts.app')

@section('content')
<div class="mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-center mb-6">
            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-4 text-center">{{ $title ?? 'Fitur Sedang Dikembangkan' }}</h2>
        <p class="text-gray-600 mb-6 text-center">{{ $message ?? 'Fitur ini sedang dalam tahap pengembangan dan akan segera tersedia.' }}</p>
        <div class="flex justify-center">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>
@endsection

