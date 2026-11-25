<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tarifa extends Model
{
    use HasFactory;

    protected $table = 'tarifas';

    protected $fillable = [
        'categoria',
        'monto',
        'activa',
    ];

    protected function casts(): array
    {
        return [
            'monto' => 'decimal:2',
            'activa' => 'boolean',
        ];
    }
}
