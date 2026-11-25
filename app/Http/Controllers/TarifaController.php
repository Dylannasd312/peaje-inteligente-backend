<?php

namespace App\Http\Controllers;

use App\Models\Tarifa;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TarifaController extends Controller
{
    public function index()
    {
        $tarifas = Tarifa::orderBy('categoria')->paginate(10);

        return view('tarifas.index', compact('tarifas'));
    }

    public function store(Request $request)
    {
        $this->soloAdministrador();

        $datos = $request->validate([
            'categoria' => [
                'required',
                'string',
                'max:100',
                Rule::unique('tarifas', 'categoria'),
            ],
            'monto' => ['required', 'numeric', 'min:0'],
        ]);

        $datos['activa'] = true;

        Tarifa::create($datos);

        return redirect()->route('tarifas.index')
            ->with('success', 'Tarifa registrada correctamente.');
    }

    public function update(Request $request, Tarifa $tarifa)
    {
        $this->soloAdministrador();

        $datos = $request->validate([
            'categoria' => [
                'required',
                'string',
                'max:100',
                Rule::unique('tarifas', 'categoria')->ignore($tarifa->id),
            ],
            'monto' => ['required', 'numeric', 'min:0'],
        ]);

        $tarifa->update($datos);

        return redirect()->route('tarifas.index')
            ->with('success', 'Tarifa actualizada correctamente.');
    }

    public function cambiarEstado(Tarifa $tarifa)
    {
        $this->soloAdministrador();

        $tarifa->update([
            'activa' => !$tarifa->activa,
        ]);

        return redirect()->route('tarifas.index')
            ->with('success', 'Estado de la tarifa actualizado.');
    }

    private function soloAdministrador(): void
    {
        abort_unless(
            auth()->check() && auth()->user()->role === 'Administrador',
            403,
            'No tienes permiso para realizar esta acción.'
        );
    }
}
