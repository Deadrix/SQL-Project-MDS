<?php

$twig = require_once '../functions/twigSetup.php';
require_once '../functions/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['email']) || !empty($_POST['password'])) {

        $email = $_POST['email'];
        $password = $_POST['password'];

        $pdo = connectToDatabase();
        $query = $pdo->prepare('SELECT id, email, password, role, id_abonne FROM utilisateur WHERE email = :email');
        $query->execute(['email' => $email]);
        $user = $query->fetch();
        $pdo = null;

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['currentUser'] = $user;
            echo "true";
        } else {
            echo "false";
        }
    }
} else {
    echo $twig->render('Login.twig', [
        'title' => 'Login'
    ]);
}
