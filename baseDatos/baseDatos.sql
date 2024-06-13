CREATE DATABASE IF NOT EXISTS psyconnect;
use psyconnect;

-- PACIENTES
CREATE TABLE pacientes(
    emailPaciente VARCHAR(100) PRIMARY KEY NOT NULL,
    contrasenia VARCHAR (100),
    nombre VARCHAR(100),
    fechaNacimiento DATE,
    fotoPerfil BLOB
);

-- HORARIOS
CREATE TABLE horarios(
    idHorario INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    fechaInicio DATETIME,
    fechaFin DATETIME,  
    estado ENUM('libre', 'ocupado', 'espera')
);

-- CITAS
CREATE TABLE citas(
    idCita INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    motivo TEXT,
    via ENUM('online', 'presencial'),
    emailPaciente VARCHAR(100),
    idHorario INT,
    idGoogleCalendar VARCHAR(255),
    FOREIGN KEY (emailPaciente) REFERENCES pacientes(emailPaciente),
    FOREIGN KEY (idHorario) REFERENCES horarios(idHorario)
);

