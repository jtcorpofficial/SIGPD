<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/funciones_sesion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../login.php'); exit;
}

$usuario = trim($_POST['nombre_usuario'] ?? '');
$pass    = trim($_POST['contrasena'] ?? '');

if (!preg_match('/^[A-Za-z0-9_]{4,20}$/', $usuario)) {
  poner_mensaje_flasheo('error', 'Usuario inválido.');
  $_SESSION['login_usuario_temp'] = $usuario;
  header('Location: ../login.php'); exit;
}
if ($pass === '') {
  poner_mensaje_flasheo('error', 'Ingresá tu contraseña.');
  $_SESSION['login_usuario_temp'] = $usuario;
  header('Location: ../login.php'); exit;
}

if (!isset($cn) || !$cn) { die('No hay conexión a la base de datos'); }

try {
  // Buscar usuario
  $st = $cn->prepare("SELECT id_usuario, contrasena, nombre_usuario, rol FROM usuario WHERE nombre_usuario = ? LIMIT 1");
  $st->bind_param("s", $usuario);
  $st->execute();
  $res  = $st->get_result();
  $fila = $res ? $res->fetch_assoc() : null;
  $st->close();

  // Comparar directamente (sin hash)
  if (!$fila || $pass !== $fila['contrasena']) {
    poner_mensaje_flasheo('error', 'Usuario o contraseña incorrectos.');
    $_SESSION['login_usuario_temp'] = $usuario;
    header('Location: ../login.php'); exit;
  }

  // Iniciar sesión
  iniciar_sesion_usuario((int)$fila['id_usuario'], $fila['nombre_usuario']);
  $_SESSION['rol'] = $fila['rol'];

  header('Location: ../menu.php'); exit;

} catch (Throwable $e) {
  poner_mensaje_flasheo('error', 'Error inesperado al iniciar sesión.');
  $_SESSION['login_usuario_temp'] = $usuario;
  header('Location: ../login.php'); exit;
}
