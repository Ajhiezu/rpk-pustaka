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

    protected static function booted(): void
    {
        static::deleting(function (Book $book) {
            // Nullify ISBN when soft deleting so it won't block creating new active books with the same ISBN
            if (!empty($book->isbn)) {
                $book->isbn = null;
            }
            // Suffix book_code when soft deleting so it won't block reusing or manually entering the book_code
            if (!empty($book->book_code) && !str_contains($book->book_code, '_DEL_')) {
                $book->book_code = $book->book_code . '_DEL_' . $book->id;
            }
            $book->saveQuietly();
        });
    }

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

    /**
     * Calculate damaged / lost fine amount according to fine_type and fine_value.
     */
    public function getCalculatedFineAmount(): float
    {
        $price = (float) filter_var($this->price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        if ($this->fine_type === 'multiplier' && !empty($this->fine_value)) {
            $multiplierStr = preg_replace('/[^0-9.]/', '', (string) $this->fine_value);
            $multiplier = (float) ($multiplierStr ?: 1);
            if ($price > 0) {
                return $price * $multiplier;
            }
        }

        if ($this->fine_type === 'fixed' && !empty($this->fine_value)) {
            $val = (float) filter_var($this->fine_value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            if ($val > 0) {
                return $val;
            }
        }

        if (!empty($this->fine_value)) {
            $val = (float) filter_var($this->fine_value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            if ($val > 0) {
                return $val;
            }
        }

        return $price > 0 ? $price : 50000.0;
    }
}
