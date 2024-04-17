document.getElementById('loginForm').addEventListener('submit', function (event) {
    event.preventDefault();  // Empêcher l'envoi du formulaire

    var username = document.getElementById('username').value;
    var password = document.getElementById('password').value;

    // Hasher le mot de passe
    var hashedPassword = CryptoJS.SHA256(password).toString();

    // Créer un nouveau formulaire qui sera envoyé
    var form = document.createElement('form');
    form.action = this.action;
    form.method = this.method;

    // Ajouter le nom d'utilisateur au formulaire
    var inputUser = document.createElement('input');
    inputUser.type = 'hidden';
    inputUser.name = 'username';
    inputUser.value = username;
    form.appendChild(inputUser);

    // Ajouter le mot de passe hashé au formulaire
    var inputPass = document.createElement('input');
    inputPass.type = 'hidden';
    inputPass.name = 'password';
    inputPass.value = hashedPassword;
    form.appendChild(inputPass);

    // Ajouter le formulaire au corps de la page et le soumettre
    document.body.appendChild(form);
    form.submit();
});