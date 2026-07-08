(() => {
  const frame = document.getElementById('bluemap-frame');
  const button = frame?.querySelector('.bluemap-fullscreen');

  if (!frame || !button || !frame.requestFullscreen) return;

  button.addEventListener('click', async () => {
    try {
      if (document.fullscreenElement === frame) {
        await document.exitFullscreen();
      } else {
        await frame.requestFullscreen();
      }
    } catch (error) {
      console.warn('Kartan kunde inte växla helskärmsläge.', error);
    }
  });

  document.addEventListener('fullscreenchange', () => {
    const isFullscreen = document.fullscreenElement === frame;
    button.setAttribute('aria-label', isFullscreen ? 'Stäng helskärm' : 'Visa kartan i helskärm');
    button.setAttribute('title', isFullscreen ? 'Stäng helskärm' : 'Visa i helskärm');
    button.querySelector('span').textContent = isFullscreen ? '×' : '⛶';
  });
})();
