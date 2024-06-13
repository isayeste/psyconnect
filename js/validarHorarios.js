document.addEventListener("DOMContentLoaded", function(){
    const form = document.querySelector("form");
    const checkboxes = document.querySelectorAll("input[type='checkbox'][name='dias[]']");
    const duracionInput = document.getElementById("duracion");
    const errorDias = document.querySelector(".errorDias");
    const errorMinutos = document.querySelector(".errorMinutos");
    const errorTime = document.createElement(".errorTime");

    form.addEventListener("submit", function(event) {
        let valid = true;

        // Que al menos un checkbox esté seleccionado
        const isCheckboxChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
        if (!isCheckboxChecked) {
            errorDias.textContent = "Señalar al menos un día";
            valid = false;
        } else {
            errorDias.textContent = "";
        }

        // Wue el input de duración tenga al menos un valor escrito
        if (duracionInput.value.trim() === "") {
            errorMinutos.textContent = "Introducir la duración de la sesión";
            valid = false;
        } else {
            errorMinutos.textContent = "";
        }

        //Que se cumplan los campos de tiempo
        timeInputs.forEach(input => {
            if (input.value) {
                const minTime = input.min;
                const maxTime = input.max;
                if (input.value < minTime || input.value > maxTime) {
                    input.setCustomValidity("Por favor, ingrese un valor entre " + minTime + " y " + maxTime);
                    valid = false;
                } else {
                    input.setCustomValidity("");
                }
            }
        });

        // Si alguno falla, evitar el envío del formulario
        if (!valid) {
            event.preventDefault();
        }
    });
});