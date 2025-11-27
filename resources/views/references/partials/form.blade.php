@php
use Illuminate\Support\Facades\Storage;
@endphp
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
        <label for="image" class="block text-sm font-medium text-gray-700">{{ __('Gambar (JPEG/PNG)') }}</label>
        <div class="mt-1">
            @if(isset($reference) && $reference->image_path)
                <div class="mb-3">
                    <img src="{{ storage_url($reference->image_path) }}" 
                         alt="Preview" 
                         class="max-w-xs h-auto rounded-lg border border-gray-300">
                    <div class="mt-2 flex items-center">
                        <input id="remove_image" name="remove_image" type="checkbox" value="1"
                            class="h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                        <label for="remove_image" class="ml-2 text-sm text-red-600">
                            {{ __('Hapus gambar') }}
                        </label>
                    </div>
                </div>
            @endif
            <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/jpg"
                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
        </div>
        <p class="mt-2 text-sm text-gray-500">
            {{ __('Format: JPEG atau PNG. Maksimal ukuran: 5MB.') }}
        </p>
        @error('image')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="tags" class="block text-sm font-medium text-gray-700">{{ __('Tag') }}</label>
        <div class="mt-1">
            <div id="tags-container" class="flex flex-wrap gap-2 mb-2">
                @if(isset($reference) && $reference->tags)
                    @foreach($reference->tags as $tag)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 tag-item" data-tag-id="{{ $tag->id }}">
                            {{ $tag->name }}
                            <button type="button" class="ml-2 text-indigo-600 hover:text-indigo-800 remove-tag" aria-label="Hapus tag">
                                ×
                            </button>
                        </span>
                    @endforeach
                @endif
            </div>
            <div class="flex gap-2">
                <input type="text" 
                       id="tag-input" 
                       placeholder="{{ __('Ketik tag dan tekan Enter') }}"
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                @if(isset($tags) && $tags->count() > 0)
                    <select id="tag-select" 
                            class="block w-48 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">{{ __('Pilih tag yang sudah ada') }}</option>
                        @foreach($tags as $tag)
                            <option value="{{ $tag->name }}">{{ $tag->name }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
            <div id="tags-inputs" class="hidden">
                @if(isset($reference) && $reference->tags)
                    @foreach($reference->tags as $tag)
                        <input type="hidden" name="tags[]" value="{{ $tag->name }}">
                    @endforeach
                @endif
            </div>
        </div>
        <p class="mt-2 text-sm text-gray-500">
            {{ __('Tambahkan tag untuk memudahkan pencarian. Tekan Enter untuk menambahkan tag baru.') }}
        </p>
        @error('tags')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tagInput = document.getElementById('tag-input');
    const tagSelect = document.getElementById('tag-select');
    const tagsContainer = document.getElementById('tags-container');
    const tagsInputs = document.getElementById('tags-inputs');
    const existingTags = new Set();

    // Initialize existing tags
    document.querySelectorAll('.tag-item').forEach(item => {
        const tagName = item.textContent.trim().replace('×', '').trim();
        existingTags.add(tagName);
    });

    function addTag(tagName) {
        tagName = tagName.trim();
        if (!tagName || existingTags.has(tagName)) {
            return;
        }

        existingTags.add(tagName);

        // Create visual tag
        const tagSpan = document.createElement('span');
        tagSpan.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 tag-item';
        tagSpan.innerHTML = tagName + 
            '<button type="button" class="ml-2 text-indigo-600 hover:text-indigo-800 remove-tag" aria-label="Hapus tag">×</button>';
        
        // Add remove handler
        tagSpan.querySelector('.remove-tag').addEventListener('click', function() {
            removeTag(tagName, tagSpan);
        });

        tagsContainer.appendChild(tagSpan);

        // Create hidden input
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'tags[]';
        hiddenInput.value = tagName;
        tagsInputs.appendChild(hiddenInput);

        // Clear input
        tagInput.value = '';
        if (tagSelect) tagSelect.value = '';
    }

    function removeTag(tagName, tagSpan) {
        existingTags.delete(tagName);
        tagSpan.remove();
        
        // Remove hidden input
        const hiddenInputs = tagsInputs.querySelectorAll('input[value="' + tagName + '"]');
        hiddenInputs.forEach(input => input.remove());
    }

    // Handle Enter key in tag input
    if (tagInput) {
        tagInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addTag(tagInput.value);
            }
        });
    }

    // Handle tag select change
    if (tagSelect) {
        tagSelect.addEventListener('change', function(e) {
            if (e.target.value) {
                addTag(e.target.value);
            }
        });
    }

    // Handle remove tag buttons
    document.querySelectorAll('.remove-tag').forEach(button => {
        button.addEventListener('click', function() {
            const tagItem = this.closest('.tag-item');
            const tagName = tagItem.textContent.trim().replace('×', '').trim();
            removeTag(tagName, tagItem);
        });
    });
});
</script>
@endpush

