<?php
require_once 'auth.php';
requireRole(['gerente']);
include 'conexion.php';

$message = '';
$messageType = '';
$backupLink = '';
$scheduleStatus = 'No configurado. Cuando la programación esté lista, aquí se mostrará el horario.';
$allowedBackupTables = ['clientes', 'empleados', 'productos', 'ventas', 'reportes'];
$tables = array_values(array_intersect($allowedBackupTables, $conexion->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN)));
$manualSelectedTables = [];
$scheduleSelectedTables = [];
$manualBackupMode = 'full';
$scheduleBackupMode = 'full';
$schedule = loadBackupSchedule();

if (!empty($schedule['selected_tables']) && is_array($schedule['selected_tables'])) {
    $scheduleSelectedTables = normalizeBackupTables($schedule['selected_tables'], $tables);
}
if (!empty($schedule['backup_mode']) && in_array($schedule['backup_mode'], ['full', 'data'], true)) {
    $scheduleBackupMode = $schedule['backup_mode'];
}

function normalizeBackupTables(array $selectedTables, array $availableTables): array
{
    $selectedTables = array_filter(array_map('trim', $selectedTables));
    return array_values(array_intersect($availableTables, $selectedTables));
}

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

    $tablesLabel = 'Todas las tablas';
    if (!empty($schedule['selected_tables']) && is_array($schedule['selected_tables'])) {
        $tablesLabel = implode(', ', $schedule['selected_tables']);
    }

    $modeLabel = isset($schedule['backup_mode']) && $schedule['backup_mode'] === 'data' ? 'solo datos' : 'completo';
    $formattedDate = date('d/m/Y H:i', strtotime($schedule['scheduled_at']));

    if (!empty($schedule['last_run'])) {
        $lastRun = date('d/m/Y H:i', strtotime($schedule['last_run']));
        return "Programado para {$formattedDate} ({$modeLabel}, {$tablesLabel}). Ejecutado el {$lastRun}.";
    }

    return "Programado para {$formattedDate} ({$modeLabel}, {$tablesLabel}). El respaldo se ejecutará cuando la hora llegue y visites esta página o uses el programador.";
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

function runBackup(PDO $conexion, array $tables = [], string $mode = 'full'): string|false
{
    if (!ensureBackupDirectory()) {
        return false;
    }

    $backupFileName = 'respaldo_' . date('Ymd_His') . '.sql';
    $backupFilePath = getBackupDir() . DIRECTORY_SEPARATOR . $backupFileName;

    $exportOk = false;
    $mysqldumpExe = findMysqldumpExecutable();

    if ($mysqldumpExe !== null) {
        $exportOk = exportDatabaseWithMysqldump($mysqldumpExe, $backupFilePath, $tables, $mode);
    }

    if (!$exportOk) {
        try {
            $exportOk = exportDatabaseWithPDO($conexion, $backupFilePath, $tables, $mode);
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

function exportDatabaseWithMysqldump(string $exePath, string $backupFilePath, array $tables = [], string $mode = 'full'): bool
{
    $user = DB_USER;
    $passOption = DB_PASS !== '' ? '--password=' . DB_PASS : '--password=';
    $host = DB_HOST;
    $database = DB_NAME;
    $modeOption = $mode === 'data' ? '--no-create-info' : '';

    $command = sprintf(
        '"%s" --user=%s %s --host=%s %s %s %s > "%s"',
        $exePath,
        escapeshellarg($user),
        $passOption,
        escapeshellarg($host),
        $modeOption,
        escapeshellarg($database),
        implode(' ', array_map('escapeshellarg', $tables)),
        $backupFilePath
    );

    @shell_exec($command);
    return is_file($backupFilePath) && filesize($backupFilePath) > 0;
}

function exportDatabaseWithPDO(PDO $connection, string $backupFilePath, array $tables = [], string $mode = 'full'): bool
{
    $sqlDump = '';
    if (empty($tables)) {
        $tables = $connection->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    }

    foreach ($tables as $table) {
        if ($mode !== 'data') {
            $createResult = $connection->query("SHOW CREATE TABLE `{$table}`")->fetch(PDO::FETCH_ASSOC);
            if ($createResult && isset($createResult['Create Table'])) {
                $sqlDump .= "DROP TABLE IF EXISTS `{$table}`;\n";
                $sqlDump .= $createResult['Create Table'] . ";\n\n";
            }
        }

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $selectedTables = normalizeBackupTables((array)($_POST['selected_tables'] ?? []), $tables);
    $backupMode = in_array($_POST['backup_mode'] ?? 'full', ['full', 'data'], true) ? $_POST['backup_mode'] : 'full';

    if ($action === 'manual') {
        $manualSelectedTables = $selectedTables;
        $manualBackupMode = $backupMode;
        $backupLinkResult = runBackup($conexion, $selectedTables, $backupMode);
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

        $scheduleSelectedTables = $selectedTables;
        $scheduleBackupMode = $backupMode;

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
                'selected_tables' => $selectedTables,
                'backup_mode' => $backupMode,
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
        $scheduledBackupLink = runBackup($conexion, $schedule['selected_tables'] ?? [], $schedule['backup_mode'] ?? 'full');
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
$historyCount = count($backupHistory);
$nextScheduled = !empty($schedule['scheduled_at']) ? date('d/m/Y H:i', strtotime($schedule['scheduled_at'])) : 'No programado';
$scheduleModeLabel = isset($schedule['backup_mode']) && $schedule['backup_mode'] === 'data' ? 'Solo datos' : 'Completo';

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
    <title>Gestión de Respaldos</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<?php include 'layout/navbar.php'; ?>
<main class="section main">
    <div class="topbar">
        <div class="breadcrumb">
            <a href="dashboard.php">Inicio</a>
            <span class="sep">/</span>
            <span class="current">Respaldos</span>
        </div>
        <div class="header-actions">
            <button class="btn btn-secondary" onclick="document.getElementById('schedule_datetime').focus();">Programar respaldo</button>
            <button class="btn" onclick="window.scrollTo({top: document.querySelector('.card').offsetTop, behavior: 'smooth'});">Crear respaldo</button>
        </div>
    </div>

    <div class="content">
        <?php if ($message): ?>
            <div class="alert <?= $messageType === 'success' ? 'alert-success' : 'alert-error' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="page-header">
            <div>
                <h1>Gestión de respaldos</h1>
                <p class="subtitle">Genera respaldos manuales, agenda procesos automáticos y revisa el historial.</p>
            </div>
        </div>

        <div class="summary-grid">
            <div class="summary-card">
                <div class="summary-label">Respaldos guardados</div>
                <div class="summary-value"><?= $historyCount ?></div>
                <div class="summary-sub">Archivos disponibles en la carpeta de respaldos</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Próximo respaldo</div>
                <div class="summary-value"><?= htmlspecialchars($nextScheduled) ?></div>
                <div class="summary-sub">Modo: <?= htmlspecialchars($scheduleModeLabel) ?></div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Estado de programación</div>
                <div class="summary-value"><?= htmlspecialchars($schedule['last_run'] ? date('d/m/Y H:i', strtotime($schedule['last_run'])) : 'Nunca ejecutado') ?></div>
                <div class="summary-sub"><?= htmlspecialchars($scheduleStatus) ?></div>
            </div>
        </div>

        <div class="table-section">
            <div class="table-header">
                <h2 class="table-title">Historial de respaldos</h2>
                <div class="table-filters">
                    <div class="search-box">
                        <span>🔍</span>
                        <input type="text" placeholder="Buscar archivo..." disabled>
                    </div>
                </div>
            </div>
            <div class="card" style="margin:0; border:none; box-shadow:none;">
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
                                    <td><a class="action-link action-dl" href="<?= htmlspecialchars($history['link']) ?>" target="_blank">Descargar</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <h3>Respaldo manual</h3>
            <p>Presiona el botón para generar un archivo SQL con el contenido actual de la base de datos.</p>
            <form method="post" style="display: grid; gap: 0.75rem;">
                <input type="hidden" name="action" value="manual">
                <label for="manual_selected_tables">Seleccionar tablas (deja vacío para todas)</label>
                <select id="manual_selected_tables" name="selected_tables[]" multiple size="6">
                    <?php foreach ($tables as $table): ?>
                        <option value="<?= htmlspecialchars($table) ?>" <?= in_array($table, $manualSelectedTables, true) ? 'selected' : '' ?>><?= htmlspecialchars($table) ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="form-row">
                    <label><input type="radio" name="backup_mode" value="full" <?= $manualBackupMode === 'full' ? 'checked' : '' ?>> Backup completo</label>
                    <label><input type="radio" name="backup_mode" value="data" <?= $manualBackupMode === 'data' ? 'checked' : '' ?>> Solo datos</label>
                </div>
                <button type="submit" class="btn">Crear respaldo ahora</button>
            </form>
            <?php if ($backupLink): ?>
                <p class="subtitle">Descargar respaldo: <a href="<?= htmlspecialchars($backupLink) ?>" target="_blank"><?= htmlspecialchars($backupLink) ?></a></p>
            <?php endif; ?>
        </div>

        <div class="card">
            <h3>Respaldo programado</h3>
            <p>Programa un respaldo para una fecha y hora futura. Si la hora llega mientras nadie visita esta página, usa <code>backup_scheduler.php</code> para ejecutarlo automáticamente.</p>
            <form method="post" style="display: grid; gap: 0.75rem;">
                <input type="hidden" name="action" value="schedule">
                <label for="schedule_selected_tables">Seleccionar tablas (deja vacío para todas)</label>
                <select id="schedule_selected_tables" name="selected_tables[]" multiple size="6">
                    <?php foreach ($tables as $table): ?>
                        <option value="<?= htmlspecialchars($table) ?>" <?= in_array($table, $scheduleSelectedTables, true) ? 'selected' : '' ?>><?= htmlspecialchars($table) ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="form-row">
                    <label><input type="radio" name="backup_mode" value="full" <?= $scheduleBackupMode === 'full' ? 'checked' : '' ?>> Backup completo</label>
                    <label><input type="radio" name="backup_mode" value="data" <?= $scheduleBackupMode === 'data' ? 'checked' : '' ?>> Solo datos</label>
                </div>
                <label for="schedule_datetime">Fecha y hora del respaldo</label>
                <input id="schedule_datetime" type="datetime-local" name="schedule_datetime" required>
                <button type="submit" class="btn">Programar respaldo</button>
            </form>
            <p class="subtitle"><?= htmlspecialchars($scheduleStatus) ?></p>
            <p class="subtitle">Para ejecución totalmente automática, configura una tarea del sistema para llamar periódicamente a <code>backup_scheduler.php</code>.</p>
        </div>
    </div>
</main>
</body>
</html>
