<?php
require_once '../auth.php';
requireRole(['gerente','contador','auditor']);
include '../conexion.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $sqlProductosMasVendido = "SELECT p.nombre AS producto, SUM(dv.cantidad) AS total_cantidad, SUM(dv.cantidad * dv.precio_unitario) AS total_ingresos
                           FROM detalle_ventas dv
                           INNER JOIN productos p ON dv.id_producto = p.id_producto
                           GROUP BY dv.id_producto, p.nombre
                           ORDER BY total_cantidad DESC, total_ingresos DESC
                           LIMIT 1";

    $sqlClienteMasCompras = "SELECT c.nombre AS nombre, c.apellido AS apellido, COUNT(v.id_venta) AS total_compras
                           FROM ventas v
                           INNER JOIN clientes c ON v.id_cliente = c.id_cliente
                           GROUP BY c.id_cliente, c.nombre, c.apellido
                           ORDER BY total_compras DESC
                           LIMIT 1";

    $sqlCategoriaMasBuscada = "SELECT cat.nombre AS categoria, SUM(dv.cantidad) AS total_cantidad
                           FROM detalle_ventas dv
                           INNER JOIN productos p ON dv.id_producto = p.id_producto
                           INNER JOIN categorias cat ON p.id_categoria = cat.id_categoria
                           GROUP BY cat.id_categoria, cat.nombre
                           ORDER BY total_cantidad DESC
                           LIMIT 1";

    $stmtProdTop = $conexion->query($sqlProductosMasVendido);
    $prodTop = $stmtProdTop ? $stmtProdTop->fetch(PDO::FETCH_ASSOC) : null;

    $stmtClienteTop = $conexion->query($sqlClienteMasCompras);
    $clienteTop = $stmtClienteTop ? $stmtClienteTop->fetch(PDO::FETCH_ASSOC) : null;

    $stmtCatTop = $conexion->query($sqlCategoriaMasBuscada);
    $catTop = $stmtCatTop ? $stmtCatTop->fetch(PDO::FETCH_ASSOC) : null;

    $sqlCategorias = "SELECT c.nombre AS categoria, COUNT(p.id_producto) AS total
                  FROM categorias c
                  LEFT JOIN productos p ON p.id_categoria = c.id_categoria
                  GROUP BY c.id_categoria
                  ORDER BY total DESC";

    $sqlClientes = "SELECT cl.nombre AS cliente, COUNT(v.id_venta) AS total
                FROM ventas v
                LEFT JOIN clientes cl ON v.id_cliente = cl.id_cliente
                GROUP BY cl.id_cliente
                ORDER BY total DESC";

    $sqlProductos = "SELECT nombre, stock FROM productos ORDER BY nombre";

    $sqlVentas = "SELECT v.id_venta, cl.nombre AS cliente, v.total
              FROM ventas v
              LEFT JOIN clientes cl ON v.id_cliente = cl.id_cliente
              ORDER BY v.id_venta";

    $categorias = $conexion->query($sqlCategorias)->fetchAll(PDO::FETCH_ASSOC);
    $clientesVentas = $conexion->query($sqlClientes)->fetchAll(PDO::FETCH_ASSOC);
    $productos = $conexion->query($sqlProductos)->fetchAll(PDO::FETCH_ASSOC);
    $ventas = $conexion->query($sqlVentas)->fetchAll(PDO::FETCH_ASSOC);

    $catLabels = [];
    $catData = [];
    foreach ($categorias as $row) {
        $catLabels[] = $row['categoria'];
        $catData[] = (int) $row['total'];
    }

    $clienteLabels = [];
    $clienteData = [];
    foreach ($clientesVentas as $row) {
        $clienteLabels[] = $row['cliente'] ?: 'Cliente desconocido';
        $clienteData[] = (int) $row['total'];
    }

    $productoLabels = [];
    $productoData = [];
    foreach ($productos as $row) {
        $productoLabels[] = $row['nombre'];
        $productoData[] = (int) $row['stock'];
    }

    $ventaLabels = [];
    $ventaData = [];
    foreach ($ventas as $row) {
        $ventaLabels[] = 'Venta #' . $row['id_venta'];
        $ventaData[] = (float) $row['total'];
    }

    echo json_encode([
        'prodTop' => $prodTop,
        'clienteTop' => $clienteTop,
        'catTop' => $catTop,
        'catLabels' => $catLabels,
        'catData' => $catData,
        'clienteLabels' => $clienteLabels,
        'clienteData' => $clienteData,
        'productoLabels' => $productoLabels,
        'productoData' => $productoData,
        'ventaLabels' => $ventaLabels,
        'ventaData' => $ventaData,
        'updatedAt' => date('c')
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([ 'error' => $e->getMessage() ]);
}
