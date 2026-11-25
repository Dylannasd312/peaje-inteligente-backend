
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alertas | Peaje Inteligente</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: #f1f5f9;
            color: #1e293b;
            font-family: Arial, sans-serif;
        }

        .sidebar {
            width: 245px;
            min-height: 100vh;
            position: fixed;
            background: #111827;
            color: white;
            padding: 25px 18px;
        }

        .brand {
            font-size: 19px;
            font-weight: bold;
            margin-bottom: 35px;
        }

        .brand span { color: #38bdf8; }

        .menu-title {
            color: #64748b;
            font-size: 11px;
            font-weight: bold;
            margin: 25px 10px 12px;
        }

        .menu a {
            display: block;
            text-decoration: none;
            color: #cbd5e1;
            padding: 13px;
            border-radius: 7px;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .menu a:hover, .menu a.active {
            background: #1e40af;
            color: white;
        }

        .main { margin-left: 245px; }

        .topbar {
            background: white;
            padding: 20px 30px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }

        .topbar h2 { margin: 0; font-size: 19px; }

        .content { padding: 30px; }

        .heading { margin-bottom: 25px; }

        .heading h1 {
            margin: 0 0 8px;
            font-size: 25px;
        }

        .heading p {
            color: #64748b;
            margin: 0;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-bottom: 25px;
        }

        .stat, .panel {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
        }

        .stat { padding: 20px; }

        .stat span {
            color: #64748b;
            font-size: 13px;
        }

        .stat strong {
            display: block;
            font-size: 28px;
            margin-top: 10px;
        }

        .panel {
            padding: 22px;
            margin-bottom: 25px;
        }

        .panel h3 {
            margin: 0 0 20px;
            font-size: 17px;
        }

        .filters {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr auto;
            gap: 12px;
            align-items: end;
        }

        .field { margin: 0; }

        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        input, select {
            width: 100%;
            padding: 11px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background: white;
        }

        .btn {
            display: inline-block;
            border: none;
            border-radius: 6px;
            padding: 10px 14px;
            text-decoration: none;
            cursor: pointer;
            font-size: 12px;
        }

        .primary { background: #0284c7; color: white; }
        .secondary { background: #e2e8f0; color: #334155; }
        .success { background: #15803d; color: white; }

        .alert-success {
            padding: 14px;
            border-radius: 7px;
            margin-bottom: 20px;
            background: #dcfce7;
            color: #166534;
        }

        .table-wrapper { overflow-x: auto; }

        table {
            width: 100%;
            min-width: 900px;
            border-collapse: collapse;
            font-size: 13px;
        }

        th, td {
            padding: 14px 10px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background: #f8fafc;
            color: #64748b;
        }

        .badge {
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }

        .pending { background: #fef3c7; color: #92400e; }
        .attended { background: #dcfce7; color: #166534; }

        .logout {
            border: 0;
            padding: 9px 13px;
            background: #dc2626;
            color: white;
            border-radius: 6px;
            cursor: pointer;
        }

        .pagination { margin-top: 20px; }

        @media(max-width: 1000px) {
            .filters { grid-template-columns: 1fr 1fr; }
        }

        @media(max-width: 650px) {
            .sidebar {
                position: relative;
                width: 100%;
                min-height: auto;
            }

            .main { margin-left: 0; }
            .content { padding: 15px; }
            .stats, .filters { grid-template-columns: 1fr; }
            .topbar { flex-wrap: wrap; padding: 15px; }
        }
    </style>
</head>

<body>

<aside class="sidebar">
    <div class="brand">PEAJE <span>INTELIGENTE</span></div>

    <div class="menu-title">GENERAL</div>
    <nav class="menu">
        <a href="{{ route('dashboard') }}">Dashboard</a>
    </nav>

    <div class="menu-title">GESTIÓN DEL PEAJE</div>
    <nav class="menu">
        <a href="{{ route('vehiculos.index') }}">Vehículos</a>
        <a href="{{ route('pasos.index') }}">Control de pasos</a>
        <a href="{{ route('alertas.index') }}" class="active">Alertas</a>
    </nav>
</aside>

<main class="main">

    <header class="topbar">
        <h2>Centro de alertas</h2>

        <div>
            {{ auth()->user()->name }}
            | {{ auth()->user()->role }}

            <form method="POST" action="{{ route('logout') }}"
                  style="display:inline">
                @csrf
                <button class="logout" type="submit">Cerrar sesión</button>
            </form>
        </div>
    </header>

    <div class="content">

        <div class="heading">
            <h1>Gestión de alertas</h1>
            <p>Supervisión de incidencias detectadas durante el control vehicular.</p>
        </div>

        @if(session('success'))
            <div class="alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="stats">
            <div class="stat">
                <span>Total de alertas</span>
                <strong>{{ $estadisticas['total'] }}</strong>
            </div>

            <div class="stat">
                <span>Pendientes</span>
                <strong>{{ $estadisticas['pendientes'] }}</strong>
            </div>

            <div class="stat">
                <span>Atendidas</span>
                <strong>{{ $estadisticas['atendidas'] }}</strong>
            </div>
        </div>

        <section class="panel">
            <h3>Buscar y filtrar alertas</h3>

            <form method="GET"
                  action="{{ route('alertas.index') }}"
                  class="filters">

                <div class="field">
                    <label>Buscar</label>
                    <input type="text"
                           name="buscar"
                           value="{{ request('buscar') }}"
                           placeholder="Placa, tipo o descripción">
                </div>

                <div class="field">
                    <label>Estado</label>
                    <select name="estado">
                        <option value="">Todos</option>
                        <option value="PENDIENTE"
                            @selected(request('estado') === 'PENDIENTE')>
                            Pendientes
                        </option>
                        <option value="ATENDIDA"
                            @selected(request('estado') === 'ATENDIDA')>
                            Atendidas
                        </option>
                    </select>
                </div>

                <div class="field">
                    <label>Tipo de alerta</label>
                    <select name="tipo">
                        <option value="">Todos</option>
                        @foreach($tipos as $tipo)
                            <option value="{{ $tipo }}"
                                @selected(request('tipo') === $tipo)>
                                {{ $tipo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <button class="btn primary" type="submit">Filtrar</button>
                    <a class="btn secondary"
                       href="{{ route('alertas.index') }}">Limpiar</a>
                </div>
            </form>
        </section>

        <section class="panel">
            <h3>Listado de alertas</h3>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Placa</th>
                            <th>Descripción</th>
                            <th>Caseta</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($alertas as $alerta)
                            <tr>
                                <td>{{ $alerta->created_at?->format('d/m/Y H:i') }}</td>

                                <td>{{ $alerta->tipo }}</td>

                                <td>
                                    {{ $alerta->paso?->placa_detectada ?? 'No identificada' }}
                                </td>

                                <td>{{ $alerta->descripcion }}</td>

                                <td>
                                    {{ $alerta->paso?->caseta?->nombre ?? 'No disponible' }}
                                </td>

                                <td>
                                    @if($alerta->estado === 'PENDIENTE')
                                        <span class="badge pending">PENDIENTE</span>
                                    @else
                                        <span class="badge attended">
                                            {{ $alerta->estado }}
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @if($alerta->paso)
                                        <a class="btn secondary"
                                           href="{{ route('pasos.show', $alerta->paso) }}">
                                            Ver paso
                                        </a>
                                    @endif

                                    @if($alerta->estado === 'PENDIENTE')
                                        <form method="POST"
                                              action="{{ route('alertas.atender', $alerta) }}"
                                              style="display:inline">
                                            @csrf
                                            @method('PATCH')

                                            <button class="btn success" type="submit">
                                                Atender
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7"
                                    style="text-align:center;padding:30px">
                                    No existen alertas que coincidan con los filtros.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                {{ $alertas->links() }}
            </div>
        </section>

    </div>
</main>

</body>
</html>
