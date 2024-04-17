window.addEventListener('scroll', function () {
    var h1_up = document.getElementById("h1up");
    var scrollPosition = window.scrollY;
    var newPosition = scrollPosition * 0.1;

    h1_up.style.transform = 'translateY(' + newPosition + 'px)';
});