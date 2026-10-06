<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Loan extends Model
{
    use HasFactory;

    public const STATUS_PENDING            = 'pending';
    public const STATUS_APPROVED           = 'approved';
    public const STATUS_BORROWED           = 'borrowed';
    public const STATUS_PARTIALLY_RETURNED = 'partially_returned';
    public const STATUS_RETURNED           = 'returned';
    public const STATUS_REJECTED           = 'rejected';
    public const STATUS_CANCELLED          = 'cancelled';
    public const STATUS_EXPIRED            = 'expired';
    public const STATUS_OVERDUE            = 'overdue';

    protected $fillable = [
        'user_id',
        'loan_code',
        'loan_type',
        'loan_date',
        'due_date',
        'pickup_deadline',
        'approved_at',
        'borrowed_at',
        'rejection_reason',
        'status',
        'total_books',
    ];

    protected $casts = [
        'loan_date' => 'date',
        'due_date' => 'date',
        'pickup_deadline' => 'datetime',
        'approved_at' => 'datetime',
        'borrowed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function loanDetails(): HasMany
    {
        return $this->hasMany(LoanDetail::class);
    }

    public function returnBook(): HasOne
    {
        return $this->hasOne(ReturnBook::class);
    }

    public function fine(): HasOne
    {
        return $this->hasOne(Fine::class);
    }

    public function isDigital(): bool
    {
        return $this->loan_type === 'digital';
    }

    public function isPhysical(): bool
    {
        return $this->loan_type === 'physical';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isBorrowed(): bool
    {
        return $this->status === 'borrowed';
    }

    public function isReturned(): bool
    {
        return $this->status === 'returned';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isExpiredState(): bool
    {
        return $this->status === 'expired';
    }

    public function isPickupExpired(): bool
    {
        return $this->isPending() && $this->pickup_deadline && now()->gt($this->pickup_deadline);
    }

    public function isActive(): bool
    {
        return $this->status === 'borrowed' && now()->startOfDay()->lte($this->due_date);
    }

    public function isExpired(): bool
    {
        return $this->status === 'borrowed' && now()->startOfDay()->gt($this->due_date);
    }

    public function isOverdue(): bool
    {
        return $this->status === 'overdue' || ($this->status === 'borrowed' && $this->due_date && now()->startOfDay()->gt($this->due_date));
    }

    public function getStatusBadgeLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Persetujuan',
            'approved' => 'Disetujui (Menunggu Pengambilan)',
            'borrowed' => 'Sedang Dipinjam',
            'partially_returned' => 'Sebagian Dikembalikan',
            'returned' => 'Dikembalikan (Lengkap)',
            'rejected' => 'Ditolak',
            'cancelled' => 'Dibatalkan',
            'expired' => 'Kedaluwarsa',
            'overdue' => 'Terlambat',
            default => ucfirst((string) $this->status),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status_badge_label;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'borrowed')->whereDate('due_date', '>=', now()->toDateString());
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeDigital($query)
    {
        return $query->where('loan_type', 'digital');
    }

    public function scopePhysical($query)
    {
        return $query->where('loan_type', 'physical');
    }
}
