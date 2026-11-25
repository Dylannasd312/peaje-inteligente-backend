<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\AgenteInteligenteService;
use App\Services\ModeloLenguajeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class AgenteWebController extends Controller
{
    public function index(Request $request)
    {
        $this->autorizar($request);

        return view('agente.index', [
            'historial' => $request->session()->get('agente_historial', []),
        ]);
    }

    public function enviar(
        Request $request,
        AgenteInteligenteService $agente,
        ModeloLenguajeService $modelo
    ) {
        $this->autorizar($request);

        $datos = $request->validate([
            'mensaje' => ['required', 'string', 'min:2', 'max:1000'],
        ]);

        try {
            $historial = $request->session()->get('agente_historial', []);

            // Obtener información real de PostgreSQL.
            $resultado = $agente->responder($datos['mensaje']);

            // Generar una respuesta natural usando el historial.
            try {
                $respuesta = $modelo->generarRespuesta(
                    $datos['mensaje'],
                    $resultado,
                    array_slice($historial, -10)
                );

                $modeloUtilizado = 'qwen3:14b';

            } catch (Throwable $errorModelo) {
                Log::warning('Ollama no disponible desde el chat web.', [
                    'error' => $errorModelo->getMessage(),
                ]);

                $respuesta = $resultado['respuesta'];
                $modeloUtilizado = 'respuesta_local';
            }

            // Guardar el intercambio en la sesión.
            $historial[] = [
                'rol' => 'user',
                'contenido' => $datos['mensaje'],
            ];

            $historial[] = [
                'rol' => 'assistant',
                'contenido' => $respuesta,
            ];

            $historial = array_slice($historial, -20);

            $request->session()->put('agente_historial', $historial);

            return response()->json([
                'success' => true,
                'respuesta' => $respuesta,
                'tipo' => $resultado['tipo'],
                'datos' => $resultado['datos'],
                'modelo' => $modeloUtilizado,
                'historial' => $historial,
            ]);

        } catch (Throwable $e) {
            Log::error('Error en el chat web del agente.', [
                'usuario_id' => $request->user()?->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'mensaje' => 'No fue posible procesar tu consulta.',
            ], 500);
        }
    }

    public function limpiar(Request $request)
    {
        $this->autorizar($request);

        $request->session()->forget('agente_historial');

        return response()->json([
            'success' => true,
            'mensaje' => 'Conversación reiniciada.',
        ]);
    }

    private function autorizar(Request $request): void
    {
        abort_unless(
            $request->user()
            && $request->user()->activo
            && in_array($request->user()->role, ['Administrador', 'Operador'], true),
            403,
            'No tienes permiso para utilizar el agente inteligente.'
        );
    }
}
