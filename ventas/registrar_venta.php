<?php
require_once '../auth.php';
requireRole(['gerente','vendedor']);
include '../conexion.php';



if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: nueva_venta.php');
    exit;
}

$cantidad = intval($_POST['cantidad'] ?? 0);
if ($cantidad < 1) {
    header('Location: nueva_venta.php?error=invalid');
    exit;
}

// Handle manual client creation
if (!empty($_POST['manual_cliente'])) {
    $nombreC = trim($_POST['manual_nombre'] ?? '');
    $apellidoC = trim($_POST['manual_apellido'] ?? '');
    $correoC = trim($_POST['manual_correo'] ?? '');

    if ($nombreC === '' || $apellidoC === '' || $correoC === '') {
        header('Location: nueva_venta.php?error=invalid');
        exit;
    }

    if (!filter_var($correoC, FILTER_VALIDATE_EMAIL)) {
        header('Location: nueva_venta.php?error=email');
        exit;
    }

    try {
        $conexion->beginTransaction();

        $stmtCheck = $conexion->prepare('SELECT id_cliente FROM clientes WHERE email = :email LIMIT 1');
        $stmtCheck->execute([':email' => $correoC]);
        $existing = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $id_cliente = intval($existing['id_cliente']);
        } else {
            $stmtIns = $conexion->prepare('INSERT INTO clientes(nombre, apellido, email, telefono, direccion) VALUES(:nombre, :apellido, :email, :telefono, :direccion)');
            $stmtIns->execute([
                ':nombre' => $nombreC,
                ':apellido' => $apellidoC,
                ':email' => $correoC,
                ':telefono' => null,
                ':direccion' => null
            ]);
            $id_cliente = intval($conexion->lastInsertId());
        }

        $conexion->commit();
    } catch (PDOException $e) {
        if ($conexion->inTransaction()) {
            $conexion->rollBack();
        }
        header('Location: nueva_venta.php?error=cliente');
        exit;
    }
} else {
    $id_cliente = intval($_POST['id_cliente'] ?? 0);
}


// Handle manual product creation
if (!empty($_POST['manual_producto']) && !empty($_POST['manual_prod_nombre'])) {
    $prodNombre = trim($_POST['manual_prod_nombre']);
    $prodPrecio = floatval($_POST['manual_prod_precio'] ?? 0);
    $prodStock = intval($_POST['manual_prod_stock'] ?? 0);
    $prodCategoria = intval($_POST['manual_prod_categoria'] ?? 1) ?: 1;

    $stmtProd = $conexion->prepare('INSERT INTO productos(nombre, precio, stock, id_categoria) VALUES(:nombre, :precio, :stock, :categoria)');
    $stmtProd->execute([
        ':nombre' => $prodNombre,
        ':precio' => $prodPrecio,
        ':stock' => $prodStock,
        ':categoria' => $prodCategoria
    ]);
    $id_producto = $conexion->lastInsertId();
} else {
    $id_producto = intval($_POST['id_producto'] ?? 0);
}

if (!$id_cliente || !$id_producto) {
    header('Location: nueva_venta.php?error=invalid');
    exit;
}

$sqlProducto = "SELECT * FROM productos WHERE id_producto = :id";
$stmt = $conexion->prepare($sqlProducto);
$stmt->execute([':id' => $id_producto]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto || $cantidad > $producto['stock']) {
    header('Location: nueva_venta.php?error=stock');
    exit;
}

$total = $producto['precio'] * $cantidad;

try {
    $conexion->beginTransaction();

    $sqlVenta = "INSERT INTO ventas(id_cliente, total) VALUES(:cliente, :total)";
    $stmtVenta = $conexion->prepare($sqlVenta);
    $stmtVenta->execute([
        ':cliente' => $id_cliente,
        ':total' => $total
    ]);

    $id_venta = $conexion->lastInsertId();

    $sqlDetalle = "INSERT INTO detalle_ventas(id_venta, id_producto, cantidad, precio, subtotal)
                   VALUES(:venta, :producto, :cantidad, :precio, :subtotal)";
    $stmtDetalle = $conexion->prepare($sqlDetalle);
    $stmtDetalle->execute([
        ':venta' => $id_venta,
        ':producto' => $id_producto,
        ':cantidad' => $cantidad,
        ':precio' => $producto['precio'],
        ':subtotal' => $total
    ]);

    $nuevoStock = $producto['stock'] - $cantidad;
    $sqlStock = "UPDATE productos SET stock = :stock WHERE id_producto = :id";
    $stmtStock = $conexion->prepare($sqlStock);
    $stmtStock->execute([
        ':stock' => $nuevoStock,
        ':id' => $id_producto
    ]);

    $conexion->commit();
    header('Location: nueva_venta.php?success=1&reportes_refresh=1');
    exit;
} catch (PDOException $e) {
    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }
    header('Location: nueva_venta.php?error=registro');
    exit;
}


