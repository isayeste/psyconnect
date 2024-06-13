document.addEventListener('DOMContentLoaded', function() {

    const btnAceptar = document.querySelectorAll('.btnAceptar');
    const btnCancelarEspera = document.querySelectorAll('.btnCancelarEspera');
    const btnCancelarOcupado = document.querySelectorAll('.btnCancelarOcupado');

    // Iterar sobre cada botón de aceptar y añadirle un evento
    btnAceptar.forEach(btn => {
        btn.addEventListener('click', function() {
            const idHorario = this.dataset.idhorario; //Añadirle un dataset para poder obtener su id
            console.log(idHorario);
            aceptarCita(idHorario);
        });
    });

    //Botón cancelar
    btnCancelarEspera.forEach(btn => {
        btn.addEventListener('click', function() {
            const idHorario = this.dataset.idhorario;
            //console.log('ID del horario asociado al botón Cancelar Espera:', idHorario);
            cancelarCitaEspera(idHorario);
        });
    });

    //Botón cancelar el aceptado/ocupado
    btnCancelarOcupado.forEach(btn => {
        // Añade un event listener para el evento 'click' a cada botón de cancelar ocupado
        btn.addEventListener('click', function() {
            const idHorario = this.dataset.idhorario; 
            console.log('ID del horario asociado al botón Cancelar Ocupado:', idHorario);
            cancelarCitaOcupado(idHorario);
        });
    });

    // Función para enviar una solicitud al servidor para aceptar una cita
    function aceptarCita(cita) {
        //console.log("cita aceptada");
        fetch('../php/aceptarCita.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(cita)
        })
        .then(response => response.text())
        .then(data => {
            // console.log('Respuesta del servidor:', data);
            // Recarga la página después de aceptar la cita
            location.reload();
        })
        .catch(error => console.error('Error:', error));
    }

    // Función para enviar una solicitud al servidor para cancelar una cita en espera
    function cancelarCitaEspera(cita) {
        fetch('../php/cancelarCita.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(cita)
        })
        .then(response => response.text())
        .then(data => {
            // console.log('Respuesta del servidor:', data);
            location.reload();
        })
        .catch(error => console.error('Error:', error));
    }

    // Función para enviar una solicitud al servidor para cancelar una cita ocupada
    function cancelarCitaOcupado(cita) {
        fetch('../php/cancelarCitaAceptada.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(cita)
        })
        .then(response => response.text())
        .then(data => {
            // console.log('Respuesta del servidor:', data);
            location.reload();
        })
        .catch(error => console.error('Error:', error));
    }
});
