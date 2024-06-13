<?php
    // Incluir el autoloader de Composer
    require_once '../vendor/autoload.php';

    // Importar las clases necesarias de la biblioteca de Google API Client
    use Google\Client;

    // Crear una instancia del cliente
    $client = new Client();

    // Establecer la ruta al archivo JSON de credenciales descargado desde la Consola de Desarrolladores de Google
    $client->setAuthConfig('../config/client_secret_817642552550-grgavacspiedvqco6uu785u561bepi4o.apps.googleusercontent.com.json');

    // Añadir el alcance necesario para la autenticación (email, perfil y calendario)
    $client->addScope('email');
    $client->addScope('profile');
    $client->addScope('https://www.googleapis.com/auth/calendar');

    // Establecer la URL de redireccionamiento después de la autorización
    $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/proyectoFinal/proyectoFinal/php/inicioSesionPsicologo.php';
    $client->setRedirectUri($redirect_uri);
    
    // Si se recibió un código de autorización -> intercambia el código por un token de acceso
    if (isset($_GET['code'])) {
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        // Verificar si hay errores
        if (array_key_exists('error', $token)) {
            die('Error al intentar obtener el token de acceso: ' . $token['error']);
        }
        // Guardar el token de acceso en una sesión para su uso posterior
        session_start();
        $_SESSION['access_token'] = $token;
        // Redirigir a la página de inicio después de la autenticación
        header('Location: ../html/inicioPsicologo.php?token=' . $token['access_token']);
    }

    // Si no hay un token de acceso, redirige al usuario a loguearse
    if (!isset($_SESSION['access_token'])) {
        $authUrl = $client->createAuthUrl();
        header('Location: ' . $authUrl);
        exit;
    }
?>
