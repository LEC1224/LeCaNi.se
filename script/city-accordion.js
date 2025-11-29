// City Accordion functionality
document.addEventListener('DOMContentLoaded', () => {
  // Initialize all city accordions
  const cityContainers = document.querySelectorAll('.city-container');
  
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
        // Expand
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
      } else {
        // Collapse
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
    });
  });
});

