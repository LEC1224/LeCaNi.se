document.addEventListener('DOMContentLoaded', function() {
  const cityHeaders = document.querySelectorAll('.city-header');
  
  cityHeaders.forEach(header => {
    header.addEventListener('click', () => {
      const content = header.nextElementSibling;
      const isActive = header.classList.contains('active');
      
      // Close all other open cities
      document.querySelectorAll('.city-header.active').forEach(activeHeader => {
        if (activeHeader !== header) {
          activeHeader.classList.remove('active');
          activeHeader.nextElementSibling.classList.remove('active');
        }
      });
      
      // Toggle current city
      header.classList.toggle('active');
      content.classList.toggle('active');
      
      // If opening this city, scroll it into view
      if (!isActive) {
        header.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });
}); 