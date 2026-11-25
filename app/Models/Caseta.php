<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Caseta extends Model
{
    use HasFactory;

    protected $table = 'casetas';

    protected $fillable = [
        'nombre',
        'ubicacion',
        'activa',
    ];

    protected function casts(): array
    {
        return [
            'activa' => 'boolean',
        ];
    }
}
