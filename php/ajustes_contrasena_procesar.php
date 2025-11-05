<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/funciones_sesion.php';
exigir_login();

// Validar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../ajustes.php'); exit;
}

$id_usuario         = (int)($_SESSION['id_usuario'] ?? 0);
$contrasena_actual  = trim($_POST['contrasena_actual']  ?? '');
$contrasena_nueva   = trim($_POST['contrasena_nueva']   ?? '');
$contrasena_repetir = trim($_POST['contrasena_repetir'] ?? '');

// Validaciones básicas
if ($contrasena_actual === '' || $contrasena_nueva === '' || $contrasena_repetir === '') {
  poner_mensaje_flasheo('error', 'Completá todos los campos de contraseña.');
  header('Location: ../ajustes.php'); exit;
}
if (strlen($contrasena_nueva) < 6) {
  poner_mensaje_flasheo('error', 'La nueva contraseña debe tener al menos 6 caracteres.');
  header('Location: ../ajustes.php'); exit;
}
if ($contrasena_nueva !== $contrasena_repetir) {
  poner_mensaje_flasheo('error', 'Las contraseñas nuevas no coinciden.');
  header('Location: ../ajustes.php'); exit;
}

// Verificar conexión
if (!isset($cn) || !$cn) { die('No hay conexión a la base de datos.'); }

// Traer contraseña actual desde la BD
$stmt = $cn->prepare("SELECT contrasena FROM usuario WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();
$row = $res ? $res->fetch_assoc() : null;
$stmt->close();

if (!$row) {
  poner_mensaje_flasheo('error', 'No se encontró el usuario.');
  header('Location: ../ajustes.php'); exit;
}

$contrasenaBD = $row['contrasena'] ?? '';

// Verificar contraseña actual (texto plano)
if ($contrasena_actual !== $contrasenaBD) {
  poner_mensaje_flasheo('error', 'La contraseña actual no es correcta.');
  header('Location: ../ajustes.php'); exit;
}

// Evitar que la nueva sea igual a la actual
if ($contrasena_nueva === $contrasenaBD) {
  poner_mensaje_flasheo('error', 'La nueva contraseña no puede ser igual a la actual.');
  header('Location: ../ajustes.php'); exit;
}

// Guardar nueva contraseña (directa)
$stmt = $cn->prepare("UPDATE usuario SET contrasena = ? WHERE id_usuario = ?");
$stmt->bind_param("si", $contrasena_nueva, $id_usuario);
$ok = $stmt->execute();
$stmt->close();

if (!$ok) {
  poner_mensaje_flasheo('error', 'No se pudo actualizar la contraseña. Intentalo de nuevo.');
  header('Location: ../ajustes.php'); exit;
}

poner_mensaje_flasheo('ok', '¡Contraseña actualizada correctamente!');
header('Location: ../ajustes.php'); exit;
