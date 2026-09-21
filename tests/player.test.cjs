const test = require('node:test');
const assert = require('node:assert/strict');
const p = require('../public/player-core.js');
test('El reproductor recorre el catálogo sin depender de la cantidad de tarjetas del DOM', () => {
  assert.equal(p.nextIndex(5, 6), 0);
  assert.equal(p.nextIndex(0, 6, -1), 5);
  assert.equal(p.nextIndex(-1, 6), 0);
  assert.equal(p.nextIndex(-1, 0), -1);
});
test('Repetición y aleatorio mantienen índices válidos y evitan repetir en aleatorio', () => {
  assert.equal(p.nextIndex(3, 6, 1, 'repeat'), 3);
  for (let i = 0; i < 6; i++) for (const r of [0, .5, .99999]) {
    const next = p.nextIndex(i, 6, 1, 'random', () => r);
    assert.ok(next >= 0 && next < 6); assert.notEqual(next, i);
  }
  assert.equal(p.nextIndex(0, 1, 1, 'random'), 0);
});
test('La selección por identificador funciona con IDs no consecutivos y resultados de búsqueda', () => {
  const songs = [{id: 2}, {id: 15}, {id: 29}];
  assert.equal(p.findSong(songs, '15'), 1);
  assert.equal(p.findSong(songs, '3'), -1);
});
test('No muestra NaN cuando aún no hay metadatos de audio', () => {
  assert.equal(p.formatTime(NaN), '0:00');
  assert.equal(p.formatTime(Infinity), '0:00');
  assert.equal(p.formatTime(-1), '0:00');
  assert.equal(p.formatTime(125.8), '2:05');
});
