<?php
declare(strict_types=1);
function save_cover(): ?string {
    $file = $_FILES['playlist_imagen'] ?? null;
    if ($file === null || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    if (!is_array($file) || !is_int($file['error'] ?? null) || $file['error'] !== UPLOAD_ERR_OK) throw new InvalidArgumentException('No se pudo subir la imagen.');
    if (!is_string($file['tmp_name'] ?? null) || !is_uploaded_file($file['tmp_name'])) throw new InvalidArgumentException('Archivo no válido.');
    if (filesize($file['tmp_name']) > 2 * 1024 * 1024) throw new InvalidArgumentException('La imagen debe ocupar como máximo 2 MB.');
    $type = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!in_array($type, ['image/jpeg', 'image/png', 'image/webp'], true)) throw new InvalidArgumentException('Utiliza una imagen JPG, PNG o WebP.');
    $size = @getimagesize($file['tmp_name']);
    if (!$size || $size[0] > 3000 || $size[1] > 3000 || $size[0] * $size[1] > 6000000) throw new InvalidArgumentException('La imagen es demasiado grande (máximo 3000 px y 6 megapíxeles).');
    $image = @imagecreatefromstring(file_get_contents($file['tmp_name']));
    if ($image === false) throw new InvalidArgumentException('No se pudo leer la imagen.');
    $folder = __DIR__ . '/../storage/covers';
    if (!is_dir($folder) && !mkdir($folder, 0750, true)) throw new RuntimeException('No se pudo crear storage/covers.');
    $name = bin2hex(random_bytes(16)) . '.jpg';
    // Recodificar elimina el nombre y los metadatos del archivo original.
    $ok = imagejpeg($image, $folder . '/' . $name, 85); imagedestroy($image);
    if (!$ok) throw new RuntimeException('No se pudo guardar la imagen.');
    return $name;
}
