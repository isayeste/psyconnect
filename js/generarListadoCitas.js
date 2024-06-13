document.addEventListener("DOMContentLoaded", function() {
    fetch('../php/generarListadoCitas.php')
        .then(response => response.text())
        .then(data => {
            console.log(data);
        })
        .catch(error => console.error('Error:', error));
});