<?php
require_once 'auth.php';
requireAuth();
include 'conexion.php';

header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$error = $_GET['error'] ?? '';

$clientes = $conexion->query("SELECT COUNT(*) FROM clientes")->fetchColumn();
$productos = $conexion->query("SELECT COUNT(*) FROM productos")->fetchColumn();
$ventas = $conexion->query("SELECT COUNT(*) FROM ventas")->fetchColumn();
$ultimasVentas = $conexion->query(
    'SELECT v.id_venta, v.fecha, v.total, c.nombre AS cliente_nombre, c.apellido AS cliente_apellido FROM ventas v INNER JOIN clientes c ON v.id_cliente = c.id_cliente ORDER BY v.fecha DESC LIMIT 5'
)->fetchAll(PDO::FETCH_ASSOC);
?>


<?php require_once 'i18n.php'; ?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
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
            <h1><?= htmlspecialchars(t('dashboard.title')) ?></h1>
            <p class="subtitle"><?= htmlspecialchars(t('dashboard.welcome')) ?> <?= htmlspecialchars(currentUser()) ?>. <?= htmlspecialchars(t('dashboard.manage')) ?></p>
        </div>
        <div class="button-group">
            <?php if (!empty($error) && $error === 'ups_algo_salio_mal'): ?>
                <p class="alert alert-error"><?= htmlspecialchars(t('dashboard.error')) ?></p>
            <?php endif; ?>
            <button type="button" class="btn" onclick="location.href='ventas/nueva_venta.php'"><?= htmlspecialchars(t('dashboard.register_sale')) ?></button>
            <?php if (hasRole('gerente')): ?>
                <button type="button" class="btn" onclick="location.href='empleados/listar.php'"><?= htmlspecialchars(t('dashboard.manage_users')) ?></button>
                <button type="button" class="btn" onclick="location.href='backup.php'"><?= htmlspecialchars(t('dashboard.create_backup')) ?></button>
            <?php endif; ?>

        </div>

    </div>

    <div class="dashboard">
        <div class="box box-figura box-clientes">

            <div class="box-figura-icon" aria-hidden="true">👥</div>
            <h2><?php echo $clientes; ?></h2>
            <p><?= htmlspecialchars(t('dashboard.clients_registered')) ?></p>
            <a class="btn" href="clientes/listar.php"><?= htmlspecialchars(t('dashboard.view_clients')) ?></a>
        </div>

        <div class="box box-figura box-productos">
            <div class="box-figura-icon" aria-hidden="true">📦</div>
            <h2><?php echo $productos; ?></h2>
            <p><?= htmlspecialchars(t('dashboard.products_available')) ?></p>
            <a class="btn" href="productos/listar.php"><?= htmlspecialchars(t('dashboard.view_products')) ?></a>
        </div>

        <div class="box box-figura box-ventas">
            <div class="box-figura-icon" aria-hidden="true">🧾</div>
            <h2 id="sales-count"><?php echo $ventas; ?></h2>
            <p><?= htmlspecialchars(t('dashboard.sales_registered')) ?></p>
            <a class="btn" href="ventas/listar.php"><?= htmlspecialchars(t('dashboard.view_sales')) ?></a>
        </div>
    </div>

    <section class="sales-history">
        <div class="section-header">
            <h2><?= htmlspecialchars(t('dashboard.last_sales')) ?></h2>
            <p class="subtitle"><?= htmlspecialchars(t('dashboard.last_sales_sub')) ?></p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID Venta</th>
                    <th>Cliente</th>
                    <th>Total</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody id="recent-sales-body">
                <?php if (count($ultimasVentas) === 0): ?>
                    <tr>
                        <td colspan="4"><?= htmlspecialchars(t('dashboard.no_sales')) ?></td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($ultimasVentas as $venta): ?>
                        <tr>
                            <td><?= htmlspecialchars($venta['id_venta']) ?></td>
                            <td><?= htmlspecialchars($venta['cliente_nombre'] . ' ' . $venta['cliente_apellido']) ?></td>
                            <td>$<?= number_format($venta['total'], 2) ?></td>
                            <td><?= htmlspecialchars($venta['fecha']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

</main>

<script>
    async function refreshDashboardSales() {
        try {
            const response = await fetch('api/ventas.php?dashboard=1');
            if (!response.ok) {
                console.warn('Error al obtener datos de ventas:', response.status);
                return;
            }
            const data = await response.json();
            document.getElementById('sales-count').textContent = data.count;
            const tbody = document.getElementById('recent-sales-body');
            if (!tbody) return;

            if (data.recent.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4"><?= htmlspecialchars(t('dashboard.no_sales')) ?></td></tr>';
                return;
            }

            tbody.innerHTML = data.recent.map(venta => `
                <tr>
                    <td>${venta.id_venta}</td>
                    <td>${venta.cliente_nombre} ${venta.cliente_apellido}</td>
                    <td>$${Number(venta.total).toFixed(2)}</td>
                    <td>${venta.fecha}</td>
                </tr>
            `).join('');
        } catch (error) {
            console.error('No se pudo actualizar el historial de ventas:', error);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        refreshDashboardSales();
        setInterval(refreshDashboardSales, 15000);
    });

    window.addEventListener('pageshow', (event) => {
        if (event.persisted) {
            refreshDashboardSales();
        }
    });
</script>

</body>
</html>



