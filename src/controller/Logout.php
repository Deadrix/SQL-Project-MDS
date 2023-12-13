<?php
session_start();
$_SESSION['user'] = null;

$twig = require_once '../functions/twigSetup.php';
require_once '../functions/functions.php';

header('Location: Accueil.php');