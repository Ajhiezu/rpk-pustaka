<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'book_code',
        'category_id',
        'location_id',
        'title',
        'slug',
        'author',
        'publisher',
        'year',
        'isbn',
        'language',
        'page_count',
        'collection_type',
        'stock',
        'available_stock',
        'price',
        'fine_type',
        'fine_value',
        'image',
        'description',
        'pdf_path',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function loanDetails(): HasMany
    {
        return $this->hasMany(LoanDetail::class);
    }

    public function hasDigital(): bool
    {
        return in_array($this->collection_type, ['digital', 'fisik_digital']) && !empty($this->pdf_path);
    }

    public function hasPhysical(): bool
    {
        return in_array($this->collection_type, ['fisik', 'fisik_digital']) && $this->stock > 0;
    }

    public function isHybrid(): bool
    {
        return $this->collection_type === 'fisik_digital';
    }

    public function isDigitalOnly(): bool
    {
        return $this->collection_type === 'digital';
    }

    public function isPhysicalOnly(): bool
    {
        return $this->collection_type === 'fisik';
    }

    public function getFormatLabelAttribute(): string
    {
        if ($this->collection_type === 'fisik_digital') {
            return 'Fisik & Digital';
        }
        if ($this->collection_type === 'digital') {
            return 'Digital';
        }
        return 'Fisik';
    }

    public function isPlaceholderCover(): bool
    {
        if (empty($this->image)) {
            return true;
        }
        $lower = strtolower($this->image);
        return str_contains($lower, 'placeholder') || str_ends_with($lower, '.svg');
    }

    public function getCoverUrlAttribute(): ?string
    {
        if ($this->isPlaceholderCover()) {
            return null;
        }

        $cleanPath = ltrim(str_replace(['public/', 'storage/'], '', $this->image), '/');
        return asset('storage/' . $cleanPath);
    }
}
