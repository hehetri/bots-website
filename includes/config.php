<?php
return [
    'db_host' => getenv('DB_HOST') ?: 'localhost',
    'db_user' => getenv('DB_USER') ?: 'root',
    'db_pass' => getenv('DB_PASS') ?: 'ascent',
    'db_name' => getenv('DB_NAME') ?: 'tbot_base',
    'session_name' => 'tbot_dashboard'
];
