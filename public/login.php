<?php
declare(strict_types=1);
require __DIR__ . '/../src/bootstrap.php';
$register = false; $message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_post();
    $username = trim(field('usuario_nombre')); $password = field('password');
    if (strlen($username) > 160 || strlen($password) > 72) {
        $message = 'Usuario o contraseña incorrectos.';
    } else {
        $user = rows('SELECT idusuario, contrasena FROM usuario WHERE usuario = ?', 's', [$username])[0] ?? null;
        // Hash ficticio para evitar una salida inmediata cuando no existe el usuario.
        $hash = $user['contrasena'] ?? '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.';
        if (password_verify($password, $hash) && $user !== null) {
            session_regenerate_id(true);
            $_SESSION = ['user_id' => (int)$user['idusuario'], 'csrf' => bin2hex(random_bytes(32))];
            header('Location: inicio.php', true, 303); exit;
        }
        $message = 'Usuario o contraseña incorrectos.';
    }
}
require __DIR__ . '/../src/auth-form.php';
