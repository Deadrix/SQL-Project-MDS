<?php

require_once '../../vendor/autoload.php';
require_once '../functions/functions.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader('../templates');
$twig = new Environment($loader);

session_start();
$_SESSION['user'] = null;
header('Location: Accueil.php');