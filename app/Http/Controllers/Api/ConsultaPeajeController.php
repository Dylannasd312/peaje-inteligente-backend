<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Caseta;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ConsultaPeajeController extends Controller
{
    /**
     * Consultar un vehículo por su placa.
     */
    public function consultarPlaca(Request $request)
    {
        $datos = $request->validate([
            'placa' => ['required', 'string', 'max:20'],
        ]);

        $placa = Str::upper(trim($datos['placa']));

        $vehiculo = Vehiculo::whereRaw(
            'UPPER(placa) = ?',
            [$placa]
        )->first();

        $consultaPasos = DB::table('pasos')
            ->whereRaw('UPPER(placa_detectada) = ?', [$placa]);

        $ultimoPaso = (clone $consultaPasos)
            ->orderByDesc('fecha_hora')
            ->first();

        return response()->json([
            'success' => true,
            'registrado' => $vehiculo !== null,
            'vehiculo' => $vehiculo ? [
                'id' => $vehiculo->id,
                'placa' => $vehiculo->placa,
                'categoria' => $vehiculo->categoria,
                'marca' => $vehiculo->marca,
                'modelo' => $vehiculo->modelo,
                'color' => $vehiculo->color,
                'activo' => $vehiculo->activo,
            ] : null,
            'ultimo_paso' => $ultimoPaso ? [
                'id' => $ultimoPaso->id,
                'caseta_id' => $ultimoPaso->caseta_id,
                'categoria_detectada' => $ultimoPaso->categoria_detectada,
                'ejes' => $ultimoPaso->ejes,
                'fecha_hora' => $ultimoPaso->fecha_hora,
                'estado' => $ultimoPaso->estado,
                'monto_tarifa' => $ultimoPaso->monto_tarifa,
            ] : null,
            'total_pasos' => (clone $consultaPasos)->count(),
        ]);
    }

    /**
     * Consultar historial de pasos de una placa.
     */
    public function historialPlaca(string $placa)
    {
        $placa = Str::upper(trim($placa));

        $historial = DB::table('pasos')
            ->leftJoin('casetas', 'pasos.caseta_id', '=', 'casetas.id')
            ->leftJoin('tarifas', 'pasos.tarifa_id', '=', 'tarifas.id')
            ->whereRaw('UPPER(pasos.placa_detectada) = ?', [$placa])
            ->select([
                'pasos.id',
                'pasos.placa_detectada',
                'pasos.categoria_detectada',
                'pasos.ejes',
                'pasos.confianza_placa',
                'pasos.confianza_categoria',
                'pasos.fecha_hora',
                'pasos.estado',
                'pasos.monto_tarifa',
                'casetas.nombre as caseta',
                'tarifas.categoria as categoria_tarifa',
            ])
            ->orderByDesc('pasos.fecha_hora')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'placa' => $placa,
            'historial' => $historial,
        ]);
    }

    /**
     * Obtener resumen de actividad de una caseta.
     */
    public function resumenCaseta(int $id)
    {
        $caseta = Caseta::find($id);

        if (!$caseta) {
            return response()->json([
                'success' => false,
                'mensaje' => 'La caseta solicitada no existe.',
            ], 404);
        }

        $consulta = DB::table('pasos')
            ->where('caseta_id', $id);

        $totalPasos = (clone $consulta)->count();

        $pasosHoy = (clone $consulta)
            ->whereDate('fecha_hora', now()->toDateString());

        $totalHoy = (clone $pasosHoy)->count();

        $recaudacionHoy = (clone $pasosHoy)
            ->sum('monto_tarifa');

        $recaudacionHistorica = (clone $consulta)
            ->sum('monto_tarifa');

        $categoriasHoy = (clone $pasosHoy)
            ->select(
                'categoria_detectada',
                DB::raw('COUNT(*) as cantidad'),
                DB::raw('COALESCE(SUM(monto_tarifa), 0) as recaudacion')
            )
            ->groupBy('categoria_detectada')
            ->get();

        $ultimoPaso = (clone $consulta)
            ->orderByDesc('fecha_hora')
            ->first();

        return response()->json([
            'success' => true,
            'caseta' => [
                'id' => $caseta->id,
                'nombre' => $caseta->nombre,
                'ubicacion' => $caseta->ubicacion,
                'activa' => $caseta->activa,
            ],
            'resumen' => [
                'total_pasos_historicos' => $totalPasos,
                'total_pasos_hoy' => $totalHoy,
                'recaudacion_hoy' => (float) $recaudacionHoy,
                'recaudacion_historica' => (float) $recaudacionHistorica,
                'categorias_hoy' => $categoriasHoy,
                'ultimo_paso' => $ultimoPaso,
            ],
        ]);
    }
}
