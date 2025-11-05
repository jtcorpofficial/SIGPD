<?php
require_once __DIR__ . '/php/funciones_sesion.php';
require_once __DIR__ . '/php/conexion.php';
exigir_login();

// Verificar conexión
if (!isset($cn) || !$cn) { die('No hay conexión a la base de datos'); }

// Sanitizar orden mediante whitelist
$orden = isset($_GET['orden']) ? strtolower(trim($_GET['orden'])) : 'victorias';
$ordenMap = [
  'victorias' => 'victorias DESC, promedio_puntos DESC, puntos_totales DESC',
  'promedio'  => 'promedio_puntos DESC, victorias DESC, puntos_totales DESC',
  'puntos'    => 'puntos_totales DESC, victorias DESC, promedio_puntos DESC',
  'partidas'  => 'partidas DESC, victorias DESC, promedio_puntos DESC'
];
$orderSql = isset($ordenMap[$orden]) ? $ordenMap[$orden] : $ordenMap['victorias'];

// Mínimo de partidas (>=1)
$minPartidas = isset($_GET['min']) ? max(1, (int)$_GET['min']) : 1;

// Consulta (solo humanos: usuario_id IS NOT NULL)
$sql = "
  SELECT
    hj.usuario_id,
    COALESCE(MAX(u.nombre_usuario), MAX(hj.nombre)) AS nombre_mostrar,
    COUNT(*) AS partidas,
    SUM(hj.puntos) AS puntos_totales,
    AVG(hj.puntos) AS promedio_puntos,
    MAX(hj.puntos) AS mejor_puntaje,
    SUM(CASE WHEN hj.posicion = 1 THEN 1 ELSE 0 END) AS victorias
  FROM historial_jugador hj
  LEFT JOIN usuario u ON u.id_usuario = hj.usuario_id
  WHERE hj.usuario_id IS NOT NULL
  GROUP BY hj.usuario_id
  HAVING partidas >= ?
  ORDER BY {$orderSql}
  LIMIT 100
";

// Ejecutar preparada (bind en HAVING)
$rows = [];
if ($stmt = $cn->prepare($sql)) {
  $stmt->bind_param("i", $minPartidas);
  $stmt->execute();
  $res = $stmt->get_result();
  if ($res) {
    while ($r = $res->fetch_assoc()) { $rows[] = $r; }
  }
  $stmt->close();
}

// Helper para porcentaje
function pct($num, $den){
  if ($den <= 0) return '0%';
  return number_format(($num*100.0)/$den, 1) . '%';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Draftosaurus · Ranking global</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600;800&display=swap"/>
  <link rel="stylesheet" href="css/styles.css"/>
  <style>
    body{ font-family:'Fredoka', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; }
    .rk-card{ background:#fff; border-radius:16px; box-shadow:0 10px 35px rgba(0,0,0,.12); padding:1rem; }
    .tabla-rk th{ color:#555; text-transform:uppercase; font-size:.85rem; }
    .pos{ width:64px; }
    .toolbar{ display:flex; justify-content:flex-end; margin-bottom:.75rem; }
    .toolbar form{ display:flex; align-items:center; gap:.5rem; }
    .toolbar label{ margin:0; color:#555; }
    .toolbar .form-control{ border-radius:10px; width:120px; }
    .toolbar .btn{ border-radius:10px; }
    .pill{ border-radius:999px; padding:.25rem .6rem; font-size:.85rem; }
    .pill-win{ background:#ecfdf5; color:#065f46; border:1px solid #bbf7d0; }
  </style>
</head>
<body class="con-fondo d-flex flex-column min-vh-100">

  <!-- Header -->
  <header class="navbar navbar-expand-lg jungle-header sticky-top">
    <div class="container px-4 d-flex justify-content-between align-items-center">
      <a class="navbar-brand text-white fs-3" href="menu.php">Draftosaurus</a>
      <div class="d-flex gap-2">
        <a class="btn btn-light fw-bold" href="menu.php">Menú</a>
      </div>
    </div>
  </header>

  <!-- Contenido -->
  <main class="flex-grow-1 container py-4">
    <h1 class="text-white mb-3">Ranking global</h1>

    <div class="rk-card">
      <div class="toolbar">
        <form method="get" action="">
          <input type="hidden" name="orden" value="<?php echo htmlspecialchars($orden, ENT_QUOTES, 'UTF-8'); ?>"/>
          <label>Mín. partidas:</label>
          <input class="form-control" type="number" min="1" name="min" value="<?php echo (int)$minPartidas; ?>" />
          <button class="btn btn-dark" type="submit">Aplicar</button>
        </form>
      </div>

      <?php if (empty($rows)): ?>
        <p class="m-0">No hay datos suficientes para el ranking.</p>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-hover align-middle tabla-rk">
            <thead>
              <tr>
                <th class="pos text-center">#</th>
                <th>Jugador</th>
                <th class="text-center">Victorias</th>
                <th class="text-center">Winrate</th>
                <th class="text-center">Partidas</th>
                <th class="text-end">Promedio</th>
                <th class="text-end">Puntos totales</th>
                <th class="text-end">Mejor puntaje</th>
              </tr>
            </thead>
            <tbody>
              <?php $pos=1; foreach ($rows as $r): ?>
                <tr>
                  <td class="text-center fw-bold">#<?php echo $pos++; ?></td>
                  <td><?php echo htmlspecialchars($r['nombre_mostrar'], ENT_QUOTES, 'UTF-8'); ?></td>
                  <td class="text-center"><span class="pill pill-win"><?php echo (int)$r['victorias']; ?></span></td>
                  <td class="text-center"><?php echo pct((int)$r['victorias'], (int)$r['partidas']); ?></td>
                  <td class="text-center"><?php echo (int)$r['partidas']; ?></td>
                  <td class="text-end"><?php echo number_format((float)$r['promedio_puntos'], 2); ?></td>
                  <td class="text-end"><?php echo (int)$r['puntos_totales']; ?></td>
                  <td class="text-end"><?php echo (int)$r['mejor_puntaje']; ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </main>

  <footer class="text-white text-center py-3">
    <p class="m-0">Desarrollado por JT Corp © 2025</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
