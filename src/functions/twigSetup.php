<?php

require_once '../../vendor/autoload.php';
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

$loader = new FilesystemLoader('../templates');
$twig = new Environment($loader);

session_start();
$twig->addFunction(new TwigFunction('getCurrentUser', function () {
    return $_SESSION['currentUser'] ?? null; // Récupération de la variable 'loggedUser'
}));

return $twig;