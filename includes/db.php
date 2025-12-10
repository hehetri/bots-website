<?php
function get_db_connection(): mysqli
{
    static $connection = null;
    if ($connection instanceof mysqli) {
        return $connection;
    }

    $config = include __DIR__ . '/config.php';
    $connection = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);

    if ($connection->connect_error) {
        throw new RuntimeException('Database connection failed: ' . $connection->connect_error);
    }

    $connection->set_charset('utf8mb4');

    return $connection;
}
