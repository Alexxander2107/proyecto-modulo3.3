<?php
require_once __DIR__ . '/../conexion.php';
header('Content-Type: application/json; charset=utf-8');
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        if (isset($_GET['dashboard'])) {
            $countStmt = $conexion->query('SELECT COUNT(*) AS total FROM ventas');
            $count = intval($countStmt->fetchColumn());
            $recentStmt = $conexion->query('SELECT v.id_venta, v.fecha, v.total, c.nombre AS cliente_nombre, c.apellido AS cliente_apellido FROM ventas v INNER JOIN clientes c ON v.id_cliente = c.id_cliente ORDER BY v.fecha DESC LIMIT 5');
            $recent = $recentStmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['count' => $count, 'recent' => $recent]);
            exit;
        }

        $stmt = $conexion->query('SELECT ventas.*, clientes.nombre AS cliente_nombre, clientes.apellido AS cliente_apellido FROM ventas INNER JOIN clientes ON ventas.id_cliente = clientes.id_cliente ORDER BY ventas.fecha DESC');
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }

    if ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $cliente = intval($input['id_cliente'] ?? 0);
        $producto = intval($input['id_producto'] ?? 0);
        $cantidad = intval($input['cantidad'] ?? 0);

        if (!$cliente || !$producto || $cantidad < 1) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos incompletos.']);
            exit;
        }

        $stmt = $conexion->prepare('SELECT * FROM productos WHERE id_producto = :id');
        $stmt->execute([':id' => $producto]);
        $productoData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$productoData || $cantidad > $productoData['stock']) {
            http_response_code(400);
            echo json_encode(['error' => 'Stock insuficiente o producto no válido.']);
            exit;
        }

        $subtotal = $productoData['precio'] * $cantidad;
        $conexion->beginTransaction();
        try {
            $stmtVenta = $conexion->prepare('INSERT INTO ventas(id_cliente, total) VALUES(:cliente, :total)');
            $stmtVenta->execute([':cliente' => $cliente, ':total' => $subtotal]);
            $ventaId = $conexion->lastInsertId();

            $stmtDetalle = $conexion->prepare('INSERT INTO detalle_ventas(id_venta, id_producto, cantidad, precio, subtotal) VALUES(:venta, :producto, :cantidad, :precio, :subtotal)');
            $stmtDetalle->execute([
                ':venta' => $ventaId,
                ':producto' => $producto,
                ':cantidad' => $cantidad,
                ':precio' => $productoData['precio'],
                ':subtotal' => $subtotal,
            ]);

            $stmtStock = $conexion->prepare('UPDATE productos SET stock = stock - :cantidad WHERE id_producto = :id');
            $stmtStock->execute([':cantidad' => $cantidad, ':id' => $producto]);

            $conexion->commit();
            echo json_encode(['success' => true, 'venta' => $ventaId]);
        } catch (PDOException $e) {
            $conexion->rollBack();
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
