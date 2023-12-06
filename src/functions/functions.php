<?php
function connectToDatabase() : PDO {
    $user = 'SQLMDS';
    $pass = 'SQLMDS';
    $dbname = 'sqlmds';
    $host = 'localhost';
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

    try {
        $bdh = new PDO($dsn, $user, $pass);
        $bdh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $bdh; // Retourne l'objet PDO pour la connexion
    } catch (PDOException $e) {
        die("Erreur de base de données : " . $e->getMessage());
    }
}