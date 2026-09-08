<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CEAN — Telão</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,600,700,800,900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
            color: white;
            min-height: 100vh;
            overflow: hidden;
        }

        .header {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(90deg, #a78bfa, #818cf8, #6366f1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header .clock {
            font-size: 2rem;
            font-weight: 700;
            color: rgba(255,255,255,0.7);
        }

        .header .date {
            font-size: 1rem;
            color: rgba(255,255,255,0.5);
        }

        .container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            padding: 30px 40px;
            height: calc(100vh - 100px);
        }

        .column {
            display: flex;
            flex-direction: column;
        }

        .column-header {
            padding: 16px 24px;
            border-radius: 16px 16px 0 0;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .column-aplicacao .column-header {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            box-shadow: 0 4px 20px rgba(59, 130, 246, 0.3);
        }

        .column-atendimento .column-header {
            background: linear-gradient(135deg, #8b5cf6, #6d28d9);
            box-shadow: 0 4px 20px rgba(139, 92, 246, 0.3);
        }

        .column-body {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(10px);
            border-radius: 0 0 16px 16px;
            border: 1px solid rgba(255,255,255,0.1);
            border-top: none;
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }

        .person-card {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 12px;
            padding: 16px 24px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            animation: slideIn 0.5s ease-out;
            transition: all 0.3s ease;
        }

        .person-card:hover {
            background: rgba(255,255,255,0.12);
            transform: scale(1.01);
        }

        .person-card .name {
            font-size: 1.4rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .person-card .time {
            font-size: 1rem;
            color: rgba(255,255,255,0.5);
            font-weight: 600;
        }

        .person-card.chamado {
            border-left: 4px solid #fbbf24;
        }

        .person-card.em_atendimento {
            border-left: 4px solid #34d399;
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .status-badge.chamado {
            background: rgba(251, 191, 36, 0.2);
            color: #fbbf24;
            border: 1px solid rgba(251, 191, 36, 0.3);
        }

        .status-badge.em_atendimento {
            background: rgba(52, 211, 153, 0.2);
            color: #34d399;
            border: 1px solid rgba(52, 211, 153, 0.3);
        }

        .empty-message {
            text-align: center;
            padding: 60px 20px;
            color: rgba(255,255,255,0.3);
            font-size: 1.2rem;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .pulse { animation: pulse 2s infinite; }
    </style>
</head>
<body>
    <div class="header">
        <h1>✨ CEAN — Centro Espírita</h1>
        <div style="text-align: right;">
            <div class="clock" id="clock">--:--:--</div>
            <div class="date" id="date">--/--/----</div>
        </div>
    </div>

    <div class="container">
        <!-- APLICAÇÃO -->
        <div class="column column-aplicacao">
            <div class="column-header">💧 Aplicação</div>
            <div class="column-body" id="col-aplicacao">
                <div class="empty-message">Aguardando chamados...</div>
            </div>
        </div>

        <!-- ATENDIMENTO -->
        <div class="column column-atendimento">
            <div class="column-header">🗣️ Atendimento</div>
            <div class="column-body" id="col-atendimento">
                <div class="empty-message">Aguardando chamados...</div>
            </div>
        </div>
    </div>

    <script>
        function updateClock() {
            const now = new Date();
            document.getElementById('clock').textContent = now.toLocaleTimeString('pt-BR');
            document.getElementById('date').textContent = now.toLocaleDateString('pt-BR');
        }

        function fetchData() {
            fetch('/api/telao')
                .then(res => res.json())
                .then(data => {
                    const aplicacao = data.chamados.filter(c => c.tipo_raw === 'APLICACAO');
                    const atendimento = data.chamados.filter(c => c.tipo_raw === 'ATENDIMENTO');

                    renderColumn('col-aplicacao', aplicacao);
                    renderColumn('col-atendimento', atendimento);
                })
                .catch(err => console.error('Erro ao buscar dados:', err));
        }

        function renderColumn(elementId, items) {
            const el = document.getElementById(elementId);

            if (items.length === 0) {
                el.innerHTML = '<div class="empty-message">Aguardando chamados...</div>';
                return;
            }

            el.innerHTML = items.map(item => {
                const statusClass = item.status === 'CHAMADO' ? 'chamado' : 'em_atendimento';
                const statusText = item.status === 'CHAMADO' ? '📢 Chamado' : '🔄 Em Atendimento';

                return `
                    <div class="person-card ${statusClass}">
                        <div>
                            <div class="name">${item.nome}</div>
                        </div>
                        <div style="text-align: right;">
                            <div class="status-badge ${statusClass}">${statusText}</div>
                            <div class="time">${item.hora}</div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Initial load
        updateClock();
        fetchData();

        // Auto-refresh
        setInterval(updateClock, 1000);
        setInterval(fetchData, 5000);
    </script>
</body>
</html>
