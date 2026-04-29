(() => {
  const sections = document.querySelectorAll('.content-section[data-bg]');

  const loadBackground = section => {
    const bg = section.dataset.bg;
    if (!bg) return;

    section.style.backgroundImage = `url("${bg}")`;
    section.removeAttribute('data-bg');
  };

  if (!('IntersectionObserver' in window)) {
    sections.forEach(loadBackground);
    return;
  }

  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;

      loadBackground(entry.target);
      observer.unobserve(entry.target);
    });
  }, {
    rootMargin: '900px 0px',
    threshold: 0.01,
  });

  sections.forEach(section => observer.observe(section));
})();
