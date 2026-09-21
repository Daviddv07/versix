<?php
declare(strict_types=1);
require __DIR__ . '/../src/bootstrap.php';
$user = require_user(); $id = positive_id($_GET['id'] ?? null);
$playlist = rows('SELECT nombre FROM playlist WHERE idplaylist = ? AND idusuario = ?', 'ii', [$id, $user['idusuario']])[0] ?? null;
if (!$playlist) fail(404, 'Playlist no encontrada.');
$pageTitle = $playlist['nombre']; $pageSubtitle = 'Tu selección, a tu manera.';
$ids = array_column(rows('SELECT idcancion FROM cancion_playlist WHERE idplaylist = ?', 'i', [$id]), 'idcancion');
$visibleSongs = array_values(array_filter(catalogue(), static fn(array $song): bool => in_array($song['id'], $ids)));
require __DIR__ . '/../src/music-page.php';
