@extends('layouts.app')

@section('title', 'Pilih Rumah Sakit')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Pilih Rumah Sakit</h1>
        
        <p class="text-gray-600 mb-6">Sebagai Super Admin, Anda perlu memilih rumah sakit untuk mengakses data SIMRS.</p>
        
        <form action="{{ route('hospitals.select.set') }}" method="POST">
            @csrf
            
            <div class="mb-6">
                <label for="hospital_id" class="block text-sm font-medium text-gray-700 mb-2">Rumah Sakit</label>
                <select name="hospital_id" id="hospital_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                    <option value="">-- Pilih Rumah Sakit --</option>
                    @foreach($hospitals as $hospital)
                        <option value="{{ $hospital->id }}">{{ $hospital->name }}</option>
                    @endforeach
                </select>
                @error('hospital_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="flex items-center justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Pilih Rumah Sakit
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
