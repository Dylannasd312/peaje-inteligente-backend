<?php

namespace App\Http\Controllers;

use App\Models\Caseta;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CasetaController extends Controller
{
    public function index()
    {
        $casetas = Caseta::orderByDesc('id')->paginate(10);

        return view('casetas.index', compact('casetas'));
    }

    public function store(Request $request)
    {
        $this->soloAdministrador();

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'ubicacion' => ['nullable', 'string', 'max:255'],
        ]);

        $datos['activa'] = true;

        Caseta::create($datos);

        return redirect()->route('casetas.index')
            ->with('success', 'Caseta registrada correctamente.');
    }

    public function update(Request $request, Caseta $caseta)
    {
        $this->soloAdministrador();

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'ubicacion' => ['nullable', 'string', 'max:255'],
        ]);

        $caseta->update($datos);

        return redirect()->route('casetas.index')
            ->with('success', 'Caseta actualizada correctamente.');
    }

    public function cambiarEstado(Caseta $caseta)
    {
        $this->soloAdministrador();

        $caseta->update([
            'activa' => !$caseta->activa,
        ]);

        return redirect()->route('casetas.index')
            ->with('success', 'Estado de la caseta actualizado.');
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
