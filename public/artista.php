<?php
declare(strict_types=1);
require __DIR__ . '/../src/bootstrap.php';
$user = require_user(); $id = positive_id($_GET['id'] ?? null);
$artist = rows('SELECT nombre FROM artista WHERE idartista = ?', 'i', [$id])[0] ?? null;
if (!$artist) fail(404, 'Artista no encontrado.');
$pageTitle = $artist['nombre']; $pageSubtitle = 'Explora su colección de sonidos.';
$visibleSongs = array_values(array_filter(catalogue(), static fn(array $song): bool => (int)$song['artistId'] === $id));
require __DIR__ . '/../src/music-page.php';
