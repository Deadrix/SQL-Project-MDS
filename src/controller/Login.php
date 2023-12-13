<?php

require_once '../../vendor/autoload.php';
require_once '../functions/functions.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader('../templates');
$twig = new Environment($loader);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['email']) || !empty($_POST['password'])) {
        session_start();
        $email = $_POST['email'];
        $password = $_POST['password'];

        $pdo = connectToDatabase();
        $query = $pdo->prepare('SELECT * FROM utilisateur WHERE email = :email');
        $query->execute(['email' => $email]);
        $user = $query->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            echo "true";
        } else {
            echo "false";
        }
    }
} else {
    echo $twig->render('Login.twig', [
        'title' => 'login'
    ]);
}
