<?php
// Navbar reutilizable para todas las pantallas
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../i18n.php';
?>

<header>
    <h1>MarketPlace Express</h1>
    <nav>
        <a href="<?= htmlspecialchars($navLinks['dashboard'] ?? '../dashboard.php') ?>"><?= htmlspecialchars(t('nav.dashboard')) ?></a>
        <a href="<?= htmlspecialchars($navLinks['clientes'] ?? '../clientes/listar.php') ?>"><?= htmlspecialchars(t('nav.clients')) ?></a>
        <a href="<?= htmlspecialchars($navLinks['productos'] ?? '../productos/listar.php') ?>"><?= htmlspecialchars(t('nav.products')) ?></a>
        <a href="<?= htmlspecialchars($navLinks['ventas'] ?? '../ventas/nueva_venta.php') ?>"><?= htmlspecialchars(t('nav.sales')) ?></a>

        <?php if (function_exists('hasRole') && hasRole('gerente')): ?>
            <a href="<?= htmlspecialchars($navLinks['usuarios'] ?? '../empleados/listar.php') ?>"><?= htmlspecialchars(t('nav.users')) ?></a>
        <?php endif; ?>

        <a href="<?= htmlspecialchars($navLinks['logout'] ?? '../logout.php') ?>"><?= htmlspecialchars(t('nav.logout')) ?></a>

        <div class="lang-switch">
            <a class="lang-link" href="?lang=es" aria-label="Español">ES</a>
            <a class="lang-link" href="?lang=en" aria-label="English">EN</a>
        </div>
    </nav>
</header>

