<?php
declare(strict_types=1);
require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/upload.php';
$user = require_user(); $message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_post(); $name = trim(field('playlist_nombre')); $cover = null;
    if ($name === '' || preg_match('/\A.{1,80}\z/us', $name) !== 1) $message = 'Escribe un nombre de entre 1 y 80 caracteres.';
    else {
        try {
            $cover = save_cover();
            query('INSERT INTO playlist (nombre, imagen, idusuario) VALUES (?, ?, ?)', 'ssi', [$name, $cover ?? '', $user['idusuario']]);
            header('Location: playlist_pagina.php?id=' . db()->insert_id, true, 303); exit;
        } catch (InvalidArgumentException $error) { $message = $error->getMessage(); }
        catch (Throwable $error) {
            if ($cover !== null) @unlink(__DIR__ . '/../storage/covers/' . $cover);
            throw $error;
        }
    }
}
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Crear playlist · Versix</title><link rel="stylesheet" href="style.css"><link rel="stylesheet" href="portfolio.css"></head><body class="auth-body"><main class="auth-layout"><section class="auth-panel"><a class="brand" href="inicio.php">Versix</a><p class="eyebrow">TU SELECCIÓN PERSONAL</p><h1>Crea una playlist</h1><p class="muted">Dale un nombre a tu próximo descubrimiento.</p>
<?php if ($message !== ''): ?><p class="notice" role="alert"><?= e($message) ?></p><?php endif; ?>
<form method="post" enctype="multipart/form-data"><?= csrf_input() ?><label for="playlist_nombre">Nombre</label><input id="playlist_nombre" name="playlist_nombre" maxlength="80" required value="<?= e(field('playlist_nombre')) ?>"><label for="playlist_imagen">Portada (opcional)</label><input type="file" id="playlist_imagen" name="playlist_imagen" accept="image/jpeg,image/png,image/webp"><p class="muted">JPG, PNG o WebP. Hasta 2 MB.</p><button class="primary" type="submit">Crear playlist</button></form><p><a href="inicio.php">Volver a mi música</a></p></section><section class="auth-art"><span>V</span><h2>Una lista. Mil momentos.</h2><p>Hazla tuya.</p></section></main></body></html>
