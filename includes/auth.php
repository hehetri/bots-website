<?php
require_once __DIR__ . '/db.php';

function start_secure_session(): void
{
    $config = include __DIR__ . '/config.php';
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_name($config['session_name']);
        session_start();
    }
}

function current_user_id(): ?int
{
    start_secure_session();
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}

function login_user(string $username, string $password): bool
{
    $conn = get_db_connection();

    $stmt = $conn->prepare('SELECT id, password FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user) {
        return false;
    }

    if (!password_verify($password, $user['password'])) {
        return false;
    }

    start_secure_session();
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['last_activity'] = time();

    return true;
}

function logout_user(): void
{
    start_secure_session();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

function require_authentication(): void
{
    $userId = current_user_id();
    if (!$userId) {
        header('Location: /index.php');
        exit;
    }
}

function register_user(string $username, string $email, string $password, string $ip): array
{
    $errors = [];
    if (strlen($username) < 3 || strlen($username) > 16) {
        $errors[] = 'O nome de usuário deve ter entre 3 e 16 caracteres.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'E-mail inválido.';
    }

    if (strlen($password) < 8) {
        $errors[] = 'A senha deve ter pelo menos 8 caracteres.';
    }

    if ($errors) {
        return ['success' => false, 'errors' => $errors];
    }

    $conn = get_db_connection();

    $stmt = $conn->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
    $stmt->bind_param('ss', $username, $email);
    $stmt->execute();
    $existing = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($existing) {
        return ['success' => false, 'errors' => ['Usuário ou e-mail já cadastrado.']];
    }

    $hash = password_hash($password, PASSWORD_ARGON2ID);

    $stmt = $conn->prepare('INSERT INTO users (username, password, email, email_verified, last_ip, warnet_bonus, cash) VALUES (?, ?, ?, 0, ?, 0, 0)');
    $stmt->bind_param('ssss', $username, $hash, $email, $ip);
    $success = $stmt->execute();
    $stmt->close();

    if (!$success) {
        return ['success' => false, 'errors' => ['Erro ao registrar. Tente novamente.']];
    }

    return ['success' => true, 'errors' => []];
}
