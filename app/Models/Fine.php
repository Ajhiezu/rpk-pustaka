<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['loan_id', 'amount', 'status', 'type', 'payment_date'])]
class Fine extends Model
{
    use HasFactory;

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }
}
