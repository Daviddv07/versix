/* Funciones puras del reproductor: utilizables en el navegador y en Node para pruebas. */
(function (root) {
  'use strict';
  const api = {
    nextIndex(index, count, direction = 1, mode = 'next', random = Math.random) {
      if (count <= 0) return -1;
      if (index < 0) return direction < 0 ? count - 1 : 0;
      if (mode === 'repeat') return index;
      if (mode === 'random' && count > 1) return (index + 1 + Math.floor(random() * (count - 1))) % count;
      return (index + direction + count) % count;
    },
    formatTime(seconds) {
      if (!Number.isFinite(seconds) || seconds < 0) return '0:00';
      return `${Math.floor(seconds / 60)}:${String(Math.floor(seconds % 60)).padStart(2, '0')}`;
    },
    findSong(songs, id) { return songs.findIndex(song => Number(song.id) === Number(id)); },
  };
  if (typeof module !== 'undefined' && module.exports) module.exports = api;
  else root.VersixPlayer = api;
})(typeof window !== 'undefined' ? window : this);
