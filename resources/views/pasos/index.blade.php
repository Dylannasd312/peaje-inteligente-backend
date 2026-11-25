
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de pasos</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f3f4f6; color: #1f2937; }
        header { background: #111827; color: white; padding: 18px 30px; display: flex; justify-content: space-between; align-items: center; }
        header a { color: white; text-decoration: none; }
        main { max-width: 1400px; margin: 30px auto; padding: 0 20px; }
        .panel { background: white; padding: 22px; border-radius: 10px; margin-bottom: 22px; box-shadow: 0 2px 8px #0000000b; }
        .stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 22px; }
        .stat { background: white; padding: 20px; border-radius: 10px; }
        .stat strong { display: block; font-size: 27px; margin-top: 8px; }
        input, select { padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; width: 100%; margin: 6px 0 14px; }
        button, .btn { border: 0; border-radius: 6px; padding: 9px 13px; cursor: pointer; background: #2563eb; color: white; text-decoration: none; display: inline-block; }
        button:hover, .btn:hover { opacity: .9; }
        table { width: 100%; border-collapse: collapse; min-width: 1050px; }
        th, td { text-align: left; padding: 13px; border-bottom: 1px solid #e5e7eb; }
        th { background: #f9fafb; }
        .table-wrap { overflow-x: auto; }
        .status { padding: 5px 9px; border-radius: 20px; font-size: 12px; background: #dcfce7; color: #166534; }
        .warning { background: #fee2e2; color: #991b1b; }
        .message { background: #dcfce7; color: #166534; padding: 12px; border-radius: 6px; margin-bottom: 15px; }
        .error { color: #dc2626; font-size: 13px; margin-bottom: 8px; }
        .filters { display: grid; grid-template-columns: 2fr 1fr auto; gap: 12px; align-items: end; }
        .amount { font-weight: bold; white-space: nowrap; }
        @media(max-width:700px) {
            .stats { grid-template-columns: 1fr; }
            .filters { grid-template-columns: 1fr; }
            header { padding: 15px; }
        }
    </style>
</head>
<body>

<header>
    <strong>Sistema Inteligente de Control de Peaje</strong>
    <a href="{{ route('dashboard') }}">Volver al dashboard</a>
</header>

<main>
    <h1>Control de pasos vehiculares</h1>
    <p>Registro de detecciones, vehículos, evidencias y tarifas aplicadas.</p>

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

    <section class="stats">
        <div class="stat">
            Total de pasos
            <strong>{{ $estadisticas['total'] }}</strong>
        </div>

        <div class="stat">
            Pasos registrados hoy
            <strong>{{ $estadisticas['hoy'] }}</strong>
        </div>

        <div class="stat">
            Vehículos no registrados
            <strong>{{ $estadisticas['no_registrados'] }}</strong>
        </div>
    </section>

    <section class="panel">
        <h2>Procesar imagen vehicular</h2>

        <form action="{{ route('pasos.procesar') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <label>Imagen del vehículo</label>
            <input type="file" name="archivo" accept="image/*" required>

            <label>Caseta</label>
            <select name="caseta_id" required>
                <option value="">Seleccionar caseta</option>
                @foreach($casetas as $caseta)
                    <option value="{{ $caseta->id }}">
                        {{ $caseta->nombre }}
                    </option>
                @endforeach
            </select>

            <button type="submit">Analizar y registrar paso</button>
        </form>
    </section>

    <section class="panel">
        <h2>Historial de pasos</h2>

        <form method="GET" action="{{ route('pasos.index') }}" class="filters">
            <div>
                <label>Buscar por placa</label>
                <input
                    type="text"
                    name="buscar"
                    value="{{ request('buscar') }}"
                    placeholder="Ej. LPZ-1001"
                >
            </div>

            <div>
                <label>Estado</label>
                <select name="estado">
                    <option value="">Todos</option>
                    <option value="PROCESADO" @selected(request('estado') === 'PROCESADO')>Procesado</option>
                    <option value="NO_REGISTRADO" @selected(request('estado') === 'NO_REGISTRADO')>No registrado</option>
                </select>
            </div>

            <div>
                <button type="submit">Buscar</button>
                <a href="{{ route('pasos.index') }}" class="btn" style="background:#4b5563">Limpiar</a>
            </div>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Placa</th>
                        <th>Categoría</th>
                        <th>Ejes</th>
                        <th>Caseta</th>
                        <th>Tarifa aplicada</th>
                        <th>Estado</th>
                        <th>Evidencia</th>
                        <th>Detalle</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($pasos as $paso)
                        <tr>
                            <td>
                                {{ $paso->fecha_hora?->format('d/m/Y H:i') }}
                            </td>

                            <td>
                                <strong>{{ $paso->placa_detectada ?: 'No detectada' }}</strong>
                            </td>

                            <td>{{ $paso->categoria_detectada ?: 'No identificada' }}</td>

                            <td>{{ $paso->ejes ?? 'N/D' }}</td>

                            <td>{{ $paso->caseta?->nombre ?? 'N/D' }}</td>

                            <td class="amount">
                                @if($paso->monto_tarifa !== null)
                                    Bs {{ number_format((float) $paso->monto_tarifa, 2) }}
                                @else
                                    <span class="status warning">Sin tarifa</span>
                                @endif
                            </td>

                            <td>
                                <span class="status {{ $paso->estado === 'PROCESADO' ? '' : 'warning' }}">
                                    {{ str_replace('_', ' ', $paso->estado) }}
                                </span>
                            </td>


<td>
    @if($paso->imagen_evidencia)
        @php
            $urlImagen = asset(
                'storage/' . ltrim($paso->imagen_evidencia, '/')
            );
        @endphp

        <a
            class="btn"
            href="{{ $urlImagen }}"
            target="_blank"
            rel="noopener noreferrer"
        >
            Ver imagen
        </a>
    @else
        N/D
    @endif
</td>


                            <td>
                                <a class="btn" href="{{ route('pasos.show', $paso) }}">
                                    Ver detalle
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">No existen pasos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top:20px">
            {{ $pasos->links() }}
        </div>
    </section>
</main>

</body>
</html>
