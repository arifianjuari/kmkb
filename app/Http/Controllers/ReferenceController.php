<?php

namespace App\Http\Controllers;

use App\Models\Reference;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReferenceController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Reference::class, 'reference');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->string('search')->toString();
        $status = $request->string('status')->toString();
        $tagId = $request->integer('tag');

        $references = Reference::with(['author', 'tags'])
            ->where('hospital_id', hospital('id'))
            ->when($status && $status !== 'all', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%");
                });
            })
            ->when($tagId, function ($query) use ($tagId) {
                $query->whereHas('tags', function ($q) use ($tagId) {
                    $q->where('tags.id', $tagId);
                });
            })
            ->orderByDesc('is_pinned')
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $tags = Tag::where('hospital_id', hospital('id'))
            ->orderBy('name')
            ->get();

        return view('references.index', [
            'references' => $references,
            'statusOptions' => $this->statusOptions(includeAll: true),
            'tags' => $tags,
            'filters' => [
                'search' => $search,
                'status' => $status ?: 'all',
                'tag' => $tagId,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tags = Tag::where('hospital_id', hospital('id'))
            ->orderBy('name')
            ->get();

        return view('references.create', [
            'reference' => new Reference(),
            'statusOptions' => $this->statusOptions(),
            'tags' => $tags,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data['hospital_id'] = hospital('id');
        $data['author_id'] = Auth::id();
        $data['is_pinned'] = $request->boolean('is_pinned');
        $data['slug'] = $this->generateUniqueSlug($data['title']);
        $data['published_at'] = $this->resolvePublishedAt($data['status'], $request->input('published_at'));

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = Str::slug($data['title']) . '-' . time() . '.' . $image->getClientOriginalExtension();
            $uploadDisk = $this->getUploadDisk();
            
            // S3 doesn't need makeDirectory, but local does
            if ($uploadDisk === 'public') {
                Storage::disk($uploadDisk)->makeDirectory('references');
            }
            
            $image->storeAs('references', $imageName, $uploadDisk);
            $data['image_path'] = 'references/' . $imageName;
        }

        $reference = Reference::create($data);

        // Handle tags
        $this->syncTags($reference, $request->input('tags', []));

        return redirect()->route('references.index')
            ->with('success', 'Referensi berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Reference $reference)
    {
        $reference->increment('view_count');

        return view('references.show', [
            'reference' => $reference->fresh(['author', 'tags']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reference $reference)
    {
        $tags = Tag::where('hospital_id', hospital('id'))
            ->orderBy('name')
            ->get();

        return view('references.edit', [
            'reference' => $reference->load('tags'),
            'statusOptions' => $this->statusOptions(),
            'tags' => $tags,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reference $reference)
    {
        $data = $this->validatedData($request);
        $data['is_pinned'] = $request->boolean('is_pinned');

        if ($data['title'] !== $reference->title) {
            $data['slug'] = $this->generateUniqueSlug($data['title'], $reference->id);
        }

        $data['published_at'] = $this->resolvePublishedAt(
            $data['status'],
            $request->input('published_at'),
            $reference->published_at
        );

        // Handle image upload
        $uploadDisk = $this->getUploadDisk();
        
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($reference->image_path && Storage::disk($uploadDisk)->exists($reference->image_path)) {
                Storage::disk($uploadDisk)->delete($reference->image_path);
            }

            $image = $request->file('image');
            $imageName = Str::slug($data['title']) . '-' . time() . '.' . $image->getClientOriginalExtension();
            
            // S3 doesn't need makeDirectory, but local does
            if ($uploadDisk === 'public') {
                Storage::disk($uploadDisk)->makeDirectory('references');
            }
            
            $image->storeAs('references', $imageName, $uploadDisk);
            $data['image_path'] = 'references/' . $imageName;
        } elseif ($request->has('remove_image') && $request->boolean('remove_image')) {
            // Remove image if requested
            if ($reference->image_path && Storage::disk($uploadDisk)->exists($reference->image_path)) {
                Storage::disk($uploadDisk)->delete($reference->image_path);
            }
            $data['image_path'] = null;
        }

        $reference->update($data);

        // Handle tags
        $this->syncTags($reference, $request->input('tags', []));

        return redirect()->route('references.index')
            ->with('success', 'Referensi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reference $reference)
    {
        // Delete image if exists
        $uploadDisk = $this->getUploadDisk();
        if ($reference->image_path && Storage::disk($uploadDisk)->exists($reference->image_path)) {
            Storage::disk($uploadDisk)->delete($reference->image_path);
        }

        $reference->delete();

        return redirect()->route('references.index')
            ->with('success', 'Referensi berhasil dihapus.');
    }

    /**
     * Validate incoming request.
     */
    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:5120'], // Max 5MB
            'remove_image' => ['nullable', 'boolean'],
            'status' => ['required', 'in:' . implode(',', array_keys($this->statusOptions()))],
            'is_pinned' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['nullable', 'string'],
        ], [], [
            'title' => 'judul',
            'content' => 'konten',
            'image' => 'gambar',
            'status' => 'status',
            'tags' => 'tag',
        ]);
    }

    protected function statusOptions(bool $includeAll = false): array
    {
        $options = [
            Reference::STATUS_DRAFT => 'Draft',
            Reference::STATUS_PUBLISHED => 'Published',
            Reference::STATUS_ARCHIVED => 'Archived',
        ];

        if ($includeAll) {
            return ['all' => 'Semua Status'] + $options;
        }

        return $options;
    }

    protected function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title) ?: Str::random(8);
        $slug = $baseSlug;
        $counter = 1;

        while (
            Reference::where('hospital_id', hospital('id'))
                ->where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $baseSlug . '-' . ($counter++);
        }

        return $slug;
    }

    protected function resolvePublishedAt(string $status, ?string $inputDate, ?Carbon $existing = null): ?Carbon
    {
        if ($status !== Reference::STATUS_PUBLISHED) {
            return null;
        }

        if ($inputDate) {
            return Carbon::parse($inputDate);
        }

        return $existing ?? now();
    }

    /**
     * Get the upload disk to use (S3 if credentials available, otherwise public).
     */
    protected function getUploadDisk(): string
    {
        return uploads_disk();
    }

    /**
     * Sync tags for a reference.
     */
    protected function syncTags(Reference $reference, array $tagInputs): void
    {
        $tagIds = [];

        foreach ($tagInputs as $tagInput) {
            if (empty(trim($tagInput))) {
                continue;
            }

            // Check if tag exists by name
            $tag = Tag::where('hospital_id', hospital('id'))
                ->where('name', trim($tagInput))
                ->first();

            // If tag doesn't exist, create it
            if (!$tag) {
                $tag = Tag::create([
                    'hospital_id' => hospital('id'),
                    'name' => trim($tagInput),
                    'slug' => Str::slug(trim($tagInput)),
                ]);
            }

            $tagIds[] = $tag->id;
        }

        // Sync tags
        $reference->tags()->sync($tagIds);
    }
}

