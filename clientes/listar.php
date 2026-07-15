<?php require_once '../auth.php'; requireRole(['gerente','vendedor','contador','auditor']); include '../conexion.php'; ?>


<?php
$clientes = $conexion->query("SELECT * FROM clientes");
$error = $_GET['error'] ?? '';
$success = isset($_GET['success']);
?>


<?php require_once '../i18n.php'; ?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang ?? 'es') ?>">
<head>
    <meta charset="UTF-8">
    <title>Clientes</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
<?php
$navLinks = [
    'dashboard' => '../dashboard.php',
    'clientes' => 'listar.php',
    'productos' => '../productos/listar.php',
    'ventas' => '../ventas/nueva_venta.php',
    'reportes' => '../reportes/reportes.php',
    'usuarios' => '../empleados/listar.php',
    'logout' => '../logout.php',
];
include '../layout/navbar.php';
?>
<main class="section">
    <div class="page-header">
        <div>
            <h1><?= htmlspecialchars(t('clients.list_title')) ?></h1>
            <p class="subtitle"><?= htmlspecialchars(t('clients.list_subtitle')) ?></p>
        </div>
        <button type="button" class="btn back-button" onclick="history.back()"><?= htmlspecialchars(t('global.back')) ?></button>
    </div>

    <a href="agregar.php" class="btn"><?= htmlspecialchars(t('clients.new_title')) ?></a>

    <?php if ($error === 'has_sales'): ?>
        <p class="alert alert-error"><?= htmlspecialchars(t('clients.alert.cannot_delete_has_sales')) ?></p>
    <?php elseif ($success): ?>
        <p class="alert alert-success"><?= htmlspecialchars(t('clients.alert.deleted')) ?></p>
    <?php endif; ?>

    <table>
    <tr>
            <th>ID</th>
            <th><?= htmlspecialchars(t('client.field.name')) ?></th>
            <th><?= htmlspecialchars(t('client.field.last_name')) ?></th>
            <th><?= htmlspecialchars(t('client.field.email')) ?></th>
            <th><?= htmlspecialchars(t('client.field.phone')) ?></th>
            <th><?= htmlspecialchars(t('global.edit')) ?></th>
    </tr>

    <?php foreach($clientes as $cliente): ?>
    <tr>
        <td><?= $cliente['id_cliente']; ?></td>
        <td><?= $cliente['nombre']; ?></td>
        <td><?= $cliente['apellido']; ?></td>
        <td><?= $cliente['email']; ?></td>
        <td><?= $cliente['telefono']; ?></td>
        <td class="action-cell">
            <a href="editar_clientes.php?id=<?= $cliente['id_cliente']; ?>" class="action-link" title="Editar cliente">
                <img src="../img/edit.png" alt="Editar" class="action-icon">
            </a>
            <a href="eliminar.php?id=<?= $cliente['id_cliente']; ?>" class="action-link" title="Eliminar cliente" onclick="return confirm('¿Confirmar eliminación del cliente?');">
                <img src="../img/delete.png" alt="Eliminar" class="action-icon">
            </a>
        </td>
    </tr>
    <?php endforeach; ?>

</table>
</main>

</body>
</html> 