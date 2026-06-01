<?php require_once '../auth.php'; requireRole(['gerente']); ?>

<?php include '../conexion.php'; ?>

<?php
$categorias = $conexion->query("SELECT * FROM categorias");


$error = '';
$success = '';

$nombre_post = '';
$precio_post = '';
$stock_post = '';
$categoria_post = '';

if($_POST){
    $nombre = trim($_POST['nombre']);
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $categoria = $_POST['categoria'];

    $nombre_post = $nombre;
    $precio_post = $precio;
    $stock_post = $stock;
    $categoria_post = $categoria;

    // Comprobar si el producto ya existe (mismo nombre)
    $check = $conexion->prepare("SELECT COUNT(*) FROM productos WHERE nombre = :nombre");
    $check->execute([':nombre' => $nombre]);
    $exists = $check->fetchColumn();

    if($exists){
        // Redirigir a la lista con mensaje de error si ya existe
        header('Location: listar.php?msg=exists&nombre=' . urlencode($nombre).'&reportes_refresh=0');
        exit;
    } else {
        $sql = "INSERT INTO productos(nombre, precio, stock, id_categoria)
                VALUES(:nombre, :precio, :stock, :categoria)";

        $stmt = $conexion->prepare($sql);

        $stmt->execute([
            ':nombre' => $nombre,
            ':precio' => $precio,
            ':stock' => $stock,
            ':categoria' => $categoria
        ]);

        // Redirigir a la lista con mensaje de éxito
        header('Location: listar.php?msg=created&reportes_refresh=1');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Producto</title>
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
            <h1>Agregar Producto</h1>
            <p class="subtitle">Registra un nuevo producto en el inventario.</p>
        </div>
        <button type="button" class="btn back-button" onclick="history.back()">Volver</button>
    </div>

    

    <form method="POST">
        <input type="text" name="nombre" placeholder="Nombre del producto" required value="<?= htmlspecialchars($nombre_post) ?>">
        <input type="number" step="0.01" name="precio" placeholder="Precio" required value="<?= htmlspecialchars($precio_post) ?>">
        <input type="number" name="stock" placeholder="Stock" required value="<?= htmlspecialchars($stock_post) ?>">
        <select name="categoria" required>
            <?php foreach($categorias as $categoria): ?>
                <option value="<?= $categoria['id_categoria']; ?>" <?= ($categoria['id_categoria'] == $categoria_post) ? 'selected' : '';?> ><?= $categoria['nombre']; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Guardar Producto</button>
    </form>
</main>
</body>
</html>
