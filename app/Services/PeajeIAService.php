<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PeajeIAService
{
    public function analizar(UploadedFile $imagen): array
    {
        $baseUrl = rtrim(
            (string) config('services.peaje_ia.url'),
            '/'
        );

        if ($baseUrl === '') {
            throw new RuntimeException(
                'No está configurada la dirección de la API de inteligencia artificial.'
            );
        }

        $rutaImagen = $imagen->getRealPath();

        if (!$rutaImagen || !file_exists($rutaImagen)) {
            throw new RuntimeException(
                'No se pudo acceder al archivo de imagen seleccionado.'
            );
        }

        $contenidoImagen = file_get_contents($rutaImagen);

        if ($contenidoImagen === false) {
            throw new RuntimeException(
                'No se pudo leer el contenido de la imagen.'
            );
        }

        $respuesta = Http::withoutVerifying()
            ->timeout(180)
            ->connectTimeout(20)
            ->acceptJson()
            ->attach(
                'archivo',
                $contenidoImagen,
                $imagen->getClientOriginalName()
            )
            ->post(
                $baseUrl . '/api/v1/pasos/frame'
            );

        if ($respuesta->failed()) {
            throw new RuntimeException(
                'La API de IA respondió con error HTTP ' .
                $respuesta->status() . ': ' .
                $respuesta->body()
            );
        }

        $datos = $respuesta->json();

        if (!is_array($datos)) {
            throw new RuntimeException(
                'La respuesta de la IA no contiene JSON válido.'
            );
        }

        return $datos;
    }
}
