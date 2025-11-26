<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Reference extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'hospital_id',
        'author_id',
        'title',
        'slug',
        'content',
        'image_path',
        'status',
        'is_pinned',
        'published_at',
        'view_count',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'published_at' => 'datetime',
        'view_count' => 'integer',
    ];

    /**
     * Author relationship.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Hospital scope relationship.
     */
    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    /**
     * Tags relationship.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'reference_tag');
    }
}

