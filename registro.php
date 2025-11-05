<?php
require __DIR__.'/php/funciones_sesion.php';
$mensaje = tomar_mensaje_flasheo();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro - Draftosaurus</title>
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/tablero.css">
  <link rel="stylesheet" href="css/stylereglas.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg jungle-header">
  <div class="container-fluid">
    <a class="navbar-brand text-white" href="index.php">Draftosaurus</a>
  </div>
</nav>

<?php if ($mensaje): ?>
  <div class="container mt-3">
    <div class="alert <?= $mensaje['tipo']==='ok' ? 'alert-success' : 'alert-danger' ?> mb-0" role="alert">
      <?= htmlspecialchars($mensaje['texto']) ?>
    </div>
  </div>
<?php endif; ?>

<!-- Formulario de registro -->
<section class="d-flex flex-column justify-content-center align-items-center text-center" style="min-height: 80vh;">
  <div class="card p-4" style="max-width:400px; width:100%;">
    <h2 class="mb-3">Crear cuenta</h2>
    <form id="form-registro" method="post" action="php/registro_procesar.php" novalidate>
  <div class="mb-3">
    <label class="form-label">Nombre de usuario</label>
    <input type="text" class="form-control" name="nombre_usuario" id="nombre_usuario" required>
    <div class="invalid-feedback">El nombre no es válido.</div>
  </div>

  <div class="mb-3">
    <label class="form-label">Contraseña</label>
    <input type="password" class="form-control" name="contrasena" id="contrasena" required>
    <div class="invalid-feedback">La contraseña no cumple los requisitos.</div>
  </div>

  <div class="mb-3">
    <label class="form-label">Repetir contraseña</label>
    <input type="password" class="form-control" name="contrasena2" id="contrasena2" required>
    <div class="invalid-feedback">Las contraseñas no coinciden.</div>
  </div>

  <button class="btn btn-safari fw-bold" type="submit">Crear cuenta</button>
</form>

    <p class="mt-3">¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
  </div>
</section>

<footer class="text-center text-white mt-4 p-3 jungle-header">
  <p class="mb-0">Proyecto Draftosaurus © 2025</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php unset($_SESSION['registro_usuario_temp']); ?>

</body>

<script>
(function(){
  const form = document.getElementById('form-registro');
  if (!form) return;

  const nombre = form.querySelector('[name="nombre_usuario"]');
  const pass   = form.querySelector('[name="contrasena"]');
  const pass2  = form.querySelector('[name="contrasena2"]');

  function clearInvalid(el){
    el.classList.remove('is-invalid');
  }
  function setInvalid(el, msg){
    el.classList.add('is-invalid');
    const fb = el.nextElementSibling;
    if (fb && fb.classList.contains('invalid-feedback')) fb.textContent = msg;
  }

  form.addEventListener('submit', function(e){
    let ok = true;
    [nombre, pass, pass2].forEach(clearInvalid);

    // Usuario: 4–20, alfanumérico o _
    const user = (nombre.value || '').trim();
    if (!/^[a-zA-Z0-9_]{4,20}$/.test(user)) {
      ok = false;
      setInvalid(nombre, 'Usá 4–20 caracteres (letras, números o _).');
    }

    // Contraseña: 8–64 con mayúscula, minúscula y número
    const p = pass.value || '';
    if (p.length < 8 || p.length > 64 || !/[a-z]/.test(p) || !/[A-Z]/.test(p) || !/[0-9]/.test(p)) {
      ok = false;
      setInvalid(pass, 'Debe tener 8–64 caracteres, mayúscula, minúscula y número.');
    }

    // Confirmación
    if (p !== pass2.value) {
      ok = false;
      setInvalid(pass2, 'Las contraseñas no coinciden.');
    }

    if (!ok) e.preventDefault();
  });
})();
</script>
</html>
