# Instalación local

Estado: instrucciones preparadas, todavía no verificadas con PHP/MySQL en el entorno de creación de este paquete. No importes estos scripts sobre la base del TFG: utiliza una base vacía.

## Opción 1: Docker

Requisitos: Docker con Compose y Python 3. En Windows puedes utilizar Docker Desktop y ejecutar los comandos en PowerShell desde la carpeta del proyecto.

```bash
python scripts/setup_local.py
docker compose up --build -d
docker compose ps
```

El primer comando genera dos contraseñas aleatorias en `.env`, que está excluido de Git. Si ese archivo ya existe, se conserva. La primera construcción descarga PHP/Apache, MySQL y dependencias; necesita acceso a Internet. Después, la aplicación y sus recursos funcionan localmente sin fuentes ni bibliotecas de CDN.

Espera a que la base esté saludable y abre `http://localhost:8080`. Registra un usuario nuevo. Para detener el entorno conservando datos:

```bash
docker compose down
```

Para consultar un error:

```bash
docker compose logs web db
```

Los datos y las portadas se guardan en volúmenes. No borres `.env` mientras conserves la base de datos: sus credenciales deben seguir coincidiendo. Los scripts SQL se importan solo cuando el volumen de MySQL está vacío.

## Opción 2: PHP local o XAMPP

Requisitos previstos: PHP 8.2 o posterior con MySQLi/mysqlnd, Fileinfo y GD (JPEG, PNG y WebP), y MySQL 8.0 o posterior. El esquema usa `utf8mb4_unicode_ci`; no se ha probado en MariaDB.

1. Extrae el proyecto fuera de la carpeta pública de Apache.
2. Crea una base de datos vacía llamada `versix` con `utf8mb4`.
3. Importa `database/schema.sql` y después `database/demo.sql`, por ejemplo con MySQL Workbench o phpMyAdmin.
4. Crea un usuario de base de datos con acceso solo a `versix` (SELECT, INSERT, UPDATE y DELETE).
5. Copia `config/config.example.php` a `config/config.local.php`. Ajusta servidor, puerto, usuario y contraseña. Utiliza el puerto real de tu MySQL; no tiene por qué ser 3306.
6. Comprueba que PHP puede escribir en `storage/covers`.
7. Desde la raíz del proyecto, ejecuta:

```bash
php -S 127.0.0.1:8080 -t public
```

En Windows, si PHP no está en el PATH:

```powershell
C:\xampp\php\php.exe -S 127.0.0.1:8080 -t public
```

El directorio público debe ser **public/**. No sirvas la raíz del proyecto: ahí se encuentran configuración y almacenamiento privados.

## Verificación

```bash
node --test tests/player.test.cjs
python tests/smoke.py http://127.0.0.1:8080
```

El segundo comando crea dos usuarios y playlists de prueba en tu base local. No borra esos datos. Usa una instalación de prueba.

Comprueba también reproducción real, controles de tiempo y volumen, búsqueda, creación de playlists con una portada válida, persistencia al recargar, cierre de sesión y visualización móvil.
