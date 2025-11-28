@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white shadow-sm rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-4">Migrasi File ke Object Storage</h1>
        
        @php
            $awsKey = config('filesystems.disks.uploads.key') ?? config('filesystems.disks.s3.key');
        @endphp
        @if(!$awsKey)
            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-4">
                <p class="text-yellow-800">
                    <strong>⚠️ Object Storage belum dikonfigurasi.</strong><br>
                    Pastikan credentials AWS sudah di-set di Laravel Cloud Dashboard.<br>
                    Setelah setup, jalankan: <code>php artisan config:clear && php artisan config:cache</code>
                </p>
            </div>
        @endif

        <div class="mb-6">
            <p class="text-gray-600 mb-4">
                Script ini akan memigrasikan semua file (logo hospital dan gambar references) dari local storage ke Object Storage (S3/R2 bucket).
            </p>
            <ul class="list-disc list-inside text-gray-600 space-y-2 mb-4">
                <li>File yang sudah di Object Storage akan di-skip</li>
                <li>File yang tidak ditemukan akan di-skip</li>
                <li>Proses ini aman dan tidak akan menghapus file asli</li>
            </ul>
        </div>

        <form id="migrateForm" method="POST" action="{{ route('migrate-storage') }}">
            @csrf
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 bg-biru-dongker-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700 focus:ring-offset-2 transition ease-in-out duration-150">
                Mulai Migrasi
            </button>
        </form>

        @if(session('success'))
            <div class="mt-6 bg-green-50 border border-green-200 rounded-md p-4">
                <p class="text-green-800 font-semibold">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('results'))
            <div class="mt-6 bg-gray-50 rounded-md p-4">
                <h3 class="font-semibold mb-2">Detail Hasil Migrasi:</h3>
                <pre class="text-sm overflow-auto">{{ json_encode(session('results'), JSON_PRETTY_PRINT) }}</pre>
            </div>
        @endif
    </div>
</div>

@endsection

