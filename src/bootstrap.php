<?php
declare(strict_types=1);

ini_set('display_errors', '0');
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
session_set_cookie_params([
    'httponly' => true,
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'samesite' => 'Lax',
]);
session_start();
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: same-origin');
header('Cache-Control: no-store');
header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; media-src 'self'; style-src 'self'; script-src 'self'; frame-ancestors 'none'; base-uri 'none'; form-action 'self'");

set_exception_handler(function (Throwable $error): void {
    error_log('Versix: ' . $error->getMessage());
    http_response_code(500);
    if (defined('VERSIX_API')) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'No se pudo completar la operación.']);
    } else {
        echo 'No se pudo completar la operación. Revisa la configuración local y el registro del servidor.';
    }
});

function db(): mysqli {
    static $db;
    if ($db instanceof mysqli) return $db;
    $file = __DIR__ . '/../config/config.local.php';
    $config = is_file($file) ? require $file : [];
    $get = static fn(string $key, string $env, mixed $default = null): mixed => getenv($env) !== false ? getenv($env) : ($config[$key] ?? $default);
    $password = $get('password', 'DB_PASSWORD');
    if ($password === null) throw new RuntimeException('Configura config/config.local.php o DB_PASSWORD.');
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $db = new mysqli((string)$get('host', 'DB_HOST', '127.0.0.1'), (string)$get('username', 'DB_USER', 'versix'), (string)$password, (string)$get('database', 'DB_NAME', 'versix'), (int)$get('port', 'DB_PORT', 3306));
    $db->set_charset('utf8mb4');
    return $db;
}
function query(string $sql, string $types = '', array $values = []): mysqli_stmt {
    $stmt = db()->prepare($sql);
    if ($types !== '') $stmt->bind_param($types, ...$values);
    $stmt->execute();
    return $stmt;
}
function rows(string $sql, string $types = '', array $values = []): array {
    return query($sql, $types, $values)->get_result()->fetch_all(MYSQLI_ASSOC);
}
function e(mixed $value): string { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function field(string $key): string { return is_string($_POST[$key] ?? null) ? $_POST[$key] : ''; }
function csrf(): string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(32)); }
function csrf_input(): string { return '<input type="hidden" name="csrf" value="' . e(csrf()) . '">'; }
function fail(int $status, string $message): never {
    http_response_code($status);
    if (defined('VERSIX_API')) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => $message], JSON_UNESCAPED_UNICODE);
    } else { echo e($message); }
    exit;
}
function check_post(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Allow: POST'); fail(405, 'Método no permitido.'); }
    if (!hash_equals(csrf(), field('csrf'))) fail(403, 'La sesión del formulario ha caducado. Recarga la página.');
}
function current_user(): ?array {
    $id = $_SESSION['user_id'] ?? null;
    if (!is_int($id)) return null;
    return rows('SELECT idusuario, usuario, idbiblioteca FROM usuario WHERE idusuario = ?', 'i', [$id])[0] ?? null;
}
function require_user(): array {
    $user = current_user();
    if ($user) return $user;
    if (defined('VERSIX_API')) fail(401, 'Inicia sesión para continuar.');
    header('Location: login.php', true, 303); exit;
}
function positive_id(mixed $value): int {
    $id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($id === false) fail(400, 'Identificador no válido.');
    return $id;
}
function json_response(array $data): never {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR); exit;
}
function catalogue(): array {
    return rows('SELECT c.idcancion AS id, c.nombre AS name, c.imagen AS poster, c.audio, a.nombre AS artist, a.idartista AS artistId FROM cancion c JOIN artista a ON a.idartista = c.idartista ORDER BY c.idcancion');
}
