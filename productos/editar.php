<?php require_once '../auth.php'; requireRole(['gerente']); include '../conexion.php'; ?>


<?php
$id = $_GET['id'];

$sql = "SELECT * FROM productos WHERE id_producto = :id";
$stmt = $conexion->prepare($sql);
$stmt->execute([':id' => $id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if($_POST){

    $update = "UPDATE productos SET
                nombre = :nombre,
                precio = :precio,
                stock = :stock
                WHERE id_producto = :id";

    $stmt = $conexion->prepare($update);

    $stmt->execute([
        ':nombre' => $_POST['nombre'],
        ':precio' => $_POST['precio'],
        ':stock' => $_POST['stock'],
        ':id' => $id
    ]);

    header('Location: listar.php?reportes_refresh=1');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
<?php
$navLinks = [
    'dashboard' => '../dashboard.php',
    'clientes' => '../clientes/listar.php',
    'productos' => 'listar.php',
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
            <h1>Editar Producto</h1>
            <p class="subtitle">Actualiza los datos del producto seleccionado.</p>
        </div>
        <button type="button" class="btn back-button" onclick="history.back()">Volver</button>
    </div>

    <form method="POST">
        <input type="text" name="nombre" value="<?= htmlspecialchars($producto['nombre']); ?>" required>
        <input type="number" step="0.01" name="precio" value="<?= htmlspecialchars($producto['precio']); ?>" required>
        <input type="number" name="stock" value="<?= htmlspecialchars($producto['stock']); ?>" required>
        <button type="submit">Actualizar Producto</button>
    </form>
</main>
</body>
</html>