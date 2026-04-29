class Slideshow {
    constructor(containerId, images, hiresImages) {
        this.container = document.getElementById(containerId);
        this.images = images;
        this.hiresImages = hiresImages;
        this.currentImageIndex = 0;
        this.isAnimating = false;
        this.animationDuration = 200;
        this.reducedMotion = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches ?? false;
        
        if (!this.container) {
            console.warn(`Slideshow container not found: ${containerId}`);
            return;
        }
        
        const prevBtn = this.container.querySelector('.prev');
        const nextBtn = this.container.querySelector('.next');
        
        if (prevBtn) {
            prevBtn.addEventListener('click', () => this.changeImage(-1));
        }
        if (nextBtn) {
            nextBtn.addEventListener('click', () => this.changeImage(1));
        }
    }
    
    updateImage(index) {
        const imgElement = this.container.querySelector('.main-image img');
        const linkElement = this.container.querySelector('.main-image a');

        if (imgElement) {
            imgElement.src = this.images[index];
        }
        if (linkElement) {
            linkElement.href = this.hiresImages[index];
        }
    }

    changeImage(step) {
        if (this.images.length < 2 || this.isAnimating) return;
        
        const imgElement = this.container.querySelector('.main-image img');
        const mainImage = this.container.querySelector('.main-image');
        const nextIndex = (this.currentImageIndex + step + this.images.length) % this.images.length;

        if (!imgElement || !mainImage || this.reducedMotion) {
            this.currentImageIndex = nextIndex;
            this.updateImage(this.currentImageIndex);
            return;
        }

        this.isAnimating = true;
        const currentClass = step > 0 ? 'slide-current-left' : 'slide-current-right';
        const incomingStartClass = step > 0 ? 'slide-next-from-right' : 'slide-next-from-left';
        const incomingActiveClass = step > 0 ? 'slide-next-active-left' : 'slide-next-active-right';
        const animationClasses = [
            'slide-current-left',
            'slide-current-right',
            'slide-next-from-left',
            'slide-next-from-right',
            'slide-next-active-left',
            'slide-next-active-right',
        ];

        const incomingImage = imgElement.cloneNode(false);
        incomingImage.src = this.images[nextIndex];
        incomingImage.alt = imgElement.alt;
        incomingImage.removeAttribute('id');
        incomingImage.classList.remove(...animationClasses);
        incomingImage.classList.add('slide-image-next', incomingStartClass);

        imgElement.classList.remove(...animationClasses);
        imgElement.classList.add(currentClass);
        mainImage.appendChild(incomingImage);

        requestAnimationFrame(() => {
            incomingImage.classList.add(incomingActiveClass);
        });

        window.setTimeout(() => {
            this.currentImageIndex = nextIndex;
            imgElement.classList.add('slide-no-transition');
            this.updateImage(this.currentImageIndex);
            imgElement.classList.remove(...animationClasses);
            incomingImage.remove();

            imgElement.offsetHeight;
            requestAnimationFrame(() => {
                imgElement.classList.remove('slide-no-transition');
                this.isAnimating = false;
            });
        }, this.animationDuration);
    }
}

window.slideshows = {};

async function loadSlideshowImages(slideshowId, folder) {
    try {
        // Fetch the list of images from PHP
        const response = await fetch(`list-images.php?folder=${encodeURIComponent(folder)}`);
        const data = await response.json();
        
        if (data.error) {
            console.error(`Error loading images for ${slideshowId}:`, data.error);
            return;
        }
        
        if (!data.images || data.images.length === 0) {
            console.warn(`No images found for ${slideshowId} in folder ${folder}`);
            return;
        }
        
        const imageArray = [];
        const hiresImageArray = [];
        
        // Process each image pair
        data.images.forEach(imagePair => {
            // Use full-size for display in expanded slideshow, thumbnail only for collapsed state
            imageArray.push(imagePair.fullsize);
            hiresImageArray.push(imagePair.fullsize);
        });
        
        // Initialize the slideshow
        window.slideshows[slideshowId] = new Slideshow(slideshowId, imageArray, hiresImageArray);

        const imgElement = window.slideshows[slideshowId].container?.querySelector('.main-image img');
        const linkElement = window.slideshows[slideshowId].container?.querySelector('.main-image a');
        if (imgElement) {
            imgElement.src = imageArray[0];
        }
        if (linkElement) {
            linkElement.href = hiresImageArray[0];
        }
        
        // Set thumbnail for collapsed state (first image - use thumbnail version)
        if (data.images.length > 0) {
          const itemName = slideshowId.replace('-slideshow', '');
          setItemThumbnail(itemName, data.images[0].thumbnail);
        }
        
    } catch (error) {
        console.error(`Error loading slideshow ${slideshowId}:`, error);
    }
}

// Function to set thumbnail image for collapsed state (works for both cities and monuments)
function setItemThumbnail(itemName, imagePath) {
  // Try city- prefix first
  let container = document.getElementById(`city-${itemName}`);
  // If not found, try monument- prefix
  if (!container) {
    container = document.getElementById(`monument-${itemName}`);
  }
  
  if (!container) return;
  
  const thumbnail = container.querySelector('.city-thumbnail img');
  if (thumbnail) {
    thumbnail.src = imagePath;
    thumbnail.alt = itemName;
  }
}

function initializeSlideshowWhenNeeded(container) {
    if (container.dataset.slideshowLoaded === 'true') return;

    container.dataset.slideshowLoaded = 'true';
    loadSlideshowImages(container.id, container.dataset.imageFolder);
}

window.initializeSlideshowWhenNeeded = initializeSlideshowWhenNeeded;

const slideshowContainers = document.querySelectorAll('.slideshow-container[data-image-folder]');
if ('IntersectionObserver' in window) {
    const slideshowObserver = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;

            initializeSlideshowWhenNeeded(entry.target);
            slideshowObserver.unobserve(entry.target);
        });
    }, {
        rootMargin: '700px 0px',
        threshold: 0.01,
    });

    slideshowContainers.forEach(container => slideshowObserver.observe(container));
} else {
    slideshowContainers.forEach(initializeSlideshowWhenNeeded);
}

document.querySelectorAll('.download-version-select').forEach(select => {
    const link = select.closest('.download-texture')?.querySelector('.download-selected-version');
    if (!link) return;

    select.addEventListener('change', () => {
        link.href = select.value;
    });
});
