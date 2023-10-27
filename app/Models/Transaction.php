<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory, HasUuids;

    protected $casts = [
        'value' => 'float',
    ];

    protected $fillable = [
        'id',
        'account_id',
        'status',
        'cancel_description',
        'description',
        'reference',
        'value',
        'kind',
        'key',
        'type',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
