
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de tarifas</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f3f4f6; color: #1f2937; }
        header { background: #111827; color: white; padding: 18px 30px; display: flex; justify-content: space-between; align-items: center; }
        header a { color: white; text-decoration: none; }
        main { max-width: 1150px; margin: 30px auto; padding: 0 20px; }
        .panel { background: white; padding: 22px; border-radius: 10px; margin-bottom: 22px; box-shadow: 0 2px 8px #0000000b; }
        h2 { margin-top: 0; }
        input { padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; width: 100%; margin: 6px 0 14px; }
        button { border: 0; border-radius: 6px; padding: 9px 13px; cursor: pointer; background: #2563eb; color: white; }
        button:hover { opacity: .9; }
        .danger { background: #dc2626; }
        .secondary { background: #4b5563; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 13px; border-bottom: 1px solid #e5e7eb; }
        th { background: #f9fafb; }
        .status { padding: 5px 9px; border-radius: 20px; font-size: 12px; background: #dcfce7; color: #166534; }
        .inactive { background: #fee2e2; color: #991b1b; }
        .message { background: #dcfce7; color: #166534; padding: 12px; border-radius: 6px; margin-bottom: 15px; }
        .error { color: #dc2626; font-size: 13px; }
        .actions { display: flex; gap: 7px; flex-wrap: wrap; }
        .inline-form { display: inline; }
        @media(max-width:700px) { .table-wrap { overflow-x: auto; } }
    </style>
</head>
<body>
<header>
    <strong>Sistema Inteligente de Control de Peaje</strong>
    <a href="{{ route('dashboard') }}">Volver al dashboard</a>
</header>

<main>
    <h1>Administración de tarifas</h1>
    <p>Configuración de los montos por categoría de vehículo.</p>

    @if(session('success'))
        <div class="message">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="panel">
            @foreach($errors->all() as $error)
                <div class="error">{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @if(auth()->user()->role === 'Administrador')
        <section class="panel">
            <h2>Registrar nueva tarifa</h2>

            <form action="{{ route('tarifas.store') }}" method="POST">
                @csrf

                <label>Categoría</label>
                <input type="text" name="categoria" value="{{ old('categoria') }}" placeholder="Ej. auto" required>

                <label>Monto (Bs)</label>
                <input type="number" name="monto" value="{{ old('monto') }}" min="0" step="0.01" required>

                <button type="submit">Registrar tarifa</button>
            </form>
        </section>
    @endif

    <section class="panel">
        <h2>Tarifas registradas</h2>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Categoría</th>
                        <th>Monto</th>
                        <th>Estado</th>
                        @if(auth()->user()->role === 'Administrador')
                            <th>Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($tarifas as $tarifa)
                        <tr>
                            <td>{{ $tarifa->id }}</td>
                            <td>{{ ucfirst($tarifa->categoria) }}</td>
                            <td>Bs {{ number_format((float) $tarifa->monto, 2) }}</td>
                            <td>
                                <span class="status {{ $tarifa->activa ? '' : 'inactive' }}">
                                    {{ $tarifa->activa ? 'Activa' : 'Inactiva' }}
                                </span>
                            </td>

                            @if(auth()->user()->role === 'Administrador')
                                <td>
                                    <div class="actions">
                                        <form action="{{ route('tarifas.update', $tarifa) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="text" name="categoria" value="{{ $tarifa->categoria }}" required>
                                            <input type="number" name="monto" value="{{ $tarifa->monto }}" min="0" step="0.01" required>
                                            <button type="submit" class="secondary">Guardar cambios</button>
                                        </form>

                                        <form class="inline-form" action="{{ route('tarifas.estado', $tarifa) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="{{ $tarifa->activa ? 'danger' : '' }}">
                                                {{ $tarifa->activa ? 'Desactivar' : 'Activar' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No existen tarifas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top:20px">
            {{ $tarifas->links() }}
        </div>
    </section>
</main>
</body>
</html>
