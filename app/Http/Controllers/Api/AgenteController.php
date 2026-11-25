<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AgenteInteligenteService;
use App\Services\ModeloLenguajeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class AgenteController extends Controller
{
    public function chat(
        Request $request,
        AgenteInteligenteService $agente,
        ModeloLenguajeService $modelo
    ) {
        $datos = $request->validate([
            'mensaje' => ['required', 'string', 'min:2', 'max:1000'],

            'historial' => ['nullable', 'array', 'max:10'],

            'historial.*.rol' => [
                'required',
                'in:user,assistant',
            ],

            'historial.*.contenido' => [
                'required',
                'string',
                'max:1000',
            ],
        ]);

        try {
            // Obtener los datos reales desde PostgreSQL.
            $resultado = $agente->responder($datos['mensaje']);

            // Interpretar los resultados con el modelo de lenguaje.
            try {
                $respuestaNatural = $modelo->generarRespuesta(
                    $datos['mensaje'],
                    $resultado,
                    $datos['historial'] ?? []
                );

                $respuesta = $respuestaNatural;
                $modeloUtilizado = 'qwen3:14b';

            } catch (Throwable $errorModelo) {
                // Si Ollama no está disponible, conservar la respuesta
                // determinística obtenida desde PostgreSQL.
                Log::warning('El modelo de lenguaje no está disponible.', [
                    'error' => $errorModelo->getMessage(),
                ]);

                $respuesta = $resultado['respuesta'];
                $modeloUtilizado = 'respuesta_local';
            }

            return response()->json([
                'success' => true,
                'respuesta' => $respuesta,
                'tipo' => $resultado['tipo'],
                'datos' => $resultado['datos'],
                'modelo' => $modeloUtilizado,
            ]);

        } catch (Throwable $e) {
            Log::error('Error en el agente inteligente.', [
                'usuario_id' => $request->user()?->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'mensaje' => 'No fue posible procesar la consulta.',
            ], 500);
        }
    }
}
