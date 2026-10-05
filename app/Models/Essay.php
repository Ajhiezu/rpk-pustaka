<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Essay extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'submission_type',
        'content',
        'file_path',
        'file_type',
        'status',
        'review_note',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function isOnline(): bool
    {
        return $this->submission_type === 'online';
    }

    public function isFile(): bool
    {
        return $this->submission_type === 'file';
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }
}
