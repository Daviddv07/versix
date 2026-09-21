'use strict';
(async () => {
  const $ = id => document.getElementById(id);
  const player = new Audio();
  player.preload = 'metadata';
  player.volume = 0.6;
  let songs = [], index = -1, mode = 'next', noticeTimer;
  const notice = message => {
    $('status').textContent = message; $('status').hidden = false;
    clearTimeout(noticeTimer); noticeTimer = setTimeout(() => { $('status').hidden = true; }, 4500);
  };
  const play = async () => {
    try { await player.play(); } catch { notice('No se pudo reproducir el audio. Vuelve a pulsar reproducir.'); }
  };
  const select = async target => {
    if (target < 0 || target >= songs.length) return;
    index = target;
    const song = songs[index];
    player.src = song.audio;
    $('poster_master_play').src = song.poster;
    $('title').textContent = song.name;
    $('artist-title').textContent = song.artist;
    $('download_music').href = song.audio;
    $('download_music').download = `versix-${song.id}.wav`;
    $('download_music').hidden = false;
    document.querySelectorAll('[data-song]').forEach(node => node.classList.toggle('is-playing', Number(node.dataset.song) === Number(song.id)));
    await play();
  };
  document.addEventListener('click', event => {
    const button = event.target.closest('[data-song]');
    if (button) select(VersixPlayer.findSong(songs, button.dataset.song));
  });
  $('masterPlay').addEventListener('click', () => {
    if (index === -1) { if (songs.length) select(0); else notice('El catálogo todavía no está disponible.'); }
    else if (player.paused) play();
    else player.pause();
  });
  for (const [id, direction] of [['back', -1], ['next', 1]]) {
    $(id).addEventListener('click', () => select(VersixPlayer.nextIndex(index, songs.length, direction, mode === 'random' ? mode : 'next')));
  }
  player.addEventListener('play', () => { $('masterPlay').textContent = 'Ⅱ'; $('masterPlay').setAttribute('aria-label', 'Pausar'); });
  player.addEventListener('pause', () => { $('masterPlay').textContent = '▶'; $('masterPlay').setAttribute('aria-label', 'Reproducir'); });
  player.addEventListener('ended', () => select(VersixPlayer.nextIndex(index, songs.length, 1, mode)));
  player.addEventListener('error', () => notice('El archivo de audio no está disponible.'));
  const updateTime = () => {
    $('currentStart').textContent = VersixPlayer.formatTime(player.currentTime);
    $('currentEnd').textContent = VersixPlayer.formatTime(player.duration);
    $('seek').value = Number.isFinite(player.duration) && player.duration > 0 ? player.currentTime / player.duration * 100 : 0;
  };
  player.addEventListener('timeupdate', updateTime);
  player.addEventListener('loadedmetadata', updateTime);
  $('seek').addEventListener('input', () => { if (Number.isFinite(player.duration)) player.currentTime = Number($('seek').value) * player.duration / 100; });
  $('vol').addEventListener('input', () => { player.volume = Number($('vol').value) / 100; });
  const modes = {next: ['→', 'secuencial'], repeat: ['↻', 'repetir canción'], random: ['⤨', 'aleatorio']};
  $('mode').addEventListener('click', () => {
    mode = {next: 'repeat', repeat: 'random', random: 'next'}[mode];
    $('mode').textContent = modes[mode][0]; $('mode').setAttribute('aria-label', `Modo de reproducción: ${modes[mode][1]}`);
    notice(`Modo ${modes[mode][1]}`);
  });
  const makeSong = song => {
    const button = document.createElement('button'); button.type = 'button'; button.className = 'songItem library-song'; button.dataset.song = song.id;
    const image = document.createElement('img'); image.src = song.poster; image.alt = '';
    const text = document.createElement('span'); const title = document.createElement('strong'); const artist = document.createElement('small');
    title.textContent = song.name; artist.textContent = song.artist; text.append(title, artist); button.append(image, text); return button;
  };
  const save = async (url, values) => {
    const response = await fetch(url, {method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: new URLSearchParams({...values, csrf: document.querySelector('meta[name="csrf-token"]').content})});
    const data = await response.json(); if (!response.ok) throw new Error(data.error || 'No se pudo guardar.'); notice(data.message); return data;
  };
  $('guardarBiblio').addEventListener('click', async () => {
    if (index === -1) return notice('Elige una canción primero.');
    const song = songs[index];
    try {
      await save('guardarBiblioteca.php', {cancionId: song.id});
      if (![...$('library-list').querySelectorAll('[data-song]')].some(node => Number(node.dataset.song) === Number(song.id))) $('library-list').append(makeSong(song));
      $('empty-library').hidden = true;
    } catch (error) { notice(error.message); }
  });
  $('guardarPlaylist').addEventListener('click', async () => {
    if (index === -1) return notice('Elige una canción primero.');
    const playlistId = $('opcionesDesplegables').value;
    if (!playlistId) return notice('Selecciona una playlist. Si no tienes ninguna, créala desde el menú.');
    try { await save('guardarEnPlaylist.php', {cancionId: songs[index].id, playlistId}); }
    catch (error) { notice(error.message); }
  });
  $('search').addEventListener('input', () => {
    const value = $('search').value.trim().toLocaleLowerCase('es'); const panel = $('search-results');
    panel.replaceChildren(); panel.hidden = value === '';
    if (!value) return;
    const matched = songs.filter(song => `${song.name} ${song.artist}`.toLocaleLowerCase('es').includes(value));
    matched.forEach(song => panel.append(makeSong(song)));
    const artists = new Map(songs.filter(song => song.artist.toLocaleLowerCase('es').includes(value)).map(song => [song.artistId, song.artist]));
    artists.forEach((name, id) => { const link = document.createElement('a'); link.href = `artista.php?id=${encodeURIComponent(id)}`; link.textContent = `Ver artista: ${name}`; panel.append(link); });
    if (!matched.length) panel.textContent = 'No se encontraron resultados.';
  });
  try {
    const response = await fetch('bd_canciones.php');
    if (!response.ok) throw new Error('No se pudo cargar el catálogo. Inicia sesión de nuevo o revisa el servidor.');
    songs = (await response.json()).songs;
  } catch (error) { notice(error.message); }
})();
