window.addEventListener('scroll', function () {
    var image = document.getElementById('image');
    var scrollPosition = window.scrollY;
    var newPosition = scrollPosition * 0.5; // Adjust the speed of scrolling here

    // Set the new background position
    if (window.innerWidth > 1200) {
        // Définir la nouvelle position verticale de l'image
        image.style.backgroundPositionY = 'calc(50% + ' + newPosition + 'px)';
    } else {
        image.style.backgroundPositionY = "0px"
    }
});