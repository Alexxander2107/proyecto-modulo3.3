<?php require_once 'auth.php'; requireAuth(); ?>
<?php
session_start();
if (isset($_SESSION['usuario'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<?php require_once 'i18n.php'; ?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars(t('app.title')) ?></title>

    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <header class="header">
    <div class="logo-container">
        <img src="img/logo.png" alt="Logo Empresa" class="logo">
    <h2 class="nombre-empresa">Marketplace Express</h2>

    </div>
</header>

<header>
    <h1><?= htmlspecialchars(t('app.title')) ?></h1>
    <nav>
        <a href="login.php"><?= htmlspecialchars(t('auth.login')) ?></a>
        <a href="registro.php"><?= htmlspecialchars(t('auth.register')) ?></a>

        <a class="lang-translate-btn" href="?lang=<?= htmlspecialchars($lang === 'es' ? 'en' : 'es') ?>" title="Traducir">
            <span class="lang-translate-label">Traducir</span>
            <span class="lang-translate-sep">|</span>
            <span class="lang-translate-value"><?= htmlspecialchars(strtoupper($lang === 'es' ? 'EN' : 'ES')) ?></span>
        </a>
    </nav>
</header>

<section class="banner">
    <h2><?= htmlspecialchars(t('home.tagline')) ?></h2>
    <p><?= htmlspecialchars(t('home.description')) ?></p>

</section>
    
<section class="cards"> 
    <div class="card">
        <h3><?= htmlspecialchars(t('home.cards.clients.title')) ?></h3>
        <a href="clientes/listar.php"><?= htmlspecialchars(t('home.cards.clients.link')) ?></a>

    </div>

    <div class="card">
        <h3><?= htmlspecialchars(t('home.cards.products.title')) ?></h3>
        <a href="productos/listar.php"><?= htmlspecialchars(t('home.cards.products.link')) ?></a>

    </div>

    <div class="card">
        <h3><?= htmlspecialchars(t('home.cards.sales.title')) ?></h3>
        <a href="ventas/listar.php"><?= htmlspecialchars(t('home.cards.sales.link')) ?></a>

    </div>
</section>

</body>
</html>