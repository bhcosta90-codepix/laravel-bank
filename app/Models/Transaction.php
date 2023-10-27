<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory, HasUuids;

    protected $casts = [
        'value' => 'float',
    ];

    protected $fillable = [
        'id',
        'bank',
        'status',
        'cancel_description',
        'description',
        'reference',
        'value',
        'kind',
        'key',
    ];
}
