<?php
require_once 'auth.php';
requireRole(['gerente']);
include 'conexion.php';

$message = '';
$messageType = '';
$backupLink = '';
$scheduleStatus = 'No configurado. Cuando la programación esté lista, aquí se mostrará el horario.';$schedule = [];

function getBackupDir(): string
{
    return __DIR__ . DIRECTORY_SEPARATOR . 'backups';
}

function getBackupScheduleFilePath(): string
{
    return getBackupDir() . DIRECTORY_SEPARATOR . 'backup_schedule.json';
}

function ensureBackupDirectory(): bool
{
    $backupDir = getBackupDir();
    return is_dir($backupDir) || mkdir($backupDir, 0755, true);
}

function loadBackupSchedule(): array
{
    $file = getBackupScheduleFilePath();
    if (!is_file($file)) {
        return [];
    }

    $data = json_decode(file_get_contents($file), true);
    return is_array($data) ? $data : [];
}

function saveBackupSchedule(array $schedule): bool
{
    if (!ensureBackupDirectory()) {
        return false;
    }
    return file_put_contents(getBackupScheduleFilePath(), json_encode($schedule, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
}

function formatBackupScheduleStatus(array $schedule): string
{
    if (empty($schedule['scheduled_at'])) {
        return 'No configurado. Cuando la programación esté lista, aquí se mostrará el horario.';
    }

    $formattedDate = date('d/m/Y H:i', strtotime($schedule['scheduled_at']));
    if (!empty($schedule['last_run'])) {
        $lastRun = date('d/m/Y H:i', strtotime($schedule['last_run']));
        return "Programado para {$formattedDate}. Ejecutado el {$lastRun}.";
    }

    return "Programado para {$formattedDate}. El respaldo se ejecutará cuando la hora llegue y visites esta página o uses el programador.";
}

function loadBackupHistory(): array
{
    $backupDir = getBackupDir();
    if (!is_dir($backupDir)) {
        return [];
    }

    $files = glob($backupDir . DIRECTORY_SEPARATOR . 'respaldo_*.sql');
    if (!$files) {
        return [];
    }

    $history = [];
    foreach ($files as $filePath) {
        if (!is_file($filePath)) {
            continue;
        }

        $history[] = [
            'name' => basename($filePath),
            'link' => 'backups/' . rawurlencode(basename($filePath)),
            'date' => date('d/m/Y H:i:s', filemtime($filePath)),
            'timestamp' => filemtime($filePath),
        ];
    }

    usort($history, fn($a, $b) => $b['timestamp'] <=> $a['timestamp']);
    return $history;
}

function runBackup(PDO $conexion): string|false
{
    if (!ensureBackupDirectory()) {
        return false;
    }

    $backupFileName = 'respaldo_' . date('Ymd_His') . '.sql';
    $backupFilePath = getBackupDir() . DIRECTORY_SEPARATOR . $backupFileName;

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

    return $exportOk ? 'backups/' . rawurlencode($backupFileName) : false;
}
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

$schedule = loadBackupSchedule();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'manual') {
        $backupLinkResult = runBackup($conexion);
        if ($backupLinkResult !== false) {
            $message = 'Respaldo creado correctamente.';
            $messageType = 'success';
            $backupLink = $backupLinkResult;
        } else {
            $message = 'No se pudo generar el respaldo. Verifica los permisos o la configuración del servidor.';
            $messageType = 'error';
        }
    }

    if ($action === 'schedule') {
        $scheduleDatetime = trim($_POST['schedule_datetime'] ?? '');
        $scheduledAt = DateTime::createFromFormat('Y-m-d\TH:i', $scheduleDatetime);
        $now = new DateTime();

        if (!$scheduledAt) {
            $message = 'Fecha y hora inválidas. Usa el formato correcto.';
            $messageType = 'error';
        } elseif ($scheduledAt <= $now) {
            $message = 'La fecha y hora deben ser futuras para programar el respaldo.';
            $messageType = 'error';
        } else {
            $schedule = [
                'scheduled_at' => $scheduledAt->format('Y-m-d H:i:s'),
                'created_at' => $now->format('Y-m-d H:i:s'),
                'last_run' => '',
            ];

            if (saveBackupSchedule($schedule)) {
                $message = 'Respaldo programado correctamente.';
                $messageType = 'success';
            } else {
                $message = 'No se pudo guardar la programación del respaldo.';
                $messageType = 'error';
            }
        }
    }
}

if (!empty($schedule['scheduled_at']) && empty($schedule['last_run'])) {
    $now = new DateTime();
    $scheduledAt = DateTime::createFromFormat('Y-m-d H:i:s', $schedule['scheduled_at']);

    if ($scheduledAt !== false && $scheduledAt <= $now) {
        $scheduledBackupLink = runBackup($conexion);
        if ($scheduledBackupLink !== false) {
            $schedule['last_run'] = date('Y-m-d H:i:s');
            saveBackupSchedule($schedule);
            $message = 'Respaldo automatizado creado.';
            $messageType = 'success';
            $backupLink = $scheduledBackupLink;
        } else {
            $message = 'Error al ejecutar el respaldo automatizado. Revisa permisos y configuración.';
            $messageType = 'error';
        }
    }
}

$scheduleStatus = formatBackupScheduleStatus($schedule);
$backupHistory = loadBackupHistory();

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
        <p>Programa un respaldo para una fecha y hora futura. Si la hora llega mientras nadie visita esta página, puedes usar <code>backup_scheduler.php</code> para ejecutarlo automáticamente.</p>
        <form method="post" style="display: grid; gap: 0.75rem;">
            <input type="hidden" name="action" value="schedule">
            <label for="schedule_datetime">Fecha y hora del respaldo</label>
            <input id="schedule_datetime" type="datetime-local" name="schedule_datetime" required>
            <button type="submit" class="btn">Programar respaldo</button>
        </form>
        <p class="subtitle"><?= htmlspecialchars($scheduleStatus) ?></p>
        <p class="subtitle">Para ejecución totalmente automática, configura una tarea del sistema para llamar periódicamente a <code>backup_scheduler.php</code>.</p>
    </div>

    <div class="card" style="margin-top: 1rem;">
        <h3>Historial de respaldos</h3>
        <?php if (empty($backupHistory)): ?>
            <p>No hay respaldos guardados aún.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Archivo</th>
                        <th>Fecha</th>
                        <th>Descargar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($backupHistory as $history): ?>
                        <tr>
                            <td><?= htmlspecialchars($history['name']) ?></td>
                            <td><?= htmlspecialchars($history['date']) ?></td>
                            <td><a href="<?= htmlspecialchars($history['link']) ?>" target="_blank">Descargar</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</main>
</body>
</html>
