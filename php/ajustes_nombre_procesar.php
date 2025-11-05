<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/funciones_sesion.php';
exigir_login();

// Validar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../ajustes.php'); exit;
}

$id_usuario    = (int)($_SESSION['id_usuario'] ?? 0);
$nombre_actual = trim($_SESSION['nombre_usuario'] ?? '');
$nuevo_nombre  = trim($_POST['nuevo_nombre'] ?? '');

if ($nuevo_nombre === '' || mb_strlen($nuevo_nombre) < 3) {
  poner_mensaje_flasheo('error', 'El nuevo nombre debe tener al menos 3 caracteres.');
  header('Location: ../ajustes.php'); exit;
}

// ¿Es el mismo nombre?
if (mb_strtolower($nuevo_nombre) === mb_strtolower($nombre_actual)) {
  poner_mensaje_flasheo('error', 'El nuevo nombre no puede ser igual al actual.');
  header('Location: ../ajustes.php'); exit;
}

// Verificar conexión
if (!isset($cn) || !$cn) { die('No hay conexión a la base de datos.'); }

// ¿Ya existe alguien con ese nombre?
$stmt = $cn->prepare("SELECT id_usuario FROM usuario WHERE nombre_usuario = ? AND id_usuario <> ?");
$stmt->bind_param("si", $nuevo_nombre, $id_usuario);
$stmt->execute();
$res = $stmt->get_result();
$existe = $res ? ($res->num_rows > 0) : false;
$stmt->close();

if ($existe) {
  poner_mensaje_flasheo('error', 'Ese nombre de usuario ya está en uso.');
  header('Location: ../ajustes.php'); exit;
}

// Actualizar nombre
$stmt = $cn->prepare("UPDATE usuario SET nombre_usuario = ? WHERE id_usuario = ?");
$stmt->bind_param("si", $nuevo_nombre, $id_usuario);
$ok = $stmt->execute();
$stmt->close();

if (!$ok) {
  poner_mensaje_flasheo('error', 'No se pudo guardar el nombre. Intentá nuevamente.');
  header('Location: ../ajustes.php'); exit;
}

// Actualizar sesión y feedback
$_SESSION['nombre_usuario'] = $nuevo_nombre;
poner_mensaje_flasheo('ok', '¡Nombre actualizado correctamente!');
header('Location: ../ajustes.php'); exit;

