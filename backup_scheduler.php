<?php
require_once 'conexion.php';

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

function findMysqldumpExecutable(): ?string
{
    $paths = [];

    $output = @shell_exec('where mysqldump 2>NUL');
    if ($output) {
        $paths = array_merge($paths, preg_split('/\R+/', trim($output)));
    }

    $paths[] = 'C:\xampp\mysql\bin\mysqldump.exe';
    $paths[] = 'C:\Program Files\MySQL\MySQL Server 8.0\bin\mysqldump.exe';
    $paths[] = 'C:\Program Files\MySQL\MySQL Server 5.7\bin\mysqldump.exe';
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

$schedule = loadBackupSchedule();

if (empty($schedule['scheduled_at'])) {
    echo 'No hay un respaldo programado actualmente.';
    exit;
}

if (!empty($schedule['last_run'])) {
    echo 'El respaldo programado ya se ejecutó el ' . htmlspecialchars($schedule['last_run']);
    exit;
}

$scheduledAt = DateTime::createFromFormat('Y-m-d H:i:s', $schedule['scheduled_at']);
if ($scheduledAt === false) {
    echo 'Fecha de programación inválida.';
    exit;
}

$now = new DateTime();
if ($scheduledAt > $now) {
    echo 'Aún no es hora del respaldo programado. Programado para: ' . htmlspecialchars($schedule['scheduled_at']);
    exit;
}

$backupLink = runBackup($conexion);
if ($backupLink !== false) {
    $schedule['last_run'] = date('Y-m-d H:i:s');
    saveBackupSchedule($schedule);
    echo 'Respaldo programado ejecutado correctamente: ' . htmlspecialchars($backupLink);
} else {
    echo 'Error al ejecutar el respaldo programado. Revisa permisos y configuración.';
}
