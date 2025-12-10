<?php
require_once __DIR__ . '/includes/auth.php';
require_authentication();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TBOT HQ | Painel</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar">
            <div class="brand">
                <span class="badge">TBOT HQ</span>
                <strong>Comando</strong>
            </div>
            <nav class="nav">
                <a href="#home">🏠 Home</a>
                <a href="#download" id="download-btn">⬇️ Download</a>
                <a href="#support">💬 Support</a>
                <a href="/logout.php">🚪 Logout</a>
            </nav>
            <div class="card">
                <h3>⭐ Ranking de Jogadores</h3>
                <div id="players" class="ranking-list"></div>
            </div>
            <div class="card">
                <h3>⭐ Ranking de Guilds</h3>
                <div id="guilds" class="ranking-list"></div>
            </div>
        </aside>
        <main class="main">
            <section class="card" id="home">
                <h2>Boas-vindas, <span id="user-name">...</span></h2>
                <p class="small">Aqui você encontra seus recursos e estatísticas mais recentes.</p>
                <div class="stats-grid" style="margin-top: 12px;">
                    <div class="card">
                        <h3>Cash</h3>
                        <p><strong id="user-cash">0</strong></p>
                    </div>
                    <div class="card">
                        <h3>Gigas</h3>
                        <p><strong id="user-gigas">0</strong></p>
                    </div>
                    <div class="card">
                        <h3>Guilds</h3>
                        <p><strong id="user-guides">0</strong></p>
                    </div>
                </div>
            </section>
            <section class="card" id="download">
                <h2>Download</h2>
                <p class="small">Baixe o cliente oficial do jogo e conecte-se aos servidores.</p>
                <button class="button" onclick="handleDownload()">Iniciar download</button>
                <p id="download-message" class="small" style="margin-top: 8px;"></p>
            </section>
            <section class="card" id="support">
                <h2>Support</h2>
                <p class="small">Precisa de ajuda? Entre em contato com a equipe ou consulte a documentação interna.</p>
                <ul class="small" style="margin-top: 8px;">
                    <li>📧 Email: suporte@tbot.local</li>
                    <li>💬 Discord: #help-desk</li>
                    <li>📖 Base de conhecimento disponível no painel.</li>
                </ul>
            </section>
        </main>
    </div>
    <script>
        async function fetchJSON(url) {
            const response = await fetch(url, { credentials: 'same-origin' });
            if (!response.ok) {
                throw new Error('Falha ao carregar dados');
            }
            return await response.json();
        }

        function renderPlayers(players) {
            const container = document.getElementById('players');
            container.innerHTML = '';
            players.forEach((player, index) => {
                const item = document.createElement('div');
                item.className = 'ranking-item';
                item.innerHTML = `<div><strong>#${index + 1} ${player.character_name}</strong><p class="small">Usuário: ${player.username}</p></div><div class="small">Lvl ${player.level}</div>`;
                container.appendChild(item);
            });
        }

        function renderGuilds(guilds) {
            const container = document.getElementById('guilds');
            container.innerHTML = '';
            guilds.forEach((guild, index) => {
                const item = document.createElement('div');
                item.className = 'ranking-item';
                item.innerHTML = `<div><strong>#${index + 1} ${guild.name}</strong><p class="small">${guild.members} membros</p></div><div class="small">Nível médio ${guild.average_level}</div>`;
                container.appendChild(item);
            });
        }

        function hydrateUser(user) {
            document.getElementById('user-name').textContent = user.username;
            document.getElementById('user-cash').textContent = user.cash;
            document.getElementById('user-gigas').textContent = user.gigas;
            document.getElementById('user-guides').textContent = user.guilds;
        }

        async function loadData() {
            try {
                const [user, rankings] = await Promise.all([
                    fetchJSON('/api/user.php'),
                    fetchJSON('/api/rankings.php')
                ]);
                hydrateUser(user);
                renderPlayers(rankings.players);
                renderGuilds(rankings.guilds);
            } catch (error) {
                console.error(error);
            }
        }

        function handleDownload() {
            const message = document.getElementById('download-message');
            message.textContent = 'Download iniciado... (use a rota /download para apontar para o cliente oficial).';
        }

        document.addEventListener('DOMContentLoaded', loadData);
    </script>
</body>
</html>
