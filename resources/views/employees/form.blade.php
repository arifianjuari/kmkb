@extends('layouts.app')

@section('content')
<section class="mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-gray-800">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center dark:border-gray-700">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                    {{ $isEdit ? 'Edit Pegawai' : 'Tambah Pegawai Baru' }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ $isEdit ? 'Perbarui data pegawai dan penempatan' : 'Isi data pegawai dan penempatan unit kerja' }}
                </p>
            </div>
            <a href="{{ route('employees.index') }}" class="btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>

        <form action="{{ $isEdit ? route('employees.update', $employee) : route('employees.store') }}" method="POST" class="p-6">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg dark:bg-red-900 dark:border-red-700 dark:text-red-100">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Employee Number -->
                <div>
                    <label for="employee_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        NIK/NIP <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="employee_number" id="employee_number" required
                           value="{{ old('employee_number', $employee?->employee_number) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                           placeholder="Contoh: P001">
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" required
                           value="{{ old('name', $employee?->name) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                           placeholder="Nama lengkap pegawai">
                </div>

                <!-- Job Title -->
                <div>
                    <label for="job_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Jabatan
                    </label>
                    <input type="text" name="job_title" id="job_title"
                           value="{{ old('job_title', $employee?->job_title) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                           placeholder="Dokter, Perawat, Kepala Ruang, dll">
                </div>

                <!-- Employment Type -->
                <div>
                    <label for="employment_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Tipe Kepegawaian
                    </label>
                    <select name="employment_type" id="employment_type"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Pilih Tipe</option>
                        @foreach(\App\Models\Employee::getEmploymentTypes() as $value => $label)
                            <option value="{{ $value }}" {{ old('employment_type', $employee?->employment_type) == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Education Level -->
                <div>
                    <label for="education_level" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Pendidikan Terakhir
                    </label>
                    <select name="education_level" id="education_level"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Pilih Pendidikan</option>
                        @foreach(\App\Models\Employee::getEducationLevels() as $value => $label)
                            <option value="{{ $value }}" {{ old('education_level', $employee?->education_level) == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Professional Category -->
                <div>
                    <label for="professional_category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Kategori Profesi
                    </label>
                    <select name="professional_category" id="professional_category"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Pilih Kategori</option>
                        @foreach(\App\Models\Employee::getProfessionalCategories() as $value => $label)
                            <option value="{{ $value }}" {{ old('professional_category', $employee?->professional_category) == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Join Date -->
                <div>
                    <label for="join_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Tanggal Masuk
                    </label>
                    <input type="date" name="join_date" id="join_date"
                           value="{{ old('join_date', $employee?->join_date?->format('Y-m-d')) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                @if($isEdit)
                <!-- Resign Date -->
                <div>
                    <label for="resign_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Tanggal Keluar/Resign
                    </label>
                    <input type="date" name="resign_date" id="resign_date"
                           value="{{ old('resign_date', $employee?->resign_date?->format('Y-m-d')) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                @endif

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @foreach(\App\Models\Employee::getStatuses() as $value => $label)
                            <option value="{{ $value }}" {{ old('status', $employee?->status ?? 'active') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Base Salary -->
                <div>
                    <label for="base_salary" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Gaji Pokok (Rp)
                    </label>
                    <input type="number" name="base_salary" id="base_salary" step="1000" min="0"
                           value="{{ old('base_salary', $employee?->base_salary) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                           placeholder="0">
                </div>

                <!-- Allowances -->
                <div>
                    <label for="allowances" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Tunjangan & Kompensasi (Rp)
                    </label>
                    <input type="number" name="allowances" id="allowances" step="1000" min="0"
                           value="{{ old('allowances', $employee?->allowances) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                           placeholder="Insentif, tunjangan, dll">
                </div>

                <!-- Notes -->
                <div class="lg:col-span-3 md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Catatan
                    </label>
                    <textarea name="notes" id="notes" rows="2"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                              placeholder="Catatan tambahan (opsional)">{{ old('notes', $employee?->notes) }}</textarea>
                </div>
            </div>

            <!-- Assignments Section -->
            <div class="mt-8" x-data="assignmentsManager()">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Penempatan Unit Kerja</h3>
                    <button type="button" @click="addAssignment()" class="btn-secondary text-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Tambah Penempatan
                    </button>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 dark:bg-gray-700">
                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        <p><strong>Catatan:</strong> Total FTE tidak boleh melebihi 100%. Gunakan split assignment jika pegawai bekerja di lebih dari satu unit (misal: 50% IGD + 50% Rawat Jalan).</p>
                    </div>

                    <div class="space-y-4" id="assignments-container">
                        <template x-for="(assignment, index) in assignments" :key="index">
                            <div class="bg-white rounded-lg p-4 border border-gray-200 dark:bg-gray-800 dark:border-gray-600">
                                <div class="flex justify-between items-start mb-3">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300" x-text="'Penempatan #' + (index + 1)"></span>
                                    <button type="button" @click="removeAssignment(index)" x-show="assignments.length > 1"
                                            class="text-red-600 hover:text-red-800 text-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Cost Center <span class="text-red-500">*</span>
                                        </label>
                                        <select :name="'assignments[' + index + '][cost_center_id]'" required
                                                x-model="assignment.cost_center_id"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <option value="">Pilih Cost Center</option>
                                            @foreach($costCenters as $cc)
                                                <option value="{{ $cc->id }}">{{ $cc->code }} - {{ $cc->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            FTE (%) <span class="text-red-500">*</span>
                                        </label>
                                        <div class="mt-1 flex rounded-md shadow-sm">
                                            <input type="number" :name="'assignments[' + index + '][fte_percentage]'"
                                                   x-model="assignment.fte_percentage"
                                                   min="1" max="100" step="1" required
                                                   @input="updateTotalFte()"
                                                   class="block w-full rounded-l-md border-gray-300 focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm dark:bg-gray-600 dark:border-gray-600 dark:text-gray-300">%</span>
                                        </div>
                                    </div>
                                    <div class="flex items-end">
                                        <label class="flex items-center">
                                            <input type="checkbox" :name="'assignments[' + index + '][is_primary]'"
                                                   x-model="assignment.is_primary" value="1"
                                                   @change="setPrimary(index)"
                                                   class="rounded border-gray-300 text-biru-dongker-600 shadow-sm focus:border-biru-dongker-300 focus:ring focus:ring-biru-dongker-200 focus:ring-opacity-50">
                                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Penempatan Utama</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Total FTE Display -->
                    <div class="mt-4 flex items-center justify-between p-3 rounded-lg" :class="totalFte > 100 ? 'bg-red-100 dark:bg-red-900' : 'bg-green-100 dark:bg-green-900'">
                        <span class="text-sm font-medium" :class="totalFte > 100 ? 'text-red-700 dark:text-red-100' : 'text-green-700 dark:text-green-100'">
                            Total FTE:
                        </span>
                        <span class="text-lg font-bold" :class="totalFte > 100 ? 'text-red-700 dark:text-red-100' : 'text-green-700 dark:text-green-100'" x-text="totalFte + '%'"></span>
                    </div>
                    <p x-show="totalFte > 100" class="mt-2 text-sm text-red-600 dark:text-red-400">Total FTE tidak boleh melebihi 100%</p>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('employees.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">
                    {{ $isEdit ? 'Simpan Perubahan' : 'Tambah Pegawai' }}
                </button>
            </div>
        </form>
    </div>
</section>

@push('scripts')
@php
$existingAssignments = [];
if ($employee && $employee->assignments) {
    $existingAssignments = $employee->assignments->whereNull('end_date')->map(function($a) {
        return [
            'cost_center_id' => $a->cost_center_id,
            'fte_percentage' => $a->fte_percentage * 100,
            'is_primary' => $a->is_primary,
        ];
    })->values()->toArray();
}
if (empty($existingAssignments)) {
    $existingAssignments = [['cost_center_id' => '', 'fte_percentage' => 100, 'is_primary' => true]];
}
$assignmentsJson = old('assignments', $existingAssignments);
@endphp
<script>
function assignmentsManager() {
    return {
        assignments: @json($assignmentsJson),
        totalFte: 0,
        
        init() {
            if (this.assignments.length === 0) {
                this.assignments = [{ cost_center_id: '', fte_percentage: 100, is_primary: true }];
            }
            // Convert fte_percentage from decimal to percentage if needed
            this.assignments = this.assignments.map(a => ({
                ...a,
                fte_percentage: a.fte_percentage > 1 ? a.fte_percentage : a.fte_percentage * 100
            }));
            this.updateTotalFte();
        },
        
        addAssignment() {
            this.assignments.push({ cost_center_id: '', fte_percentage: 0, is_primary: false });
        },
        
        removeAssignment(index) {
            if (this.assignments.length > 1) {
                const wasPrimary = this.assignments[index].is_primary;
                this.assignments.splice(index, 1);
                if (wasPrimary && this.assignments.length > 0) {
                    this.assignments[0].is_primary = true;
                }
                this.updateTotalFte();
            }
        },
        
        setPrimary(index) {
            this.assignments.forEach((a, i) => {
                a.is_primary = (i === index);
            });
        },
        
        updateTotalFte() {
            this.totalFte = this.assignments.reduce((sum, a) => sum + (parseFloat(a.fte_percentage) || 0), 0);
        }
    }
}
</script>
@endpush
@endsection
