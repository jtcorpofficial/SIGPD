<?php
require __DIR__.'/php/funciones_sesion.php';
exigir_login();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Menú - Draftosaurus</title>

  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/tablero.css">
  <link rel="stylesheet" href="css/stylereglas.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600;700;800&display=swap" rel="stylesheet">

  <style>
   .menu-columna{
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 14px;
  margin: 0 auto;
}
.menu-columna .btn-menu {
    width: 260px; /* mismo ancho para todos */
    padding: 14px 18px;
    font-size: 1.2rem;
    border-radius: 14px;
    text-align: center;
    margin-top: 30px;

    /* estilos visuales */
    background-color: #f5f5dc; /* beige */
    color: #333; /* texto oscuro */
    border: 1px solid #ddd;
    box-shadow: 6px 6px 12px rgba(0,0,0,0.15);

    transition: transform .2s ease, box-shadow .2s ease, background-color .3s ease;
}


.menu-columna .btn-menu:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.25);
    background-color: #e0e0b8; /* beige más oscuro al pasar el mouse */
}

/* Botón Salir normal, rojo solo en hover */
.menu-columna .btn-salir{
  background-color: #f8fafc;
  color: #111;
  border: 1px solid #ddd;
}
.menu-columna .btn-salir:hover{
  background-color: #dc2626 !important;
  color: #fff !important;
  border-color: #b91c1c !important;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    position: relative;
    min-height: 100vh;
    overflow-x: hidden;
}

body::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: url("img/fondo.png") no-repeat center center fixed;
    background-size: cover;
    filter: blur(6px); /* intensidad del blur */
    z-index: -1; /* se queda detrás de todo */
}
.btn-volver {
    background-color: #f8f4dc;   /* beige */
    color: #333;                /* texto oscuro */
    padding: 8px 16px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: bold;
    box-shadow: 2px 4px 8px rgba(0,0,0,0.2);
    transition: all 0.2s ease;
}

.btn-volver:hover {
    background-color: #e2d9b3;   /* beige más oscuro */
    color: #000;
    transform: translateY(-2px);
}
.menu-contenedor {
    background: #ffffff;   /* blanco sólido */
    border-radius: 20px;   /* bordes redondeados */
    padding: 40px 60px;    /* espacio interno */
    text-align: center;
    display: inline-block; /* se ajusta al contenido */
    box-shadow: 0 8px 20px rgba(0,0,0,0.3); /* sombra elegante */
    margin: 0 auto;        /* centrado horizontal */
}
main {
    display: flex;
    justify-content: center; /* centrado horizontal */
    align-items: center;     /* centrado vertical */
    min-height: 100vh;       /* que ocupe toda la pantalla */
}

.titulo-bienvenida {
    font-weight: 900;  /* super grueso */
    font-size: 2.5rem; /* un poco más grande (opcional) */
    color: #0d300eff;    /* aseguramos buen contraste */
}

  </style>
</head>
<body class="d-flex flex-column min-vh-100">

<!-- Barra superior -->
<nav class="navbar navbar-expand-lg jungle-header">
  <div class="container-fluid">
    <a href="index.php" class="btn-volver">Salir</a>
  </div>
</nav>

<main class="container py-5 flex-grow-1">

    <!-- Bloque con fondo blanco -->
    <div class="menu-contenedor">
        
        <!-- Título grande centrado -->
        <div class="text-center mb-4">
            <h1 class="titulo-bienvenida">¡Bienvenido a Draftosaurus!</h1>
        </div>

        <!-- Columna centrada con todas las opciones -->
        <div class="menu-columna">
            <a href="nueva.php" class="btn btn-safari btn-menu">Nueva Partida</a>
            <a href="ranking_global.php" class="btn btn-safari btn-menu">Ranking Global</a>
            <a href="historial.php" class="btn btn-safari btn-menu">Historial de Partidas</a>
            <?php if ($_SESSION['rol'] === 'admin'): ?>
              <a href="admin_usuarios.php" class="btn btn-safari btn-menu" style="background-color:#f8d7da;">Administrar usuarios</a>
            <?php endif; ?>
            <a href="ajustes.php" class="btn btn-safari btn-menu">Ajustes</a>
            <a href="index.php" class="btn btn-safari btn-menu btn-salir">Salir</a>
        </div>

    </div>
</main>

<!-- Footer pegado abajo -->
<footer class="text-center text-white p-3 jungle-header mt-auto">
  <p class="mb-0">Proyecto Draftosaurus © 2025</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
