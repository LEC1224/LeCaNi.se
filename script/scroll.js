window.addEventListener("scroll", function() {
    var sections = document.querySelectorAll(".content-section");

    sections.forEach(function(section) {
        var rect = section.getBoundingClientRect();
        
        // If the section is completely in view
        if (rect.top >= 0 || rect.bottom <= window.innerHeight) {
            section.style.backgroundAttachment = "scroll";
        } else {
            section.style.backgroundAttachment = "fixed";
        }
    });
});