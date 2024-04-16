window.addEventListener('scroll', function () {
    let scrollPosition = window.scrollY;
    let parallaxElements = document.querySelectorAll('.image_text');

    parallaxElements.forEach(function (element) {
        let parallaxSpeed = element.getAttribute('data-speed');
        let translateY = -scrollPosition * parallaxSpeed;
        element.style.transform = 'translateY(' + translateY + 'px)';
    });
});