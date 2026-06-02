<?php
require_once 'auth.php';
requireRole(['gerente']);
include 'conexion.php';

$message = '';
$messageType = '';
$backupLink = '';
$scheduleStatus = 'No configurado. Cuando la programación esté lista, aquí se mostrará el horario.';

function findMysqldumpExecutable(): ?string
{
    $paths = [];

    $output = @shell_exec('where mysqldump 2>NUL');
    if ($output) {
        $paths = array_merge($paths, preg_split('/\R+/', trim($output)));
    }

    $paths[] = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
    $paths[] = 'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe';
    $paths[] = 'C:\\Program Files\\MySQL\\MySQL Server 5.7\\bin\\mysqldump.exe';
    $paths[] = '/usr/bin/mysqldump';
    $paths[] = '/usr/local/bin/mysqldump';

    foreach ($paths as $path) {
        if (!$path) {
            continue;
        }
        if (is_executable($path)) {
            return $path;
        }
    }

    return null;
}

function exportDatabaseWithMysqldump(string $exePath, string $backupFilePath): bool
{
    $user = DB_USER;
    $passOption = DB_PASS !== '' ? '--password=' . DB_PASS : '--password=';
    $host = DB_HOST;
    $database = DB_NAME;

    $command = sprintf(
        '"%s" --user=%s %s --host=%s %s > "%s"',
        $exePath,
        escapeshellarg($user),
        $passOption,
        escapeshellarg($host),
        escapeshellarg($database),
        $backupFilePath
    );

    @shell_exec($command);
    return is_file($backupFilePath) && filesize($backupFilePath) > 0;
}

function exportDatabaseWithPDO(PDO $connection, string $backupFilePath): bool
{
    $sqlDump = '';
    $tables = $connection->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);

    foreach ($tables as $table) {
        $createResult = $connection->query("SHOW CREATE TABLE `{$table}`")->fetch(PDO::FETCH_ASSOC);
        if (!$createResult || !isset($createResult['Create Table'])) {
            continue;
        }

        $sqlDump .= "DROP TABLE IF EXISTS `{$table}`;\n";
        $sqlDump .= $createResult['Create Table'] . ";\n\n";

        $rows = $connection->query("SELECT * FROM `{$table}`")->fetchAll(PDO::FETCH_ASSOC);
        if (!$rows) {
            continue;
        }

        $columns = array_map(fn($column) => "`{$column}`", array_keys($rows[0]));
        $columnList = implode(', ', $columns);

        foreach ($rows as $row) {
            $values = array_map(function ($value) use ($connection) {
                if ($value === null) {
                    return 'NULL';
                }
                return $connection->quote($value);
            }, array_values($row));

            $sqlDump .= "INSERT INTO `{$table}` ({$columnList}) VALUES (" . implode(', ', $values) . ");\n";
        }

        $sqlDump .= "\n";
    }

    return file_put_contents($backupFilePath, $sqlDump) !== false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'manual') {
    $backupDir = __DIR__ . DIRECTORY_SEPARATOR . 'backups';
    if (!is_dir($backupDir) && !mkdir($backupDir, 0755, true)) {
        $message = 'No se pudo crear la carpeta de respaldos.';
        $messageType = 'error';
    } else {
        $backupFileName = 'respaldo_' . date('Ymd_His') . '.sql';
        $backupFilePath = $backupDir . DIRECTORY_SEPARATOR . $backupFileName;

        $exportOk = false;
        $mysqldumpExe = findMysqldumpExecutable();

        if ($mysqldumpExe !== null) {
            $exportOk = exportDatabaseWithMysqldump($mysqldumpExe, $backupFilePath);
        }

        if (!$exportOk) {
            try {
                $exportOk = exportDatabaseWithPDO($conexion, $backupFilePath);
            } catch (Throwable $e) {
                $exportOk = false;
            }
        }

        if ($exportOk) {
            $message = 'Respaldo creado correctamente.';
            $messageType = 'success';
            $backupLink = 'backups/' . rawurlencode($backupFileName);
        } else {
            $message = 'No se pudo generar el respaldo. Verifica los permisos o la configuración del servidor.';
            $messageType = 'error';
        }
    }
}

$navLinks = [
    'dashboard' => 'dashboard.php',
    'clientes' => 'clientes/listar.php',
    'productos' => 'productos/listar.php',
    'ventas' => 'ventas/nueva_venta.php',
    'reportes' => 'reportes/reportes.php',
    'usuarios' => 'empleados/listar.php',
    'logout' => 'logout.php',
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Respaldo manual</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<?php include 'layout/navbar.php'; ?>
<main class="section">
    <div class="page-header">
        <div>
            <h1>Respaldo de la base de datos</h1>
            <p class="subtitle">Crea un respaldo manual ahora o revisa el estado de la programación.</p>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert <?= $messageType === 'success' ? 'alert-success' : 'alert-error' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <h3>Respaldo manual</h3>
        <p>Presiona el botón para generar un archivo SQL con el contenido actual de la base de datos.</p>
        <form method="post">
            <input type="hidden" name="action" value="manual">
            <button type="submit" class="btn">Crear respaldo ahora</button>
        </form>
        <?php if ($backupLink): ?>
            <p class="subtitle">Descargar respaldo: <a href="<?= htmlspecialchars($backupLink) ?>" target="_blank"><?= htmlspecialchars($backupLink) ?></a></p>
        <?php endif; ?>
    </div>

    <div class="card" style="margin-top: 1rem;">
        <h3>Respaldo programado</h3>
        <p>Estado actual:</p>
        <p class="subtitle"><?= htmlspecialchars($scheduleStatus) ?></p>
    </div>
</main>
</body>
</html>
