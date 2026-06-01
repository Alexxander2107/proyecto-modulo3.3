<?php
require_once '../auth.php';
requireRole(['gerente']);
include '../conexion.php';


$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id > 0) {
    try {
        $conexion->beginTransaction();

        $deleteDetalle = $conexion->prepare(
            'DELETE detalle_ventas FROM detalle_ventas
             INNER JOIN ventas ON detalle_ventas.id_venta = ventas.id_venta
             WHERE ventas.id_cliente = :id'
        );
        $deleteDetalle->execute([':id' => $id]);

        $deleteVentas = $conexion->prepare('DELETE FROM ventas WHERE id_cliente = :id');
        $deleteVentas->execute([':id' => $id]);

        $sql = "DELETE FROM clientes WHERE id_cliente = :id";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([':id' => $id]);

        // Reordenar IDs de clientes tras la eliminación y ajustar ventas relacionadas
        $conexion->exec('SET FOREIGN_KEY_CHECKS = 0');
        $conexion->exec('CREATE TEMPORARY TABLE tmp_client_ids (old_id INT PRIMARY KEY, new_id INT NOT NULL)');
        $conexion->exec('SET @n = 0');
        $conexion->exec('INSERT INTO tmp_client_ids (old_id, new_id)
            SELECT id_cliente, (@n := @n + 1) AS new_id
            FROM clientes
            ORDER BY id_cliente');
        $conexion->exec('UPDATE ventas INNER JOIN tmp_client_ids ON ventas.id_cliente = tmp_client_ids.old_id
            SET ventas.id_cliente = tmp_client_ids.new_id');
        $conexion->exec('UPDATE clientes INNER JOIN tmp_client_ids ON clientes.id_cliente = tmp_client_ids.old_id
            SET clientes.id_cliente = tmp_client_ids.new_id');
        $conexion->exec('ALTER TABLE clientes AUTO_INCREMENT = 1');
        $conexion->exec('DROP TEMPORARY TABLE IF EXISTS tmp_client_ids');
        $conexion->exec('SET FOREIGN_KEY_CHECKS = 1');

        $conexion->commit();
        header('Location: listar.php?success=1&reportes_refresh=1');
        exit;
    } catch (PDOException $e) {
        if ($conexion->inTransaction()) {
            $conexion->rollBack();
        }
        header('Location: listar.php?error=delete_failed&reportes_refresh=0');
        exit;
    }
}

header('Location: listar.php');
exit;
?>