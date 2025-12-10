<?php
require_once __DIR__ . '/includes/auth.php';

start_secure_session();

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (login_user($username, $password)) {
        header('Location: /dashboard.php');
        exit;
    }

    $error = 'Credenciais inválidas. Verifique usuário e senha.';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TBOT HQ | Login</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <div class="layout">
        <div class="panel">
            <div class="panel-header">
                <div>
                    <p class="badge">Acesso seguro</p>
                    <h1>Entrar</h1>
                    <p>Conecte-se para acessar o painel do jogador.</p>
                </div>
                <div>
                    <a class="button secondary" href="/register.php">Criar conta</a>
                </div>
            </div>
            <form method="POST">
                <div class="form-grid">
                    <div class="field">
                        <label for="username">Usuário</label>
                        <input type="text" id="username" name="username" placeholder="Seu nickname" required>
                    </div>
                    <div class="field">
                        <label for="password">Senha</label>
                        <input type="password" id="password" name="password" placeholder="Senha de acesso" required>
                    </div>
                </div>
                <div class="actions">
                    <button class="button" type="submit">Entrar</button>
                    <span class="small">Recuperação de senha disponível via administrador.</span>
                </div>
            </form>
            <?php if ($error): ?>
                <div class="feedback error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
