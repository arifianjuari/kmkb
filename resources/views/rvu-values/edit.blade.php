@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Edit RVU</h2>
            <a href="{{ route('rvu-values.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                Kembali ke Daftar
            </a>
        </div>

        <!-- Keterangan Section -->
        <div class="bg-white shadow rounded-lg p-4 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Keterangan</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="font-semibold text-gray-700 mb-1">Waktu (mnt)</p>
                    <p class="text-gray-600">Waktu yang dibutuhkan untuk melakukan 1x tindakan (dalam menit)</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-700 mb-1">Profesionalisme</p>
                    <p class="text-gray-600">
                        1 = Perawat<br>
                        2 = Nurse/Bidan<br>
                        3 = Dokter Umum<br>
                        4 = Dokter Spesialis<br>
                        5 = Dokter Subspesialis
                    </p>
                </div>
                <div>
                    <p class="font-semibold text-gray-700 mb-1">Tingkat Kesulitan</p>
                    <p class="text-gray-600">Ranking 1-10, dimana 1 = Paling Mudah dan 10 = Paling Sulit</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Data RVU</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <form action="{{ route('rvu-values.update', $rvuValue) }}" method="POST" id="rvu-form">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-12">
                        <div class="col-span-12 md:col-span-6">
                            <label for="cost_reference_id" class="block text-sm font-medium text-gray-700">Cost Reference <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <select id="cost_reference_id" name="cost_reference_id" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                    <option value="">Pilih Cost Reference</option>
                                    @foreach($costReferences as $cr)
                                        <option value="{{ $cr->id }}" {{ old('cost_reference_id', $rvuValue->cost_reference_id) == $cr->id ? 'selected' : '' }}>
                                            {{ $cr->service_description }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('cost_reference_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="col-span-12 md:col-span-6">
                            <label for="cost_center_id" class="block text-sm font-medium text-gray-700">Cost Center <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <select id="cost_center_id" name="cost_center_id" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                    <option value="">Pilih Cost Center</option>
                                    @foreach($costCenters as $cc)
                                        <option value="{{ $cc->id }}" {{ old('cost_center_id', $rvuValue->cost_center_id) == $cc->id ? 'selected' : '' }}>
                                            {{ $cc->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('cost_center_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="col-span-12 md:col-span-3">
                            <label for="period_year" class="block text-sm font-medium text-gray-700">Tahun Periode <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <input type="number" id="period_year" name="period_year" value="{{ old('period_year', $rvuValue->period_year) }}" min="2020" max="2100" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                @error('period_year')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="col-span-12 md:col-span-3">
                            <label for="period_month" class="block text-sm font-medium text-gray-700">Bulan Periode</label>
                            <div class="mt-1">
                                <select id="period_month" name="period_month" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                    <option value="">Tahunan</option>
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ old('period_month', $rvuValue->period_month) == $i ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($i)->locale('id')->monthName }}
                                        </option>
                                    @endfor
                                </select>
                                @error('period_month')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="col-span-12 md:col-span-4">
                            <label for="time_factor" class="block text-sm font-medium text-gray-700">Waktu (menit) <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <input type="number" id="time_factor" name="time_factor" value="{{ old('time_factor', $rvuValue->time_factor) }}" min="1" step="1" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700" onchange="calculateRvu()">
                                @error('time_factor')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Waktu yang dibutuhkan untuk melakukan 1x tindakan</p>
                            </div>
                        </div>

                        <div class="col-span-12 md:col-span-4">
                            <label for="professionalism_factor" class="block text-sm font-medium text-gray-700">Profesionalisme <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <select id="professionalism_factor" name="professionalism_factor" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700" onchange="calculateRvu()">
                                    <option value="">Pilih Profesionalisme</option>
                                    <option value="1" {{ old('professionalism_factor', $rvuValue->professionalism_factor) == '1' ? 'selected' : '' }}>1 - Perawat</option>
                                    <option value="2" {{ old('professionalism_factor', $rvuValue->professionalism_factor) == '2' ? 'selected' : '' }}>2 - Nurse/Bidan</option>
                                    <option value="3" {{ old('professionalism_factor', $rvuValue->professionalism_factor) == '3' ? 'selected' : '' }}>3 - Dokter Umum</option>
                                    <option value="4" {{ old('professionalism_factor', $rvuValue->professionalism_factor) == '4' ? 'selected' : '' }}>4 - Dokter Spesialis</option>
                                    <option value="5" {{ old('professionalism_factor', $rvuValue->professionalism_factor) == '5' ? 'selected' : '' }}>5 - Dokter Subspesialis</option>
                                </select>
                                @error('professionalism_factor')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="col-span-12 md:col-span-4">
                            <label for="difficulty_factor" class="block text-sm font-medium text-gray-700">Tingkat Kesulitan <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <input type="number" id="difficulty_factor" name="difficulty_factor" value="{{ old('difficulty_factor', $rvuValue->difficulty_factor) }}" min="1" max="10" step="1" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700" onchange="calculateRvu()">
                                @error('difficulty_factor')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">1 = Paling Mudah, 10 = Paling Sulit</p>
                            </div>
                        </div>

                        <div class="col-span-12 md:col-span-6">
                            <label for="normalization_factor" class="block text-sm font-medium text-gray-700">Faktor Normalisasi</label>
                            <div class="mt-1">
                                <input type="number" id="normalization_factor" name="normalization_factor" value="{{ old('normalization_factor', $rvuValue->normalization_factor) }}" min="0.1" max="10.0" step="0.1" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700" onchange="calculateRvu()">
                                @error('normalization_factor')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Default: 1.0</p>
                            </div>
                        </div>

                        <div class="col-span-12 md:col-span-6">
                            <label class="block text-sm font-medium text-gray-700">RVU Value (Preview)</label>
                            <div class="mt-1">
                                <div id="rvu_preview" class="py-2 px-3 block w-full border border-gray-300 rounded-md bg-gray-50 text-lg font-semibold text-gray-900">
                                    -
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Formula: (Waktu × Profesionalisme × Kesulitan) / Normalisasi</p>
                            </div>
                        </div>

                        <div class="col-span-12">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Catatan</label>
                            <div class="mt-1">
                                <textarea id="notes" name="notes" rows="3" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 resize-y">{{ old('notes', $rvuValue->notes) }}</textarea>
                                @error('notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="col-span-12">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $rvuValue->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-biru-dongker-600 shadow-sm focus:border-biru-dongker-300 focus:ring focus:ring-biru-dongker-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Aktif</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-biru-dongker-800 hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                            Update RVU
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function calculateRvu() {
    const time = parseFloat(document.getElementById('time_factor').value) || 0;
    const professionalism = parseFloat(document.getElementById('professionalism_factor').value) || 0;
    const difficulty = parseFloat(document.getElementById('difficulty_factor').value) || 0;
    const normalization = parseFloat(document.getElementById('normalization_factor').value) || 1.0;
    
    if (time > 0 && professionalism > 0 && difficulty > 0 && normalization > 0) {
        const rvuValue = (time * professionalism * difficulty) / normalization;
        document.getElementById('rvu_preview').textContent = rvuValue.toFixed(4);
    } else {
        document.getElementById('rvu_preview').textContent = '-';
    }
}

// Calculate on page load if values exist
document.addEventListener('DOMContentLoaded', function() {
    calculateRvu();
});
</script>
@endsection

