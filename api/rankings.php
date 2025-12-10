<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_authentication();

header('Content-Type: application/json');

try {
    $conn = get_db_connection();

    $players = [];
    $stmtPlayers = $conn->prepare('
        SELECT c.name AS character_name, u.username, c.level, c.experience
        FROM characters c
        JOIN users u ON u.id = c.user_id
        ORDER BY c.level DESC, c.experience DESC
        LIMIT 20
    ');
    $stmtPlayers->execute();
    $resultPlayers = $stmtPlayers->get_result();
    while ($row = $resultPlayers->fetch_assoc()) {
        $players[] = [
            'character_name' => $row['character_name'],
            'username' => $row['username'],
            'level' => (int) $row['level'],
            'experience' => (int) $row['experience']
        ];
    }
    $stmtPlayers->close();

    $guilds = [];
    $stmtGuilds = $conn->prepare('
        SELECT g.name, COUNT(gm.id) AS members, COALESCE(AVG(c.level), 0) AS average_level
        FROM guilds g
        LEFT JOIN guild_members gm ON gm.guild_id = g.id
        LEFT JOIN characters c ON c.id = gm.character_id
        GROUP BY g.id
        ORDER BY average_level DESC, members DESC
        LIMIT 20
    ');
    $stmtGuilds->execute();
    $resultGuilds = $stmtGuilds->get_result();
    while ($row = $resultGuilds->fetch_assoc()) {
        $guilds[] = [
            'name' => $row['name'],
            'members' => (int) $row['members'],
            'average_level' => round((float) $row['average_level'], 1)
        ];
    }
    $stmtGuilds->close();

    echo json_encode([
        'players' => $players,
        'guilds' => $guilds
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao carregar rankings']);
}
