<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ConfigurarAdministrador extends Command
{
    protected $signature = 'admin:configurar';

    protected $description = 'Configura las credenciales del administrador inicial';

    public function handle()
    {
        $email = 'prueba@gmail.com';

        $usuario = User::where('email', $email)->first();

        if (!$usuario) {
            $usuario = new User();
            $usuario->name = 'Administrador';
            $usuario->email = $email;
        }

        $usuario->password = Hash::make('admin123');
        $usuario->role = 'Administrador';
        $usuario->activo = true;
        $usuario->save();

        $this->info('Administrador configurado correctamente.');

        return self::SUCCESS;
    }
}
