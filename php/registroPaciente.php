<?php
    $emailPaciente = $_POST['email'];
    $contrasenia = $_POST['password'];
    $nombre = $_POST['nombre'];
    $fechaNacimiento = $_POST['fechaNacimiento'];
    $fotoPerfil = $_FILES['fotoPerfil']['tmp_name'];
    
    $servidor = 'localhost';
    $usuario = 'root';
    $password = "";
    $nombreBD = 'psyconnect';
    
    try {
        $conexion = new PDO("mysql:host=$servidor;dbname=$nombreBD", $usuario, $password);
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
        // Verificar si el email ya está registrado
        $stmt = $conexion->prepare("SELECT * FROM pacientes WHERE emailPaciente = ?");
        $stmt->execute([$emailPaciente]);
        $result = $stmt->fetch();
    
        if ($result) {
            // Redirigir con un mensaje de error si el email ya está registrado
            header("Location: ../html/preInicioSesion.html?errorRegistro=El+email+ya+está+registrado.");
            exit();
        }
    
        // Validar y guardar foto de perfil
        if ($_FILES['fotoPerfil']['error'] == 0) {
            $allowedExtensions = ['jpg', 'jpeg', 'png'];
            $fileExtension = strtolower(pathinfo($_FILES['fotoPerfil']['name'], PATHINFO_EXTENSION));
            if (!in_array($fileExtension, $allowedExtensions)) {
                header("Location: ../html/preInicioSesion.html?errorRegistro=La+foto+de+perfil+debe+ser+un+archivo+JPG+o+PNG.");
                exit();
            }
            $fotoPerfilData = file_get_contents($_FILES['fotoPerfil']['tmp_name']);
        } else {
            $fotoPerfilData = null;
        }
    
        // Insertar los datos en la base de datos
        $sql = "INSERT INTO pacientes (emailPaciente, contrasenia, nombre, fechaNacimiento, fotoPerfil) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$emailPaciente, password_hash($contrasenia, PASSWORD_BCRYPT), $nombre, $fechaNacimiento, $fotoPerfilData]);
    
        header("Location: ../html/inicioPaciente.php?email=" . urlencode($emailPaciente));
    } catch (PDOException $e) {
        header("Location: ../html/preInicioSesion.html?errorRegistro=Error+al+insertar+los+datos:+".urlencode($e->getMessage()));
    } finally {
        $conexion = null;
    }
?>
