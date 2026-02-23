<?php

namespace App\Models;

use App\Models\Concerns\HasStandardFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    use HasFactory, HasStandardFields;

    protected $fillable = [
        'user_id',
        'name',
        'cpf',
        'phone',
        'cep',
        'street',
        'number',
        'district',
        'city',
        'state',
        'complement',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
