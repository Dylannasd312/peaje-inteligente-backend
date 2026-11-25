<?php

namespace App\Services;

use App\Models\Caseta;
use App\Models\Vehiculo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AgenteInteligenteService
{
    public function responder(string $mensaje): array
    {
        $pregunta = $this->normalizar($mensaje);

        if ($this->contiene($pregunta, ['cuantos vehiculos pasaron hoy', 'total de pasos hoy', 'pasos de hoy', 'vehiculos de hoy'])) {
            return $this->pasosHoy();
        }

        if ($this->contiene($pregunta, ['recaudacion de hoy', 'cuanto se recaudo hoy', 'ingresos de hoy', 'dinero recaudado hoy'])) {
            return $this->recaudacionHoy($pregunta);
        }

        if ($this->contiene($pregunta, ['recaudacion historica', 'recaudacion total', 'cuanto se recaudo en total', 'ingresos historicos'])) {
            return $this->recaudacionHistorica($pregunta);
        }

        if ($this->contiene($pregunta, ['ultimo vehiculo', 'ultimo paso', 'vehiculo mas reciente', 'ultima deteccion'])) {
            return $this->ultimoPaso();
        }

        if ($this->contiene($pregunta, ['placas no registradas', 'vehiculos no registrados', 'vehiculos desconocidos', 'placas desconocidas'])) {
            return $this->placasNoRegistradas();
        }

        if ($this->contiene($pregunta, ['placas frecuentes', 'vehiculos frecuentes', 'vehiculos que mas pasaron', 'placas que mas pasaron'])) {
            return $this->placasFrecuentes();
        }

        if ($this->contiene($pregunta, ['cuantos vehiculos registrados', 'total de vehiculos', 'vehiculos registrados'])) {
            return $this->totalVehiculos();
        }

        if ($this->contiene($pregunta, ['por categoria', 'cuantos camiones', 'cuantos buses', 'cuantas motos', 'cuantos autos', 'cantidad de camiones', 'cantidad de buses', 'cantidad de motos'])) {
            return $this->resumenCategorias($pregunta);
        }

        if ($this->contiene($pregunta, ['resumen de casetas', 'listar casetas', 'cuantas casetas', 'casetas activas'])) {
            return $this->resumenCasetas();
        }

        return [
            'tipo' => 'AYUDA',
            'respuesta' => 'Puedo ayudarte a consultar información del sistema de peaje. Puedes preguntarme por los pasos de hoy, recaudación, vehículos registrados, placas no registradas, vehículos frecuentes, categorías, último paso o actividad de las casetas.',
            'datos' => null,
        ];
    }

    private function pasosHoy(): array
    {
        $total = DB::table('pasos')
            ->whereDate('fecha_hora', now()->toDateString())
            ->count();

        return [
            'tipo' => 'PASOS_HOY',
            'respuesta' => "Hoy se registraron {$total} pasos de vehículos en el sistema.",
            'datos' => [
                'fecha' => now()->toDateString(),
                'total_pasos' => $total,
            ],
        ];
    }

    private function recaudacionHoy(string $pregunta): array
    {
        $consulta = DB::table('pasos')
            ->whereDate('fecha_hora', now()->toDateString());

        $caseta = $this->buscarCasetaEnPregunta($pregunta);

        if ($caseta) {
            $consulta->where('caseta_id', $caseta->id);
        }

        $total = (float) $consulta->sum('monto_tarifa');

        $descripcion = $caseta
            ? " en la caseta {$caseta->nombre}"
            : '';

        return [
            'tipo' => 'RECAUDACION_HOY',
            'respuesta' => 'La recaudación registrada hoy' . $descripcion . ' es de Bs ' . number_format($total, 2, '.', ',') . '.',
            'datos' => [
                'fecha' => now()->toDateString(),
                'caseta' => $caseta?->nombre,
                'recaudacion' => $total,
                'moneda' => 'BOB',
            ],
        ];
    }

    private function recaudacionHistorica(string $pregunta): array
    {
        $consulta = DB::table('pasos');

        $caseta = $this->buscarCasetaEnPregunta($pregunta);

        if ($caseta) {
            $consulta->where('caseta_id', $caseta->id);
        }

        $total = (float) $consulta->sum('monto_tarifa');

        $descripcion = $caseta
            ? " en la caseta {$caseta->nombre}"
            : '';

        return [
            'tipo' => 'RECAUDACION_HISTORICA',
            'respuesta' => 'La recaudación histórica registrada' . $descripcion . ' es de Bs ' . number_format($total, 2, '.', ',') . '.',
            'datos' => [
                'caseta' => $caseta?->nombre,
                'recaudacion' => $total,
                'moneda' => 'BOB',
            ],
        ];
    }

    private function ultimoPaso(): array
    {
        $paso = DB::table('pasos')
            ->leftJoin('casetas', 'pasos.caseta_id', '=', 'casetas.id')
            ->whereNotNull('pasos.fecha_hora')
            ->orderByDesc('pasos.fecha_hora')
            ->select([
                'pasos.id',
                'pasos.placa_detectada',
                'pasos.categoria_detectada',
                'pasos.ejes',
                'pasos.fecha_hora',
                'pasos.estado',
                'pasos.monto_tarifa',
                'casetas.nombre as caseta',
            ])
            ->first();

        if (!$paso) {
            return [
                'tipo' => 'ULTIMO_PASO',
                'respuesta' => 'Todavía no existen pasos registrados en el sistema.',
                'datos' => null,
            ];
        }

        $placa = $paso->placa_detectada ?: 'No detectada';
        $categoria = $paso->categoria_detectada ?: 'No identificada';
        $caseta = $paso->caseta ?: 'No disponible';

        return [
            'tipo' => 'ULTIMO_PASO',
            'respuesta' => "El último paso registrado corresponde a la placa {$placa}, categoría {$categoria}, en {$caseta}, el {$paso->fecha_hora}.",
            'datos' => $paso,
        ];
    }

    private function placasNoRegistradas(): array
    {
        $placas = DB::table('pasos')
            ->whereNull('vehiculo_id')
            ->whereNotNull('placa_detectada')
            ->select(
                'placa_detectada',
                DB::raw('COUNT(*) as cantidad_pasos'),
                DB::raw('MAX(fecha_hora) as ultimo_paso')
            )
            ->groupBy('placa_detectada')
            ->orderByDesc('ultimo_paso')
            ->limit(20)
            ->get();

        $cantidad = $placas->count();

        return [
            'tipo' => 'PLACAS_NO_REGISTRADAS',
            'respuesta' => "Se encontraron {$cantidad} placas diferentes sin un vehículo asociado, considerando los últimos 20 resultados como máximo.",
            'datos' => $placas,
        ];
    }

    private function placasFrecuentes(): array
    {
        $placas = DB::table('pasos')
            ->whereNotNull('placa_detectada')
            ->select(
                'placa_detectada',
                DB::raw('COUNT(*) as cantidad_pasos'),
                DB::raw('MAX(fecha_hora) as ultimo_paso')
            )
            ->groupBy('placa_detectada')
            ->orderByDesc('cantidad_pasos')
            ->limit(10)
            ->get();

        return [
            'tipo' => 'PLACAS_FRECUENTES',
            'respuesta' => 'Estas son las 10 placas con mayor cantidad de pasos registrados.',
            'datos' => $placas,
        ];
    }

    private function totalVehiculos(): array
    {
        $total = Vehiculo::where('activo', true)->count();

        return [
            'tipo' => 'VEHICULOS_REGISTRADOS',
            'respuesta' => "Actualmente existen {$total} vehículos activos registrados.",
            'datos' => [
                'total_vehiculos_activos' => $total,
            ],
        ];
    }


private function resumenCategorias(string $pregunta): array
{
    $categoria = $this->detectarCategoria($pregunta);

    if (!$categoria) {
        return [
            'tipo' => 'CATEGORIAS',
            'respuesta' => 'Indícame qué categoría deseas consultar: autos, motos, minibuses, buses o camiones.',
            'datos' => null,
        ];
    }

    $total = DB::table('pasos')
        ->whereDate('fecha_hora', now()->toDateString())
        ->whereRaw(
            "LOWER(TRANSLATE(categoria_detectada, 'áéíóú', 'aeiou')) = ?",
            [$categoria]
        )
        ->count();

    return [
        'tipo' => 'CATEGORIA_HOY',
        'respuesta' => "Hoy se registraron {$total} pasos de la categoría {$categoria}.",
        'datos' => [
            'fecha' => now()->toDateString(),
            'categoria' => $categoria,
            'cantidad' => $total,
        ],
    ];
}


    private function resumenCasetas(): array
    {
        $casetas = Caseta::where('activa', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'ubicacion', 'activa']);

        return [
            'tipo' => 'RESUMEN_CASETAS',
            'respuesta' => 'Estas son las casetas activas registradas en el sistema.',
            'datos' => $casetas,
        ];
    }

    private function buscarCasetaEnPregunta(string $pregunta): ?Caseta
    {
        $casetas = Caseta::where('activa', true)->get();

        foreach ($casetas as $caseta) {
            $nombre = $this->normalizar($caseta->nombre);

            if ($nombre !== '' && Str::contains($pregunta, $nombre)) {
                return $caseta;
            }
        }

        return null;
    }

    private function detectarCategoria(string $pregunta): ?string
    {
        $categorias = [
            'camion' => ['camion', 'camiones'],
            'bus' => ['bus', 'buses'],
            'moto' => ['moto', 'motos', 'motocicleta'],
            'auto' => ['auto', 'autos', 'automovil', 'automoviles', 'taxi'],
            'minibus' => ['minibus', 'minibuses'],
        ];

        foreach ($categorias as $categoria => $palabras) {
            if ($this->contiene($pregunta, $palabras)) {
                return $categoria;
            }
        }

        return null;
    }

    private function contiene(string $texto, array $opciones): bool
    {
        foreach ($opciones as $opcion) {
            if (Str::contains($texto, $opcion)) {
                return true;
            }
        }

        return false;
    }

    private function normalizar(string $texto): string
    {
        return Str::lower(trim(Str::ascii($texto)));
    }
}
