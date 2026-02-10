@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">{{ __('Tambah Aset Baru') }}</h2>
        <a href="{{ route('fixed-assets.index') }}" class="btn-secondary">{{ __('Kembali') }}</a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form action="{{ route('fixed-assets.store') }}" method="POST">
                @csrf
                
                <!-- Identifikasi BMN -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Identifikasi BMN</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="jenis_bmn" class="block text-sm font-medium text-gray-700">Jenis BMN</label>
                            <select name="jenis_bmn" id="jenis_bmn" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                                <option value="">-- Pilih Jenis --</option>
                                @foreach($jenisBmnOptions as $value => $label)
                                    <option value="{{ $value }}" {{ old('jenis_bmn') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="asset_code" class="block text-sm font-medium text-gray-700">Kode Barang <span class="text-red-500">*</span></label>
                            <input type="text" name="asset_code" id="asset_code" value="{{ old('asset_code') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm" placeholder="16 digit kode SIMAK BMN">
                            @error('asset_code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="nup" class="block text-sm font-medium text-gray-700">NUP</label>
                            <input type="number" name="nup" id="nup" value="{{ old('nup') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm" placeholder="Nomor Urut Pendaftaran">
                        </div>
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama Barang <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="kode_satker" class="block text-sm font-medium text-gray-700">Kode Satker</label>
                            <input type="text" name="kode_satker" id="kode_satker" value="{{ old('kode_satker') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                        </div>
                        <div>
                            <label for="nama_satker" class="block text-sm font-medium text-gray-700">Nama Satker</label>
                            <input type="text" name="nama_satker" id="nama_satker" value="{{ old('nama_satker') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                        </div>
                    </div>
                </div>

                <!-- Kategorisasi Internal -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Kategori & Cost Center</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="asset_category_id" class="block text-sm font-medium text-gray-700">Kategori Aset</label>
                            <select name="asset_category_id" id="asset_category_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" data-useful-life="{{ $cat->default_useful_life_years }}" {{ old('asset_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="cost_center_id" class="block text-sm font-medium text-gray-700">Cost Center</label>
                            <select name="cost_center_id" id="cost_center_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                                <option value="">-- Pilih Cost Center --</option>
                                @foreach($costCenters as $cc)
                                    <option value="{{ $cc->id }}" {{ old('cost_center_id') == $cc->id ? 'selected' : '' }}>{{ $cc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                            <textarea name="description" id="description" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Detail Barang -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Barang</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="brand" class="block text-sm font-medium text-gray-700">Merk/Brand</label>
                            <input type="text" name="brand" id="brand" value="{{ old('brand') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                        </div>
                        <div>
                            <label for="model" class="block text-sm font-medium text-gray-700">Model/Tipe</label>
                            <input type="text" name="model" id="model" value="{{ old('model') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                        </div>
                        <div>
                            <label for="serial_number" class="block text-sm font-medium text-gray-700">Nomor Seri</label>
                            <input type="text" name="serial_number" id="serial_number" value="{{ old('serial_number') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                        </div>
                        <div>
                            <label for="manufacturer" class="block text-sm font-medium text-gray-700">Produsen</label>
                            <input type="text" name="manufacturer" id="manufacturer" value="{{ old('manufacturer') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                        </div>
                        <div>
                            <label for="condition" class="block text-sm font-medium text-gray-700">Kondisi</label>
                            <select name="condition" id="condition" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                                @foreach($conditionOptions as $value => $label)
                                    <option value="{{ $value }}" {{ old('condition', 'good') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                                @foreach($statusOptions as $value => $label)
                                    <option value="{{ $value }}" {{ old('status', 'active') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Lokasi -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Lokasi</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="lokasi_ruang" class="block text-sm font-medium text-gray-700">Lokasi Ruang</label>
                            <input type="text" name="lokasi_ruang" id="lokasi_ruang" value="{{ old('lokasi_ruang') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm" placeholder="Contoh: Ruang Radiologi">
                        </div>
                        <div>
                            <label for="status_penggunaan" class="block text-sm font-medium text-gray-700">Status Penggunaan</label>
                            <select name="status_penggunaan" id="status_penggunaan" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                                <option value="">-- Pilih --</option>
                                <option value="Digunakan Sendiri" {{ old('status_penggunaan') == 'Digunakan Sendiri' ? 'selected' : '' }}>Digunakan Sendiri</option>
                                <option value="Digunakan Pihak Lain" {{ old('status_penggunaan') == 'Digunakan Pihak Lain' ? 'selected' : '' }}>Digunakan Pihak Lain</option>
                                <option value="Tidak Digunakan" {{ old('status_penggunaan') == 'Tidak Digunakan' ? 'selected' : '' }}>Tidak Digunakan</option>
                                <option value="Sewa" {{ old('status_penggunaan') == 'Sewa' ? 'selected' : '' }}>Sewa</option>
                                <option value="Pinjam Pakai" {{ old('status_penggunaan') == 'Pinjam Pakai' ? 'selected' : '' }}>Pinjam Pakai</option>
                            </select>
                        </div>
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700">Lokasi Fisik</label>
                            <input type="text" name="location" id="location" value="{{ old('location') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                        </div>
                        <div class="md:col-span-3">
                            <label for="alamat_lengkap" class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                            <input type="text" name="alamat_lengkap" id="alamat_lengkap" value="{{ old('alamat_lengkap') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                        </div>
                        <div>
                            <label for="kelurahan" class="block text-sm font-medium text-gray-700">Kelurahan/Desa</label>
                            <input type="text" name="kelurahan" id="kelurahan" value="{{ old('kelurahan') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                        </div>
                        <div>
                            <label for="kecamatan" class="block text-sm font-medium text-gray-700">Kecamatan</label>
                            <input type="text" name="kecamatan" id="kecamatan" value="{{ old('kecamatan') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                        </div>
                        <div>
                            <label for="kabupaten_kota" class="block text-sm font-medium text-gray-700">Kab/Kota</label>
                            <input type="text" name="kabupaten_kota" id="kabupaten_kota" value="{{ old('kabupaten_kota') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                        </div>
                        <div>
                            <label for="provinsi" class="block text-sm font-medium text-gray-700">Provinsi</label>
                            <input type="text" name="provinsi" id="provinsi" value="{{ old('provinsi') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                        </div>
                        <div>
                            <label for="kode_pos" class="block text-sm font-medium text-gray-700">Kode Pos</label>
                            <input type="text" name="kode_pos" id="kode_pos" value="{{ old('kode_pos') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                        </div>
                    </div>
                </div>

                <!-- Nilai & Tanggal -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Nilai & Tanggal</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="acquisition_date" class="block text-sm font-medium text-gray-700">Tanggal Perolehan <span class="text-red-500">*</span></label>
                            <input type="date" name="acquisition_date" id="acquisition_date" value="{{ old('acquisition_date') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                            @error('acquisition_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="tanggal_buku_pertama" class="block text-sm font-medium text-gray-700">Tanggal Buku Pertama</label>
                            <input type="date" name="tanggal_buku_pertama" id="tanggal_buku_pertama" value="{{ old('tanggal_buku_pertama') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                        </div>
                        <div>
                            <label for="useful_life_years" class="block text-sm font-medium text-gray-700">Umur Ekonomis (Tahun) <span class="text-red-500">*</span></label>
                            <input type="number" name="useful_life_years" id="useful_life_years" value="{{ old('useful_life_years', 4) }}" min="1" max="100" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                            @error('useful_life_years')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="acquisition_cost" class="block text-sm font-medium text-gray-700">Nilai Perolehan <span class="text-red-500">*</span></label>
                            <input type="number" name="acquisition_cost" id="acquisition_cost" value="{{ old('acquisition_cost') }}" min="0" step="1" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                            @error('acquisition_cost')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="nilai_penyusutan" class="block text-sm font-medium text-gray-700">Nilai Penyusutan</label>
                            <input type="number" name="nilai_penyusutan" id="nilai_penyusutan" value="{{ old('nilai_penyusutan', 0) }}" min="0" step="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                        </div>
                        <div>
                            <label for="nilai_buku" class="block text-sm font-medium text-gray-700">Nilai Buku</label>
                            <input type="number" name="nilai_buku" id="nilai_buku" value="{{ old('nilai_buku', 0) }}" min="0" step="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                        </div>
                        <div>
                            <label for="salvage_value" class="block text-sm font-medium text-gray-700">Nilai Sisa</label>
                            <input type="number" name="salvage_value" id="salvage_value" value="{{ old('salvage_value', 0) }}" min="0" step="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                        </div>
                        <div>
                            <label for="nilai_mutasi" class="block text-sm font-medium text-gray-700">Nilai Mutasi</label>
                            <input type="number" name="nilai_mutasi" id="nilai_mutasi" value="{{ old('nilai_mutasi', 0) }}" step="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                        </div>
                    </div>
                </div>

                <!-- Tanah/Bangunan (Collapsible) -->
                <div class="border-b border-gray-200 pb-6 mb-6" x-data="{ open: false }">
                    <button type="button" @click="open = !open" class="flex items-center justify-between w-full text-left">
                        <h3 class="text-lg font-medium text-gray-900">Data Tanah/Bangunan <span class="text-sm font-normal text-gray-500">(opsional)</span></h3>
                        <svg class="w-5 h-5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="luas_tanah_seluruhnya" class="block text-sm font-medium text-gray-700">Luas Tanah (m²)</label>
                            <input type="number" name="luas_tanah_seluruhnya" id="luas_tanah_seluruhnya" value="{{ old('luas_tanah_seluruhnya') }}" step="0.01" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                        </div>
                        <div>
                            <label for="luas_bangunan" class="block text-sm font-medium text-gray-700">Luas Bangunan (m²)</label>
                            <input type="number" name="luas_bangunan" id="luas_bangunan" value="{{ old('luas_bangunan') }}" step="0.01" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                        </div>
                        <div>
                            <label for="jumlah_lantai" class="block text-sm font-medium text-gray-700">Jumlah Lantai</label>
                            <input type="number" name="jumlah_lantai" id="jumlah_lantai" value="{{ old('jumlah_lantai') }}" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                        </div>
                        <div>
                            <label for="status_sertifikasi" class="block text-sm font-medium text-gray-700">Status Sertifikasi</label>
                            <select name="status_sertifikasi" id="status_sertifikasi" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                                <option value="">-- Pilih --</option>
                                <option value="Sudah Bersertifikat" {{ old('status_sertifikasi') == 'Sudah Bersertifikat' ? 'selected' : '' }}>Sudah Bersertifikat</option>
                                <option value="Belum Bersertifikat" {{ old('status_sertifikasi') == 'Belum Bersertifikat' ? 'selected' : '' }}>Belum Bersertifikat</option>
                                <option value="Dalam Proses" {{ old('status_sertifikasi') == 'Dalam Proses' ? 'selected' : '' }}>Dalam Proses</option>
                            </select>
                        </div>
                        <div>
                            <label for="jenis_sertifikat" class="block text-sm font-medium text-gray-700">Jenis Sertifikat</label>
                            <select name="jenis_sertifikat" id="jenis_sertifikat" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                                <option value="">-- Pilih --</option>
                                <option value="SHM" {{ old('jenis_sertifikat') == 'SHM' ? 'selected' : '' }}>SHM (Sertifikat Hak Milik)</option>
                                <option value="HGB" {{ old('jenis_sertifikat') == 'HGB' ? 'selected' : '' }}>HGB (Hak Guna Bangunan)</option>
                                <option value="HP" {{ old('jenis_sertifikat') == 'HP' ? 'selected' : '' }}>HP (Hak Pakai)</option>
                                <option value="HPL" {{ old('jenis_sertifikat') == 'HPL' ? 'selected' : '' }}>HPL (Hak Pengelolaan)</option>
                            </select>
                        </div>
                        <div>
                            <label for="no_sertifikat" class="block text-sm font-medium text-gray-700">No Sertifikat</label>
                            <input type="text" name="no_sertifikat" id="no_sertifikat" value="{{ old('no_sertifikat') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                        </div>
                    </div>
                </div>

                <!-- Dokumen Kendaraan (Collapsible) -->
                <div class="border-b border-gray-200 pb-6 mb-6" x-data="{ open: false }">
                    <button type="button" @click="open = !open" class="flex items-center justify-between w-full text-left">
                        <h3 class="text-lg font-medium text-gray-900">Data Kendaraan <span class="text-sm font-normal text-gray-500">(opsional)</span></h3>
                        <svg class="w-5 h-5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="no_polisi" class="block text-sm font-medium text-gray-700">No Polisi</label>
                            <input type="text" name="no_polisi" id="no_polisi" value="{{ old('no_polisi') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                        </div>
                        <div>
                            <label for="no_bpkb" class="block text-sm font-medium text-gray-700">No BPKB</label>
                            <input type="text" name="no_bpkb" id="no_bpkb" value="{{ old('no_bpkb') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                        </div>
                    </div>
                </div>

                <!-- Maintenance -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Pemeliharaan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="warranty_end_date" class="block text-sm font-medium text-gray-700">Tanggal Berakhir Garansi</label>
                            <input type="date" name="warranty_end_date" id="warranty_end_date" value="{{ old('warranty_end_date') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                        </div>
                        <div>
                            <label for="calibration_due_date" class="block text-sm font-medium text-gray-700">Jatuh Tempo Kalibrasi</label>
                            <input type="date" name="calibration_due_date" id="calibration_due_date" value="{{ old('calibration_due_date') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2">
                    <a href="{{ route('fixed-assets.index') }}" class="btn-secondary">{{ __('Batal') }}</a>
                    <button type="submit" class="btn-primary">{{ __('Simpan') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('asset_category_id').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const usefulLife = selected.dataset.usefulLife;
    if (usefulLife) {
        document.getElementById('useful_life_years').value = usefulLife;
    }
});
</script>
@endpush
@endsection
