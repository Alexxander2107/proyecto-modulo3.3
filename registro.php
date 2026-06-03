<?php
session_start();
include 'conexion.php';

$error = '';
if ($_POST) {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $clave = $_POST['clave'];
    $confirmar = $_POST['confirmar_clave'];

    if ($clave !== $confirmar) {
        $error = 'Las contraseñas no coinciden.';
    } elseif (!preg_match('/^(?=.*[A-Z])(?=.*@).{8,}$/', $clave)) {
        $error = 'La contraseña debe tener al menos 8 caracteres, contener una letra mayúscula y el símbolo "@".';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = 'Ingrese un correo válido.';
    } else {
        $stmt = $conexion->prepare('SELECT COUNT(*) FROM empleados WHERE correo = :correo');
        $stmt->execute([':correo' => $correo]);
        if ($stmt->fetchColumn() > 0) {
            $error = 'Ya existe una cuenta con ese correo.';
        } else {
            $claveHash = password_hash($clave, PASSWORD_DEFAULT);
            $insert = $conexion->prepare('INSERT INTO empleados(nombre, correo, clave, cargo) VALUES(:nombre, :correo, :clave, :cargo)');
            $insert->execute([
                ':nombre' => $nombre,
                ':correo' => $correo,
                ':clave' => $claveHash,
                ':cargo' => 'usuario'
            ]);
            header('Location: login.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<div class="login-container">
    <form method="POST" class="login-form">
        <h1>Crear Cuenta</h1>
        <?php if ($error): ?>
            <p><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="email" name="correo" placeholder="Correo" required>
        <input type="password" name="clave" placeholder="Contraseña" required pattern="(?=.*[A-Z])(?=.*@).{8,}" title="Mínimo 8 caracteres, al menos una mayúscula y el símbolo @">
        <input type="password" name="confirmar_clave" placeholder="Confirmar contraseña" required pattern="(?=.*[A-Z])(?=.*@).{8,}" title="Mínimo 8 caracteres, al menos una mayúscula y el símbolo @">
        <button type="submit">Registrar</button>
        <p style="margin-top:1rem; text-align:center; font-size:0.95rem; color:#cbd5e1;">¿Ya tienes cuenta? <a href="login.php">Iniciar sesión</a></p>
    </form>
</div>
</body>
</html>