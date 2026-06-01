<?php
// Navbar reutilizable para todas las pantallas
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<header>
    <h1>MarketPlace Express</h1>
    <nav>
        <a href="<?= htmlspecialchars($navLinks['dashboard'] ?? '../dashboard.php') ?>">Dashboard</a>
        <a href="<?= htmlspecialchars($navLinks['clientes'] ?? '../clientes/listar.php') ?>">Clientes</a>
        <a href="<?= htmlspecialchars($navLinks['productos'] ?? '../productos/listar.php') ?>">Productos</a>
        <a href="<?= htmlspecialchars($navLinks['ventas'] ?? '../ventas/nueva_venta.php') ?>">Ventas</a>
        <a href="<?= htmlspecialchars($navLinks['reportes'] ?? '../reportes/reportes.php') ?>">Reportes</a>

        <?php if (function_exists('hasRole') && hasRole('gerente')): ?>
            <a href="<?= htmlspecialchars($navLinks['usuarios'] ?? '../empleados/listar.php') ?>">Usuarios</a>
        <?php endif; ?>

        <a href="<?= htmlspecialchars($navLinks['logout'] ?? '../logout.php') ?>">Cerrar sesión</a>
    </nav>
</header>

