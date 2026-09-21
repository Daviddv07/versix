"""Genera seis bucles sintéticos y portadas SVG reproducibles, sin material musical externo."""
from pathlib import Path
import array
import math
import sys
import wave

ROOT = Path(__file__).resolve().parents[1]
TRACKS = [
    ('Horizonte', 'Aurora Lab', (220, 277.18, 329.63), '#ed19a6'),
    ('Órbita', 'Aurora Lab', (261.63, 329.63, 392), '#923eff'),
    ('Brisa', 'Costa Digital', (196, 246.94, 293.66), '#00b8c4'),
    ('Marea', 'Costa Digital', (174.61, 220, 261.63), '#147bed'),
    ('Pulso', 'Neón Estudio', (164.81, 196, 246.94), '#ff674e'),
    ('Medianoche', 'Neón Estudio', (146.83, 174.61, 220), '#b027e8'),
]

def cover(label, color, number):
    return f'''<svg xmlns="http://www.w3.org/2000/svg" width="600" height="600" viewBox="0 0 600 600"><rect width="600" height="600" fill="#081524"/><circle cx="420" cy="175" r="250" fill="{color}" opacity=".18"/><g fill="none" stroke="{color}" stroke-width="22"><circle cx="300" cy="265" r="155"/><circle cx="300" cy="265" r="110" opacity=".65"/><circle cx="300" cy="265" r="65" opacity=".3"/></g><text x="42" y="64" fill="#fff" font-family="sans-serif" font-size="23" letter-spacing="5">VERSIX / {number}</text><text x="42" y="535" fill="#fff" font-family="sans-serif" font-size="44">{label}</text></svg>'''

for directory in ['public/audio', 'public/img', 'database']:
    (ROOT / directory).mkdir(parents=True, exist_ok=True)
for index, (title, artist, frequencies, color) in enumerate(TRACKS, 1):
    sample_rate = 22050
    samples = array.array('h')
    for n in range(12 * sample_rate):
        t = n / sample_rate
        beat = int(t * 3)
        phase = (t * 3) % 1
        envelope = math.sin(math.pi * phase) ** 2
        fade = min(1.0, t / .2, (12 - t) / .3)
        frequency = frequencies[beat % 3]
        value = .18 * envelope * math.sin(2 * math.pi * frequency * t)
        value += .055 * math.sin(2 * math.pi * frequencies[0] / 2 * t)
        samples.append(int(32767 * value * fade))
    if sys.byteorder == 'big': samples.byteswap()
    with wave.open(str(ROOT / 'public/audio' / f'{index}.wav'), 'wb') as output:
        output.setnchannels(1); output.setsampwidth(2); output.setframerate(sample_rate)
        output.writeframes(samples.tobytes())
    (ROOT / 'public/img' / f'{index}.svg').write_text(cover(title, color, f'{index:02}'), encoding='utf8')
(ROOT / 'public/img/playlist.svg').write_text(cover('Mi playlist', '#ed19a6', 'MIX'), encoding='utf8')
statements = ["-- Datos ficticios: no contiene usuarios ni contraseñas.", "SET NAMES utf8mb4;", "START TRANSACTION;", "INSERT INTO estilo (idestilo, nombre) VALUES (1, 'Electrónica de demostración');"]
for i, (name, track) in enumerate([('Aurora Lab', 1), ('Costa Digital', 3), ('Neón Estudio', 5)], 1):
    statements.append(f"INSERT INTO artista (idartista, nombre, imagen) VALUES ({i}, '{name}', 'img/{track}.svg');")
    statements.append(f"INSERT INTO album (idalbum, nombre, imagen, idartista) VALUES ({i}, 'Sesiones {i}', 'img/{track}.svg', {i});")
for i, (title, _, _, _) in enumerate(TRACKS, 1):
    artist = (i - 1) // 2 + 1
    statements.append(f"INSERT INTO cancion (idcancion, nombre, imagen, audio, idartista, idalbum, estilo_idestilo) VALUES ({i}, '{title}', 'img/{i}.svg', 'audio/{i}.wav', {artist}, {artist}, 1);")
    statements.append(f"INSERT INTO artista_tiene_cancion (idartista, idcancion) VALUES ({artist}, {i});")
statements.extend(['COMMIT;', ''])
(ROOT / 'database/demo.sql').write_text('\n'.join(statements), encoding='utf8')
print('Generados 6 audios WAV de 12 segundos, 7 portadas y datos ficticios.')
