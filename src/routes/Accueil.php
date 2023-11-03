<?php

require_once '../../vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader('../templates');
$twig = new Environment($loader);

//// Database connection
//$user = 'SQLMDS';
//$pass = 'SQLMDS';
//$dbname = 'sqlmds';
//$host = 'localhost';
//$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
//$bdh = new PDO($dsn, $user, $pass);

echo $twig->render('Accueil.twig', [
    'title' => 'Accueil',
]);

