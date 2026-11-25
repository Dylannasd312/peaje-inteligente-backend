<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use Illuminate\Http\Request;

class AlertaController extends Controller
{
    public function index(Request $request)
    {
        $consulta = Alerta::with(['paso.caseta', 'paso.vehiculo', 'usuario']);

        if ($request->filled('buscar')) {
            $buscar = trim($request->buscar);

            $consulta->where(function ($q) use ($buscar) {
                $q->where('tipo', 'ILIKE', "%{$buscar}%")
                    ->orWhere('descripcion', 'ILIKE', "%{$buscar}%")
                    ->orWhereHas('paso', function ($paso) use ($buscar) {
                        $paso->where('placa_detectada', 'ILIKE', "%{$buscar}%");
                    });
            });
        }

        if ($request->filled('estado')) {
            $consulta->where('estado', $request->estado);
        }

        if ($request->filled('tipo')) {
            $consulta->where('tipo', $request->tipo);
        }

        $alertas = $consulta->latest()->paginate(12)->withQueryString();

        $estadisticas = [
            'total' => Alerta::count(),
            'pendientes' => Alerta::where('estado', 'PENDIENTE')->count(),
            'atendidas' => Alerta::where('estado', 'ATENDIDA')->count(),
        ];

        $tipos = Alerta::query()
            ->select('tipo')
            ->whereNotNull('tipo')
            ->distinct()
            ->orderBy('tipo')
            ->pluck('tipo');

        return view('alertas.index', compact(
            'alertas',
            'estadisticas',
            'tipos'
        ));
    }

    public function atender(Alerta $alerta)
    {
        $alerta->update([
            'estado' => 'ATENDIDA',
        ]);

        return redirect()->route('alertas.index')
            ->with('success', 'La alerta fue marcada como atendida.');
    }
}
