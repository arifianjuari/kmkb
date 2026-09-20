@php
    $simrsStatus = $simrsConnectionStatus ?? app(\App\Services\SimrsService::class)->connectionStatus();
@endphp
@if (!($simrsStatus['available'] ?? false))
    <div class="mb-4 rounded-lg border px-4 py-3 text-sm {{ ($simrsStatus['configured'] ?? false) ? 'border-amber-300 bg-amber-50 text-amber-900' : 'border-red-300 bg-red-50 text-red-900' }}" role="alert">
        <p class="font-medium">{{ ($simrsStatus['configured'] ?? false) ? 'Database SIMRS tidak dapat dijangkau' : 'Database SIMRS belum dikonfigurasi' }}</p>
        <p class="mt-1">{{ $simrsStatus['message'] ?? 'Periksa pengaturan koneksi SIMRS.' }}</p>
        @if (Route::has('setup.simrs-integration.settings'))
            <p class="mt-2">
                <a href="{{ route('setup.simrs-integration.settings') }}" class="underline font-medium">Pengaturan integrasi SIMRS</a>
            </p>
        @endif
    </div>
@endif

<script>
    window.simrsApiFetch = function (url, options) {
        const defaultHeaders = {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        };

        return fetch(url, {
            credentials: 'include',
            ...options,
            headers: {
                ...defaultHeaders,
                ...(options && options.headers ? options.headers : {}),
            },
        }).then(async function (response) {
            if (response.status === 401) {
                throw { type: 'auth', message: 'Anda perlu login untuk mengakses data ini.' };
            }

            if (response.status === 400) {
                const data = await response.json();
                throw { type: 'hospital', message: data.message || 'Konteks rumah sakit diperlukan.' };
            }

            if (response.status === 503) {
                const data = await response.json();
                throw { type: 'simrs', message: data.message || 'Database SIMRS tidak tersedia.' };
            }

            return response.json();
        });
    };

    window.simrsShowTableError = function (tableBody, colspan, message) {
        if (!tableBody) {
            return;
        }
        tableBody.innerHTML = '<tr><td colspan="' + colspan + '" class="px-6 py-2 text-center text-red-500">' + message + '</td></tr>';
    };
</script>
