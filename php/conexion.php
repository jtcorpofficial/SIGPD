<?php
$host       = 'localhost';
$usuario    = 'root';
$contrasena = ''; 
$base       = 'bd-jtcorp';

$cn = new mysqli($host, $usuario, $contrasena, $base);
if ($cn->connect_error) {
  die('Error de conexión a MySQL: ' . $cn->connect_error);
}

$cn->set_charset('utf8mb4');
