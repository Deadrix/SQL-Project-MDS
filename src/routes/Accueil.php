<?php

require_once '../../vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader('../templates');
$twig = new Environment($loader);



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Database connection
    $user = 'SQLMDS';
    $pass = 'SQLMDS';
    $dbname = 'sqlmds';
    $host = 'localhost';
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

    try {
        $bdh = new PDO($dsn, $user, $pass);
        $bdh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Erreur de base de données : " . $e->getMessage());
    }



    echo $twig->render('Accueil.twig', [
        'title' => 'Accueil',
        'livres' => 'mettre la var des livres'
    ]);
} else {
    echo $twig->render('Accueil.twig', [
        'title' => 'Accueil',
    ]);
}



