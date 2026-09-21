<?php
declare(strict_types=1);
require __DIR__ . '/../src/bootstrap.php';
$user = require_user(); $pageTitle = 'Tu próxima canción favorita';
$pageSubtitle = 'Descubre el catálogo, crea tus playlists y encuentra tu ritmo.';
$visibleSongs = catalogue();
require __DIR__ . '/../src/music-page.php';
