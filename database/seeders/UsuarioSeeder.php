<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\CorreoAutorizado;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'doc.alvaro.pinto.ma@unifranz.edu.bo';

        CorreoAutorizado::firstOrCreate(
            ['email' => $email],
            [
                'role' => 'Administrador',
                'activo' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Administrador',
                'role' => 'Administrador',
                'activo' => true,
                'password' => bin2hex(random_bytes(16)),
            ]
        );
    }
}
