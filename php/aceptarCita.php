<?php
    require_once '../vendor/autoload.php';
    use Google\Client;
    use Google\Service\Calendar;
    use Google\Service\Calendar\Event;

    $json= file_get_contents('php://input');
    $data = json_decode($json, true); 
    //$data = 17;
    print_r($data);

    $servidor = 'localhost';
    $usuario = 'root';
    $password = '';
    $nombreBD = 'psyconnect';

    $idCita = null;
    $idHorario = null;
    $fechaInicio = null;
    $fechaFin = null;
    $nombre = null;
    $emailPaciente = null;
    $motivo = null;
    $via = null;

    try {
        $pdo = new PDO("mysql:host=$servidor;dbname=$nombreBD", $usuario, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "
            SELECT 
                citas.idCita, 
                citas.idHorario, 
                horarios.fechaInicio, 
                horarios.fechaFin, 
                pacientes.nombre, 
                citas.emailPaciente, 
                citas.motivo, 
                citas.via 
            FROM 
                citas 
            JOIN 
                pacientes ON citas.emailPaciente = pacientes.emailPaciente 
            JOIN 
                horarios ON citas.idHorario = horarios.idHorario
            WHERE 
                horarios.idHorario = :idHorario
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':idHorario', $data, PDO::PARAM_INT);
        $stmt->execute();
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si hay un resultado, guardar los valores en las variables correspondientes
        if ($fila) {
            $idCita = $fila['idCita'];
            $idHorario = $fila['idHorario'];
            $fechaInicio = $fila['fechaInicio'];
            $fechaFin = $fila['fechaFin'];
            $nombre = $fila['nombre'];
            $emailPaciente = $fila['emailPaciente'];
            $motivo = $fila['motivo'];
            $via = $fila['via'];

            // Actualizar el estado del horario a 'ocupado'
            $sqlUpdate = "UPDATE horarios SET estado = 'ocupado' WHERE idHorario = :idHorario";
            $stmtUpdate = $pdo->prepare($sqlUpdate);
            $stmtUpdate->bindParam(':idHorario', $idHorario, PDO::PARAM_INT);
            $stmtUpdate->execute();
            // Sobreescribir JSON
            $sql2 = "SELECT idHorario, fechaInicio, fechaFin, estado FROM horarios";
            $stmt = $pdo->prepare($sql2);
            $stmt->execute();
            $horarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $jsonHorarios = json_encode($horarios, JSON_PRETTY_PRINT);

            // Guardar los datos en el archivo JSON
            $filePath = '../js/lecturaHorario.json';
            file_put_contents($filePath, $jsonHorarios);

        } else {
            echo "No se encontraron resultados para el idHorario especificado.";
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    // Cerrar la conexión
    $pdo = null;


    //Generar evento en google calendar
    // Iniciar sesión si no está activa
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    //Instancia de google client -> obtener credenciales
    $client = new Google\Client();
    $client->setAuthConfig('../config/client_secret_817642552550-grgavacspiedvqco6uu785u561bepi4o.apps.googleusercontent.com.json');
    $client->addScope(Google\Service\Calendar::CALENDAR);
    $client->addScope('https://www.googleapis.com/auth/gmail.send');
    $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/proyectoFinal/proyectoFinal/php/aceptarCita.php';
    $client->setRedirectUri($redirect_uri);

    //Ver si ha caducado el token, si esta caducado -> iniciar sesion
    if (isset($_SESSION['access_token'])) {
        $client->setAccessToken($_SESSION['access_token']);
    }
    if ($client->isAccessTokenExpired() || !$client->getAccessToken()) {
        $authUrl = $client->createAuthUrl();
        header('Location: ' . $authUrl);
        exit;
    }

    // Crea una instancia del servicio de Google Calendar
    $calendarService = new Google\Service\Calendar($client);

    $fechaEvento = strstr($fechaInicio, ' ', true);
    $nombreEvento = $nombre. ", ". $via;
    $horaInicio = strstr($fechaInicio, ' ');
    $horaInicio = ltrim($horaInicio);
    
    $horaFin = strstr($fechaFin, ' ');
    $horaFin = ltrim($horaFin);
    
    $fecha = new DateTime($fechaEvento);

    $newEvent = new Event([
        'summary' => $nombreEvento,
        'start' => ['dateTime' => $fecha->format('Y-m-d') . 'T'. $horaInicio, 'timeZone' => 'Europe/Madrid'],
        'end' => ['dateTime' => $fecha->format('Y-m-d') . 'T' . $horaFin, 'timeZone' => 'Europe/Madrid'],
    ]);

    //var_dump($newEvent);

    // // Insertar el evento en el calendario del usuario
    $calendarId = 'primary';
    $createdEvent = $calendarService->events->insert($calendarId, $newEvent);

    // // Mostrar el ID del evento creado
    $eventId = $createdEvent->getId();
    echo 'Evento creado: ' . $eventId;

    // Actualizar el idGoogleCalendar en la base de datos
    try {
        // Crear una nueva conexión PDO
        $pdo = new PDO("mysql:host=$servidor;dbname=$nombreBD", $usuario, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sqlUpdateEventId = "UPDATE citas SET idGoogleCalendar = :idGoogleCalendar WHERE idCita = :idCita";
        $stmtUpdateEventId = $pdo->prepare($sqlUpdateEventId);
        $stmtUpdateEventId->bindParam(':idGoogleCalendar', $eventId, PDO::PARAM_STR);
        $stmtUpdateEventId->bindParam(':idCita', $idCita, PDO::PARAM_INT);
        $stmtUpdateEventId->execute();

        echo 'ID de Google Calendar actualizado en la base de datos.';

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }


?>
