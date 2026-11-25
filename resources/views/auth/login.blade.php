<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión | Peaje Inteligente</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #101827;
            font-family: Arial, sans-serif;
            color: #fff;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 38px;
            background: #1e293b;
            border-radius: 14px;
            box-shadow: 0 15px 40px rgba(0,0,0,.3);
        }

        h1 {
            margin-bottom: 8px;
            font-size: 25px;
        }

        .subtitle {
            color: #94a3b8;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin: 17px 0 8px;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 13px;
            border: 1px solid #475569;
            border-radius: 7px;
            background: #0f172a;
            color: white;
            outline: none;
        }

        input:focus {
            border-color: #38bdf8;
        }

        button {
            width: 100%;
            padding: 14px;
            margin-top: 25px;
            border: none;
            border-radius: 7px;
            background: #0284c7;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #0369a1;
        }

        .error {
            margin-top: 15px;
            color: #fca5a5;
            font-size: 13px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 15px;
            color: #cbd5e1;
            font-size: 13px;
        }

        .remember input {
            width: auto;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Peaje Inteligente</h1>
        <p class="subtitle">Ingresa tus credenciales para continuar</p>

        <form method="POST" action="{{ route('login.authenticate') }}">
            @csrf

            <label for="email">Correo electrónico</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="usuario@correo.com"
                required
                autofocus
            >

            <label for="password">Contraseña</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="Ingresa tu contraseña"
                required
            >

            <label class="remember">
                <input type="checkbox" name="remember" value="1">
                Recordar mi sesión
            </label>

            @if ($errors->any())
                <div class="error">
                    {{ $errors->first() }}
                </div>
            @endif

            <button type="submit">Iniciar sesión</button>
        </form>
    </div>
</body>
</html>
