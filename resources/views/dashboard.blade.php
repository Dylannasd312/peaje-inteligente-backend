
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Peaje Inteligente</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: #111827;
            color: white;
            padding: 25px 18px;
            flex-shrink: 0;
        }

        .brand {
            font-size: 19px;
            font-weight: bold;
            padding: 10px;
            margin-bottom: 35px;
        }

        .brand span { color: #38bdf8; }

        .menu-title {
            color: #64748b;
            font-size: 11px;
            font-weight: bold;
            margin: 25px 10px 12px;
            letter-spacing: 1px;
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

        .menu a.active,
        .menu a:hover {
            background: #1e40af;
            color: white;
        }

        .main {
            flex: 1;
            min-width: 0;
        }

        .topbar {
            background: white;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
        }

        .topbar h2 {
            margin: 0;
            font-size: 19px;
        }

        .user-info {
            font-size: 13px;
            color: #475569;
        }

        .content { padding: 30px; }

        .welcome { margin-bottom: 28px; }

        .welcome h1 {
            font-size: 25px;
            margin: 0 0 8px;
        }

        .welcome p {
            color: #64748b;
            margin: 0;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: white;
            padding: 22px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }

        .stat-label {
            color: #64748b;
            font-size: 13px;
        }

        .stat-number {
            font-size: 30px;
            font-weight: bold;
            margin-top: 12px;
        }

        .stat-detail {
            font-size: 12px;
            color: #64748b;
            margin-top: 8px;
        }

        .section {
            background: white;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 22px;
            margin-bottom: 25px;
        }

        .section h3 {
            margin: 0 0 20px;
            font-size: 17px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        th, td {
            padding: 13px 10px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        th {
            color: #64748b;
            font-weight: 600;
            background: #f8fafc;
        }

        .empty {
            color: #94a3b8;
            padding: 20px 0;
            text-align: center;
        }

        .logout {
            border: none;
            background: #dc2626;
            color: white;
            padding: 9px 15px;
            border-radius: 6px;
            cursor: pointer;
        }

        @media(max-width: 900px) {
            .stats { grid-template-columns: repeat(2, 1fr); }
            .sidebar { width: 210px; }
        }

        @media(max-width: 650px) {
            .layout { display: block; }
            .sidebar { width: 100%; }
            .stats { grid-template-columns: 1fr; }
            .content { padding: 15px; }
            .topbar { padding: 15px; }
            .section { overflow-x: auto; }
        }
    </style>
</head>

<body>
<div class="layout">

    <aside class="sidebar">
        <div class="brand">
            PEAJE <span>INTELIGENTE</span>
        </div>

        <div class="menu-title">GENERAL</div>

        <nav class="menu">
            <a href="{{ route('dashboard') }}" class="active">
                Dashboard
            </a>
        </nav>

        <div class="menu-title">GESTIÓN DEL PEAJE</div>

        <nav class="menu">
            <a href="{{ route('vehiculos.index') }}">Vehículos</a>
            <a href="{{ route('pasos.index') }}">Control de pasos</a>
            <a href="#alertas">Alertas</a>
            <a href="{{ route('casetas.index') }}">Administración de casetas</a>
            <a href="{{ route('tarifas.index') }}">Administración de tarifas</a>
            <a href="{{ route('agente.index') }}" class="nav-link">
    Agente inteligente
</a>
        </nav>

        <div class="menu-title">SISTEMA</div>

        <nav class="menu">
            <a href="#usuarios">Usuarios</a>
            <a href="#configuracion">Configuración</a>
        </nav>
    </aside>

    <main class="main">

        <header class="topbar">
            <h2>Panel de administración</h2>

            <div class="user-info">
                {{ auth()->user()->name }}
                ({{ auth()->user()->role }})

                <form method="POST"
                      action="{{ route('logout') }}"
                      style="display:inline">
                    @csrf
                    <button class="logout" type="submit">
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </header>

        <div class="content">

            <div class="welcome">
                <h1>Resumen general</h1>
                <p>Estado actual del Sistema Inteligente de Control de Peaje.</p>
            </div>

            <div class="stats">

                <div class="stat-card">
                    <div class="stat-label">Vehículos registrados</div>
                    <div class="stat-number">
                        {{ $estadisticas['vehiculos'] }}
                    </div>
                    <div class="stat-detail">
                        {{ $estadisticas['vehiculos_activos'] }} activos
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Pasos registrados</div>
                    <div class="stat-number">
                        {{ $estadisticas['pasos'] }}
                    </div>
                    <div class="stat-detail">Registros históricos</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Alertas pendientes</div>
                    <div class="stat-number">
                        {{ $estadisticas['alertas'] }}
                    </div>
                    <div class="stat-detail">Requieren atención</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Casetas configuradas</div>
                    <div class="stat-number">
                        {{ $estadisticas['casetas'] }}
                    </div>
                    <div class="stat-detail">Puntos de control</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Tarifas configuradas</div>
                    <div class="stat-number">
                        {{ $estadisticas['tarifas'] }}
                    </div>
                    <div class="stat-detail">Categorías tarifarias</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Usuario conectado</div>
                    <div class="stat-number" style="font-size:20px">
                        {{ auth()->user()->role }}
                    </div>
                    <div class="stat-detail">
                        {{ auth()->user()->email }}
                    </div>
                </div>

            </div>

            <section class="section" id="pasos">
                <h3>Últimos pasos registrados</h3>

                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Placa detectada</th>
                            <th>Categoría</th>
                            <th>Caseta</th>
                            <th>Estado</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($ultimosPasos as $paso)
                            <tr>
                                <td>{{ $paso->fecha_hora ?? $paso->created_at }}</td>
                                <td>{{ $paso->placa_detectada }}</td>
                                <td>{{ $paso->categoria_detectada }}</td>
                                <td>{{ $paso->caseta?->nombre ?? 'No asignada' }}</td>
                                <td>{{ $paso->estado }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty">
                                    Todavía no existen pasos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </section>

            <section class="section" id="alertas">
                <h3>Últimas alertas</h3>

                <table>
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($ultimasAlertas as $alerta)
                            <tr>
                                <td>{{ $alerta->tipo }}</td>
                                <td>{{ $alerta->descripcion }}</td>
                                <td>{{ $alerta->estado }}</td>
                                <td>{{ $alerta->created_at }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty">
                                    No existen alertas registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </section>

        </div>
    </main>
</div>
</body>
</html>
