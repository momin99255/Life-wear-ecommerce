// 1. SMART NAVBAR SCROLL LOGIC
let lastScrollTop = 0;
const navbar = document.getElementById("navbar");

window.addEventListener("scroll", function() {
    let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    
    if (scrollTop > lastScrollTop) {
        // Scroll DOWN -> Hide Navbar (Add Class)
        navbar.classList.add("nav-hide");
    } else {
        // Scroll UP -> Show Navbar (Remove Class)
        navbar.classList.remove("nav-hide");
    }
    lastScrollTop = scrollTop;
});

// 2. SEARCH BAR TOGGLE
function toggleSearch() {
    const searchBox = document.getElementById("searchBox");
    if (searchBox.style.display === "block") {
        searchBox.style.display = "none";
    } else {
        searchBox.style.display = "block";
    }
}

// 3. SLIDER LOGIC
let slides = document.querySelectorAll('.hero-slider .slide');
let currentSlide = 0;
if(slides.length > 0) {
    function nextSlide() {
        slides[currentSlide].classList.remove('active');
        currentSlide = (currentSlide + 1) % slides.length;
        slides[currentSlide].classList.add('active');
    }
    setInterval(nextSlide, 4000); 
}