let fechaActual = new Date(); // Variable global para almacenar la fecha actual
let horariosCompleto = []; // Para almacenar todos los horarios desde el archivo JSON
let fechaSeleccionada = ''; // Para almacenar la fecha seleccionada
let email = "";
let citaSeleccionada = null; // Para almacenar el ID de la cita seleccionada

// Función para obtener la fecha en formato "YYYY-MM-DD"
function obtenerFechaFormateada(fecha) {
    let dia = fecha.getDate();
    let mes = fecha.getMonth() + 1;
    let anio = fecha.getFullYear();
    // Añadir ceros a la izquierda si es necesario
    if (mes < 10) mes = '0' + mes;
    if (dia < 10) dia = '0' + dia;
    return `${anio}-${mes}-${dia}`;
}

// Función para obtener la fecha en formato "DD de MMMM"
function obtenerFechaLegible(fecha) {
    const opciones = { day: 'numeric', month: 'long' };
    return fecha.toLocaleDateString('es-ES', opciones);
}

// Función para generar el calendario del mes
function generarCalendario(fecha) {
    // Obtener referencia al cuerpo del calendario y al nombre del mes
    let cuerpoCalendario = document.getElementById('calendario');
    let nombreMesElemento = document.getElementById('nombreMes');
    cuerpoCalendario.innerHTML = ''; // Limpiar el calendario

    // Obtener fechas desde el archivo JSON
    obtenerFechas().then(function(fechas) {
        horariosCompleto = fechas; // Guardar las fechas obtenidas
        let fechasBD = fechas.map(horario => horario.fechaInicio.split(' ')[0]); // Solo las fechas

        let primerDiaMes = new Date(fecha.getFullYear(), fecha.getMonth(), 1);
        let ultimoDiaMes = new Date(fecha.getFullYear(), fecha.getMonth() + 1, 0);
        let primerDiaSemana = primerDiaMes.getDay();
        if (primerDiaSemana === 0) primerDiaSemana = 7; // Ajustar para que lunes sea el primer día

        // Crear fila con los nombres de los días
        let filaDiasSemana = document.createElement('tr');
        let nombresDiasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        nombresDiasSemana.forEach(nombreDia => {
            let celdaDiaSemana = document.createElement('th');
            celdaDiaSemana.textContent = nombreDia;
            filaDiasSemana.append(celdaDiaSemana);
        });
        cuerpoCalendario.append(filaDiasSemana);

        // Crear fechas para el calendario
        let fechaIteracion = new Date(primerDiaMes);
        fechaIteracion.setDate(fechaIteracion.getDate() - (primerDiaSemana - 1));

        // Crear las filas del calendario
        for (let i = 0; i < 6; i++) {
            let fila = document.createElement('tr');
            for (let j = 0; j < 7; j++) {
                let fechaC = obtenerFechaFormateada(fechaIteracion);
                let celda = document.createElement('td');
                
                // Marcar días con citas disponibles
                if (fechasBD.includes(fechaC)) {
                    celda.style.backgroundColor = '#acf2d4';
                }

                celda.textContent = fechaIteracion.getDate(); // Mostrar el día del mes
                if (fechaIteracion.getMonth() !== primerDiaMes.getMonth()) {
                    celda.style.color = '#D3D3D3'; // Diferenciar días fuera del mes actual
                }

                // Guardar la fecha en atributos para usar más tarde
                celda.setAttribute('data-fecha', fechaC);
                celda.setAttribute('data-fecha-legible', obtenerFechaLegible(fechaIteracion));
                
                fila.append(celda);
                fechaIteracion.setDate(fechaIteracion.getDate() + 1);
            }
            cuerpoCalendario.append(fila);
        }

        // Mostrar el nombre del mes
        let meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        nombreMesElemento.textContent = `${meses[fecha.getMonth()]} ${fecha.getFullYear()}`;

        //botones
        let btnAnterior = document.getElementById('anterior');
        let btnSiguiente = document.getElementById('siguiente');
        

        let mesAnterior = new Date(fecha.getFullYear(), fecha.getMonth() - 1, 1);
        let mesSiguiente = new Date(fecha.getFullYear(), fecha.getMonth() + 1, 1);
    
        btnAnterior.textContent = '<'+meses[mesAnterior.getMonth()];
        btnSiguiente.textContent = meses[mesSiguiente.getMonth()]+'>';

    }).catch(function(error) {
        console.error(error);
    });
}

// Funciones para cambiar el mes
function mesAnterior() {
    fechaActual.setMonth(fechaActual.getMonth() - 1);
    generarCalendario(fechaActual);
}

function mesSiguiente() {
    fechaActual.setMonth(fechaActual.getMonth() + 1);
    generarCalendario(fechaActual);
}

// Función para obtener fechas desde el archivo JSON
function obtenerFechas() {
    return fetch('../js/lecturaHorario.json')
        .then(respuesta => respuesta.json())
        .then(horarios => {
            return horarios.map(horario => ({
                id: horario.idHorario, // Utilizar el campo idHorario
                fechaInicio: horario.fechaInicio.split(' ')[0],
                hora: horario.fechaInicio.split(' ')[1].slice(0, 5), // Solo hora y minuto
                estado: horario.estado
            }));
        })
        .catch(error => {
            console.error(error);
            return []; // Si hay error, devolver array vacío
        });
}

document.addEventListener('DOMContentLoaded', function() {
    const btnAnterior = document.getElementById('anterior');
    const btnSiguiente = document.getElementById('siguiente');
    const cuerpoCalendario = document.getElementById('calendario');
    const tablaHorasDisponibles = document.getElementById('horasDisponibles');
    const modal = document.getElementById('myModal');
    const modalText = document.getElementById('modalText');
    const botonAceptar = document.getElementById('acceptButton');
    const botonCancelar = document.getElementById('cancelButton');

    btnAnterior.addEventListener('click', mesAnterior);
    btnSiguiente.addEventListener('click', mesSiguiente);
    generarCalendario(fechaActual); // Mostrar el calendario al cargar la página

    // Obtener el email de la URL
    const parametroUrl = new URLSearchParams(window.location.search);
    email = parametroUrl.get('email');
    console.log('Email:', email);

    // Click en una celda del calendario
    cuerpoCalendario.addEventListener('click', function(event) {
        const target = event.target;
        if (target.tagName === 'TD') {
            fechaSeleccionada = target.getAttribute('data-fecha-legible');
            const fechaFormateada = target.getAttribute('data-fecha');
            console.log(fechaSeleccionada);
            document.getElementById('tituloHorariosDisponibles').textContent = `Horas disponibles para el ${fechaSeleccionada}`;

                        // Filtrar horarios para la fecha seleccionada
                        const horariosSeleccionados = horariosCompleto.filter(horario => horario.fechaInicio === fechaFormateada && horario.estado === 'libre');
                        const horasDisponibles = horariosSeleccionados.map(horario => ({ id: horario.id, hora: horario.hora }));
            
                        // Limpiar la tabla de horas disponibles
                        tablaHorasDisponibles.innerHTML = '';
            
                        // Añadir horas disponibles a la tabla
                        horasDisponibles.forEach(({ id, hora }) => {
                            //let fila = document.createElement('div');
                            let celda = document.createElement('div');
                            celda.textContent = hora;
                            celda.setAttribute('data-hora', hora);
                            celda.setAttribute('data-id', id); // Guardar el ID de la cita
                            //fila.append(celda);
                            //tablaHorasDisponibles.append(fila);
                            tablaHorasDisponibles.append(celda);
                        });
                    }
                });
            
                // Click en una hora disponible
                tablaHorasDisponibles.addEventListener('click', function(event) {
                    const target = event.target;
                    if (target.tagName === 'DIV') {
                        const hora = target.getAttribute('data-hora');
                        citaSeleccionada = target.getAttribute('data-id'); // Guardar el ID de la cita seleccionada
                        console.log(`Cita ID: ${citaSeleccionada}`); // Imprimir el ID de la cita seleccionada
            
                        // Mostrar el modal con la fecha y la hora seleccionadas
                        modal.style.display = "block";
                        modalText.textContent = `Confirmar cita: ${fechaSeleccionada} a las ${hora}h`;

                        botonAceptar.onclick = function() {
                            const motivo = document.getElementById('motivoConsulta').value;
                            const via = document.querySelector('input[name="tipoConsulta"]:checked').value;
                            const datosCita = {
                                idHorario: citaSeleccionada,
                                motivo: motivo,
                                via: via,
                                emailPaciente: email
                            };
                        
                            // Enviar datos al servidor mediante fetch
                            fetch('../php/guardarCita.php', { //solicitud HTTP POST para enviar al archivo /php/guardarCita.php
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json' //enviar datos tipo json
                                },
                                body: JSON.stringify(datosCita)
                            })
                            .then(response => {
                                if (response.ok) {
                                    console.log('Solicitud enviada con éxito');
                                    return response.text(); // Leer la respuesta como texto
                                } else {
                                    console.error('Error en la solicitud:', response.status);
                                }
                            })
                            .then(data => {
                                console.log(data);
                                myModal.style.display = "none";
                                
                                location.reload();
                            })
                            .catch(error => console.error('Error:', error));
                        };
                        
                        
            
                        // Botón cancelar en el modal
                        botonCancelar.onclick = function() {
                            console.log(`Cancelar: Fecha ${fechaSeleccionada}, Hora ${hora}, Cita ID: ${citaSeleccionada}`);
                            modal.style.display = "none";
                        };
            
                        // Cerrar el modal al hacer clic fuera
                        window.onclick = function(event) {
                            if (event.target == modal) {
                                modal.style.display = "none";
                            }
                        };
                    }
                });

                  

            });

           
            
