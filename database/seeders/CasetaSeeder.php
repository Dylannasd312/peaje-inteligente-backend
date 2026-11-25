<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Caseta;

class CasetaSeeder extends Seeder
{
    public function run(): void
    {
        Caseta::firstOrCreate(
            ['nombre' => 'Caseta Principal'],
            [
                'ubicacion' => 'Acceso principal',
                'activa' => true
            ]
        );
    }
}
