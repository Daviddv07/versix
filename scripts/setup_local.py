"""Genera credenciales locales aleatorias sin sobrescribir una configuración previa."""
from pathlib import Path
import secrets
root = Path(__file__).resolve().parents[1]
file = root / '.env'
try:
    with file.open('x') as stream:
        stream.write('DB_PASSWORD=' + secrets.token_hex(24) + '\nDB_ROOT_PASSWORD=' + secrets.token_hex(24) + '\n')
    file.chmod(0o600)
    print('Configuración local creada. Ejecuta: docker compose up --build -d')
except FileExistsError:
    print('Ya existe .env. Se conserva la configuración actual.')
