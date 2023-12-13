<?php

function connectToDatabase() : PDO {
    $user = 'SQLMDS';
    $pass = 'SQLMDS';
    $dbname = 'sqlmds';
    $host = 'localhost';
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

    try {
        $pdo = new PDO($dsn, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Erreur de base de données : " . $e->getMessage());
    }
}

