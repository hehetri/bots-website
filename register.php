<?php
require_once __DIR__ . '/includes/auth.php';

start_secure_session();
$feedback = null;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm) {
        $errors[] = 'As senhas precisam ser iguais.';
    } else {
        $result = register_user($username, $email, $password, $_SERVER['REMOTE_ADDR'] ?? '');
        if ($result['success']) {
            $feedback = 'Cadastro concluído! Faça login para continuar.';
        } else {
            $errors = array_merge($errors, $result['errors']);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TBOT HQ | Cadastro</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <div class="layout">
        <div class="panel">
            <div class="panel-header">
                <div>
                    <p class="badge">Novo piloto</p>
                    <h1>Crie sua conta</h1>
                    <p>Prepare-se para acessar o painel e acompanhar seu progresso.</p>
                </div>
                <div>
                    <a class="button secondary" href="/index.php">Voltar ao login</a>
                </div>
            </div>
            <form method="POST">
                <div class="form-grid">
                    <div class="field">
                        <label for="username">Usuário</label>
                        <input type="text" id="username" name="username" placeholder="Seu nickname" required>
                    </div>
                    <div class="field">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" name="email" placeholder="email@dominio.com" required>
                    </div>
                    <div class="field">
                        <label for="password">Senha</label>
                        <input type="password" id="password" name="password" placeholder="Mínimo 8 caracteres" required>
                    </div>
                    <div class="field">
                        <label for="confirm_password">Confirmar senha</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                </div>
                <div class="actions">
                    <button class="button" type="submit">Cadastrar</button>
                </div>
            </form>
            <?php if ($feedback): ?>
                <div class="feedback success"><?= htmlspecialchars($feedback, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <?php if ($errors): ?>
                <div class="feedback error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
