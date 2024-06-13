<?php
    $json = file_get_contents('php://input');
    $data = json_decode($json, true); 
    
    $idHorario = $data['idHorario'];
    $motivo = $data['motivo'];
    $via = $data['via'];
    $emailPaciente = $data['emailPaciente'];
    
    $servidor = 'localhost';
    $usuario = 'root';
    $password = "";
    $nombreBD = 'psyconnect';
    
    try {
        $conexion = new PDO("mysql:host=$servidor;dbname=$nombreBD", $usuario, $password);
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
        $conexion->beginTransaction();
    
        $sqlCitas = "INSERT INTO citas (motivo, via, emailPaciente, idHorario) VALUES (?, ?, ?, ?)";
        $stmtCitas = $conexion->prepare($sqlCitas);
        $stmtCitas->execute([$motivo, $via, $emailPaciente, $idHorario]);
    
        $sqlHorarios = "UPDATE horarios SET estado = 'espera' WHERE idHorario = ?";
        $stmtHorarios = $conexion->prepare($sqlHorarios);
        $stmtHorarios->execute([$idHorario]);
    
        $conexion->commit();
    
        $sqlLecturaHorario = "SELECT * FROM horarios";
        $stmtLecturaHorario = $conexion->query($sqlLecturaHorario);
        $lecturaHorario = $stmtLecturaHorario->fetchAll(PDO::FETCH_ASSOC);
    
        file_put_contents('../js/lecturaHorario.json', json_encode($lecturaHorario));
    
        echo "Cita guardada con éxito";
    } catch (PDOException $e) {
        $conexion->rollBack();
        echo "Error al insertar los datos: " . $e->getMessage();
    } finally {
        $conexion = null;
    }

?>
