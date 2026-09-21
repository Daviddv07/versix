"""Pruebas HTTP con dos usuarios ficticios en una instalación LOCAL desechable.
Uso: python3 tests/smoke.py http://127.0.0.1:8080
No borra datos. Deja las dos cuentas y sus playlists para inspección.
"""
import html
import http.cookiejar
import json
import re
import secrets
import sys
import urllib.error
import urllib.parse
import urllib.request

BASE = (sys.argv[1] if len(sys.argv) > 1 else 'http://127.0.0.1:8080').rstrip('/')
if urllib.parse.urlparse(BASE).hostname not in ('localhost', '127.0.0.1', '::1'):
    raise SystemExit('Estas pruebas crean datos. Utiliza únicamente una instalación local de prueba.')

class Client:
    def __init__(self):
        self.opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()))
    def request(self, page, values=None, body=None, content_type=None):
        headers = {}
        if values is not None: body = urllib.parse.urlencode(values).encode()
        if content_type: headers['Content-Type'] = content_type
        request = urllib.request.Request(BASE + '/' + page, data=body, headers=headers)
        try: response = self.opener.open(request, timeout=15)
        except urllib.error.HTTPError as error: response = error
        raw = response.read()
        text = raw.decode('utf8', errors='replace')
        return response.status, text, response.geturl(), response.headers
    def token(self, page='login.php'):
        status, body, _, _ = self.request(page)
        assert status == 200, (page, status)
        match = re.search(r'name="csrf" value="([a-f0-9]+)"', body) or re.search(r'name="csrf-token" content="([a-f0-9]+)"', body)
        assert match, 'Falta token CSRF'
        return match.group(1)

def expect(condition, message):
    if not condition: raise AssertionError(message)
    print('PASS', message)

suffix = secrets.token_hex(5)
password = secrets.token_urlsafe(20)
alice, bob, guest = Client(), Client(), Client()
for client, name in [(alice, 'a_' + suffix), (bob, 'b_' + suffix)]:
    status, _, url, _ = client.request('registro.php', {'csrf': client.token('registro.php'), 'usuario_nombre': name, 'usuario_email': name+'@example.invalid', 'password': password, 'password_repeat': password})
    expect(status == 200 and url.endswith('/login.php'), 'Registro y creación de biblioteca: ' + name)
    status, _, url, _ = client.request('login.php', {'csrf': client.token(), 'usuario_nombre': name, 'password': password})
    expect(status == 200 and url.endswith('/inicio.php'), 'Inicio de sesión: ' + name)

status, body, _, _ = alice.request('bd_canciones.php')
catalogue = json.loads(body)['songs']
expect(status == 200 and len(catalogue) == 6, 'Catálogo JSON: seis canciones')
status, _, _, _ = guest.request('bd_canciones.php')
expect(status == 401, 'Catálogo privado sin sesión: 401')
status, _, _, _ = alice.request('guardarBiblioteca.php', {'cancionId': 1})
expect(status == 403, 'Rechazo de petición sin CSRF')
token = alice.token('inicio.php')
for _ in range(2):
    status, _, _, _ = alice.request('guardarBiblioteca.php', {'csrf': token, 'cancionId': 1})
    expect(status == 200, 'Guardar en biblioteca de forma idempotente')

playlist_name = '<script>alert(1)</script>'
status, body, url, _ = alice.request('playlist_formulario.php', {'csrf': token, 'playlist_nombre': playlist_name})
expect(status == 200 and 'playlist_pagina.php?id=' in url, 'Creación y apertura de playlist')
playlist_id = urllib.parse.parse_qs(urllib.parse.urlparse(url).query)['id'][0]
expect('&lt;script&gt;alert(1)&lt;/script&gt;' in body and playlist_name not in body, 'Escape HTML del nombre de playlist')
status, _, _, _ = alice.request('guardarEnPlaylist.php', {'csrf': token, 'cancionId': 1, 'playlistId': playlist_id})
expect(status == 200, 'Añadir canción a playlist propia')
status, _, _, _ = bob.request('playlist_pagina.php?id=' + playlist_id)
expect(status == 404, 'Otro usuario no puede leer la playlist')
status, _, _, _ = bob.request('guardarEnPlaylist.php', {'csrf': bob.token('inicio.php'), 'cancionId': 1, 'playlistId': playlist_id})
expect(status == 404, 'Otro usuario no puede modificar la playlist')
status, _, _, _ = alice.request('guardarEnPlaylist.php', {'csrf': token, 'cancionId': '1 OR 1=1', 'playlistId': playlist_id})
expect(status == 400, 'Rechazo de ID mal formado')
status, body, url, _ = guest.request('login.php', {'csrf': guest.token(), 'usuario_nombre': "' OR 1=1 -- ", 'password': 'incorrect-password'})
expect(url.endswith('login.php'), 'Entrada SQL no permite iniciar sesión')
status, _, url, _ = guest.request('inicio.php')
expect(url.endswith('login.php'), 'Un login fallido no crea sesión autenticada')

# Rechazar contenido ejecutable que finge ser una imagen.
boundary = 'VersixBoundary' + suffix
parts = [f'--{boundary}\r\nContent-Disposition: form-data; name="csrf"\r\n\r\n{token}\r\n', f'--{boundary}\r\nContent-Disposition: form-data; name="playlist_nombre"\r\n\r\nPrueba de subida\r\n', f'--{boundary}\r\nContent-Disposition: form-data; name="playlist_imagen"; filename="cover.php"\r\nContent-Type: image/jpeg\r\n\r\n<?php echo "invalid"; ?>\r\n', f'--{boundary}--\r\n']
status, body, url, _ = alice.request('playlist_formulario.php', body=''.join(parts).encode(), content_type='multipart/form-data; boundary=' + boundary)
expect('playlist_formulario.php' in url and 'Utiliza una imagen' in body, 'Rechazo de archivo disfrazado de imagen')
status, _, url, _ = alice.request('logout.php', {'csrf': token})
expect(url.endswith('login.php'), 'Cierre de sesión')
status, _, _, _ = alice.request('bd_canciones.php')
expect(status == 401, 'Sesión revocada después de salir')
print('Todas las comprobaciones HTTP han pasado. Las cuentas ficticias permanecen en la BD local.')
