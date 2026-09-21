<?php
declare(strict_types=1);
define('VERSIX_API', true);
require __DIR__ . '/../src/bootstrap.php';
$user = require_user(); check_post(); $song = positive_id(field('cancionId'));
if (!rows('SELECT idcancion FROM cancion WHERE idcancion = ?', 'i', [$song])) fail(404, 'Canción no encontrada.');
query('INSERT INTO biblioteca_tiene_cancion (idcancion, idbiblioteca) VALUES (?, ?) ON DUPLICATE KEY UPDATE idcancion = VALUES(idcancion)', 'ii', [$song, $user['idbiblioteca']]);
json_response(['ok' => true, 'message' => 'Canción guardada en tu biblioteca.']);
