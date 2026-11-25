<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VehiculoController extends Controller
{
    public function index(Request $request)
    {
        $consulta = Vehiculo::query();

        if ($request->filled('buscar')) {
            $buscar = trim($request->buscar);

            $consulta->where(function ($q) use ($buscar) {
                $q->where('placa', 'ILIKE', "%{$buscar}%")
                    ->orWhere('marca', 'ILIKE', "%{$buscar}%")
                    ->orWhere('modelo', 'ILIKE', "%{$buscar}%");
            });
        }

        if ($request->filled('categoria')) {
            $consulta->where('categoria', $request->categoria);
        }

        if ($request->estado === 'activo') {
            $consulta->where('activo', true);
        } elseif ($request->estado === 'inactivo') {
            $consulta->where('activo', false);
        }

        $vehiculos = $consulta->orderBy('placa')
            ->paginate(10)
            ->withQueryString();

        $categorias = [
            'moto',
            'auto',
            'minibús',
            'camión',
            'bus',
        ];

        $estadisticas = [
            'total' => Vehiculo::count(),
            'activos' => Vehiculo::where('activo', true)->count(),
            'inactivos' => Vehiculo::where('activo', false)->count(),
        ];

        return view('vehiculos.index', compact(
            'vehiculos',
            'categorias',
            'estadisticas'
        ));
    }

    public function store(Request $request)
    {
        $this->autorizarAdministrador();

        $datos = $request->validate([
            'placa' => [
                'required',
                'string',
                'max:20',
                'unique:vehiculos,placa',
            ],
            'categoria' => [
                'required',
                Rule::in(['moto', 'auto', 'minibús', 'camión', 'bus']),
            ],
            'marca' => ['nullable', 'string', 'max:100'],
            'modelo' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:50'],
        ]);

        $datos['placa'] = strtoupper(trim($datos['placa']));
        $datos['activo'] = true;

        Vehiculo::create($datos);

        return redirect()->route('vehiculos.index')
            ->with('success', 'Vehículo registrado correctamente.');
    }

    public function update(Request $request, Vehiculo $vehiculo)
    {
        $this->autorizarAdministrador();

        $datos = $request->validate([
            'placa' => [
                'required',
                'string',
                'max:20',
                Rule::unique('vehiculos', 'placa')->ignore($vehiculo->id),
            ],
            'categoria' => [
                'required',
                Rule::in(['moto', 'auto', 'minibús', 'camión', 'bus']),
            ],
            'marca' => ['nullable', 'string', 'max:100'],
            'modelo' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:50'],
        ]);

        $datos['placa'] = strtoupper(trim($datos['placa']));

        $vehiculo->update($datos);

        return redirect()->route('vehiculos.index')
            ->with('success', 'Vehículo actualizado correctamente.');
    }

    public function cambiarEstado(Vehiculo $vehiculo)
    {
        $this->autorizarAdministrador();

        $vehiculo->update([
            'activo' => !$vehiculo->activo,
        ]);

        return redirect()->route('vehiculos.index')
            ->with(
                'success',
                $vehiculo->activo
                    ? 'Vehículo activado correctamente.'
                    : 'Vehículo desactivado correctamente.'
            );
    }

    private function autorizarAdministrador(): void
    {
        abort_unless(
            auth()->user()->role === 'Administrador',
            403,
            'No tienes permisos para realizar esta operación.'
        );
    }
}
