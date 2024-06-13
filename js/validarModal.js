document.getElementById('acceptButton').addEventListener('click', function(e){
    e.preventDefault;
    const motivoConsulta = document.getElementById('motivoConsulta').value;
    const tipoConsulta = document.querySelector('input[name="tipoConsulta"]:checked');

    let valido = true;

    if(motivoConsulta.length<=3){
        document.querySelector('.errorMotivo').textContent = 'El motivo de la consulta debe ser más largo';
        valido = false;
    } else{
        document.querySelector('.errorMotivo').textContent = "";
    }

    if(!tipoConsulta){
        document.querySelector('.errorConsulta').textContent = 'Debe elegir un tipo de cita';
        valido = false;
    } else{
        document.querySelector('.errorConsulta').textContent = "";
    }

    


});