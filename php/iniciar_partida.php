<?php
require_once __DIR__ . '/funciones_sesion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../nueva.php'); exit;
}

$cant = isset($_POST['cant']) ? (int)$_POST['cant'] : 0;
if ($cant < 2 || $cant > 4) {
  poner_mensaje_flasheo('error', 'Cantidad de jugadores inválida.');
  header('Location: ../nueva.php'); exit;
}

$idUsuario = (int)($_SESSION['id_usuario'] ?? 0);
$nombreUsu = isset($_SESSION['nombre_usuario']) ? trim((string)$_SESSION['nombre_usuario']) : '';

$jugadores   = [];
$jugadores[] = [
  'id'     => $idUsuario,
  'nombre' => ($nombreUsu !== '' ? $nombreUsu : 'Jugador 1'),
  'puntos' => 0
];

for ($i = 2; $i <= $cant; $i++) {
  $jugadores[] = [
    'id'     => 0,
    'nombre' => 'Jugador ' . $i,
    'puntos' => 0
  ];
}

$_SESSION['partida'] = [
  'cantidad_jugadores' => $cant,
  'jugadores'          => $jugadores,
  'ronda'              => 1,
  'turno'              => 1,
  'historial_dados'    => [],
  'estado'             => 'preparada'
];
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Partida preparada · Draftosaurus</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../css/styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&display=swap" rel="stylesheet">

  <style>
    body {
      margin: 0;
      font-family: 'Fredoka', sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      background: url('../img/fondo.png') center/cover no-repeat fixed;
      backdrop-filter: blur(8px);
    }

    .contenedor {
      width: 90%;
      max-width: 480px;
      min-height: 320px;
      padding: 36px 32px;
      background: rgba(255, 255, 255, 0.96);
      border-radius: 22px;
      box-shadow: 0 10px 35px rgba(0, 0, 0, 0.25);
      text-align: center;
      transform: translateY(20px);
    }

    h1 {
      font-size: 26px;
      font-weight: 600;
      margin-bottom: 22px;
      color: #222;
    }

    p {
      margin: 10px 0;
      font-size: 16px;
      color: #333;
    }

    ul {
      list-style: disc;
      padding-left: 24px;
      text-align: left;
      color: #444;
      font-size: 15px;
      margin: 14px 0 36px;
    }

    .acciones {
      display: flex;
      gap: 14px;
      justify-content: flex-end;
      margin-top: 100px;
    }

    .boton {
      border: 0;
      padding: 12px 20px;
      border-radius: 10px;
      cursor: pointer;
      font-weight: 600;
      transition: all 0.2s ease;
      text-decoration: none;
      display: inline-block;
    }

    .boton-primario {
      background: #f0a800;
      color: #fff;
    }

    .boton-primario:hover {
      background: #d38f00;
    }

    .boton-fantasma {
      background: #eee;
      color: #333;
    }

    .boton-fantasma:hover {
      background: #ddd;
    }

    @media (max-width: 576px) {
      .contenedor {
        padding: 24px;
        min-height: 280px;
        transform: translateY(10px);
      }
    }
  </style>
</head>

<body>
  <div class="contenedor">
    <h1>Partida preparada</h1>
    <p><strong>Cantidad de jugadores:</strong> <?php echo htmlspecialchars($cant, ENT_QUOTES, 'UTF-8'); ?></p>
    <p><strong>Lista de jugadores:</strong></p>
    <ul>
      <?php foreach ($jugadores as $j): ?>
        <li><?php echo htmlspecialchars($j['nombre'], ENT_QUOTES, 'UTF-8'); ?></li>
      <?php endforeach; ?>
    </ul>

    <div class="acciones">
      <a class="boton boton-fantasma" href="../menu.php">Volver al menú</a>
      <a class="boton boton-primario" href="../tablero.php">Ir al tablero</a>
    </div>
  </div>
</body>
</html>
