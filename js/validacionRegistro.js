document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);

    if (urlParams.has('errorRegistro')) {
        const error = urlParams.get('errorRegistro');
        document.getElementById('errorEmail').textContent = error;
    }

    // if (urlParams.has('success')) {
    //     const success = urlParams.get('success');
    //     alert(success);
    // }

    document.getElementById('registroForm').addEventListener('submit', function(event) {
        let valid = true;

        // Validar email
        const email = document.getElementById('email').value;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            valid = false;
            document.getElementById('errorEmail').textContent = "El formato del email es incorrecto.";
        } else {
            document.getElementById('errorEmail').textContent = "";
        }

        // Validar contraseña
        const password = document.getElementById('password').value;
        const passwordRegex = /^(?=.*[A-Z])(?=.*\d).{8,}$/;
        if (!passwordRegex.test(password)) {
            valid = false;
            document.getElementById('errorPassword').textContent = "La contraseña debe tener al menos 8 caracteres, incluyendo una letra mayúscula y un número.";
        } else {
            document.getElementById('errorPassword').textContent = "";
        }

        // Validar fecha de nacimiento
        const fechaNacimiento = document.getElementById('fechaNacimiento').value;
        const fechaActual = new Date().toISOString().split('T')[0];
        if (fechaNacimiento && fechaNacimiento >= fechaActual) {
            valid = false;
            document.getElementById('errorFechaNacimiento').textContent = "La fecha de nacimiento debe ser anterior a hoy.";
        } else {
            document.getElementById('errorFechaNacimiento').textContent = "";
        }

        // Validar foto de perfil
        const fotoPerfil = document.getElementById('fotoPerfil').files[0];
        if (fotoPerfil && !/\.(jpg|jpeg|png)$/i.test(fotoPerfil.name)) {
            valid = false;
            document.getElementById('errorFotoPerfil').textContent = "La foto de perfil debe ser un archivo JPG o PNG.";
        } else {
            document.getElementById('errorFotoPerfil').textContent = "";
        }

        if (!valid) {
            event.preventDefault();
        }
    });
});
