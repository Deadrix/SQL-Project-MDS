<?php

require_once __DIR__ . "/../../config.php";

function connectToDatabase(): PDO {

    global $dsn, $user, $pass;
    try {
        $pdo = new PDO($dsn, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Erreur de base de données : " . $e->getMessage());
    }
}

