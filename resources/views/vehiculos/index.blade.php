
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehículos | Peaje Inteligente</title>

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
            background: #111827;
            color: white;
            position: fixed;
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
            margin: 25px 10px 12px;
            font-weight: bold;
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

        .main {
            margin-left: 245px;
            min-height: 100vh;
        }

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

        .user {
            font-size: 13px;
            color: #475569;
        }

        .content { padding: 30px; }

        .heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .heading h1 {
            margin: 0 0 7px;
            font-size: 25px;
        }

        .heading p {
            color: #64748b;
            margin: 0;
            font-size: 14px;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-bottom: 25px;
        }

        .stat {
            background: white;
            padding: 20px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
        }

        .stat span {
            font-size: 13px;
            color: #64748b;
        }

        .stat strong {
            display: block;
            font-size: 28px;
            margin-top: 10px;
        }

        .panel {
            background: white;
            padding: 22px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            margin-bottom: 22px;
        }

        .panel h3 {
            margin: 0 0 20px;
            font-size: 17px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

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
            color: #1e293b;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #0284c7;
        }

        .field { margin-bottom: 15px; }

        .btn {
            display: inline-block;
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            text-decoration: none;
            text-align: center;
        }

        .primary {
            background: #0284c7;
            color: white;
        }

        .secondary {
            background: #e2e8f0;
            color: #334155;
        }

        .warning {
            background: #fef3c7;
            color: #92400e;
        }

        .success {
            background: #dcfce7;
            color: #166534;
        }

        .danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .filters {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr auto;
            gap: 12px;
            align-items: end;
        }

        .filters .field { margin: 0; }

        .table-wrapper { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 850px;
            font-size: 13px;
        }

        th, td {
            padding: 14px 12px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
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

        .active-badge {
            background: #dcfce7;
            color: #166534;
        }

        .inactive-badge {
            background: #fee2e2;
            color: #991b1b;
        }

        .actions {
            display: flex;
            gap: 7px;
            flex-wrap: wrap;
        }

        .edit-form {
            min-width: 260px;
            padding: 12px;
            background: #f8fafc;
            border-radius: 8px;
        }

        .edit-form input, .edit-form select {
            margin-bottom: 8px;
        }

        .alert {
            padding: 14px;
            margin-bottom: 20px;
            border-radius: 7px;
            font-size: 14px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
        }

        .pagination {
            margin-top: 20px;
        }

        .logout {
            border: 0;
            padding: 9px 13px;
            background: #dc2626;
            color: white;
            border-radius: 6px;
            cursor: pointer;
        }

        @media(max-width: 1000px) {
            .form-grid { grid-template-columns: repeat(2, 1fr); }
            .filters { grid-template-columns: 1fr 1fr; }
            .stats { grid-template-columns: 1fr 1fr; }
        }

        @media(max-width: 650px) {
            .sidebar {
                position: relative;
                width: 100%;
                min-height: auto;
            }

            .main { margin-left: 0; }
            .content { padding: 15px; }
            .form-grid, .filters, .stats {
                grid-template-columns: 1fr;
            }

            .topbar {
                padding: 15px;
                flex-wrap: wrap;
            }
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
        <a href="{{ route('vehiculos.index') }}" class="active">Vehículos</a>
        <a href="{{ route('dashboard') }}#pasos">Control de pasos</a>
        <a href="{{ route('dashboard') }}#alertas">Alertas</a>
        <a href="{{ route('dashboard') }}">Casetas</a>
        <a href="{{ route('dashboard') }}">Tarifas</a>
    </nav>

    <div class="menu-title">SISTEMA</div>
    <nav class="menu">
        <a href="{{ route('dashboard') }}">Volver al dashboard</a>
    </nav>
</aside>

<main class="main">

    <header class="topbar">
        <h2>Gestión de vehículos</h2>

        <div class="user">
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
            <div>
                <h1>Vehículos registrados</h1>
                <p>Administración del registro vehicular del sistema de peaje.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <strong>No se pudo completar la operación:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="stats">
            <div class="stat">
                <span>Total de vehículos</span>
                <strong>{{ $estadisticas['total'] }}</strong>
            </div>

            <div class="stat">
                <span>Vehículos activos</span>
                <strong>{{ $estadisticas['activos'] }}</strong>
            </div>

            <div class="stat">
                <span>Vehículos inactivos</span>
                <strong>{{ $estadisticas['inactivos'] }}</strong>
            </div>
        </div>

        @if(auth()->user()->role === 'Administrador')
            <section class="panel">
                <h3>Registrar nuevo vehículo</h3>

                <form method="POST" action="{{ route('vehiculos.store') }}">
                    @csrf

                    <div class="form-grid">

                        <div class="field">
                            <label>Placa *</label>
                            <input type="text" name="placa"
                                   value="{{ old('placa') }}"
                                   placeholder="Ej. LPZ-1021" required>
                        </div>

                        <div class="field">
                            <label>Categoría *</label>
                            <select name="categoria" required>
                                <option value="">Seleccionar categoría</option>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria }}"
                                        @selected(old('categoria') === $categoria)>
                                        {{ ucfirst($categoria) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="field">
                            <label>Marca</label>
                            <input type="text" name="marca"
                                   value="{{ old('marca') }}"
                                   placeholder="Marca del vehículo">
                        </div>

                        <div class="field">
                            <label>Modelo</label>
                            <input type="text" name="modelo"
                                   value="{{ old('modelo') }}"
                                   placeholder="Modelo">
                        </div>

                        <div class="field">
                            <label>Color</label>
                            <input type="text" name="color"
                                   value="{{ old('color') }}"
                                   placeholder="Color">
                        </div>

                    </div>

                    <button class="btn primary" type="submit">
                        Registrar vehículo
                    </button>
                </form>
            </section>
        @endif

        <section class="panel">
            <h3>Buscar y filtrar vehículos</h3>

            <form method="GET" action="{{ route('vehiculos.index') }}"
                  class="filters">

                <div class="field">
                    <label>Buscar por placa, marca o modelo</label>
                    <input type="text" name="buscar"
                           value="{{ request('buscar') }}"
                           placeholder="Escribe para buscar...">
                </div>

                <div class="field">
                    <label>Categoría</label>
                    <select name="categoria">
                        <option value="">Todas</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria }}"
                                @selected(request('categoria') === $categoria)>
                                {{ ucfirst($categoria) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label>Estado</label>
                    <select name="estado">
                        <option value="">Todos</option>
                        <option value="activo"
                            @selected(request('estado') === 'activo')>
                            Activos
                        </option>
                        <option value="inactivo"
                            @selected(request('estado') === 'inactivo')>
                            Inactivos
                        </option>
                    </select>
                </div>

                <div class="field">
                    <button class="btn primary" type="submit">
                        Buscar
                    </button>

                    <a class="btn secondary"
                       href="{{ route('vehiculos.index') }}">
                        Limpiar
                    </a>
                </div>

            </form>
        </section>

        <section class="panel">
            <h3>Listado de vehículos</h3>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Placa</th>
                            <th>Categoría</th>
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>Color</th>
                            <th>Estado</th>
                            @if(auth()->user()->role === 'Administrador')
                                <th>Acciones</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($vehiculos as $vehiculo)
                            <tr>
                                <td><strong>{{ $vehiculo->placa }}</strong></td>
                                <td>{{ ucfirst($vehiculo->categoria) }}</td>
                                <td>{{ $vehiculo->marca ?: 'No especificada' }}</td>
                                <td>{{ $vehiculo->modelo ?: 'No especificado' }}</td>
                                <td>{{ $vehiculo->color ?: 'No especificado' }}</td>

                                <td>
                                    @if($vehiculo->activo)
                                        <span class="badge active-badge">ACTIVO</span>
                                    @else
                                        <span class="badge inactive-badge">INACTIVO</span>
                                    @endif
                                </td>

                                @if(auth()->user()->role === 'Administrador')
                                    <td>
                                        <details>
                                            <summary class="btn secondary">
                                                Editar
                                            </summary>

                                            <form class="edit-form"
                                                  method="POST"
                                                  action="{{ route('vehiculos.update', $vehiculo) }}">
                                                @csrf
                                                @method('PUT')

                                                <label>Placa</label>
                                                <input type="text" name="placa"
                                                       value="{{ $vehiculo->placa }}" required>

                                                <label>Categoría</label>
                                                <select name="categoria" required>
                                                    @foreach($categorias as $categoria)
                                                        <option value="{{ $categoria }}"
                                                            @selected($vehiculo->categoria === $categoria)>
                                                            {{ ucfirst($categoria) }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                <label>Marca</label>
                                                <input type="text" name="marca"
                                                       value="{{ $vehiculo->marca }}">

                                                <label>Modelo</label>
                                                <input type="text" name="modelo"
                                                       value="{{ $vehiculo->modelo }}">

                                                <label>Color</label>
                                                <input type="text" name="color"
                                                       value="{{ $vehiculo->color }}">

                                                <button class="btn primary" type="submit">
                                                    Guardar cambios
                                                </button>
                                            </form>
                                        </details>

                                        <form method="POST"
                                              action="{{ route('vehiculos.estado', $vehiculo) }}"
                                              style="margin-top:8px">
                                            @csrf
                                            @method('PATCH')

                                            <button class="btn {{ $vehiculo->activo ? 'warning' : 'success' }}"
                                                    type="submit">
                                                {{ $vehiculo->activo ? 'Desactivar' : 'Activar' }}
                                            </button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align:center;padding:30px">
                                    No se encontraron vehículos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                {{ $vehiculos->links() }}
            </div>
        </section>

    </div>
</main>

</body>
</html>
