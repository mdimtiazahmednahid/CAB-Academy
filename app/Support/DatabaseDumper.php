<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use PDO;
use RuntimeException;

class DatabaseDumper
{
    /**
     * Dump the configured database connection into a gzipped SQL file.
     *
     * @return array{path: string, filename: string, size_bytes: int, method: string}
     */
    public function dumpToGzip(?string $connection = null, ?string $directory = null): array
    {
        $connection = $connection ?: config('database.default');
        $config = config("database.connections.{$connection}");

        if (!is_array($config) || empty($config['driver'])) {
            throw new RuntimeException("Unsupported or missing database connection [{$connection}].");
        }

        $targetDir = $directory ?: config('backup.temp_directory', storage_path('app/backups'));
        if (!is_dir($targetDir) && !@mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
            throw new RuntimeException("Unable to create backup directory [{$targetDir}].");
        }

        $timestamp = now(config('backup.timezone', 'Asia/Dhaka'))->format('Y-m-d_H-i-s');
        $filename = "acs_backup_{$timestamp}.sql.gz";
        $outputPath = rtrim($targetDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

        $driver = strtolower((string) $config['driver']);

        try {
            if (in_array($driver, ['mysql', 'mariadb'], true)) {
                $method = $this->dumpMySql($connection, $config, $outputPath);
            } elseif ($driver === 'sqlite') {
                $method = $this->dumpSqliteViaPdo($connection, $outputPath);
            } else {
                throw new RuntimeException("Database driver [{$driver}] is not supported for backup.");
            }
        } catch (\Throwable $e) {
            if (is_file($outputPath)) {
                @unlink($outputPath);
            }
            throw $e;
        }

        clearstatcache(true, $outputPath);
        $sizeBytes = is_file($outputPath) ? (int) filesize($outputPath) : 0;

        if ($sizeBytes <= 0) {
            @unlink($outputPath);
            throw new RuntimeException('Generated backup file is empty.');
        }

        return [
            'path' => $outputPath,
            'filename' => $filename,
            'size_bytes' => $sizeBytes,
            'method' => $method,
        ];
    }

    /**
     * Attempt mysqldump first when proc_open is available, falling back to pure-PHP PDO.
     */
    protected function dumpMySql(string $connection, array $config, string $outputPath): string
    {
        if ($this->canUseMysqldump() && $this->dumpViaMysqldumpBinary($config, $outputPath)) {
            return 'mysqldump';
        }

        $this->dumpMySqlViaPdo($connection, $outputPath);

        return 'pdo';
    }

    protected function canUseMysqldump(): bool
    {
        if (!function_exists('proc_open')) {
            return false;
        }

        $disabled = array_map(
            'trim',
            explode(',', (string) ini_get('disable_functions'))
        );

        if (in_array('proc_open', $disabled, true)) {
            return false;
        }

        return $this->resolveMysqldumpBinary() !== null;
    }

    protected function resolveMysqldumpBinary(): ?string
    {
        foreach (['/usr/bin/mysqldump', '/usr/local/bin/mysqldump', '/usr/local/mysql/bin/mysqldump'] as $path) {
            if (@is_executable($path)) {
                return $path;
            }
        }

        return null;
    }

    protected function dumpViaMysqldumpBinary(array $config, string $outputPath): bool
    {
        $binary = $this->resolveMysqldumpBinary();
        if ($binary === null) {
            return false;
        }

        $database = (string) ($config['database'] ?? '');
        $username = (string) ($config['username'] ?? '');
        $password = (string) ($config['password'] ?? '');
        $host = (string) ($config['host'] ?? '127.0.0.1');
        $port = (string) ($config['port'] ?? '3306');
        $socket = (string) ($config['unix_socket'] ?? '');

        if ($database === '' || $username === '') {
            return false;
        }

        $args = [
            $binary,
            '--single-transaction',
            '--quick',
            '--routines',
            '--triggers',
            '--default-character-set=utf8mb4',
            '--user=' . $username,
        ];

        if ($socket !== '') {
            $args[] = '--socket=' . $socket;
        } else {
            $args[] = '--host=' . $host;
            $args[] = '--port=' . $port;
        }

        $args[] = $database;

        $command = implode(' ', array_map('escapeshellarg', $args));

        $env = $_ENV;
        if ($password !== '') {
            $env['MYSQL_PWD'] = $password;
        }

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = @proc_open($command, $descriptors, $pipes, null, $env);
        if (!is_resource($process)) {
            return false;
        }

        fclose($pipes[0]);

        $gz = @gzopen($outputPath, 'wb9');
        if ($gz === false) {
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($process);
            return false;
        }

        $bytesWritten = 0;
        while (!feof($pipes[1])) {
            $chunk = fread($pipes[1], 65536);
            if ($chunk !== false && $chunk !== '') {
                gzwrite($gz, $chunk);
                $bytesWritten += strlen($chunk);
            }
        }

        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        gzclose($gz);

        $exitCode = proc_close($process);

        if ($exitCode !== 0 || $bytesWritten === 0) {
            @unlink($outputPath);
            return false;
        }

        return true;
    }

    protected function dumpMySqlViaPdo(string $connection, string $outputPath): void
    {
        $pdo = DB::connection($connection)->getPdo();
        $gz = @gzopen($outputPath, 'wb9');
        if ($gz === false) {
            throw new RuntimeException("Unable to open [{$outputPath}] for gzip writing.");
        }

        try {
            $database = (string) config("database.connections.{$connection}.database");
            $header = implode("\n", [
                '-- ACS Academy Database Backup (Pure-PHP PDO)',
                '-- Database: `' . $database . '`',
                '-- Generated: ' . now(config('backup.timezone', 'Asia/Dhaka'))->toIso8601String(),
                '',
                'SET NAMES utf8mb4;',
                'SET FOREIGN_KEY_CHECKS=0;',
                "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';",
                '',
            ]);
            gzwrite($gz, $header);

            $tablesStmt = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
            $tables = $tablesStmt ? $tablesStmt->fetchAll(PDO::FETCH_NUM) : [];

            foreach ($tables as $row) {
                $table = (string) $row[0];
                $quotedTable = '`' . str_replace('`', '``', $table) . '`';

                $createStmt = $pdo->query("SHOW CREATE TABLE {$quotedTable}");
                $createRow = $createStmt ? $createStmt->fetch(PDO::FETCH_NUM) : false;
                if ($createRow && isset($createRow[1])) {
                    gzwrite($gz, "\n-- Table structure for {$quotedTable}\n");
                    gzwrite($gz, "DROP TABLE IF EXISTS {$quotedTable};\n");
                    gzwrite($gz, $createRow[1] . ";\n\n");
                }

                $this->streamTableInserts($pdo, $quotedTable, $gz, '`');
            }

            $viewsStmt = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'VIEW'");
            $views = $viewsStmt ? $viewsStmt->fetchAll(PDO::FETCH_NUM) : [];

            foreach ($views as $row) {
                $view = (string) $row[0];
                $quotedView = '`' . str_replace('`', '``', $view) . '`';

                $createStmt = $pdo->query("SHOW CREATE VIEW {$quotedView}");
                $createRow = $createStmt ? $createStmt->fetch(PDO::FETCH_NUM) : false;
                if ($createRow && isset($createRow[1])) {
                    gzwrite($gz, "\n-- View structure for {$quotedView}\n");
                    gzwrite($gz, "DROP VIEW IF EXISTS {$quotedView};\n");
                    gzwrite($gz, $createRow[1] . ";\n\n");
                }
            }

            gzwrite($gz, "\nSET FOREIGN_KEY_CHECKS=1;\n");
        } finally {
            gzclose($gz);
        }
    }

    protected function dumpSqliteViaPdo(string $connection, string $outputPath): string
    {
        $pdo = DB::connection($connection)->getPdo();
        $gz = @gzopen($outputPath, 'wb9');
        if ($gz === false) {
            throw new RuntimeException("Unable to open [{$outputPath}] for gzip writing.");
        }

        try {
            gzwrite($gz, "-- ACS Academy SQLite Backup (Pure-PHP PDO)\n");
            gzwrite($gz, '-- Generated: ' . now(config('backup.timezone', 'Asia/Dhaka'))->toIso8601String() . "\n");
            gzwrite($gz, "PRAGMA foreign_keys=OFF;\nBEGIN TRANSACTION;\n\n");

            $stmt = $pdo->query("SELECT type, name, sql FROM sqlite_master WHERE sql IS NOT NULL AND name NOT LIKE 'sqlite_%' ORDER BY CASE type WHEN 'table' THEN 1 WHEN 'index' THEN 2 WHEN 'trigger' THEN 3 WHEN 'view' THEN 4 ELSE 5 END");
            $objects = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

            foreach ($objects as $obj) {
                if (($obj['type'] ?? '') !== 'table') {
                    continue;
                }

                $table = (string) $obj['name'];
                $quotedTable = '"' . str_replace('"', '""', $table) . '"';

                gzwrite($gz, "DROP TABLE IF EXISTS {$quotedTable};\n");
                gzwrite($gz, $obj['sql'] . ";\n");
                $this->streamTableInserts($pdo, $quotedTable, $gz, '"');
                gzwrite($gz, "\n");
            }

            foreach ($objects as $obj) {
                if (($obj['type'] ?? '') === 'table') {
                    continue;
                }
                gzwrite($gz, $obj['sql'] . ";\n");
            }

            gzwrite($gz, "\nCOMMIT;\nPRAGMA foreign_keys=ON;\n");
        } finally {
            gzclose($gz);
        }

        return 'sqlite_pdo';
    }

    /**
     * Stream table rows in 500-row chunks into the gzip handle.
     *
     * @param resource $gz
     */
    protected function streamTableInserts(PDO $pdo, string $quotedTable, $gz, string $identifierQuote): void
    {
        $chunkSize = 500;
        $offset = 0;

        while (true) {
            $stmt = $pdo->query("SELECT * FROM {$quotedTable} LIMIT {$chunkSize} OFFSET {$offset}");
            if (!$stmt) {
                break;
            }

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($rows)) {
                break;
            }

            $columns = array_keys($rows[0]);
            $quotedCols = implode(', ', array_map(
                fn (string $col): string => $identifierQuote . str_replace($identifierQuote, $identifierQuote . $identifierQuote, $col) . $identifierQuote,
                $columns
            ));

            $valueSets = [];
            foreach ($rows as $row) {
                $escapedValues = [];
                foreach ($columns as $col) {
                    $val = $row[$col] ?? null;
                    if ($val === null) {
                        $escapedValues[] = 'NULL';
                    } elseif (is_int($val) || is_float($val)) {
                        $escapedValues[] = (string) $val;
                    } else {
                        $escapedValues[] = $pdo->quote((string) $val);
                    }
                }
                $valueSets[] = '(' . implode(', ', $escapedValues) . ')';
            }

            gzwrite($gz, "INSERT INTO {$quotedTable} ({$quotedCols}) VALUES\n" . implode(",\n", $valueSets) . ";\n");

            if (count($rows) < $chunkSize) {
                break;
            }

            $offset += $chunkSize;
        }
    }
}
