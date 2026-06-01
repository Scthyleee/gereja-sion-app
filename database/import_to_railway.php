<?php
/**
 * Import clean SQL dump to Railway MySQL (line-by-line approach)
 */

$host     = 'hopper.proxy.rlwy.net';
$port     = 22510;
$user     = 'root';
$password = 'eRQZvKeFUKysBxvTPwRYRusIzmzksdZS';
$dbname   = 'railway';
$sqlFile  = __DIR__ . '/db_clean_export.sql';

echo "Connecting to Railway MySQL...\n";

try {
    $pdo = new PDO(
        "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4; SET FOREIGN_KEY_CHECKS=0;",
        ]
    );
    echo "Connected!\n";
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage() . "\n");
}

echo "Reading SQL file...\n";
$lines = file($sqlFile);
if (!$lines) {
    die("Cannot read SQL file.\n");
}

$pdo->exec("SET FOREIGN_KEY_CHECKS=0; SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';");

$stmt = '';
$success = 0;
$errors  = 0;
$total   = count($lines);

foreach ($lines as $i => $line) {
    $line = rtrim($line);
    
    // Skip empty lines and single-line comments
    if ($line === '' || str_starts_with($line, '--') || str_starts_with($line, '/*')) {
        continue;
    }
    
    $stmt .= $line . "\n";
    
    // Execute when we hit end of statement
    if (str_ends_with(trim($line), ';')) {
        $stmtTrim = trim($stmt);
        if ($stmtTrim && $stmtTrim !== ';') {
            try {
                $pdo->exec($stmtTrim);
                $success++;
            } catch (PDOException $e) {
                $errors++;
                $msg = $e->getMessage();
                // Show meaningful errors only
                if (strpos($msg, 'already exists') === false && strpos($msg, 'Duplicate') === false) {
                    echo "  ERR: " . substr($msg, 0, 120) . "\n";
                    echo "  SQL: " . substr($stmtTrim, 0, 80) . "\n";
                }
            }
        }
        $stmt = '';
        
        if ($success % 100 === 0 && $success > 0) {
            echo "  OK: {$success} statements executed...\n";
            flush();
        }
    }
}

$pdo->exec("SET FOREIGN_KEY_CHECKS=1;");

// Verify tables
echo "\n=== Verifying tables ===\n";
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo "Tables in Railway DB: " . count($tables) . "\n";
foreach ($tables as $t) {
    $count = $pdo->query("SELECT COUNT(*) FROM `{$t}`")->fetchColumn();
    echo "  {$t}: {$count} rows\n";
}

echo "\n=== DONE ===\n";
echo "Success: {$success} | Errors: {$errors}\n";
