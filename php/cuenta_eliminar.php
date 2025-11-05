<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/funciones_sesion.php';

// Asegurarse de que el usuario está logueado
exigir_login();

$id_usuario         = (int)($_POST['id_usuario'] ?? 0);
$contrasena_confirm = trim($_POST['contrasena_confirm'] ?? '');
$acepto             = isset($_POST['acepto']);

if ($id_usuario <= 0 || !$acepto) {
  poner_mensaje_flasheo('error', 'Datos inválidos o falta confirmación.');
  header('Location: ../ajustes.php');
  exit;
}

// Validar que haya ingresado la contraseña
if ($contrasena_confirm === '') {
  poner_mensaje_flasheo('error', 'Debés ingresar tu contraseña para confirmar.');
  header('Location: ../ajustes.php');
  exit;
}

try {
  // Verificar que la contraseña coincida
  $st = $cn->prepare("SELECT contrasena FROM usuario WHERE id_usuario = ?");
  $st->bind_param("i", $id_usuario);
  $st->execute();
  $res = $st->get_result();
  $fila = $res->fetch_assoc();
  $st->close();

  if (!$fila || $fila['contrasena'] !== $contrasena_confirm) {
    poner_mensaje_flasheo('error', 'La contraseña ingresada no es correcta.');
    header('Location: ../ajustes.php');
    exit;
  }

  // Iniciar una transacción
  $cn->begin_transaction();

  // Eliminar registros relacionados (historial, partidas, etc.)
  $st = $cn->prepare("DELETE FROM historial_jugador WHERE usuario_id = ?");
  $st->bind_param("i", $id_usuario);
  $st->execute();
  $st->close();

  $st = $cn->prepare("DELETE FROM juega WHERE id_usuario = ?");
  $st->bind_param("i", $id_usuario);
  $st->execute();
  $st->close();

  // Finalmente eliminar el usuario
  $st = $cn->prepare("DELETE FROM usuario WHERE id_usuario = ?");
  $st->bind_param("i", $id_usuario);
  $st->execute();
  $filas = $st->affected_rows;
  $st->close();

  if ($filas <= 0) {
    throw new Exception("No se pudo eliminar el usuario.");
  }

  // Confirmar la transacción
  $cn->commit();

  // Cerrar sesión y redirigir
  cerrar_sesion_usuario();
  poner_mensaje_flasheo('ok', 'Tu cuenta fue eliminada correctamente.');
  header('Location: ../index.php');
  exit;

} catch (Throwable $e) {
  $cn->rollback();
  poner_mensaje_flasheo('error', 'Error al eliminar la cuenta. Intentá nuevamente.');
  header('Location: ../ajustes.php');
  exit;
}
