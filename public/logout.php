<?php
declare(strict_types=1);
require __DIR__ . '/../src/bootstrap.php';
check_post(); $_SESSION = [];
$params = session_get_cookie_params();
setcookie(session_name(), '', ['expires' => time() - 3600, 'path' => $params['path'], 'secure' => $params['secure'], 'httponly' => true, 'samesite' => 'Lax']);
session_destroy(); header('Location: login.php', true, 303); exit;
