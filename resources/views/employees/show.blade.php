@extends('layouts.app')

@section('content')
<section class="mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-gray-800">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center dark:border-gray-700">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Detail Pegawai</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $employee->employee_number }} - {{ $employee->name }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('employees.edit', $employee) }}" class="btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
                <a href="{{ route('employees.index') }}" class="btn-secondary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <div class="p-6">
            <!-- Employee Info -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Basic Info -->
                <div class="bg-gray-50 rounded-lg p-4 dark:bg-gray-700">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Informasi Dasar</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">NIK/NIP</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white font-mono">{{ $employee->employee_number }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Nama</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $employee->name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Jabatan</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $employee->job_title ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Status</dt>
                            <dd>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $employee->status_badge_class }}">
                                    {{ $employee->status_label }}
                                </span>
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Tanggal Masuk</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $employee->join_date?->format('d M Y') ?? '-' }}</dd>
                        </div>
                        @if($employee->resign_date)
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Tanggal Keluar</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $employee->resign_date->format('d M Y') }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>

                <!-- Professional Info -->
                <div class="bg-gray-50 rounded-lg p-4 dark:bg-gray-700">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Informasi Kepegawaian</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Tipe</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $employee->employment_type_label ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Pendidikan</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $employee->education_level_label ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Kategori</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $employee->professional_category_label ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between border-t pt-3 mt-3 border-gray-200 dark:border-gray-600">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Gaji Pokok</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                @if($employee->base_salary)
                                    Rp {{ number_format($employee->base_salary, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Tunjangan</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                @if($employee->allowances)
                                    Rp {{ number_format($employee->allowances, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </dd>
                        </div>
                        <div class="flex justify-between border-t pt-3 mt-3 border-gray-200 dark:border-gray-600">
                            <dt class="text-sm font-medium text-gray-700 dark:text-gray-300">Total</dt>
                            <dd class="text-sm font-bold text-biru-dongker-600 dark:text-biru-dongker-400">
                                Rp {{ number_format($employee->total_salary, 0, ',', '.') }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- FTE Summary -->
                <div class="bg-gray-50 rounded-lg p-4 dark:bg-gray-700">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Ringkasan FTE</h3>
                    <div class="flex items-center justify-center h-32">
                        <div class="text-center">
                            <div class="text-4xl font-bold text-biru-dongker-600 dark:text-biru-dongker-400">
                                {{ number_format($employee->total_fte * 100, 0) }}%
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total FTE</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assignments -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Penempatan Unit Kerja</h3>
                
                @if($employee->assignments->count() > 0)
                    <div class="space-y-3">
                        @foreach($employee->assignments->sortByDesc('is_primary') as $assignment)
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 dark:bg-gray-700 dark:border-gray-600 {{ $assignment->is_primary ? 'ring-2 ring-biru-dongker-500' : '' }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        @if($assignment->is_primary)
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-biru-dongker-100 text-biru-dongker-800 dark:bg-biru-dongker-900 dark:text-biru-dongker-100">
                                                Utama
                                            </span>
                                        @endif
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white">{{ $assignment->costCenter->name }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $assignment->costCenter->code }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($assignment->fte_percentage * 100, 0) }}%</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            Sejak {{ $assignment->effective_date->format('d M Y') }}
                                            @if($assignment->end_date)
                                                - {{ $assignment->end_date->format('d M Y') }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400">Belum ada penempatan.</p>
                @endif
            </div>

            <!-- Notes -->
            @if($employee->notes)
            <div class="mt-8">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Catatan</h3>
                <p class="text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 rounded-lg p-4">{{ $employee->notes }}</p>
            </div>
            @endif

            <!-- Metadata -->
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400">
                    <span>Dibuat: {{ $employee->created_at->format('d M Y H:i') }}</span>
                    <span>Diperbarui: {{ $employee->updated_at->format('d M Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
