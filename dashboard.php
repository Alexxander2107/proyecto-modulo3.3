<?php require_once 'auth.php'; requireAuth(); include 'conexion.php'; ?>
<?php $error = $_GET['error'] ?? ''; ?>


<?php
$clientes = $conexion->query("SELECT COUNT(*) FROM clientes")->fetchColumn();
$productos = $conexion->query("SELECT COUNT(*) FROM productos")->fetchColumn();
$ventas = $conexion->query("SELECT COUNT(*) FROM ventas")->fetchColumn();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<?php
$navLinks = [
    'dashboard' => 'dashboard.php',
    'clientes' => 'clientes/listar.php',
    'productos' => 'productos/listar.php',
    'ventas' => 'ventas/nueva_venta.php',
    'reportes' => 'reportes/reportes.php',
    'usuarios' => 'empleados/listar.php',
    'logout' => 'logout.php',
];
include 'layout/navbar.php';
?>

<main class="section">
    <div class="page-header">
        <div>
            <h1>Dashboard Principal</h1>
            <p class="subtitle">Bienvenido <?= htmlspecialchars(currentUser()) ?>. Gestiona tus módulos desde aquí.</p>
        </div>
        <div class="button-group">
            <?php if (!empty($error) && $error === 'ups_algo_salio_mal'): ?>
                <p class="alert alert-error">ups, algo salio mal</p>
            <?php endif; ?>
            <button type="button" class="btn" onclick="location.href='ventas/nueva_venta.php'">Registrar Venta</button>
            <?php if (hasRole('gerente')): ?>
                <button type="button" class="btn" onclick="location.href='empleados/listar.php'">Administrar Usuarios</button>
            <?php endif; ?>

        </div>

    </div>

    <div class="dashboard">
        <div class="box box-figura box-clientes">

            <div class="box-figura-icon" aria-hidden="true">👥</div>
            <h2><?php echo $clientes; ?></h2>
            <p>Clientes registrados</p>
            <a class="btn" href="clientes/listar.php">Ver clientes</a>
        </div>

        <div class="box box-figura box-productos">
            <div class="box-figura-icon" aria-hidden="true">📦</div>
            <h2><?php echo $productos; ?></h2>
            <p>Productos disponibles</p>
            <a class="btn" href="productos/listar.php">Ver productos</a>
        </div>

        <div class="box box-figura box-ventas">
            <div class="box-figura-icon" aria-hidden="true">🧾</div>
            <h2><?php echo $ventas; ?></h2>
            <p>Ventas registradas</p>
            <a class="btn" href="ventas/listar.php">Ver ventas</a>
        </div>
    </div>


</main>

</body>
</html>



