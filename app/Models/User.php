<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'activo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    public function pasos(): HasMany
    {
        return $this->hasMany(Paso::class, 'usuario_id');
    }

    public function alertas(): HasMany
    {
        return $this->hasMany(Alerta::class, 'usuario_id');
    }

    public function esAdministrador(): bool
    {
        return $this->role === 'Administrador';
    }

    public function esOperador(): bool
    {
        return $this->role === 'Operador';
    }
}
