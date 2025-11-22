@extends('layouts.app')

@section('title', 'Sinkronisasi SIMRS')

@section('content')
<div class="mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Sinkronisasi Data dari SIMRS</h1>
        
        <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <p class="text-blue-800">Halaman ini memungkinkan Anda untuk melakukan sinkronisasi manual data obat dari sistem SIMRS ke sistem KMKB.</p>
        </div>
        
        <form id="syncForm" class="mb-8">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="limit" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Data</label>
                    <input type="number" id="limit" name="limit" value="100" min="1" max="1000" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="mt-1 text-sm text-gray-500">Jumlah maksimum data yang akan disinkronkan</p>
                </div>
                
                <div>
                    <label for="hospital_id" class="block text-sm font-medium text-gray-700 mb-2">ID Rumah Sakit (Opsional)</label>
                    <input type="text" id="hospital_id" name="hospital_id" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="mt-1 text-sm text-gray-500">Biarkan kosong untuk sinkronisasi global</p>
                </div>
            </div>
            
            <div class="flex items-center justify-between">
                <button type="button" id="syncButton" 
                        class="px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                    <span id="buttonText">Sinkronisasi Obat</span>
                    <span id="buttonSpinner" class="hidden ml-2">
                        <svg class="animate-spin h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
                
                <div class="text-sm text-gray-500">
                    <span id="lastSyncInfo">
                        @if(App\Models\CostReference::where('is_synced_from_simrs', true)->exists())
                            Terakhir disinkronkan: {{ App\Models\CostReference::where('is_synced_from_simrs', true)->latest('last_synced_at')->first()->last_synced_at->format('d M Y H:i') }}
                        @else
                            Belum pernah disinkronkan
                        @endif
                    </span>
                </div>
            </div>
        </form>
        
        <div id="resultSection" class="hidden">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Hasil Sinkronisasi</h2>
            <div id="resultContent" class="p-4 rounded-lg"></div>
        </div>
    </div>
</div>

<script>
    document.getElementById('syncButton').addEventListener('click', function() {
        const button = this;
        const buttonText = document.getElementById('buttonText');
        const buttonSpinner = document.getElementById('buttonSpinner');
        const resultSection = document.getElementById('resultSection');
        const resultContent = document.getElementById('resultContent');
        
        // Disable button and show spinner
        button.disabled = true;
        buttonText.textContent = 'Menyinkronkan...';
        buttonSpinner.classList.remove('hidden');
        
        // Hide previous results
        resultSection.classList.add('hidden');
        
        // Get form data
        const formData = new FormData(document.getElementById('syncForm'));
        
        // Send request
        fetch('{{ route('simrs.sync.drugs') }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            // Enable button and hide spinner
            button.disabled = false;
            buttonText.textContent = 'Sinkronisasi Obat';
            buttonSpinner.classList.add('hidden');
            
            // Show results
            resultSection.classList.remove('hidden');
            
            if (data.success) {
                resultContent.innerHTML = `
                    <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center mb-2">
                            <svg class="h-5 w-5 text-green-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <h3 class="text-lg font-medium text-green-800">Sinkronisasi Berhasil</h3>
                        </div>
                        <pre class="text-sm text-green-700 whitespace-pre-wrap">${data.output}</pre>
                    </div>
                `;
                
                // Update last sync info
                document.getElementById('lastSyncInfo').textContent = 'Terakhir disinkronkan: ' + new Date().toLocaleString('id-ID');
            } else {
                resultContent.innerHTML = `
                    <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-center mb-2">
                            <svg class="h-5 w-5 text-red-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            <h3 class="text-lg font-medium text-red-800">Sinkronisasi Gagal</h3>
                        </div>
                        <p class="text-sm text-red-700">${data.message}</p>
                        <pre class="mt-2 text-sm text-red-700 whitespace-pre-wrap">${data.output || ''}</pre>
                    </div>
                `;
            }
        })
        .catch(error => {
            // Enable button and hide spinner
            button.disabled = false;
            buttonText.textContent = 'Sinkronisasi Obat';
            buttonSpinner.classList.add('hidden');
            
            // Show error
            resultSection.classList.remove('hidden');
            resultContent.innerHTML = `
                <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center mb-2">
                        <svg class="h-5 w-5 text-red-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <h3 class="text-lg font-medium text-red-800">Kesalahan Jaringan</h3>
                    </div>
                    <p class="text-sm text-red-700">Tidak dapat terhubung ke server. Silakan coba lagi.</p>
                </div>
            `;
        });
    });
</script>
@endsection
