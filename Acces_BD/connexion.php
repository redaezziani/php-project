<?php

function Connect() {
    if (!extension_loaded('pdo_sqlite')) {
        die("Error: PDO SQLite driver is not installed. Please install php-sqlite3");
    }

    $env = parse_ini_file(__DIR__ . '/.env');
    $dbPath = __DIR__ . '/' . $env['DB_PATH'];
    
    try {
        // Create database directory if it doesn't exist
        $dbDir = dirname($dbPath);
        if (!is_dir($dbDir)) {
            mkdir($dbDir, 0777, true);
        }

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ];
        
        $conn = new PDO("sqlite:{$dbPath}", null, null, $options);
        
        // Enable foreign keys
        $conn->exec('PRAGMA foreign_keys = ON');
        
        return $conn;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage() . 
            "<br>Please check if: <br>" .
            "1. SQLite is installed<br>" .
            "2. Database directory is writable<br>" .
            "3. Database file path is correct: {$dbPath}<br>");
    }
}

// Initialize database if it doesn't exist
function initializeDatabase() {
    $env = parse_ini_file(__DIR__ . '/.env');
    $dbPath = __DIR__ . '/' . $env['DB_PATH'];
    $schemaPath = __DIR__ . '/../database/schema.sqlite.sql';
    
    $conn = Connect();
    
    try {
        // Begin transaction
        $conn->beginTransaction();
        
        // Read and execute schema file
        if (file_exists($schemaPath)) {
            $schema = file_get_contents($schemaPath);
            $statements = explode(';', $schema);
            
            foreach($statements as $statement) {
                if (trim($statement) != '') {
                    $conn->exec($statement);
                }
            }
        }
        
        // Commit transaction
        $conn->commit();
        return true;
    } catch (PDOException $e) {
        // Rollback on error
        $conn->rollBack();
        die("Failed to initialize database: " . $e->getMessage());
    }
}
