# Origen y cambios

## Proyecto original: abril de 2024

David Díaz Vílchez desarrolló Versix de forma individual y desde cero como proyecto final de DAW. La memoria y el ZIP original aportados por el autor son la fuente de esta edición. No se han modificado esos archivos de origen.

El original incluye interfaz musical, controles de reproducción, búsqueda, usuarios, biblioteca y playlists en PHP, JavaScript, CSS y MySQL. Su memoria contiene las pruebas manuales realizadas en la etapa académica.

## Edición de portfolio: septiembre de 2026

Las modificaciones de esta copia se han preparado con asistencia de Codex. No deben presentarse como parte de la entrega original de 2024 ni como pruebas realizadas por el autor en aquella fecha.

- Se conserva `public/style.css` como base visual del CSS original (se retira la fuente externa). `portfolio.css` adapta la distribución y formularios manteniendo el esquema oscuro, magenta y cian.
- Se consolidan las vistas musicales repetidas en `src/music-page.php`; las páginas de artistas pasan a una vista dinámica.
- Se reestructura el backend con funciones compartidas de conexión, autenticación y consultas preparadas.
- Se sustituye la lógica duplicada del reproductor por un controlador común y funciones puras verificables.
- Se conserva el modelo conceptual de diez tablas, pero se simplifican claves compuestas y relaciones redundantes. Se añaden claves autoincrementales, unicidad de usuario/email y rutas de audio explícitas.
- El esquema actual se instala en una base vacía. No se ofrece una migración automática de cuentas o contraseñas del TFG.
- Se retiran la página de diagnóstico `info.php`, credenciales incrustadas, cuentas originales y los MP3 e imágenes del catálogo comercial.
- Se incluyen recursos sintéticos y generadores reproducibles, documentación y pruebas.

Las capturas de `docs/images/` pertenecen a la memoria original. Sirven como evidencia histórica y no demuestran el funcionamiento de la nueva edición.

## Estructura

- `public/`: páginas y recursos servidos al navegador.
- `src/`: conexión, seguridad, tratamiento de imágenes y plantillas compartidas.
- `config/`: ejemplo de configuración; la configuración real queda fuera de Git.
- `database/`: esquema de instalación limpia y catálogo ficticio.
- `storage/covers/`: imágenes subidas, fuera del directorio público.
- `scripts/`: generación de configuración y recursos de demostración.
- `tests/`: pruebas del reproductor y comprobaciones HTTP.
- `docs/`: instalación, evidencia, recursos y resultados de validación.
