<?php

namespace App\Services;

use App\Models\Tarifa;
use Illuminate\Support\Str;

class CalculoTarifaService
{
    /**
     * Busca la tarifa activa correspondiente a la categoría detectada.
     */
    public function calcular(?string $categoria): ?Tarifa
    {
        if (!$categoria) {
            return null;
        }

        $categoriaNormalizada = $this->normalizar($categoria);

        $tarifas = Tarifa::where('activa', true)->get();

        foreach ($tarifas as $tarifa) {
            if (
                $this->normalizar($tarifa->categoria)
                === $categoriaNormalizada
            ) {
                return $tarifa;
            }
        }

        return null;
    }

    private function normalizar(string $categoria): string
    {
        return Str::lower(
            trim(
                Str::ascii($categoria)
            )
        );
    }
}
