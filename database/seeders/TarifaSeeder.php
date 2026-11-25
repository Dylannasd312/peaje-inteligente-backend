<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tarifa;

class TarifaSeeder extends Seeder
{
    public function run(): void
    {
        $tarifas = [
            'moto' => 2.00,
            'auto' => 5.00,
            'minibús' => 7.00,
            'camión' => 10.00,
            'bus' => 10.00
        ];

        foreach ($tarifas as $categoria => $monto) {
            Tarifa::firstOrCreate(
                ['categoria' => $categoria],
                [
                    'monto' => $monto,
                    'activa' => true
                ]
            );
        }
    }
}
