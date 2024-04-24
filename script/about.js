window.addEventListener('scroll', function () {
    var image = document.getElementById('sum');
    var scrollPosition = window.scrollY;
    var newPosition = scrollPosition * 0.2; // Adjust the speed of scrolling here

    // Set the new background position
    if (window.innerWidth > 1200) {
        // Définir la nouvelle position verticale de l'image
        image.style.backgroundPositionY = 'calc(90% + ' + newPosition + 'px)';
    } else {
        image.style.backgroundPositionY = 'calc(0% + ' + -(scrollPosition * 0.1) + 'px)';
    }
});