<?php
require __DIR__ . '/conexion.php';
$r = $cn->query("SELECT DATABASE() AS db")->fetch_assoc();
echo "BD actual: " . $r['db'] . "\n";

$r = $cn->query("SELECT id_usuario, nombre_usuario, contrasena, LENGTH(contrasena) AS largo 
                 FROM usuario WHERE nombre_usuario='admin'")->fetch_assoc();
var_dump($r);
