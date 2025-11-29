// City Accordion functionality
document.addEventListener('DOMContentLoaded', () => {
  // Initialize all city accordions
  const cityContainers = document.querySelectorAll('.city-container');
  
  // Function to collapse a city
  function collapseCity(container) {
    const toggleBtn = container.querySelector('.city-toggle-btn');
    const expandedContent = container.querySelector('.city-expanded');
    
    if (!toggleBtn || !expandedContent) return;
    
    container.classList.remove('city-expanded-state');
    container.classList.add('city-collapsed-state');
    expandedContent.style.maxHeight = '0';
    toggleBtn.innerHTML = 'Expandera &#9660;';
    
    // Hide after transition
    setTimeout(() => {
      if (container.classList.contains('city-collapsed-state')) {
        expandedContent.style.display = 'none';
      }
    }, 300);
  }
  
  // Function to expand a city
  function expandCity(container) {
    const toggleBtn = container.querySelector('.city-toggle-btn');
    const expandedContent = container.querySelector('.city-expanded');
    
    if (!toggleBtn || !expandedContent) return;
    
    container.classList.remove('city-collapsed-state');
    container.classList.add('city-expanded-state');
    expandedContent.style.display = 'block';
    expandedContent.style.maxHeight = '0';
    
    // Force reflow to ensure transition works
    expandedContent.offsetHeight;
    
    // Set maxHeight to actual content height
    requestAnimationFrame(() => {
      const content = expandedContent.querySelector('.city-expanded-content');
      expandedContent.style.maxHeight = (content ? content.scrollHeight : expandedContent.scrollHeight) + 'px';
    });
    
    toggleBtn.innerHTML = 'Minimera &#9650;';
  }
  
  // Collapse all other cities except the one being expanded
  function collapseAllExcept(exceptContainer) {
    cityContainers.forEach(container => {
      if (container !== exceptContainer && container.classList.contains('city-expanded-state')) {
        collapseCity(container);
      }
    });
  }
  
  cityContainers.forEach(container => {
    const toggleBtn = container.querySelector('.city-toggle-btn');
    const collapsedContent = container.querySelector('.city-collapsed');
    const expandedContent = container.querySelector('.city-expanded');
    
    if (!toggleBtn || !collapsedContent || !expandedContent) return;
    
    // Set initial state (collapsed)
    container.classList.add('city-collapsed-state');
    expandedContent.style.display = 'none';
    expandedContent.style.maxHeight = '0';
    expandedContent.style.overflow = 'hidden';
    
    // Toggle on button click
    toggleBtn.addEventListener('click', () => {
      const isCollapsed = container.classList.contains('city-collapsed-state');
      
      if (isCollapsed) {
        // Collapse all other cities first
        collapseAllExcept(container);
        
        // Small delay to allow other cities to start collapsing before expanding this one
        setTimeout(() => {
          expandCity(container);
        }, 50);
      } else {
        // Collapse this city
        collapseCity(container);
      }
    });
  });
});

