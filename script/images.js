class Slideshow {
    constructor(containerId, images, hiresImages) {
        this.container = document.getElementById(containerId);
        this.images = images;
        this.hiresImages = hiresImages;
        this.currentImageIndex = 0;
        
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
    
    changeImage(step) {
        if (this.images.length === 0) return;
        
        this.currentImageIndex += step;
        if (this.currentImageIndex >= this.images.length) this.currentImageIndex = 0;
        if (this.currentImageIndex < 0) this.currentImageIndex = this.images.length - 1;

        const imgElement = this.container.querySelector('.main-image img');
        const linkElement = this.container.querySelector('.main-image a');
        
        if (imgElement) {
            imgElement.src = this.images[this.currentImageIndex];
        }
        if (linkElement) {
            linkElement.href = this.hiresImages[this.currentImageIndex];
        }
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
        
        // Set thumbnail for collapsed state (first image - use thumbnail version)
        if (data.images.length > 0) {
          const itemName = slideshowId.replace('-slideshow', '');
          setItemThumbnail(itemName, data.images[0].thumbnail);
        }
        
    } catch (error) {
        console.error(`Error loading slideshow ${slideshowId}:`, error);
    }
}

//City slideshows
loadSlideshowImages("fabulania-slideshow", "city/fabulania");
loadSlideshowImages("therike-slideshow", "city/therike");
loadSlideshowImages("swampside-slideshow", "city/swampside");
loadSlideshowImages("newearth-slideshow", "city/newearth");
loadSlideshowImages("bergavik-slideshow", "city/bergavik");
loadSlideshowImages("meidera-slideshow", "city/meidera");
loadSlideshowImages("odoptafortet-slideshow", "city/odoptafortet");
loadSlideshowImages("yocadax-slideshow", "city/yocadax");
loadSlideshowImages("antares-slideshow", "city/antares");
loadSlideshowImages("arboria-slideshow", "city/arboria");
loadSlideshowImages("oldcity-slideshow", "city/oldcity");
loadSlideshowImages("bydelaby-slideshow", "city/bydelaby");
loadSlideshowImages("hydropolis-slideshow", "city/hydropolis");
loadSlideshowImages("vildtmarken-slideshow", "city/vildtmarken");
loadSlideshowImages("darkrune-slideshow", "city/darkrune");
loadSlideshowImages("alfheim-slideshow", "city/alfheim");
loadSlideshowImages("ldngruvorna-slideshow", "city/ldngruvorna");
loadSlideshowImages("thelady-slideshow", "city/thelady");
loadSlideshowImages("faburania-slideshow", "city/faburania");

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

//Monument slideshows
loadSlideshowImages("doptafortet-slideshow", "monument/doptafortet");
loadSlideshowImages("xmines-slideshow", "monument/xmines");
loadSlideshowImages("ldtornen-slideshow", "monument/ldtornen");
loadSlideshowImages("ldnkryptorna-slideshow", "monument/ldnkryptorna");
loadSlideshowImages("ottohallen-slideshow", "monument/ottohallen");

//Other slideshows
loadSlideshowImages("project-slideshow", "project");
// Add more slideshows as needed by repeating the above line with different IDs and folders.
