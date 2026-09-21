<!doctype html>
<html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= $register ? 'Crear cuenta' : 'Iniciar sesión' ?> · Versix</title><link rel="stylesheet" href="style.css"><link rel="stylesheet" href="portfolio.css"></head>
<body class="auth-body"><main class="auth-layout"><section class="auth-panel"><a class="brand" href="login.php">Versix</a><p class="eyebrow">TU MÚSICA, A TU MANERA</p><h1><?= $register ? 'Crea tu cuenta' : 'Bienvenido de nuevo' ?></h1><p class="muted">Tu biblioteca empieza aquí.</p>
<?php if ($message !== ''): ?><p class="notice" role="alert"><?= e($message) ?></p><?php endif; ?>
<form method="post"><?= csrf_input() ?>
<label for="usuario_nombre">Nombre de usuario</label><input id="usuario_nombre" name="usuario_nombre" autocomplete="username" minlength="3" maxlength="40" required value="<?= e(field('usuario_nombre')) ?>">
<?php if ($register): ?><label for="usuario_email">Correo electrónico</label><input type="email" id="usuario_email" name="usuario_email" autocomplete="email" maxlength="254" required value="<?= e(field('usuario_email')) ?>"><?php endif; ?>
<label for="password">Contraseña<?= $register ? ' (mínimo 12 caracteres)' : '' ?></label><input type="password" id="password" name="password" autocomplete="<?= $register ? 'new-password' : 'current-password' ?>" required <?= $register ? 'minlength="12"' : '' ?>>
<?php if ($register): ?><label for="password_repeat">Repite la contraseña</label><input type="password" id="password_repeat" name="password_repeat" autocomplete="new-password" required><?php endif; ?>
<button class="primary" type="submit"><?= $register ? 'Crear cuenta' : 'Entrar' ?></button></form>
<p><?= $register ? '¿Ya tienes cuenta?' : '¿Es tu primera visita?' ?> <a href="<?= $register ? 'login.php' : 'registro.php' ?>"><?= $register ? 'Inicia sesión' : 'Regístrate' ?></a></p></section><section class="auth-art" aria-label="Identidad visual de Versix"><span>V</span><h2>Encuentra tu ritmo.</h2><p>Explora. Escucha. Crea.</p></section></main></body></html>
