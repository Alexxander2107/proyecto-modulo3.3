<?php
session_start();
include 'conexion.php';

if (isset($_SESSION['usuario'])) {
    header('Location: dashboard.php');
    exit;
}

try {
    $adminCount = $conexion->query("SELECT COUNT(*) FROM empleados WHERE cargo = 'admin'")->fetchColumn();
    if (!$adminCount) {
        $adminPassword = password_hash('Admin123!', PASSWORD_DEFAULT);
        $stmtAdmin = $conexion->prepare("INSERT INTO empleados(nombre, correo, clave, cargo) VALUES(:nombre, :correo, :clave, 'admin')");
        $stmtAdmin->execute([
            ':nombre' => 'Administrador',
            ':correo' => 'admin@marketplace.com',
            ':clave' => $adminPassword
        ]);
    }
} catch (PDOException $e) {
    // Ignorar errores en caso de que la tabla aún no exista.
}

if ($_POST) {
    $correo = trim($_POST['correo']);
    $clave = $_POST['clave'];

    $sql = "SELECT * FROM empleados WHERE correo = :correo LIMIT 1";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([':correo' => $correo]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    $valid = false;
    if ($usuario) {
        if (password_verify($clave, $usuario['clave'])) {
            $valid = true;
        } elseif ($usuario['clave'] === $clave) {
            $valid = true;
            $hash = password_hash($clave, PASSWORD_DEFAULT);
            $update = $conexion->prepare("UPDATE empleados SET clave = :clave WHERE id_empleado = :id");
            $update->execute([':clave' => $hash, ':id' => $usuario['id_empleado']]);
        }
    }

    if ($valid) {
        $_SESSION['usuario'] = $usuario['nombre'];
        $_SESSION['correo'] = $usuario['correo'];
        $_SESSION['cargo'] = $usuario['cargo'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Correo o contraseña incorrectos";
    }
}
?>

<?php require_once 'i18n.php'; ?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars(t('auth.login')) ?></title>

    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<div class="login-container">

    <form method="POST" class="login-form">

        <h1><?= htmlspecialchars(t('auth.login')) ?></h1>


        <?php if(isset($error)): ?>
            <p><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <div class="form-group">
            <label for="correo"><?= htmlspecialchars(t('auth.email')) ?></label>
            <input id="correo" type="email" name="correo" placeholder="<?= htmlspecialchars(t('auth.email')) ?>" required>

        </div>

        <div class="form-group password-field">
            <label for="clave"><?= htmlspecialchars(t('auth.password')) ?></label>

            <div class="password-input-wrapper">
                <input id="clave" type="password" name="clave" placeholder="<?= htmlspecialchars(t('auth.password')) ?>" required>
                <label class="field-toggle">
                    <input type="checkbox" id="showPassword">
                    <span><?= htmlspecialchars(t('auth.show_password')) ?></span>

                </label>
            </div>
        </div>

        <button type="submit"><?= htmlspecialchars(t('auth.submit_login')) ?></button>
        <p style="margin-top:1rem; text-align:center; color:#cbd5e1;"><?= htmlspecialchars(t('auth.no_account')) ?> <a href="registro.php"><?= htmlspecialchars(t('auth.register')) ?></a></p>

    </form>
</div>

<script>
    const passwordToggle = document.getElementById('showPassword');
    const passwordInput = document.getElementById('clave');
    passwordToggle.addEventListener('change', function() {
        passwordInput.type = this.checked ? 'text' : 'password';
    });

    const form = document.querySelector('.login-form');
    form.addEventListener('submit', function(event) {
        const correo = document.getElementById('correo');
        const clave = document.getElementById('clave');

        if (!correo.value.trim() || !clave.value.trim()) {
            event.preventDefault();
            alert('Ingresa correo y contraseña para continuar.');
            return;
        }

        if (clave.value.length < 6) {
            event.preventDefault();
            alert('La contraseña debe tener al menos 6 caracteres.');
        }
    });
</script>

</body>
</html>