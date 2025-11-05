<?php
require __DIR__ . '/php/funciones_sesion.php';
exigir_login();
$mensaje = tomar_mensaje_flasheo();

$usuario_actual = $_SESSION['nombre_usuario'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ajustes de usuario - Draftosaurus</title>

  <link rel="stylesheet" href="css/styles.css" />
  <link rel="stylesheet" href="css/tablero.css" />
  <link rel="stylesheet" href="css/stylereglas.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&display=swap" rel="stylesheet" />
  <style>
  .fredoka { font-family:'Fredoka', system-ui, sans-serif !important; }
  .jungle-header .btn.btn-volver{
    background-color:#f5f5dc !important; color:#4e3422 !important; border:1px solid #e6dcc0 !important;
    font-weight:600; padding:8px 16px; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.15);
    transition: background-color .2s, transform .2s, box-shadow .2s;
  }
  .jungle-header .btn.btn-volver:hover{ background-color:#efe7c8 !important; color:#3d291c !important;
    transform: translateY(-2px); box-shadow:0 6px 16px rgba(0,0,0,.2); }
  .jungle-header .btn.btn-volver:focus{ outline:3px solid rgba(78,52,34,.35); outline-offset:2px; }
  </style>
</head>
<body class="con-fondo d-flex flex-column min-vh-100">
  <div class="fondo-desenfocado" aria-hidden="true"></div>

  <!-- Barra superior -->
  <nav class="navbar navbar-expand-lg jungle-header">
    <div class="container-fluid">
      <a href="menu.php" class="btn btn-volver fredoka">Volver</a>
    </div>
  </nav>

  <?php if ($mensaje): ?>
    <script>
      alert("<?= htmlspecialchars($mensaje['texto'], ENT_QUOTES, 'UTF-8') ?>");
    </script>
  <?php endif; ?>

  <main class="container py-5 flex-grow-1">
    <h1 class="mb-4 text-center titulo-con-caja">Ajustes de usuario</h1>

    <div class="row g-4" style="max-width:900px;margin:0 auto;">
      <!-- Cambiar nombre de usuario -->
      <div class="col-12">
        <div class="card p-4">
          <h3 class="mb-3">Cambiar nombre de usuario</h3>
          <p class="mb-2">Actual: <strong><?= htmlspecialchars($usuario_actual, ENT_QUOTES, 'UTF-8') ?></strong></p>

          <form action="php/ajustes_nombre_procesar.php" method="POST" autocomplete="off">
            <div class="mb-3">
              <label for="nuevo_nombre" class="form-label">Nuevo nombre</label>
              <input type="text" class="form-control" id="nuevo_nombre" name="nuevo_nombre" minlength="3" required>
            </div>
            <button type="submit" class="btn btn-safari">Guardar nombre</button>
          </form>
        </div>
      </div>

      <!-- Cambiar contraseña -->
      <div class="col-12">
        <div class="card p-4">
          <h3 class="mb-3">Cambiar contraseña</h3>
          <form action="php/ajustes_contrasena_procesar.php" method="POST" autocomplete="off">
            <div class="mb-3">
              <label for="contrasena_actual" class="form-label">Contraseña actual</label>
              <input type="password" class="form-control" id="contrasena_actual" name="contrasena_actual" required>
            </div>
            <div class="mb-3">
              <label for="contrasena_nueva" class="form-label">Nueva contraseña</label>
              <input type="password" class="form-control" id="contrasena_nueva" name="contrasena_nueva" minlength="6" required>
            </div>
            <div class="mb-3">
              <label for="contrasena_repetir" class="form-label">Repetir nueva contraseña</label>
              <input type="password" class="form-control" id="contrasena_repetir" name="contrasena_repetir" minlength="6" required>
            </div>
            <button type="submit" class="btn btn-safari">Guardar contraseña</button>
          </form>
        </div>
      </div>

      <!-- Idioma -->
      <div class="col-12">
        <div class="card p-4">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h3 class="mb-0">Idioma</h3>
            <span class="badge bg-warning text-dark" style="font-weight:600;">Próximamente</span>
          </div>

          <form onsubmit="event.preventDefault(); alert('La opción de idioma estará disponible más adelante.');">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="idioma" id="idioma_es" value="es" checked>
              <label class="form-check-label" for="idioma_es">Español</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="idioma" id="idioma_en" value="en">
              <label class="form-check-label" for="idioma_en">Inglés</label>
            </div>

            <div class="mt-3">
              <button type="submit" class="btn btn-safari">Guardar idioma</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Zona peligrosa -->
      <div class="col-12">
        <div class="card p-4 border-danger">
          <h3 class="mb-2 text-danger">Zona peligrosa</h3>
          <p class="mb-3">
            Eliminar tu cuenta es <strong>permanente</strong>. Se cerrará tu sesión y tu nombre
            dejará de aparecer vinculado a tus partidas (el historial conservará los registros
            pero sin asociarlos a tu usuario).
          </p>

          <form action="php/cuenta_eliminar.php" method="post"
                onsubmit="return confirm('¿Seguro que querés eliminar tu cuenta? Esta acción no se puede deshacer.');">
            <div class="mb-3">
              <label for="conf_pass" class="form-label">Escribí tu contraseña para confirmar</label>
              <input type="password" name="contrasena_confirm" id="conf_pass"
                    class="form-control" required minlength="6" autocomplete="off">
            </div>

            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" value="1" id="acepto" name="acepto" required>
              <label class="form-check-label" for="acepto">
                Sí, entiendo que esta acción es irreversible.
              </label>
            </div>
        <input type="hidden" name="id_usuario" value="<?php echo $_SESSION['id_usuario'] ?? 0; ?>">
            <button type="submit" class="btn btn-danger fw-bold">
              Eliminar mi cuenta
            </button>
          </form>
        </div>
      </div>

    </div>
  </main>

  <footer class="text-center text-white mt-4 p-3 jungle-header">
    <p class="mb-0">Proyecto Draftosaurus © 2025</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
