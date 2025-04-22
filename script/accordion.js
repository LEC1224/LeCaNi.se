document.addEventListener('DOMContentLoaded', () => {
  const ruleTitles = document.querySelectorAll('.rule-title');
  ruleTitles.forEach(title => {
    title.addEventListener('click', () => {
      const description = title.nextElementSibling;
      const isOpen = description.style.display === 'block';

      description.style.display = isOpen ? 'none' : 'block';
      title.classList.toggle('open', !isOpen);
    });
  });
});
