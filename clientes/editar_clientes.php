<?php require_once '../auth.php'; requireRole(['gerente']); include '../conexion.php'; ?>


<?php
$id = $_GET['id'];

$sql = "SELECT * FROM clientes WHERE id_cliente = :id";
$stmt = $conexion->prepare($sql);
$stmt->execute([':id' => $id]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if($_POST){

    $update = "UPDATE clientes SET
                nombre = :nombre,
                apellido = :apellido,
                email = :email,
                telefono = :telefono,
                direccion = :direccion
                WHERE id_cliente = :id";

    $stmt = $conexion->prepare($update);

    $stmt->execute([
        ':nombre' => $_POST['nombre'],
        ':apellido' => $_POST['apellido'],
        ':email' => $_POST['email'],
        ':telefono' => $_POST['telefono'],
        ':direccion' => $_POST['direccion'],
        ':id' => $id
    ]);

    header('Location: listar.php?reportes_refresh=1');
}
?>

<?php require_once '../i18n.php'; ?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang ?? 'es') ?>">
<head>
    <meta charset="UTF-8">
    <title>Editar Cliente</title>
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
            <h1><?= htmlspecialchars(t('clients.edit_title')) ?></h1>
            <p class="subtitle"><?= htmlspecialchars(t('clients.edit_subtitle')) ?></p>
        </div>
            <button type="button" class="btn back-button" onclick="history.back()"><?= htmlspecialchars(t('global.back')) ?></button>
    </div>

    <form method="POST">
        <input type="text" name="nombre" value="<?= htmlspecialchars($cliente['nombre']); ?>" required placeholder="<?= htmlspecialchars(t('client.field.name')) ?>">
        <input type="text" name="apellido" value="<?= htmlspecialchars($cliente['apellido']); ?>" required placeholder="<?= htmlspecialchars(t('client.field.last_name')) ?>">
        <input type="email" name="email" value="<?= htmlspecialchars($cliente['email']); ?>" required placeholder="<?= htmlspecialchars(t('client.field.email')) ?>">
        <input type="text" name="telefono" value="<?= htmlspecialchars($cliente['telefono']); ?>" placeholder="<?= htmlspecialchars(t('client.field.phone')) ?>">
        <input type="text" name="direccion" value="<?= htmlspecialchars($cliente['direccion']); ?>" placeholder="<?= htmlspecialchars(t('client.field.address')) ?>">
        <button type="submit"><?= htmlspecialchars(t('global.update')) ?></button>
    </form>
</main>
</body>
</html>