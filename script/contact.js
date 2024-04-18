window.addEventListener('resize', function () {
    var windowHeight = window.innerHeight;
    var divConnexion = document.querySelector('.div-connexion');
    divConnexion = windowHeight * 0.8 + 'px'; // 80% de la hauteur de la fenêtre
});