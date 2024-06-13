document.addEventListener('DOMContentLoaded', function() {
    const btnSolicitarCita = document.getElementById('btnSolicitarCita');
    //const btnIniciarSesion = document.getElementById('btnIniciarSesion');

    btnSolicitarCita.addEventListener('click', function() {
        window.location.href = 'html/preInicioSesion.html';
    });

    btnIniciarSesion.addEventListener('click', function() {
        // Iniciar sesión con Google
        iniciarSesionGoogle();
    });
});
