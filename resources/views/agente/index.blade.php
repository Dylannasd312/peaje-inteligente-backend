
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Agente Inteligente | Peaje Inteligente</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Inter, Arial, sans-serif;
            background: #f3f5f9;
            color: #202938;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 255px;
            background: #172033;
            color: #fff;
            padding: 26px 17px;
            flex-shrink: 0;
        }

        .brand {
            font-size: 19px;
            font-weight: 800;
            padding: 0 13px 32px;
            letter-spacing: .5px;
        }

        .brand span {
            color: #5b9cff;
        }

        .section-title {
            color: #8290a8;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.3px;
            padding: 18px 13px 9px;
        }

        .nav-link {
            display: block;
            color: #c4ccda;
            text-decoration: none;
            padding: 12px 13px;
            border-radius: 8px;
            margin-bottom: 4px;
            font-size: 13px;
            transition: .2s;
        }

        .nav-link:hover,
        .nav-link.active {
            background: #263957;
            color: #fff;
        }

        .nav-link.active {
            border-left: 3px solid #5b9cff;
        }

        .main {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            height: 76px;
            background: #fff;
            border-bottom: 1px solid #e7eaf0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
        }

        .topbar h1 {
            font-size: 19px;
        }

        .user-info {
            font-size: 13px;
            color: #687386;
        }

        .content {
            padding: 27px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 0;
        }

        .intro {
            margin-bottom: 20px;
        }

        .intro h2 {
            font-size: 23px;
            margin-bottom: 7px;
        }

        .intro p {
            color: #778195;
            font-size: 13px;
        }

        .chat-card {
            background: #fff;
            border: 1px solid #e7eaf0;
            border-radius: 13px;
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 510px;
            overflow: hidden;
        }

        .chat-header {
            padding: 17px 22px;
            border-bottom: 1px solid #edf0f4;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .agent-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .agent-icon {
            width: 42px;
            height: 42px;
            background: #e9f1ff;
            color: #2872d6;
            border-radius: 11px;
            display: grid;
            place-items: center;
            font-size: 19px;
            font-weight: bold;
        }

        .agent-title strong {
            display: block;
            font-size: 14px;
        }

        .agent-title small {
            display: block;
            margin-top: 4px;
            color: #8290a1;
            font-size: 11px;
        }

        .online {
            color: #159765;
            font-size: 11px;
        }

        .clear-button {
            border: 1px solid #e2e6ed;
            background: #fff;
            border-radius: 7px;
            padding: 9px 13px;
            cursor: pointer;
            color: #697386;
            font-size: 12px;
        }

        .messages {
            flex: 1;
            padding: 24px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 17px;
            min-height: 350px;
            max-height: 600px;
        }

        .message {
            display: flex;
            gap: 10px;
            max-width: 85%;
        }

        .message.user {
            align-self: flex-end;
            flex-direction: row-reverse;
        }

        .avatar {
            width: 31px;
            height: 31px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            font-size: 11px;
            font-weight: 700;
        }

        .assistant .avatar {
            background: #e8f0ff;
            color: #2872d6;
        }

        .user .avatar {
            background: #263957;
            color: #fff;
        }

        .bubble {
            background: #f2f5fa;
            padding: 13px 16px;
            border-radius: 4px 12px 12px 12px;
            font-size: 13px;
            line-height: 1.65;
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }

        .user .bubble {
            background: #286bd1;
            color: #fff;
            border-radius: 12px 4px 12px 12px;
        }

        .suggestions {
            padding: 0 23px 17px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .suggestion {
            background: #fff;
            border: 1px solid #dce4ef;
            color: #52647d;
            padding: 9px 12px;
            border-radius: 20px;
            font-size: 11px;
            cursor: pointer;
        }

        .suggestion:hover {
            border-color: #4385e6;
            color: #286bd1;
        }

        .input-area {
            border-top: 1px solid #edf0f4;
            padding: 17px 22px;
        }

        .input-wrapper {
            display: flex;
            gap: 12px;
            align-items: center;
            background: #f5f7fa;
            border: 1px solid #e1e6ed;
            padding: 7px 8px 7px 15px;
            border-radius: 10px;
        }

        .input-wrapper input {
            flex: 1;
            border: none;
            outline: none;
            background: transparent;
            font-size: 13px;
            min-width: 0;
        }

        .send-button {
            background: #286bd1;
            color: white;
            border: none;
            padding: 11px 19px;
            border-radius: 7px;
            cursor: pointer;
            font-weight: 600;
        }

        .send-button:disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        .footer-note {
            text-align: center;
            color: #9aa3b2;
            font-size: 10px;
            margin-top: 12px;
        }

        .typing {
            color: #8490a1;
            font-size: 12px;
            padding: 0 45px 12px;
        }

        @media(max-width: 850px) {
            .sidebar {
                width: 205px;
            }

            .content {
                padding: 17px;
            }
        }

        @media(max-width: 650px) {
            .sidebar {
                display: none;
            }

            .topbar {
                padding: 0 17px;
            }

            .message {
                max-width: 96%;
            }

            .messages {
                padding: 15px;
            }
        }
    </style>
</head>

<body>
<div class="layout">

    <aside class="sidebar">
        <div class="brand">PEAJE <span>INTELIGENTE</span></div>

        <div class="section-title">GENERAL</div>
        <a href="/dashboard" class="nav-link">Dashboard</a>

        <div class="section-title">GESTIÓN DEL PEAJE</div>
        <a href="/vehiculos" class="nav-link">Vehículos</a>
        <a href="/pasos" class="nav-link">Control de pasos</a>
        <a href="/alertas" class="nav-link">Alertas</a>
        <a href="/casetas" class="nav-link">Casetas</a>
        <a href="/tarifas" class="nav-link">Tarifas</a>

        <div class="section-title">INTELIGENCIA</div>
        <a href="{{ route('agente.index') }}" class="nav-link active">
            Agente inteligente
        </a>

        <div class="section-title">SISTEMA</div>
        <a href="/usuarios" class="nav-link">Usuarios</a>
        <a href="/configuracion" class="nav-link">Configuración</a>
    </aside>

    <main class="main">

        <header class="topbar">
            <h1>Agente inteligente</h1>

            <div class="user-info">
                {{ auth()->user()->name }}
                · {{ auth()->user()->role }}
            </div>
        </header>

        <section class="content">

            <div class="intro">
                <h2>Asistente de control de peaje</h2>
                <p>
                    Consulta información operativa, vehículos, pasos,
                    recaudación y actividad de las casetas.
                </p>
            </div>

            <div class="chat-card">

                <div class="chat-header">
                    <div class="agent-title">
                        <div class="agent-icon">IA</div>

                        <div>
                            <strong>Asistente inteligente</strong>
                            <small>
                                <span class="online">● Disponible</span>
                                · Consultas del sistema
                            </small>
                        </div>
                    </div>

                    <button class="clear-button" id="clearChat">
                        Nueva conversación
                    </button>
                </div>

                <div class="messages" id="messages">

                    <div class="message assistant">
                        <div class="avatar">IA</div>
                        <div class="bubble">
                            Hola, soy el asistente inteligente de Peaje Inteligente.

                            Puedo ayudarte a consultar los vehículos registrados,
                            pasos, recaudación, placas detectadas y actividad de
                            las casetas.

                            ¿Qué información necesitas?
                        </div>
                    </div>

                    @foreach($historial as $item)
                        <div class="message {{ $item['rol'] === 'user' ? 'user' : 'assistant' }}">
                            <div class="avatar">
                                {{ $item['rol'] === 'user' ? 'U' : 'IA' }}
                            </div>

                            <div class="bubble">{{ $item['contenido'] }}</div>
                        </div>
                    @endforeach

                </div>

                <div class="suggestions">
                    <button class="suggestion" data-message="¿Cuántos vehículos pasaron hoy?">
                        Pasos de hoy
                    </button>

                    <button class="suggestion" data-message="¿Cuánto se recaudó hoy?">
                        Recaudación
                    </button>

                    <button class="suggestion" data-message="¿Qué placas no están registradas?">
                        Placas no registradas
                    </button>

                    <button class="suggestion" data-message="¿Cuál fue el último vehículo detectado?">
                        Último vehículo
                    </button>
                </div>

                <div class="input-area">
                    <form id="chatForm">
                        <div class="input-wrapper">
                            <input
                                type="text"
                                id="messageInput"
                                placeholder="Escribe tu consulta..."
                                maxlength="1000"
                                autocomplete="off"
                                required
                            >

                            <button class="send-button" id="sendButton" type="submit">
                                Enviar
                            </button>
                        </div>
                    </form>

                    <div class="footer-note">
                        El asistente consulta información del sistema.
                        Las operaciones de escritura no están permitidas.
                    </div>
                </div>

            </div>

        </section>
    </main>
</div>

<script>
    const messages = document.getElementById('messages');
    const form = document.getElementById('chatForm');
    const input = document.getElementById('messageInput');
    const sendButton = document.getElementById('sendButton');
    const clearButton = document.getElementById('clearChat');

    const csrf = document.querySelector(
        'meta[name="csrf-token"]'
    ).content;

    const sendUrl = @json(route('agente.enviar'));
    const clearUrl = @json(route('agente.limpiar'));

    let conversationHistory = @json($historial);

    function scrollBottom() {
        messages.scrollTop = messages.scrollHeight;
    }

    function addMessage(role, content) {
        const container = document.createElement('div');
        container.className = `message ${role}`;

        const avatar = document.createElement('div');
        avatar.className = 'avatar';
        avatar.textContent = role === 'user' ? 'U' : 'IA';

        const bubble = document.createElement('div');
        bubble.className = 'bubble';
        bubble.textContent = content;

        container.appendChild(avatar);
        container.appendChild(bubble);

        messages.appendChild(container);
        scrollBottom();
    }

    function showTyping() {
        const element = document.createElement('div');
        element.className = 'typing';
        element.id = 'typing';
        element.textContent = 'El asistente está procesando tu consulta...';

        messages.appendChild(element);
        scrollBottom();
    }

    function hideTyping() {
        document.getElementById('typing')?.remove();
    }

    async function sendMessage(message) {
        message = message.trim();

        if (!message || sendButton.disabled) {
            return;
        }

        addMessage('user', message);

        input.value = '';
        sendButton.disabled = true;
        showTyping();

        try {
            const response = await fetch(sendUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({
                    mensaje: message
                })
            });

            const data = await response.json();

            hideTyping();

            if (!response.ok || !data.success) {
                throw new Error(
                    data.mensaje || 'No fue posible obtener una respuesta.'
                );
            }

            addMessage('assistant', data.respuesta);

            conversationHistory = data.historial || [];

        } catch (error) {
            hideTyping();
            addMessage('assistant', error.message);
        } finally {
            sendButton.disabled = false;
            input.focus();
        }
    }

    form.addEventListener('submit', function(event) {
        event.preventDefault();
        sendMessage(input.value);
    });

    document.querySelectorAll('.suggestion').forEach(button => {
        button.addEventListener('click', () => {
            sendMessage(button.dataset.message);
        });
    });

    clearButton.addEventListener('click', async () => {
        try {
            const response = await fetch(clearUrl, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf
                }
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error('No fue posible reiniciar la conversación.');
            }

            conversationHistory = [];
            messages.innerHTML = '';

            addMessage(
                'assistant',
                'Conversación nueva. ¿Qué información necesitas consultar?'
            );

        } catch (error) {
            addMessage('assistant', error.message);
        }
    });

    scrollBottom();
</script>

</body>
</html>
