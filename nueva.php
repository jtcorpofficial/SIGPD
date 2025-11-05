<?php
// nueva.php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Nueva partida · Draftosaurus</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&display=swap" rel="stylesheet">

  <style>
    body {
      margin: 0;
      font-family: 'Fredoka', sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      background: url('img/fondo.png') center/cover no-repeat fixed ;
      backdrop-filter: blur(8px);
    }

    /* Contenedor principal */
    .contenedor-nueva {
      width: 90%;
      max-width: 480px;
      min-height: 320px; /* más alto */
      padding: 36px 32px;
      background: rgba(255, 255, 255, 0.96);
      border-radius: 22px;
      box-shadow: 0 10px 35px rgba(0, 0, 0, 0.25);
      text-align: center;
      transform: translateY(20px); /* un poco más abajo del centro */
    }

    .titulo {
      font-size: 26px;
      margin-bottom: 20px;
      font-weight: 600;
      color: #222;
    }

    .campo {
      margin: 20px 0;
      text-align: left;
    }

    .campo label {
      display: block;
      font-weight: 500;
      margin-bottom: 6px;
      color: #333;
    }

    select {
      width: 100%;
      padding: 10px 12px;
      border-radius: 10px;
      border: 1px solid #ccc;
      font-size: 16px;
      background: #fff;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .ayuda {
      font-size: 13px;
      color: #666;
      margin-top: 6px;
      text-align: left;
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
      .contenedor-nueva {
        padding: 24px;
        min-height: 280px;
        transform: translateY(10px);
      }
    }
  </style>
</head>

<body>
  <div class="contenedor-nueva">
    <h1 class="titulo">Nueva partida</h1>

    <form action="php/iniciar_partida.php" method="post">
      <div class="campo">
        <label for="cant">Cantidad de jugadores</label>
        <select id="cant" name="cant" required>
          <option value="2">2 jugadores (tú + 1)</option>
          <option value="3">3 jugadores (tú + 2)</option>
          <option value="4">4 jugadores (tú + 3)</option>
        </select>
        <div class="ayuda">Luego verás sus puntajes a la izquierda del tablero.</div>
      </div>

      <div class="acciones">
        <a class="boton boton-fantasma" href="menu.php">Cancelar</a>
        <button class="boton boton-primario" type="submit">Comenzar</button>
      </div>
    </form>
  </div>
</body>
</html>
