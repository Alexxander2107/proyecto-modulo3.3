<?php require_once '../auth.php'; requireRole(['gerente']); include '../conexion.php'; ?>


$id = $_GET['id'];

$sql = "DELETE FROM productos WHERE id_producto = :id";
$stmt = $conexion->prepare($sql);
$stmt->execute([':id' => $id]);

header('Location: listar.php?reportes_refresh=1');
?>