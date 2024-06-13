<?php
    function guardarInicioFin($entrada, $salida, $fechaActual, $duracionCita, $duracion, $conexion) {
        $inicio = strtotime(date('Y-m-d', $fechaActual) . ' ' . $entrada);
        $fin = strtotime(date('Y-m-d', $fechaActual) . ' ' . $salida);

        while ($inicio <= $fin) {
            $inicioCita = $inicio;
            $finCita = strtotime("+" . $duracionCita . " minutes", $inicioCita);
            $fechaInicio = date("Y-m-d H:i:s", $inicioCita);
            $fechaFin = date("Y-m-d H:i:s", $finCita);
            $inicio = strtotime("+" . $duracion . " minutes", $inicio);

            try {
                $sql = "INSERT INTO horarios (fechaInicio, fechaFin, estado) VALUES (?, ?, ?)";
                $stmt = $conexion->prepare($sql);
                $stmt->execute([$fechaInicio, $fechaFin, "libre"]);
            } catch (PDOException $e) {
                echo "Error al insertar los datos " . $e->getMessage();
            }
        }
    }

    function borrarDatosExistentes($conexion) {
        try {
            $sql = "DELETE FROM horarios";
            $conexion->exec($sql);
        } catch (PDOException $e) {
            echo "Error al borrar los datos " . $e->getMessage();
        }
    }

    function leerDatos($conexion) {
        try {
            $sql = "SELECT * FROM horarios";
            $stmt = $conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            echo "Error al leer los datos " . $e->getMessage();
            return [];
        }
    }

    function guardarJson($data) {
        $jsonData = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents('../js/lecturaHorario.json', $jsonData);
    }

    $servidor = 'localhost';
    $usuario = 'root';
    $password = "";
    $nombreBD = 'psyconnect';

    try {
        $conexion = new PDO("mysql:host=$servidor;dbname=$nombreBD", $usuario, $password);
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Borrar datos existentes en la tabla
        borrarDatosExistentes($conexion);

        // Datos del formulario
        $dias = $_POST["dias"];
        $entradaManana = $_POST["entradaManana"];
        $salidaManana = $_POST["salidaManana"];
        $entradaTarde = $_POST["entradaTarde"];
        $salidaTarde = $_POST["salidaTarde"];
        $entradaNoche = $_POST["entradaNoche"];
        $salidaNoche = $_POST["salidaNoche"];
        $duracion = $_POST["duracion"];
        $duracionCita = $duracion - 1;

        $diasInt = [];
        foreach ($dias as $dia) {
            $diaInt = (int)$dia;
            array_push($diasInt, $diaInt);
        }

        $fechaActual = time();
        $fechaFinTrimestre = strtotime("+3 months", $fechaActual);

        // Iterar sobre cada día entre la fecha actual y la fecha final del trimestre
        while ($fechaActual <= $fechaFinTrimestre) {
            $diaSemanaFechaActual = date('N', $fechaActual);
            if (in_array($diaSemanaFechaActual, $diasInt)) {
                if ($entradaManana != null && $salidaManana != null) {
                    guardarInicioFin($entradaManana, $salidaManana, $fechaActual, $duracionCita, $duracion, $conexion);
                }
                if ($entradaTarde != null && $salidaTarde != null) {
                    guardarInicioFin($entradaTarde, $salidaTarde, $fechaActual, $duracionCita, $duracion, $conexion);
                }
                if ($entradaNoche != null && $salidaNoche != null) {
                    guardarInicioFin($entradaNoche, $salidaNoche, $fechaActual, $duracionCita, $duracion, $conexion);
                }
            }

            $fechaActual = strtotime("+1 day", $fechaActual);
        }

        // Leer los datos de la tabla horarios
        $horarios = leerDatos($conexion);

        // Guardar los datos en un archivo JSON
        guardarJson($horarios);
        header("Location: ../html/inicioPsicologo.php");
        exit();

    } catch (PDOException $e) {
        echo "Error de conexión: " . $e->getMessage();
    } finally {
        $conexion = null;
    }

?>
