<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function exigir_login() {              
  if (empty($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
  }
}
function requerir_autenticacion() {    
  exigir_login();
}

function iniciar_sesion_usuario($id, $nombre) {
  $_SESSION['id_usuario'] = (int)$id;
  $_SESSION['nombre_usuario'] = $nombre;
}

function cerrar_sesion_usuario() {
  $_SESSION = [];
  if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
  }
  session_destroy();
}

function poner_mensaje_flasheo($tipo, $texto) {
  $_SESSION['mensaje_flasheo'] = ['tipo' => $tipo, 'texto' => $texto];
}

function tomar_mensaje_flasheo() {
  if (!isset($_SESSION['mensaje_flasheo'])) return null;
  $m = $_SESSION['mensaje_flasheo'];
  unset($_SESSION['mensaje_flasheo']);
  return $m;
}


