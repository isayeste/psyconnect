<!-- Mostrar un calendario con las citas solicitadas y las horas con huecos para solicitar -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InicioPaciente</title>
    <link rel="stylesheet" href="../css/index.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

</head>
<body>
    <div class="contenedorPadre">
    <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
              <a class="navbar-brand" href="../index.html"><img class="logo" src="../imagenes/logoHorizontalBlanco.png" alt=""></a>
              <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
              </button>
              <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                  <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="../index.html">Home</a>
                  </li>
                  <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                      Áreas de Intervención
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                      <li><a class="dropdown-item" href="../pagina/adicciones.html">Adicciones</a></li>
                      <li><a class="dropdown-item" href="../pagina/ansiedad.html">Ansiedad</a></li>
                      <li><a class="dropdown-item" href="../pagina/depresion.html">Depresión</a></li>
                      <li><a class="dropdown-item" href="../pagina/fobias.html">Fobias</a></li>
                    </ul>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="../pagina/conoceme.html" tabindex="-1" aria-disabled="true">Conóceme</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" href="../agendaCita.html" tabindex="-1" aria-disabled="true">Agenda tu Cita</a>
                    </li>
                </ul>
              </div>
            </div>
          </nav>
    <!--  -->
        <div class="inicioPaciente">

            <div class="contenedorMensaje">
                <h4>Listado Estado de Citas Solicitadas</h4>
                <?php
                    if(isset($_GET['email'])) {
                        $emailPaciente = $_GET['email'];
                        $host = 'localhost';
                        $db = 'psyconnect';
                        $user = 'root';
                        $password = '';
                    
                        $dsn = "mysql:host=$host;dbname=$db";
                        $options = [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                            PDO::ATTR_EMULATE_PREPARES => false,
                        ];
                    
                        try {
                            $pdo = new PDO($dsn, $user, $password, $options);
                        
                            $sql = "SELECT c.idCita, c.via, h.fechaInicio, h.estado 
                                    FROM citas c
                                    JOIN horarios h ON c.idHorario = h.idHorario
                                    WHERE c.emailPaciente = :emailPaciente";

                            $stmt = $pdo->prepare($sql);
                            $stmt->execute(['emailPaciente' => $emailPaciente]);
                            $citas = $stmt->fetchAll();
                        
                            foreach ($citas as $cita) {
                                $fechaInicio = $cita['fechaInicio'];
                                $via = $cita['via'];
                                $fechaEvento = strstr($fechaInicio, ' ', true);
                                $horaInicio = strstr($fechaInicio, ' ');
                                $horaInicio = ltrim($horaInicio);
                            
                                if ($cita['estado'] == 'espera') {
                                    echo "<p>Cita Pendiente de Confirmación. Fecha: ". $fechaEvento. ", hora: ". $horaInicio .";  Vía: " . $via ."</p>";
                                } elseif ($cita['estado'] == 'libre') {
                                    echo "<p>Cita Cancelada. Fecha: ". $fechaEvento. ", hora: ". $horaInicio .";  Vía: " .$via. ". Elija otra cita. Perdone por las molestias.</p>";
                                } elseif ($cita['estado'] == 'ocupado') {
                                    echo "<p>Cita Confirmada. Fecha: ". $fechaEvento. ", hora: ". $horaInicio .";  Vía: " .$via. ".</p>";
                                }
                            }
                        } catch (PDOException $e) {
                            echo 'Error: ' . $e->getMessage();
                        }
                    }
                ?>

            </div>
            
            <div class="contenedorCalendario">
            <h4>Pedir Cita</h4>
                <div class="h2">
                
                    <h5 id="nombreMes"></h5>
                </div>
                <table class="calendar" id="calendario"></table>
                <div class="leyenda">
                    <div class="colorLeyenda"></div>
                    <div class="textoLeyenda"><p>Días disponibles</p></div>
                </div>
                
                <div id="botones" class="botones">
                    <div><button class="boton" id="anterior"></button></div>
                    <div class="final"><button class="boton" id="siguiente"></button></div>
                </div>
                <div id="horas" class="horas">
                    <h3 id="tituloHorariosDisponibles"></h3>
                    <div id="horasDisponibles" class="horasDisponibles"></div>
                </div>
            </div>
        </div>
        <!--  -->
        <div class="footer">
            <div class="footerUno">
                <div class="footerLinks">
                    <ul>
                        <li><a href="../pagina/terminosCondiciones.html">Términos y Condiciones</a></li>
                        <li><a href="../pagina/politicaPrivacidad.html">Política de Privacidad</a></li>
                        <li><a href="../pagina/politicaCookies.html">Política de Cookies</a></li>
                    </ul>
                </div>
                <div class="footerContacto">
                    <p>Dirección:  C. Isaac Peral, 2, 18800 Baza, Granada</p>
                    <p>Teléfono: 958 86 99 05</p>
                    <p>Email: iyessan445@g.educaand.es</p>
                </div>
                <div class="redes">
                  <ul>
                    <li><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-instagram" viewBox="0 0 16 16">
                      <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334"/>
                    </svg></li>
                    <li><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-twitter-x" viewBox="0 0 16 16">
                      <path d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z"/>
                    </svg></li>
                    <li><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-linkedin" viewBox="0 0 16 16">
                      <path d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854zm4.943 12.248V6.169H2.542v7.225zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248S2.4 3.226 2.4 3.934c0 .694.521 1.248 1.327 1.248zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016l.016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225z"/>
                    </svg></li>
                  </ul>
                </div>
            </div>
        </div>
        <div class="footerDos">
            <p>Derechos de autor © 2024 PsyConnect. Todos los derechos reservados.</p>
        </div>
    </div>

    <!-- Modales -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <h3 id="modalText"></h3>
            <div class="formularioModal">
                
                <div>
                    <label for="motivoConsulta">Motivo de consulta:</label>
                    <textarea id="motivoConsulta" rows="4" cols="50"></textarea>
                    <div class="errorMotivo"></div>
                </div>
                <br>
                <label for="tipoConsulta">Tipo de consulta:</label><br>
                <div>
                    <input type="radio" id="consultaOnline" name="tipoConsulta" value="online">
                    <label for="consultaOnline">Online</label><br>
                    <input type="radio" id="consultaPresencial" name="tipoConsulta" value="presencial">
                    <label for="consultaPresencial">Presencial</label>
                    <div class="errorConsulta"></div>
                </div>
                <br>
            </div>
            <div class="botonesModal">
                <button id="acceptButton">Aceptar</button>
                <button id="cancelButton">Cancelar</button>
            </div>
        </div>
    </div>
    <!--  -->
    
    <script src="../js/validarModal.js"></script>
    <script src="../js/calendar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
