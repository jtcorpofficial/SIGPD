<?php
require __DIR__ . '/php/funciones_sesion.php';
exigir_login();
require __DIR__ . '/php/conexion.php';

if ($_SESSION['rol'] !== 'admin') {
  header('Location: menu.php');
  exit;
}

if (isset($_GET['eliminar'])) {
  $id = (int)$_GET['eliminar'];
  $sql = $cn->prepare("DELETE FROM usuario WHERE id_usuario = ? AND rol != 'admin'");
  $sql->bind_param("i", $id);
  $sql->execute();
  $sql->close();
  header('Location: admin_usuarios.php');
  exit;
}

$res = $cn->query("SELECT id_usuario, nombre_usuario FROM usuario WHERE rol='jugador'");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width,initial-scale=1.0" />
<title>Administrar Usuarios - Draftosaurus</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&display=swap" rel="stylesheet">

<style>
body {
  margin: 0;
  font-family: 'Fredoka', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  color: #fff;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background-color: #3a2d37;
  position: relative;
}

/* --- Fondo difuminado --- */
body::before {
  content: "";
  position: fixed;
  inset: 0;
  background: url("img/fondo.png") center/cover no-repeat;
  filter: blur(8px) brightness(0.8);
  transform: scale(1.05);
  z-index: -2;
}
body::after {
  content: "";
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.35);
  z-index: -1;
}

/* --- Encabezado beige redondeado --- */
.header-box {
  background: #fdf5dc;
  color: #1a1a1a;
  border-radius: 18px;
  padding: 14px 22px;
  margin: 16px auto 0;
  width: calc(100% - 40px);
  max-width: 650px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 4px 12px rgba(0,0,0,0.25);
}
.header-box .titles {
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.header-box .titles .main-title {
  font-weight: 800;
  font-size: 1.1rem;
}
.header-box .titles .subtitle {
  font-weight: 500;
  font-size: 0.9rem;
  color: #333;
}
.header-box .volver-btn {
  background-color: #f59e0b;
  color: #fff;
  border: 0;
  border-radius: 10px;
  padding: 6px 14px;
  font-size: 0.85rem;
  font-weight: 700;
  text-decoration: none;
}
.header-box .volver-btn:hover {
  background-color: #fbbf24;
  color: #000;
}

/* --- Contenedor tabla --- */
.table-section {
  width: 100%;
  max-width: 650px;
  margin: 24px auto;
  padding: 0 16px;
}
.table-card {
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 6px 22px rgba(0,0,0,0.25);
}
.table-card table {
  width: 100%;
  background: rgba(255,255,255,0.95);
  color: #222;
  font-size: 1rem;
}
.table-card thead th {
  background: #f8f4dc;
  text-align: center;
  font-weight: 700;
  padding: 10px;
}
.table-card tbody td {
  text-align: center;
  padding: 12px;
  background: #fff;
}
.btn-delete {
  background: #dc2626;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 6px 10px;
  font-weight: 600;
  text-decoration: none;
  font-size: 0.9rem;
}
.btn-delete:hover {
  background: #ef4444;
}

/* --- Footer --- */
footer {
  text-align: center;
  color: #fff;
  font-size: 0.75rem;
  padding: 16px;
  opacity: 0.85;
  margin-top: auto;
}

/* --- Responsive --- */
@media (max-width: 576px) {
  .header-box {
    flex-direction: column;
    align-items: flex-start;
    text-align: left;
    gap: 6px;
  }
  .header-box .volver-btn {
    align-self: flex-end;
  }
  .table-card table {
    font-size: 0.95rem;
  }
}
</style>
</head>
<body>

<!-- Header beige -->
<div class="header-box">
  <div class="titles">
    <div class="main-title">Draftosaurus</div>
    <div class="subtitle">Administrar Usuarios</div>
  </div>
  <a href="menu.php" class="volver-btn">Volver</a>
</div>

<!-- Contenedor tabla -->
<main class="table-section">
  <div class="table-card">
    <table class="table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre de usuario</th>
          <th>Acción</th>
        </tr>
      </thead>
      <tbody>
        <?php while($u = $res->fetch_assoc()): ?>
        <tr>
          <td><?= $u['id_usuario'] ?></td>
          <td><?= htmlspecialchars($u['nombre_usuario']) ?></td>
          <td>
            <a href="?eliminar=<?= $u['id_usuario'] ?>" class="btn-delete"
               onclick="return confirm('¿Seguro que querés eliminar este usuario?')">
               Eliminar
            </a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</main>

<!-- Footer -->
<footer>
  <p>Desarrollado por JT Corp © 2025</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
