<?php
declare(strict_types=1);
define('VERSIX_API', true);
require __DIR__ . '/../src/bootstrap.php';
$user = require_user(); check_post();
$song = positive_id(field('cancionId')); $playlist = positive_id(field('playlistId'));
if (!rows('SELECT idplaylist FROM playlist WHERE idplaylist = ? AND idusuario = ?', 'ii', [$playlist, $user['idusuario']])) fail(404, 'Playlist no encontrada.');
if (!rows('SELECT idcancion FROM cancion WHERE idcancion = ?', 'i', [$song])) fail(404, 'Canción no encontrada.');
query('INSERT INTO cancion_playlist (idcancion, idplaylist) VALUES (?, ?) ON DUPLICATE KEY UPDATE idcancion = VALUES(idcancion)', 'ii', [$song, $playlist]);
json_response(['ok' => true, 'message' => 'Canción guardada en la playlist.']);
