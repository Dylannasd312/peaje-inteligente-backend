<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paso extends Model
{
    use HasFactory;

    protected $table = 'pasos';

    protected $fillable = [
        'vehiculo_id',
        'caseta_id',
        'tarifa_id',
        'usuario_id',
        'placa_detectada',
        'categoria_detectada',
        'ejes',
        'confianza_placa',
        'confianza_categoria',
        'imagen_evidencia',
        'latencia_ms',
        'monto_tarifa',
        'fecha_hora',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha_hora' => 'datetime',
            'confianza_placa' => 'float',
            'confianza_categoria' => 'float',
            'ejes' => 'integer',
            'latencia_ms' => 'float',
            'monto_tarifa' => 'decimal:2',
        ];
    }

    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function caseta(): BelongsTo
    {
        return $this->belongsTo(Caseta::class);
    }

    public function tarifa(): BelongsTo
    {
        return $this->belongsTo(Tarifa::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function alertas(): HasMany
    {
        return $this->hasMany(Alerta::class);
    }
}
