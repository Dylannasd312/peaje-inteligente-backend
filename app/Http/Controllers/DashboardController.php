<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Models\Paso;
use App\Models\Alerta;
use App\Models\Caseta;
use App\Models\Tarifa;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $estadisticas = [
            'vehiculos' => Vehiculo::count(),
            'vehiculos_activos' => Vehiculo::where('activo', true)->count(),
            'pasos' => Paso::count(),
            'alertas' => Alerta::where('estado', 'PENDIENTE')->count(),
            'casetas' => Caseta::count(),
            'tarifas' => Tarifa::count(),
        ];

        $ultimosPasos = Paso::with(['vehiculo', 'caseta'])
            ->latest()
            ->take(8)
            ->get();

        $ultimasAlertas = Alerta::latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'estadisticas',
            'ultimosPasos',
            'ultimasAlertas'
        ));
    }
}
