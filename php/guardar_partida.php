<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/conexion.php';

// Validar conexión
if (!isset($cn) || !$cn) {
  http_response_code(500);
  echo json_encode(['ok'=>false, 'msg'=>'Sin conexión a la base de datos']);
  exit;
}

// Leer JSON del cuerpo
$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data || !isset($data['resultados']) || !is_array($data['resultados'])) {
  http_response_code(400);
  echo json_encode(['ok'=>false, 'msg'=>'Payload inválido']);
  exit;
}

// Datos principales
$resultados = $data['resultados']; 
$startedAt  = isset($data['startedAt']) ? (int)$data['startedAt'] : 0;
$duracion   = $startedAt ? max(0, time() - $startedAt) : 0;

if (count($resultados) === 0) {
  http_response_code(400);
  echo json_encode(['ok'=>false, 'msg'=>'Sin resultados']);
  exit;
}

// Ordenar por puntos DESC por seguridad
usort($resultados, function($a, $b){
  return ((int)($b['puntos'] ?? 0)) <=> ((int)($a['puntos'] ?? 0));
});

$ganadorNombre = (string)($resultados[0]['nombre'] ?? 'Desconocido');
$ganadorPuntos = (int)($resultados[0]['puntos'] ?? 0);
$totalJug      = count($resultados);

// ID de usuario en sesión (si corresponde)
$session_user_id = !empty($_SESSION['id_usuario']) ? (int)$_SESSION['id_usuario'] : null;

try {
  // Iniciar transacción
  $cn->begin_transaction();

  // Insert en historial_partida
  $st = $cn->prepare("INSERT INTO historial_partida (ganador_nombre, ganador_puntos, total_jugadores, duracion_seg)
                      VALUES (?, ?, ?, ?)");
  $st->bind_param("siii", $ganadorNombre, $ganadorPuntos, $totalJug, $duracion);
  $ok = $st->execute();
  if (!$ok) throw new Exception('No se pudo crear la partida');
  $partidaId = (int)$cn->insert_id;
  $st->close();

  // Insert de jugadores
  $st2 = $cn->prepare("INSERT INTO historial_jugador (partida_id, nombre, usuario_id, puntos, posicion)
                       VALUES (?, ?, ?, ?, ?)");

  $pos = 1;
  foreach ($resultados as $r) {
    $nombre = (string)($r['nombre'] ?? 'Jugador');
    $puntos = (int)($r['puntos'] ?? 0);
    $idx    = isset($r['idx']) ? (int)$r['idx'] : null;

    // Si viene usuario_id en el payload, usarlo; si no, si es el primer jugador (índice 0), usar el de la sesión (si existe)
    $usuario_id = null;
    if (array_key_exists('usuario_id', $r)) {
      $usuario_id = ($r['usuario_id'] !== null && $r['usuario_id'] !== '') ? (int)$r['usuario_id'] : null;
    } elseif ($idx === 0 && $session_user_id) {
      $usuario_id = $session_user_id;
    }

    // Insertar jugador
    $st2->bind_param("isiii", $partidaId, $nombre, $usuario_id, $puntos, $pos);
    $ok = $st2->execute();
    if (!$ok) throw new Exception('No se pudo insertar jugador en historial');
    $pos++;
  }
  $st2->close();

  // Confirmar
  $cn->commit();

  echo json_encode(['ok'=>true, 'partida_id'=>$partidaId]);
} catch (Throwable $e) {
  // Revertir
  try { $cn->rollback(); } catch (Throwable $e2) {}
  http_response_code(500);
  echo json_encode(['ok'=>false, 'msg'=>'Error al guardar', 'err'=>$e->getMessage()]);
}
