<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use App\Models\Caseta;
use App\Models\Paso;
use App\Models\Vehiculo;
use App\Services\PeajeIAService;
use App\Services\CalculoTarifaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class PasoController extends Controller
{
    public function index(Request $request)
    {
        $consulta = Paso::with([
            'vehiculo',
            'caseta',
            'usuario',
            'tarifa',
        ]);

        if ($request->filled('buscar')) {
            $buscar = trim($request->buscar);

            $consulta->where(
                'placa_detectada',
                'ILIKE',
                "%{$buscar}%"
            );
        }

        if ($request->filled('estado')) {
            $consulta->where('estado', $request->estado);
        }

        $pasos = $consulta
            ->latest('fecha_hora')
            ->paginate(12)
            ->withQueryString();

        $casetas = Caseta::where('activa', true)->get();

        $estadisticas = [
            'total' => Paso::count(),
            'hoy' => Paso::whereDate('fecha_hora', today())->count(),
            'no_registrados' => Paso::whereNull('vehiculo_id')->count(),
        ];

        return view('pasos.index', compact(
            'pasos',
            'casetas',
            'estadisticas'
        ));
    }

    public function show(Paso $paso)
    {
        $paso->load([
            'vehiculo',
            'caseta',
            'usuario',
            'tarifa',
            'alertas',
        ]);

        return view('pasos.show', compact('paso'));
    }

    public function procesar(
        Request $request,
        PeajeIAService $ia,
        CalculoTarifaService $calculoTarifa
    ) {
        $request->validate([
            'archivo' => ['required', 'image', 'max:10240'],
            'caseta_id' => ['required', 'exists:casetas,id'],
        ]);

        $archivo = $request->file('archivo');

        $rutaEvidencia = $archivo->store(
            'evidencias/pasos',
            'public'
        );

        try {
            // ==================================================
            // 1. ANALIZAR IMAGEN MEDIANTE FASTAPI
            // ==================================================

            $resultado = $ia->analizar($archivo);

            logger()->info('RESPUESTA COMPLETA DE IA', [
                'resultado' => $resultado,
            ]);

            // ==================================================
            // 2. OBTENER PLACA DETECTADA
            // ==================================================

            $placaRaw = $resultado['placa']
                ?? $resultado['placa_detectada']
                ?? null;

            if (is_array($placaRaw)) {
                $placaRaw = $placaRaw['texto']
                    ?? $placaRaw['placa']
                    ?? $placaRaw['value']
                    ?? $placaRaw['label']
                    ?? $placaRaw[0]
                    ?? null;
            }

            $placa = (
                is_string($placaRaw) ||
                is_numeric($placaRaw)
            )
                ? strtoupper(trim((string) $placaRaw))
                : null;

            if ($placa === '') {
                $placa = null;
            }

            // ==================================================
            // 3. OBTENER CATEGORIA DEL VEHICULO
            // ==================================================

            $categoriaRaw = $resultado['categoria']
                ?? $resultado['categoria_detectada']
                ?? null;

            if (is_array($categoriaRaw)) {
                $categoriaRaw = $categoriaRaw['nombre']
                    ?? $categoriaRaw['categoria']
                    ?? $categoriaRaw['label']
                    ?? $categoriaRaw['class']
                    ?? $categoriaRaw[0]
                    ?? null;
            }

            $categoria = is_string($categoriaRaw)
                ? trim($categoriaRaw)
                : null;

            if ($categoria === '') {
                $categoria = null;
            }

            // ==================================================
            // 4. OBTENER EJES ESTIMADOS
            // ==================================================

            $ejesRaw = $resultado['ejes']
                ?? $resultado['ejes_estimados']
                ?? null;

            if (is_array($ejesRaw)) {
                $ejesRaw = $ejesRaw['cantidad']
                    ?? $ejesRaw['valor']
                    ?? $ejesRaw[0]
                    ?? null;
            }

            $ejes = is_numeric($ejesRaw)
                ? (int) $ejesRaw
                : null;

            // ==================================================
            // 5. OBTENER CONFIANZAS Y LATENCIA
            // ==================================================

            $confianzaPlacaRaw = $resultado['confianza_placa']
                ?? $resultado['confianza']
                ?? null;

            $confianzaCategoriaRaw =
                $resultado['confianza_categoria'] ?? null;

            $latenciaRaw = $resultado['latencia_ms']
                ?? $resultado['tiempo_ms']
                ?? null;

            $confianzaPlaca = is_numeric($confianzaPlacaRaw)
                ? (float) $confianzaPlacaRaw
                : null;

            $confianzaCategoria = is_numeric($confianzaCategoriaRaw)
                ? (float) $confianzaCategoriaRaw
                : null;

            $latencia = is_numeric($latenciaRaw)
                ? (float) $latenciaRaw
                : null;

            // ==================================================
            // 6. OBTENER USUARIO AUTENTICADO
            // ==================================================

            $usuarioId = $request->user()?->id;

            // ==================================================
            // 7. BUSCAR VEHICULO REGISTRADO
            // ==================================================

            $vehiculo = $placa
                ? Vehiculo::whereRaw(
                    'UPPER(placa) = ?',
                    [$placa]
                )->first()
                : null;

            // ==================================================
            // 8. CALCULAR TARIFA
            // ==================================================

            $tarifa = $calculoTarifa->calcular($categoria);

            // ==================================================
            // 9. REGISTRAR PASO Y ALERTAS
            // ==================================================

            $paso = DB::transaction(function () use (
                $request,
                $placa,
                $categoria,
                $ejes,
                $confianzaPlaca,
                $confianzaCategoria,
                $latencia,
                $rutaEvidencia,
                $vehiculo,
                $tarifa,
                $usuarioId
            ) {

                // Crear registro evitando restricciones de fillable.
                $paso = new Paso();

                $paso->forceFill([
                    'vehiculo_id' => $vehiculo?->id,
                    'caseta_id' => $request->caseta_id,
                    'tarifa_id' => $tarifa?->id,
                    'usuario_id' => $usuarioId,

                    'placa_detectada' => $placa,
                    'categoria_detectada' => $categoria,

                    'ejes' => $ejes,

                    'confianza_placa' => $confianzaPlaca,
                    'confianza_categoria' => $confianzaCategoria,

                    'imagen_evidencia' => $rutaEvidencia,
                    'latencia_ms' => $latencia,

                    'monto_tarifa' => $tarifa
                        ? $tarifa->monto
                        : null,

                    'fecha_hora' => now(),

                    'estado' => $vehiculo
                        ? 'PROCESADO'
                        : 'NO_REGISTRADO',
                ])->save();

                // ==============================================
                // ALERTA: VEHICULO NO REGISTRADO
                // ==============================================

                if (!$vehiculo) {
                    Alerta::create([
                        'paso_id' => $paso->id,

                        'tipo' => $placa
                            ? 'VEHICULO_NO_REGISTRADO'
                            : 'PLACA_NO_DETECTADA',

                        'descripcion' => $placa
                            ? "Se detectó la placa {$placa}, pero no está registrada en el sistema."
                            : 'La inteligencia artificial no pudo identificar la placa del vehículo.',

                        'estado' => 'PENDIENTE',
                        'usuario_id' => $usuarioId,
                    ]);
                }

                // ==============================================
                // ALERTA: TARIFA NO CONFIGURADA
                // ==============================================

                if ($categoria && !$tarifa) {
                    Alerta::create([
                        'paso_id' => $paso->id,

                        'tipo' => 'TARIFA_NO_CONFIGURADA',

                        'descripcion' => "No existe una tarifa activa para la categoría detectada: {$categoria}. El paso se registró sin importe.",

                        'estado' => 'PENDIENTE',
                        'usuario_id' => $usuarioId,
                    ]);
                }

                return $paso;
            });

            // ==================================================
            // 10. REDIRECCIONAR AL DETALLE
            // ==================================================

            return redirect()
                ->route('pasos.show', $paso)
                ->with(
                    'success',
                    'Imagen procesada y paso registrado correctamente.'
                );

        } catch (Throwable $e) {

            Storage::disk('public')->delete($rutaEvidencia);

            report($e);

            return back()
                ->withInput()
                ->withErrors([
                    'archivo' => 'No se pudo procesar la imagen con la IA: '
                        . $e->getMessage()
                ]);
        }
    }


public function evidencia(Paso $paso)
{
    if (!$paso->imagen_evidencia) {
        abort(404, 'Este paso no tiene una imagen de evidencia.');
    }

    $ruta = Storage::disk('public')->path(
        $paso->imagen_evidencia
    );

    if (!is_file($ruta)) {
        abort(404, 'No se encontró el archivo de evidencia.');
    }

    return response()->file($ruta, [
        'Content-Type' => mime_content_type($ruta),
        'Content-Disposition' => 'inline',
    ]);
}

}
