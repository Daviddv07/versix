<?php
declare(strict_types=1);
require __DIR__ . '/../src/bootstrap.php';
$user = require_user(); $id = positive_id($_GET['id'] ?? null);
$row = rows('SELECT imagen FROM playlist WHERE idplaylist = ? AND idusuario = ?', 'ii', [$id, $user['idusuario']])[0] ?? null;
if (!$row || !preg_match('/\A[a-f0-9]{32}\.jpg\z/', $row['imagen'])) fail(404, 'Imagen no encontrada.');
$file = __DIR__ . '/../storage/covers/' . $row['imagen'];
if (!is_file($file)) fail(404, 'Imagen no encontrada.');
header('Content-Type: image/jpeg'); readfile($file); exit;
