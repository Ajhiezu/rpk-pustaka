<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['loan_id', 'return_date', 'condition', 'notes'])]
class ReturnBook extends Model
{
    use HasFactory;

    protected $table = 'returns';

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }
}
