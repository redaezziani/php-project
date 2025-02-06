<?php

function Connect() {
    $dotenv = parse_ini_file(__DIR__ . '/.env');
    
    try {
        $dsn = sprintf(
            "mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4",
            $dotenv['SERVEUR'],
            $dotenv['DB_PORT'],
            $dotenv['DB_NAME']
        );
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ];
        
        return new PDO($dsn, $dotenv['UTILISATEUR'], $dotenv['PASSWORD'], $options);
    } catch (PDOException $e) {
        throw new Exception("Connection failed: " . $e->getMessage());
    }
}

function initializeDatabase() {
    $dotenv = parse_ini_file(__DIR__ . '/.env');
    $schemaPath = __DIR__ . '/../database/schema.mysql.sql';
    
    try {
        // Create database if it doesn't exist
        $pdo = new PDO(
            "mysql:host={$dotenv['SERVEUR']};port={$dotenv['DB_PORT']}",
            $dotenv['UTILISATEUR'],
            $dotenv['PASSWORD']
        );
        
        $pdo->exec("CREATE DATABASE IF NOT EXISTS {$dotenv['DB_NAME']} 
                    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        // Connect to the database
        $conn = Connect();
        
        // Read and execute schema file
        if (file_exists($schemaPath)) {
            $schema = file_get_contents($schemaPath);
            $conn->exec($schema);
            return true;
        }
        
        throw new Exception("Schema file not found");
    } catch (PDOException $e) {
        throw new Exception("Failed to initialize database: " . $e->getMessage());
    }
}
