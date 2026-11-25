<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorreoAutorizado extends Model
{
    protected $table = 'correos_autorizados';

    protected $fillable = [
        'email',
        'role',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}
