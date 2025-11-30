// Accordion functionality for Cities and Monuments
document.addEventListener('DOMContentLoaded', () => {
  // Initialize all accordions (cities and monuments use the same structure)
  const cityContainers = document.querySelectorAll('.city-container');
  
  // Function to collapse an item (city or monument)
  function collapseCity(container) {
    const toggleBtn = container.querySelector('.city-toggle-btn');
    const expandedContent = container.querySelector('.city-expanded');
    const collapsedInfo = container.querySelector('.city-collapsed-info');
    const attributesList = container.querySelector('.city-attributes');
    
    if (!toggleBtn || !expandedContent) return;
    
    // Move attributes list back to collapsed-info
    if (attributesList && collapsedInfo) {
      collapsedInfo.appendChild(attributesList);
    }
    
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
  
  // Function to expand an item (city or monument)
  function expandCity(container) {
    const toggleBtn = container.querySelector('.city-toggle-btn');
    const expandedContent = container.querySelector('.city-expanded');
    const attributesList = container.querySelector('.city-attributes');
    const expandedAttributesContainer = expandedContent?.querySelector('.city-expanded-attributes-container');
    
    if (!toggleBtn || !expandedContent) return;
    
    // Move attributes list to expanded layout
    if (attributesList && expandedAttributesContainer) {
      expandedAttributesContainer.appendChild(attributesList);
    }
    
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
  
  // Collapse all other items except the one being expanded
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
    const attributesList = container.querySelector('.city-attributes');
    const collapsedInfo = collapsedContent?.querySelector('.city-collapsed-info');
    const expandedAttributesContainer = expandedContent?.querySelector('.city-expanded-attributes-container');
    
    if (!toggleBtn || !collapsedContent || !expandedContent) return;
    
    // Move attributes list to collapsed-info initially
    if (attributesList && collapsedInfo) {
      collapsedInfo.appendChild(attributesList);
    }
    
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

