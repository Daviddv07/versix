# Estado de validación

Fecha: 21 de septiembre de 2026.

## Ejecutado en el entorno de preparación

- Cuatro pruebas unitarias Node del reproductor: cambio de pista circular, repetición/aleatorio, IDs no consecutivos y tiempos sin metadatos.
- Análisis sintáctico JavaScript con `node --check`.
- Validación de scripts Python y correspondencia entre catálogo, portadas y audio.
- Revisión estática de controles de sesión, consultas parametrizadas y acceso por propietario.

## Pendiente de ejecutar

En el entorno de preparación no se dispone de PHP, MySQL ni Docker. Por tanto, no se ha comprobado todavía:

- Sintaxis PHP mediante el intérprete.
- Construcción Docker y arranque de MySQL.
- Importación real del esquema y funcionamiento de las consultas.
- Pruebas HTTP `tests/smoke.py`.
- Comportamiento del navegador, reproducción real y diseño móvil de la nueva edición.
- Resultado de GitHub Actions: el repositorio aún no se ha publicado.

El flujo de CI incluido ejecutará el entorno Docker, la sintaxis PHP, las pruebas unitarias y las comprobaciones HTTP cuando se publique y GitHub Actions esté habilitado.

## Cobertura preparada de HTTP

Registro de dos usuarios; login; catálogo autenticado; rechazo de CSRF inválido; guardado idempotente; creación de playlist; escape HTML; permisos entre usuarios; rechazo de IDs mal formados; intento de inyección SQL; login fallido sin sesión autenticada; archivo ejecutable disfrazado de imagen y cierre de sesión.

## Limitaciones funcionales y operativas

- Los botones siguiente/anterior recorren el catálogo completo, también al visualizar una playlist.
- El audio se reinicia al navegar entre páginas.
- No hay recuperación de contraseña, verificación de correo, borrado de cuenta, borrado de playlists ni gestión de catálogo desde la interfaz.
- No hay limitación de intentos de login ni cuotas persistentes de almacenamiento por usuario.
- Esta configuración es local. Una demo pública requiere HTTPS, controles de abuso, límites de recursos y una revisión adicional.
- Una revisión estática y unas pruebas unitarias no constituyen una auditoría de seguridad ni una validación integral.
