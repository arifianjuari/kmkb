<div class="space-y-6">
    <div>
        <label for="title" class="block text-sm font-medium text-gray-700">{{ __('Judul') }}</label>
        <div class="mt-1">
            <input type="text" name="title" id="title"
                value="{{ old('title', $reference->title) }}"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                required>
        </div>
        @error('title')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700">{{ __('Status') }}</label>
            <div class="mt-1">
                <select id="status" name="status"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $reference->status ?? \App\Models\Reference::STATUS_DRAFT) === $value)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            @error('status')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="published_at" class="block text-sm font-medium text-gray-700">{{ __('Tanggal Publikasi (opsional)') }}</label>
            <div class="mt-1">
                <input type="datetime-local" name="published_at" id="published_at"
                    value="{{ old('published_at', optional($reference->published_at)->format('Y-m-d\TH:i')) }}"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            @error('published_at')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center mt-6 space-x-3">
            <input id="is_pinned" name="is_pinned" type="checkbox" value="1"
                class="h-5 w-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                @checked(old('is_pinned', $reference->is_pinned))>
            <label for="is_pinned" class="text-sm font-medium text-gray-700">
                {{ __('Sematkan di urutan teratas') }}
            </label>
        </div>
    </div>

    <div>
        <label for="reference-content" class="block text-sm font-medium text-gray-700">{{ __('Konten') }}</label>
        <div class="mt-1">
            <textarea id="reference-content" name="content" rows="12"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                required>{{ old('content', $reference->content) }}</textarea>
        </div>
        <p class="mt-2 text-sm text-gray-500">
            {{ __('Gunakan Markdown untuk memformat teks (mis. heading, list, link).') }}
        </p>
        @error('content')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

