<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/funciones_sesion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../registro.php'); exit;
}

$usuario = trim($_POST['nombre_usuario'] ?? '');
$pass1   = trim($_POST['contrasena']  ?? '');
$pass2   = trim($_POST['contrasena2'] ?? '');

if (!preg_match('/^[A-Za-z0-9_]{4,20}$/', $usuario)) {
  poner_mensaje_flasheo('error', 'El nombre de usuario debe tener entre 4 y 20 caracteres (letras, números o _).');
  $_SESSION['registro_usuario_temp'] = $usuario;
  header('Location: ../registro.php'); exit;
}

if ($pass1 === '' || $pass2 === '') {
  poner_mensaje_flasheo('error', 'Debés ingresar y confirmar una contraseña.');
  $_SESSION['registro_usuario_temp'] = $usuario;
  header('Location: ../registro.php'); exit;
}

if ($pass1 !== $pass2) {
  poner_mensaje_flasheo('error', 'Las contraseñas no coinciden.');
  $_SESSION['registro_usuario_temp'] = $usuario;
  header('Location: ../registro.php'); exit;
}

if (!isset($cn) || !$cn) { die('No hay conexión a la base de datos.'); }

try {
  // Verificar si el usuario ya existe
  $st = $cn->prepare("SELECT id_usuario FROM usuario WHERE nombre_usuario = ? LIMIT 1");
  $st->bind_param("s", $usuario);
  $st->execute();
  $res    = $st->get_result();
  $existe = $res && $res->num_rows > 0;
  $st->close();

  if ($existe) {
    poner_mensaje_flasheo('error', 'Ese nombre de usuario ya existe.');
    $_SESSION['registro_usuario_temp'] = $usuario;
    header('Location: ../registro.php'); exit;
  }

  // Insertar usuario con contraseña directa
  $rol = 'jugador';
  $st = $cn->prepare("INSERT INTO usuario (nombre_usuario, contrasena, rol) VALUES (?, ?, ?)");
  $st->bind_param("sss", $usuario, $pass1, $rol);
  $ok = $st->execute();
  $nuevo_id = (int)$cn->insert_id;
  $st->close();

  if (!$ok || $nuevo_id <= 0) {
    poner_mensaje_flasheo('error', 'Error al registrar. Intentá nuevamente.');
    $_SESSION['registro_usuario_temp'] = $usuario;
    header('Location: ../registro.php'); exit;
  }

  iniciar_sesion_usuario($nuevo_id, $usuario);
  $_SESSION['rol'] = $rol;

  header('Location: ../menu.php'); exit;

} catch (Throwable $e) {
  poner_mensaje_flasheo('error', 'Error inesperado al registrar. Intentá nuevamente.');
  $_SESSION['registro_usuario_temp'] = $usuario;
  header('Location: ../registro.php'); exit;
}
