@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">{{ __('Ubah Referensi') }}</h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Perbarui konten agar informasi tetap relevan.') }}
                </p>
            </div>
            <a href="{{ route('references.index') }}"
               class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Kembali') }}
            </a>
        </div>
        <div class="px-6 py-6">
            <form action="{{ route('references.update', $reference) }}" method="POST">
                @csrf
                @method('PUT')
                @include('references.partials.form', ['reference' => $reference, 'statusOptions' => $statusOptions])

                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ route('references.index') }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('Batal') }}
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('Simpan Perubahan') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simplemde@1.11.2/dist/simplemde.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/simplemde@1.11.2/dist/simplemde.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        new SimpleMDE({
            element: document.getElementById('reference-content'),
            forceSync: true,
            spellChecker: false,
            status: false,
            autosave: {
                enabled: false,
            },
            renderingConfig: {
                singleLineBreaks: true,
                codeSyntaxHighlighting: false,
            }
        });
    });
</script>
@endpush

