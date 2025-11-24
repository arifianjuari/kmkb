<?php

namespace App\Http\Controllers;

use App\Models\Reference;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
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

        $references = Reference::with('author')
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
            ->orderByDesc('is_pinned')
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('references.index', [
            'references' => $references,
            'statusOptions' => $this->statusOptions(includeAll: true),
            'filters' => [
                'search' => $search,
                'status' => $status ?: 'all',
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('references.create', [
            'reference' => new Reference(),
            'statusOptions' => $this->statusOptions(),
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

        Reference::create($data);

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
            'reference' => $reference->fresh(['author']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reference $reference)
    {
        return view('references.edit', [
            'reference' => $reference,
            'statusOptions' => $this->statusOptions(),
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

        $reference->update($data);

        return redirect()->route('references.index')
            ->with('success', 'Referensi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reference $reference)
    {
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
            'status' => ['required', 'in:' . implode(',', array_keys($this->statusOptions()))],
            'is_pinned' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ], [], [
            'title' => 'judul',
            'content' => 'konten',
            'status' => 'status',
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
}

