# Estado de validación

Fecha: 21 de septiembre de 2026.

## Ejecutado en el entorno de preparación

- Cuatro pruebas unitarias Node del reproductor: cambio de pista circular, repetición/aleatorio, IDs no consecutivos y tiempos sin metadatos.
- Análisis sintáctico JavaScript con `node --check`.
- Validación de scripts Python y correspondencia entre catálogo, portadas y audio.
- Revisión estática de controles de sesión, consultas parametrizadas y acceso por propietario.

## Ejecutado en GitHub Actions

[Ejecución completada correctamente](https://github.com/Daviddv07/versix/actions/runs/35617719652), sobre el commit `4155e3a40147c873fa7c303e43ffeb2d51770761`.

- Construcción Docker y arranque de PHP 8.3 y MySQL 8.4.
- Importación del esquema y catálogo de demostración.
- Sintaxis correcta en los 17 archivos PHP.
- Cuatro pruebas unitarias Node superadas.
- Veinte comprobaciones HTTP de `tests/smoke.py` superadas.

## Pendiente de ejecutar

- Comportamiento del navegador, reproducción real y diseño móvil de la nueva edición.
- Instalación alternativa con XAMPP.
- Subida y visualización de una portada válida (sí se ha probado el rechazo de un archivo falso).

## Cobertura ejecutada de HTTP

Registro de dos usuarios; login; catálogo autenticado; rechazo de CSRF inválido; guardado idempotente; creación de playlist; escape HTML; permisos entre usuarios; rechazo de IDs mal formados; intento de inyección SQL; login fallido sin sesión autenticada; archivo ejecutable disfrazado de imagen y cierre de sesión.

## Limitaciones funcionales y operativas

- Los botones siguiente/anterior recorren el catálogo completo, también al visualizar una playlist.
- El audio se reinicia al navegar entre páginas.
- No hay recuperación de contraseña, verificación de correo, borrado de cuenta, borrado de playlists ni gestión de catálogo desde la interfaz.
- No hay limitación de intentos de login ni cuotas persistentes de almacenamiento por usuario.
- Esta configuración es local. Una demo pública requiere HTTPS, controles de abuso, límites de recursos y una revisión adicional.
- Una revisión estática y unas pruebas unitarias no constituyen una auditoría de seguridad ni una validación integral.
