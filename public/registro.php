<?php
declare(strict_types=1);
require __DIR__ . '/../src/bootstrap.php';
$register = true; $message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_post();
    $name = trim(field('usuario_nombre')); $email = trim(field('usuario_email')); $password = field('password');
    if (!preg_match('/\A[\p{L}\p{N}_ .-]{3,40}\z/u', $name)) {
        $message = 'El nombre debe tener entre 3 y 40 letras, números, espacios, puntos, guiones o guiones bajos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 254) {
        $message = 'Introduce un correo válido.';
    } elseif (preg_match('/\A.{12,}\z/us', $password) !== 1 || strlen($password) > 72) {
        $message = 'Utiliza al menos 12 caracteres y una contraseña más corta si contiene muchos símbolos.';
    } elseif (!hash_equals($password, field('password_repeat'))) {
        $message = 'Las contraseñas no coinciden.';
    } else {
        $db = db(); $db->begin_transaction();
        try {
            query('INSERT INTO biblioteca () VALUES ()'); $library = $db->insert_id;
            query('INSERT INTO usuario (usuario, email, contrasena, idbiblioteca) VALUES (?, ?, ?, ?)', 'sssi', [$name, $email, password_hash($password, PASSWORD_DEFAULT), $library]);
            $db->commit(); header('Location: login.php', true, 303); exit;
        } catch (mysqli_sql_exception $error) {
            $db->rollback();
            if ($error->getCode() !== 1062) throw $error;
            $message = 'El nombre o el correo ya está registrado.';
        }
    }
}
require __DIR__ . '/../src/auth-form.php';
