document.addEventListener('DOMContentLoaded', () => {
    const headers = document.querySelectorAll('.accordion-header');
    headers.forEach(header => {
      header.addEventListener('click', () => {
        const body = header.nextElementSibling;
        const isOpen = body.style.display === 'block';
  
        document.querySelectorAll('.accordion-body').forEach(el => el.style.display = 'none');
        if (!isOpen) body.style.display = 'block';
      });
    });
  });
  