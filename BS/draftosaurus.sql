CREATE DATABASE IF NOT EXISTS `bd-jtcorp`;
USE `bd-jtcorp`;

-- ==========================
-- Tabla de usuarios
-- ==========================
CREATE TABLE usuario (
  id_usuario INT AUTO_INCREMENT PRIMARY KEY,
  nombre_usuario VARCHAR(50) NOT NULL UNIQUE,
  contrasena VARCHAR(100) NOT NULL,
  rol ENUM('admin','jugador') NOT NULL DEFAULT 'jugador'
);

-- ==========================
-- Tabla de partidas
-- ==========================
CREATE TABLE partida (
  id_partida INT AUTO_INCREMENT PRIMARY KEY,
  estado VARCHAR(20) NOT NULL,
  fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
  id_ganador INT NULL,
  FOREIGN KEY (id_ganador) REFERENCES usuario(id_usuario)
);

-- ==========================
-- Tabla de tableros
-- ==========================
CREATE TABLE tablero (
  id_tablero INT AUTO_INCREMENT PRIMARY KEY,
  puntaje INT NOT NULL DEFAULT 0,
  id_partida INT NOT NULL,
  id_usuario INT NOT NULL,
  FOREIGN KEY (id_partida) REFERENCES partida(id_partida),
  FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
);

-- ==========================
-- Tabla de dinosaurios
-- ==========================
CREATE TABLE dinosaurio (
  id_dinosaurio INT AUTO_INCREMENT PRIMARY KEY,
  especie VARCHAR(50) NOT NULL
);

-- ==========================
-- Tabla que relaciona tablero con dinosaurios colocados
-- ==========================
CREATE TABLE contiene (
  id_tablero INT NOT NULL,
  id_dinosaurio INT NOT NULL,
  PRIMARY KEY (id_tablero, id_dinosaurio),
  FOREIGN KEY (id_tablero) REFERENCES tablero(id_tablero) ON DELETE CASCADE,
  FOREIGN KEY (id_dinosaurio) REFERENCES dinosaurio(id_dinosaurio)
);

-- ==========================
-- Historial de jugadores por partida
-- ==========================
CREATE TABLE historial_jugador (
  id INT AUTO_INCREMENT PRIMARY KEY,
  partida_id INT NOT NULL,
  usuario_id INT NULL,
  nombre VARCHAR(80) NOT NULL,
  puntos INT NOT NULL DEFAULT 0,
  posicion TINYINT(4) NOT NULL,
  FOREIGN KEY (partida_id) REFERENCES partida(id_partida) ON DELETE CASCADE,
  FOREIGN KEY (usuario_id) REFERENCES usuario(id_usuario) ON DELETE SET NULL
);

CREATE TABLE historial_partida (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
  ganador_nombre VARCHAR(50) NOT NULL,
  ganador_puntos INT NOT NULL,
  total_jugadores INT NOT NULL,
  duracion_seg INT DEFAULT 0
);

-- ==========================
-- Relación usuario ↔ partida (participaciones)
-- ==========================
CREATE TABLE juega (
  id_partida INT NOT NULL,
  id_usuario INT NOT NULL,
  PRIMARY KEY (id_partida, id_usuario),
  FOREIGN KEY (id_partida) REFERENCES partida(id_partida),
  FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
);

-- ==========================
-- Usuario administrador único
-- ==========================
INSERT INTO usuario (nombre_usuario, contrasena, rol)
VALUES ('user_jtcorp', 'Zn1@Lp5!Kt4#Xq8V', 'admin');

ALTER TABLE usuario
MODIFY contrasena VARCHAR(100) NOT NULL;

ALTER TABLE historial_jugador
DROP FOREIGN KEY historial_jugador_ibfk_1;

ALTER TABLE historial_jugador
ADD CONSTRAINT fk_historial_partida
FOREIGN KEY (partida_id) REFERENCES historial_partida(id) ON DELETE CASCADE;