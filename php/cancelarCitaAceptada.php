<?php

    require_once '../vendor/autoload.php';
    use Google\Client;
    use Google\Service\Calendar;
    use Google\Service\Calendar\Event;

    $json = file_get_contents('php://input');
    $data = json_decode($json, true); 

    // Imprimir el contenido por consola con el JS
    // echo '<pre>';
    // print_r($data);
    // echo '</pre>';

    // Extraer el idHorario de los datos recibidos
    // $idHorario = $data;

    if ($data === null) {
        echo "Error: idHorario no está presente en los datos recibidos.";
        exit;
    }

    $servidor = 'localhost';
    $usuario = 'root';
    $password = '';
    $nombreBD = 'psyconnect';

    try {
        $pdo = new PDO("mysql:host=$servidor;dbname=$nombreBD", $usuario, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        //ELIMINAR EL EVENTO DE GOOGLE CALENDAR
        $sqlIdGoogleCalendar = "SELECT idGoogleCalendar FROM citas WHERE idHorario = :idHorario";
        $stmt = $pdo->prepare($sqlIdGoogleCalendar);
        $stmt->bindParam(':idHorario', $data, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && !empty($result['idGoogleCalendar'])) {
            $idGoogleCalendar = $result['idGoogleCalendar'];

            // Iniciar sesión si no está activa
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Instancia de google client -> obtener credenciales
            $client = new Google\Client();
            $client->setAuthConfig('../config/client_secret_817642552550-grgavacspiedvqco6uu785u561bepi4o.apps.googleusercontent.com.json');
            $client->addScope(Google\Service\Calendar::CALENDAR);
            $client->addScope('https://www.googleapis.com/auth/gmail.send');
            $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/proyectoFinal/proyectoFinal/php/aceptarCita.php';
            $client->setRedirectUri($redirect_uri);

            // Ver si ha caducado el token, si esta caducado -> iniciar sesion
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

            // Eliminar el evento de Google Calendar
            $calendarId = 'primary';
            $calendarService->events->delete($calendarId, $idGoogleCalendar);
            echo 'Evento de Google Calendar eliminado correctamente.';
        } else {
            echo 'No se encontró el idGoogleCalendar para el idHorario especificado.';
        }
        

        // Actualizar el estado del horario a 'libre'
        $sqlUpdate = "UPDATE horarios SET estado = 'libre' WHERE idHorario = :idHorario";
        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':idHorario', $data, PDO::PARAM_INT);
        $stmtUpdate->execute();

        // Verificar si la actualización fue exitosa
        if ($stmtUpdate->rowCount() > 0) {
            // Consulta para obtener todos los horarios
            $sql2 = "SELECT idHorario, fechaInicio, fechaFin, estado FROM horarios";
            $stmt = $pdo->prepare($sql2);
            $stmt->execute();
            $horarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Convertir los resultados a JSON
            $jsonHorarios = json_encode($horarios, JSON_PRETTY_PRINT);

            // Guardar los datos en el archivo JSON
            $filePath = '../js/lecturaHorario.json';
            file_put_contents($filePath, $jsonHorarios);

            echo "Actualización exitosa y JSON sobrescrito correctamente.";
        } else {
            echo "No se encontró ningún registro con idHorario = $data.";
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    $pdo = null;

?>
