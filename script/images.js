class Slideshow {
    constructor(containerId, images, hiresImages) {
        this.container = document.getElementById(containerId);
        this.images = images;
        this.hiresImages = hiresImages;
        this.currentImageIndex = 0;
        
        this.container.querySelector('.prev').addEventListener('click', () => this.changeImage(-1));
        this.container.querySelector('.next').addEventListener('click', () => this.changeImage(1));
    }
    
    changeImage(step) {
      console.log("Calling changeImage");
        this.currentImageIndex += step;
        if (this.currentImageIndex >= this.images.length) this.currentImageIndex = 0;
        if (this.currentImageIndex < 0) this.currentImageIndex = this.images.length - 1;

        this.container.querySelector('.main-image img').src = this.images[this.currentImageIndex];
        this.container.querySelector('.main-image a').href = this.hiresImages[this.currentImageIndex];
    }
}

const MAX_IMAGES = 10;

function loadSlideshowImages(slideshowId, folder) {
    const imageArray = [];
    const hiresImageArray = [];

    for (let i = 1; i <= MAX_IMAGES; i++) {
        const imagePath = 'img/' + folder + '/' + i + '.jpg';
        const hiresImagePath = 'img/' + folder + '/' + i + '.jpg';

        const img = new Image();
        img.src = imagePath;
        img.onload = function() {
            imageArray.push(imagePath);
            hiresImageArray.push(hiresImagePath);
        };
        img.onerror = function() {
            const index = imageArray.indexOf(imagePath);
            if (index > -1) {
                imageArray.splice(index, 1);
            }
        };
    }
    // Delay the slideshow initialization to give images a chance to load
    setTimeout(() => {
      window.slideshows[slideshowId] = new Slideshow(slideshowId, imageArray, hiresImageArray);
    }, 1000);  // Adjust the delay as needed
}

window.slideshows = {};

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

//Other slideshows
loadSlideshowImages("project-slideshow", "project");
// Add more slideshows as needed by repeating the above line with different IDs and folders.
