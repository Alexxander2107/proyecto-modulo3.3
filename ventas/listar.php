<?php 
require_once '../auth.php'; 
requireRole(['gerente','contador','auditor']); 
include '../conexion.php'; 

$ventas = $conexion->query(
    'SELECT ventas.*, clientes.nombre AS cliente_nombre, clientes.apellido AS cliente_apellido 
     FROM ventas 
     INNER JOIN clientes ON ventas.id_cliente = clientes.id_cliente 
     ORDER BY ventas.fecha DESC'
)->fetchAll(PDO::FETCH_ASSOC);

$detalleStmt = $conexion->prepare(
    'SELECT dv.cantidad, p.nombre AS producto_nombre, p.precio 
     FROM detalle_ventas dv
     INNER JOIN productos p ON dv.id_producto = p.id_producto 
     WHERE dv.id_venta = :venta'
);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ventas</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
<?php
$navLinks = [
    'dashboard' => '../dashboard.php',
    'clientes' => '../clientes/listar.php',
    'productos' => '../productos/listar.php',
    'ventas' => 'listar.php',
    'reportes' => '../reportes/reportes.php',
    'usuarios' => '../empleados/listar.php',
    'logout' => '../logout.php',
];
include '../layout/navbar.php';
?>
<main class="section">
    <div class="page-header">
        <div>
            <h1>Listado de Ventas</h1>
            <p class="subtitle">Historial y detalle de ventas registradas.</p>
        </div>
        <button type="button" class="btn back-button" onclick="history.back()">Volver</button>
    </div>

    <table>
        <tr>
            <th>ID Venta</th>
            <th>Cliente</th>
            <th>Total</th>
            <th>Fecha</th>
            <th>Detalle</th>
        </tr>
        <?php foreach ($ventas as $venta): ?>
        <tr>
            <td><?= $venta['id_venta'] ?></td>
            <td><?= htmlspecialchars($venta['cliente_nombre'] . ' ' . $venta['cliente_apellido']) ?></td>
            <td>$<?= number_format($venta['total'], 2) ?></td>
            <td><?= $venta['fecha'] ?></td>
            <td>
                <table class="nested-detail-table">
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Subtotal</th>
                    </tr>
                    <?php
                    $detalleStmt->execute([':venta' => $venta['id_venta']]);
                    $detalles = $detalleStmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($detalles as $detalle):
                        $precio = isset($detalle['precio']) ? floatval($detalle['precio']) : 0;
                        $cantidad = intval($detalle['cantidad']);
                        $subtotal = $precio * $cantidad;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($detalle['producto_nombre']) ?></td>
                        <td><?= $cantidad ?></td>
                        <td>$<?= number_format($precio, 2) ?></td>
                        <td>$<?= number_format($subtotal, 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="nested-total-row">
                        <td colspan="3" style="text-align:right;font-weight:700;">Total</td>
                        <td style="font-weight:700;">$<?= number_format($venta['total'], 2) ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</main>
</body>
</html>