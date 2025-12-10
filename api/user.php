<?php
require_once __DIR__ . '/../includes/auth.php';
require_authentication();

header('Content-Type: application/json');

try {
    $userId = current_user_id();
    $conn = get_db_connection();

    $query = $conn->prepare('
        SELECT 
            u.username,
            u.cash,
            COALESCE(SUM(c.currency_gigas), 0) AS gigas,
            COALESCE(COUNT(DISTINCT gm.guild_id), 0) AS guilds
        FROM users u
        LEFT JOIN characters c ON c.user_id = u.id
        LEFT JOIN guild_members gm ON gm.character_id = c.id
        WHERE u.id = ?
        GROUP BY u.id
    ');
    $query->bind_param('i', $userId);
    $query->execute();
    $result = $query->get_result()->fetch_assoc();
    $query->close();

    if (!$result) {
        echo json_encode(['error' => 'Usuário não encontrado']);
        exit;
    }

    echo json_encode([
        'username' => $result['username'],
        'cash' => (int) $result['cash'],
        'gigas' => (int) $result['gigas'],
        'guilds' => (int) $result['guilds']
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro interno']);
}
