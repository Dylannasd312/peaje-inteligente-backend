<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehiculo;

class VehiculoSeeder extends Seeder
{
    public function run(): void
    {
        $vehiculos = [
            ['LPZ-1001', 'auto', 'Toyota', 'Corolla', 'Blanco'],
            ['LPZ-1002', 'auto', 'Nissan', 'Sentra', 'Negro'],
            ['LPZ-1003', 'auto', 'Suzuki', 'Swift', 'Rojo'],
            ['LPZ-1004', 'moto', 'Honda', 'XR', 'Negro'],
            ['LPZ-1005', 'moto', 'Yamaha', 'FZ', 'Azul'],
            ['LPZ-1006', 'minibús', 'Toyota', 'Hiace', 'Blanco'],
            ['LPZ-1007', 'bus', 'Mercedes-Benz', 'Bus', 'Azul'],
            ['LPZ-1008', 'camión', 'Volvo', 'FH', 'Blanco'],
            ['LPZ-1009', 'auto', 'Hyundai', 'Accent', 'Gris'],
            ['LPZ-1010', 'auto', 'Kia', 'Rio', 'Rojo'],
            ['LPZ-1011', 'moto', 'Suzuki', 'GN', 'Negro'],
            ['LPZ-1012', 'minibús', 'Nissan', 'Urvan', 'Blanco'],
            ['LPZ-1013', 'camión', 'Isuzu', 'NPR', 'Blanco'],
            ['LPZ-1014', 'bus', 'Volvo', 'B12', 'Rojo'],
            ['LPZ-1015', 'auto', 'Mazda', '3', 'Azul'],
            ['LPZ-1016', 'auto', 'Chevrolet', 'Sail', 'Plata'],
            ['LPZ-1017', 'moto', 'Bajaj', 'Pulsar', 'Negro'],
            ['LPZ-1018', 'minibús', 'Toyota', 'Hiace', 'Gris'],
            ['LPZ-1019', 'camión', 'Hino', '500', 'Blanco'],
            ['LPZ-1020', 'bus', 'Scania', 'K410', 'Blanco'],
        ];

        foreach ($vehiculos as $v) {
            Vehiculo::firstOrCreate(
                ['placa' => $v[0]],
                [
                    'categoria' => $v[1],
                    'marca' => $v[2],
                    'modelo' => $v[3],
                    'color' => $v[4],
                    'activo' => true
                ]
            );
        }
    }
}
