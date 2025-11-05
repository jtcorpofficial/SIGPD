<?php
require_once __DIR__ . '/php/funciones_sesion.php';
require_once __DIR__ . '/php/conexion.php';
exigir_login();

// Verificar conexión
if (!isset($cn) || !$cn) { die('No hay conexión a la base de datos'); }

// Traer últimas partidas (ajustá el LIMIT si querés)
$partidas = [];
$sql = "SELECT id, fecha_hora, ganador_nombre, ganador_puntos, total_jugadores, duracion_seg
        FROM historial_partida
        ORDER BY id DESC
        LIMIT 100";
$res = $cn->query($sql);
if ($res) {
  while ($row = $res->fetch_assoc()) $partidas[] = $row;
}

function fmt_duracion($seg){
  $seg = (int)$seg;
  $m = floor($seg / 60);
  $s = $seg % 60;
  if ($m > 0) return $m . 'm ' . str_pad($s, 2, '0', STR_PAD_LEFT) . 's';
  return $s . 's';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Draftosaurus · Historial</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600;800&display=swap" />
  <link rel="stylesheet" href="css/styles.css" />
  <style>
    body{ font-family:'Fredoka', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; }
    .hist-card{
      background:#fff; border-radius:16px; box-shadow:0 10px 35px rgba(0,0,0,.12);
      padding:1rem; overflow:hidden;
    }
    .tabla-historial th{ color:#555; text-transform:uppercase; font-size:.85rem; }
    .tabla-historial td, .tabla-historial th{ vertical-align: middle; }
    .badge-win{ background:#16a34a; }
    .btn-detalles{ border-radius:12px; font-weight:700; }
    .no-data{ color:#555; }
  </style>
</head>
<body class="con-fondo d-flex flex-column min-vh-100">

  <!-- Header -->
  <header class="navbar navbar-expand-lg jungle-header sticky-top">
    <div class="container px-4 d-flex justify-content-between align-items-center">
      <a class="navbar-brand text-white fs-3" href="menu.php">Draftosaurus</a>
      <div class="d-flex gap-2">
        <a class="btn btn-safari fw-bold" href="menu.php">Volver</a>
      </div>
    </div>
  </header>

  <!-- Contenido -->
  <main class="flex-grow-1 container py-4">
    <h1 class="text-white mb-3">Historial de partidas</h1>

    <div class="hist-card">
      <?php if (empty($partidas)): ?>
        <p class="no-data m-0">Todavía no hay partidas guardadas.</p>
      <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover tabla-historial">
          <thead>
            <tr>
              <th>ID</th>
              <th>Fecha</th>
              <th>Ganador</th>
              <th class="text-end">Puntos</th>
              <th class="text-center">Jugadores</th>
              <th class="text-center">Duración</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($partidas as $p): ?>
              <tr>
                <td>#<?php echo (int)$p['id']; ?></td>
                <td><?php echo htmlspecialchars($p['fecha_hora'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                  <span class="badge badge-win text-bg-success">
                    <?php echo htmlspecialchars($p['ganador_nombre'], ENT_QUOTES, 'UTF-8'); ?>
                  </span>
                </td>
                <td class="text-end"><?php echo (int)$p['ganador_puntos']; ?> pts</td>
                <td class="text-center"><?php echo (int)$p['total_jugadores']; ?></td>
                <td class="text-center"><?php echo fmt_duracion($p['duracion_seg']); ?></td>
                <td class="text-end">
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </main>

  <!-- Footer -->
  <footer class="text-white text-center py-3">
    <p class="m-0">Desarrollado por JT Corp © 2025</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
