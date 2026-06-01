<?php require_once '../auth.php'; requireRole(['gerente','vendedor']); include '../conexion.php'; ?>

<?php

$clientes = $conexion->query("SELECT * FROM clientes ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
$productos = $conexion->query("SELECT * FROM productos ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
$categorias = $conexion->query("SELECT * FROM categorias ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
$error = $_GET['error'] ?? '';
$success = isset($_GET['success']);
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Venta</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
<?php
$navLinks = [
    'dashboard' => '../dashboard.php',
    'clientes' => '../clientes/listar.php',
    'productos' => '../productos/listar.php',
    'ventas' => 'nueva_venta.php',
    'reportes' => '../reportes/reportes.php',
    'usuarios' => '../empleados/listar.php',
    'logout' => '../logout.php',
];
include '../layout/navbar.php';
?>
<main class="section">
    <div class="page-header">
        <div>
            <h1>Registrar Nueva Venta</h1>
            <p class="subtitle">Selecciona cliente y producto para crear la venta.</p>
        </div>
<button type="button" class="btn back-button" onclick="history.back()">Volver</button>
    </div>



    <?php if ($error): ?>
        <p class="alert alert-error"><?= htmlspecialchars($error) ?></p>
    <?php elseif ($success): ?>
        <p class="alert alert-success">Venta registrada correctamente.</p>
    <?php endif; ?>

    <form method="POST" action="registrar_venta.php" id="ventaForm">
        <label class="checkbox-inline">
            <input type="checkbox" id="manualClienteChk" name="manual_cliente" value="1">
            Agregar cliente manualmente
        </label>

        <div id="manualCliente" style="display:none;">
            <input type="text" name="manual_nombre" placeholder="Nombre cliente">
            <input type="text" name="manual_apellido" placeholder="Apellido cliente">
            <input type="email" name="manual_correo" placeholder="Correo cliente">
        </div>

        <div id="selectCliente">
            <label>Cliente</label>
            <select name="id_cliente">
                <option value="">-- Seleccione cliente --</option>
                <?php foreach($clientes as $c): ?>
                    <option value="<?= $c['id_cliente']; ?>"><?= htmlspecialchars($c['nombre'] . ' ' . $c['apellido']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <hr>

        <label class="checkbox-inline">
            <input type="checkbox" id="manualProductoChk" name="manual_producto" value="1">
            Agregar producto manualmente
        </label>

        <div id="manualProducto" style="display:none;">
            <input type="text" name="manual_prod_nombre" placeholder="Nombre del producto">
            <input type="number" step="0.01" name="manual_prod_precio" placeholder="Precio">
            <input type="number" name="manual_prod_stock" placeholder="Stock inicial">
            <select name="manual_prod_categoria">
                <?php foreach($categorias as $cat): ?>
                    <option value="<?= $cat['id_categoria'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div id="selectProducto">
            <label>Producto</label>
            <select id="id_producto" name="id_producto">
                <option value="">-- Seleccione producto --</option>
                <?php foreach($productos as $p): ?>
                    <option value="<?= $p['id_producto']; ?>" data-stock="<?= $p['stock']; ?>" data-price="<?= $p['precio']; ?>">
                        <?= htmlspecialchars($p['nombre']) ?> — $<?= number_format($p['precio'], 2) ?> — stock <?= $p['stock'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="product-meta">
            <p>Stock disponible: <strong id="stockInfo">0</strong></p>
            <p>Precio unitario: <strong>$<span id="priceInfo">0.00</span></strong></p>
            <p>Total estimado: <strong>$<span id="totalInfo">0.00</span></strong></p>
        </div>

        <input type="number" name="cantidad" min="1" placeholder="Cantidad" required>
        <button type="submit">Registrar Venta</button>
    </form>
    <?php if ($error): ?>
        <p class="alert alert-error"><?= htmlspecialchars($error) ?></p>
    <?php elseif ($success): ?>
        <p class="alert alert-success">Venta registrada correctamente.</p>
    <?php endif; ?>

</main>
<script>
    const productSelect = document.getElementById('id_producto');
    const stockInfo = document.getElementById('stockInfo');
    const priceInfo = document.getElementById('priceInfo');
    const totalInfo = document.getElementById('totalInfo');
    const quantityInput = document.querySelector('input[name="cantidad"]');

    const manualClienteChk = document.getElementById('manualClienteChk');
    const manualCliente = document.getElementById('manualCliente');
    const selectCliente = document.getElementById('selectCliente');

    const manualProductoChk = document.getElementById('manualProductoChk');
    const manualProducto = document.getElementById('manualProducto');
    const selectProducto = document.getElementById('selectProducto');

    function toggleManualCliente() {
        if (manualClienteChk.checked) {
            manualCliente.style.display = 'block';
            selectCliente.style.display = 'none';
        } else {
            manualCliente.style.display = 'none';
            selectCliente.style.display = 'block';
        }
    }

    function toggleManualProducto() {
        if (manualProductoChk.checked) {
            manualProducto.style.display = 'block';
            selectProducto.style.display = 'none';
        } else {
            manualProducto.style.display = 'none';
            selectProducto.style.display = 'block';
        }
        updateProductMeta();
    }

    const updateProductMeta = () => {
        const selectedOption = productSelect ? productSelect.selectedOptions[0] : null;
        const stock = selectedOption ? (selectedOption.dataset.stock || '0') : '0';
        const price = selectedOption ? parseFloat(selectedOption.dataset.price || 0) : 0;
        stockInfo.textContent = stock;
        priceInfo.textContent = price.toFixed(2);
        const quantity = Math.max(1, parseInt(quantityInput.value) || 1);
        totalInfo.textContent = (price * quantity).toFixed(2);
    };

    if (productSelect) {
        productSelect.addEventListener('change', updateProductMeta);
    }
    quantityInput.addEventListener('input', updateProductMeta);

    if (manualClienteChk) manualClienteChk.addEventListener('change', toggleManualCliente);
    if (manualProductoChk) manualProductoChk.addEventListener('change', toggleManualProducto);

    // Initialize
    toggleManualCliente();
    toggleManualProducto();
    updateProductMeta();

    const ventaRegistrada = <?= $success ? 'true' : 'false' ?>;
    const dispatchVentaRegistrada = () => {
        const payload = JSON.stringify({ event: 'venta-registrada', updatedAt: new Date().toISOString() });
        if (window.BroadcastChannel) {
            const channel = new BroadcastChannel('reportes');
            channel.postMessage(payload);
            channel.close();
        }
        localStorage.setItem('reportes-event', payload);
        setTimeout(() => localStorage.removeItem('reportes-event'), 500);
    };

    if (ventaRegistrada) {
        dispatchVentaRegistrada();
    }
</script>
</body>
</html>