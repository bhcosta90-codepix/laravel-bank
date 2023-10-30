<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory, HasUuids;

    protected $casts = [
        'balance' => "float"
    ];

    protected $fillable = [
        'id',
        'name',
        'balance',
        'document',
    ];

    public function pix(): HasMany
    {
        return $this->hasMany(PixKey::class);
    }
}
