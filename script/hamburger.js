
document.getElementById("hamburger-icon").addEventListener("click", function() {
    var navLinks = document.querySelector(".nav-links");
    navLinks.classList.toggle("active");
});

// Hide dropdown when a nav link is clicked on smaller screens
var navLinks = document.querySelectorAll("nav .nav-links a");
navLinks.forEach(function(link) {
    link.addEventListener("click", function() {
        if (window.innerWidth <= 1280) {
            var navContainer = document.querySelector(".nav-links");
            navContainer.classList.remove("active");
        }
    });
});
