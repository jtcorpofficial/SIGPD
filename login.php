<?php
require __DIR__ . '/php/funciones_sesion.php';

$mensaje  = tomar_mensaje_flasheo() ?? null;
$old_user = $_SESSION['login_usuario_temp'] ?? '';
unset($_SESSION['login_usuario_temp']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Iniciar sesión - Draftosaurus</title>

  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/tablero.css">
  <link rel="stylesheet" href="css/stylereglas.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&display=swap" rel="stylesheet">
</head>
<body class="con-fondo">

<nav class="navbar navbar-expand-lg jungle-header">
  <div class="container-fluid">
    <a class="navbar-brand text-white" href="index.php">Draftosaurus</a>
  </div>
</nav>

<main class="d-flex flex-column justify-content-center align-items-center" style="min-height: 80vh;">
  <?php if ($mensaje): ?>
    <div class="mb-3" style="max-width:700px;width:100%;">
      <div style="padding:12px;border-radius:10px;
                  <?= $mensaje['tipo']==='ok' ? 'background:#e6ffed;border:1px solid #b7f5c0;' : 'background:#ffecec;border:1px solid #ffb4b4;' ?>">
        <?= htmlspecialchars($mensaje['texto']) ?>
      </div>
    </div>
  <?php endif; ?>

  <div class="card p-4" style="max-width:400px; width:100%;">
    <h2 class="mb-3">Iniciar sesión</h2>

    <form id="form-login" method="post" action="php/login_procesar.php" novalidate>
      <div class="mb-3">
        <label class="form-label">Nombre de usuario</label>
        <input type="text" class="form-control" name="nombre_usuario" value="<?php echo htmlspecialchars($old_user); ?>" required>
        <div class="invalid-feedback">Ingresá tu usuario (4–20, letras/números/_).</div>
      </div>

      <div class="mb-3">
        <label class="form-label">Contraseña</label>
        <input type="password" class="form-control" name="contrasena" required>
        <div class="invalid-feedback">Ingresá tu contraseña.</div>
      </div>

      <button class="btn btn-safari fw-bold" type="submit">Entrar</button>
    </form>

    <p class="mt-3">¿No tenés cuenta? <a href="registro.php">Crear cuenta</a></p>
  </div>
</main>

<footer class="text-center text-white mt-4 p-3 jungle-header">
  <p class="mb-0">Proyecto Draftosaurus © 2025</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(() => {
  const form = document.getElementById('form-login');
  if (!form) return;
  form.addEventListener('submit', e => {
    const u = form.elements['nombre_usuario'].value.trim();
    const p = form.elements['contrasena'].value;
    let ok = true;

    const userOk = /^[A-Za-z0-9_]{4,20}$/.test(u);
    form.elements['nombre_usuario'].classList.toggle('is-invalid', !userOk);
    if (!userOk) ok = false;

    const passOk = p.length > 0;
    form.elements['contrasena'].classList.toggle('is-invalid', !passOk);
    if (!passOk) ok = false;

    if (!ok) e.preventDefault();
  });
})();
</script>
</body>
</html>
