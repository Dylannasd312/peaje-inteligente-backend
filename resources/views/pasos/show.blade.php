
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del paso</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f3f4f6; color: #1f2937; }
        header { background: #111827; color: white; padding: 18px 30px; }
        header a { color: white; text-decoration: none; }
        main { max-width: 1100px; margin: 30px auto; padding: 0 20px; }
        .panel { background: white; padding: 22px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 8px #0000000b; }
        .grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px; }
        .item { border-bottom: 1px solid #e5e7eb; padding: 12px 0; }
        .item small { display: block; color: #6b7280; margin-bottom: 5px; }
        .item strong { font-size: 16px; }
        .amount { color: #166534; font-size: 27px !important; }
        .message { background: #dcfce7; color: #166534; padding: 12px; border-radius: 6px; margin-bottom: 15px; }
        .warning { background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 6px; margin-bottom: 12px; }
        .btn { display: inline-block; background: #2563eb; color: white; padding: 10px 14px; border-radius: 6px; text-decoration: none; }
        .evidence { max-width: 100%; max-height: 500px; border-radius: 8px; margin-top: 12px; }
        @media(max-width:700px) { .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<header>
    <a href="{{ route('pasos.index') }}">Volver al historial de pasos</a>
</header>

<main>
    <h1>Detalle del paso #{{ $paso->id }}</h1>

    @if(session('success'))
        <div class="message">{{ session('success') }}</div>
    @endif

    <section class="panel">
        <h2>Información del vehículo</h2>

        <div class="grid">
            <div class="item">
                <small>Placa detectada</small>
                <strong>{{ $paso->placa_detectada ?: 'No detectada' }}</strong>
            </div>

            <div class="item">
                <small>Registro en el sistema</small>
                <strong>
                    {{ $paso->vehiculo ? 'Vehículo registrado' : 'Vehículo no registrado' }}
                </strong>
            </div>

            <div class="item">
                <small>Categoría detectada</small>
                <strong>{{ $paso->categoria_detectada ?: 'No identificada' }}</strong>
            </div>

            <div class="item">
                <small>Ejes estimados por IA</small>
                <strong>{{ $paso->ejes !== null ? $paso->ejes . ' ejes' : 'No disponibles' }}</strong>
            </div>

            <div class="item">
                <small>Marca</small>
                <strong>{{ $paso->vehiculo?->marca ?? 'No registrada' }}</strong>
            </div>

            <div class="item">
                <small>Modelo</small>
                <strong>{{ $paso->vehiculo?->modelo ?? 'No registrado' }}</strong>
            </div>

            <div class="item">
                <small>Color</small>
                <strong>{{ $paso->vehiculo?->color ?? 'No registrado' }}</strong>
            </div>

            <div class="item">
                <small>Estado del paso</small>
                <strong>{{ str_replace('_', ' ', $paso->estado) }}</strong>
            </div>
        </div>
    </section>

    <section class="panel">
        <h2>Información del cobro</h2>

        <div class="grid">
            <div class="item">
                <small>Tarifa aplicada</small>
                <strong>{{ $paso->tarifa?->categoria ?? 'Sin tarifa asociada' }}</strong>
            </div>

            <div class="item">
                <small>Importe registrado</small>
                @if($paso->monto_tarifa !== null)
                    <strong class="amount">
                        Bs {{ number_format((float) $paso->monto_tarifa, 2) }}
                    </strong>
                @else
                    <strong>No se aplicó ningún importe</strong>
                @endif
            </div>

            <div class="item">
                <small>Identificador de tarifa</small>
                <strong>{{ $paso->tarifa_id ?? 'No asignado' }}</strong>
            </div>

            <div class="item">
                <small>Fecha del paso</small>
                <strong>{{ $paso->fecha_hora?->format('d/m/Y H:i:s') }}</strong>
            </div>
        </div>

        @if($paso->monto_tarifa === null)
            <div class="warning">
                Este paso no tiene un importe aplicado. Verifica que exista una tarifa activa para la categoría detectada.
            </div>
        @endif
    </section>

    <section class="panel">
        <h2>Información de procesamiento</h2>

        <div class="grid">
            <div class="item">
                <small>Confianza de placa</small>
                <strong>
                    {{ $paso->confianza_placa !== null ? number_format($paso->confianza_placa * 100, 2) . '%' : 'N/D' }}
                </strong>
            </div>

            <div class="item">
                <small>Confianza de categoría</small>
                <strong>
                    {{ $paso->confianza_categoria !== null ? number_format($paso->confianza_categoria * 100, 2) . '%' : 'N/D' }}
                </strong>
            </div>

            <div class="item">
                <small>Latencia de IA</small>
                <strong>{{ $paso->latencia_ms !== null ? number_format((float) $paso->latencia_ms, 2) . ' ms' : 'N/D' }}</strong>
            </div>

            <div class="item">
                <small>Caseta</small>
                <strong>{{ $paso->caseta?->nombre ?? 'N/D' }}</strong>
            </div>

            <div class="item">
                <small>Operador</small>
                <strong>{{ $paso->usuario?->name ?? 'N/D' }}</strong>
            </div>
        </div>
    </section>


<section class="panel">
    <h2>Evidencia fotográfica</h2>

    @if($paso->imagen_evidencia)
        @php
            $rutaImagen = 'storage/' . ltrim($paso->imagen_evidencia, '/');
            $urlImagen = asset($rutaImagen);
        @endphp

        <div style="text-align: center; margin-top: 15px;">
            <a href="{{ $urlImagen }}" target="_blank" rel="noopener noreferrer">
                <img
                    src="{{ $urlImagen }}"
                    alt="Evidencia del paso {{ $paso->id }}"
                    class="evidence"
                    style="max-width: 100%; max-height: 500px; object-fit: contain;"
                >
            </a>
        </div>
    @else
        <p>No hay imagen de evidencia disponible.</p>
    @endif
</section>


    <section class="panel">
        <h2>Alertas relacionadas</h2>

        @forelse($paso->alertas as $alerta)
            <div class="warning">
                <strong>{{ $alerta->tipo }}</strong>
                <p>{{ $alerta->descripcion }}</p>
                <small>Estado: {{ $alerta->estado }}</small>
            </div>
        @empty
            <p>No existen alertas relacionadas con este paso.</p>
        @endforelse
    </section>

    <a class="btn" href="{{ route('pasos.index') }}">Volver al historial</a>
</main>

</body>
</html>
