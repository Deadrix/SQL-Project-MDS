<?php

$twig = require_once '../functions/twigSetup.php';
require_once '../functions/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titre = '';
    $auteur = '';
    $editeur = '';
    $disponible = '';
    $page = 0;

    if (!isset($_SESSION["page"])) {
        $_SESSION["page"] = 0;
    }

    $hidePrecedent = false;
    $hideSuivant = false;

    if ($_POST["hiddenInput"] === "rechercher") {
        $titre = htmlspecialchars($_POST["titre"]);
        $auteur = htmlspecialchars($_POST["auteur"]);
        $editeur = htmlspecialchars($_POST["editeur"]);
        $disponible = htmlspecialchars($_POST["disponible"]);
        $_SESSION["filtres"] = [
            "titre" => $titre,
            "auteur" => $auteur,
            "editeur" => $editeur,
            "disponible" => $disponible
        ];
        $_SESSION["page"] = 0;
    } else if ($_POST["hiddenInput"] === "suivant") {
        if (isset($_SESSION["filtres"])) {
            $titre = $_SESSION["filtres"]["titre"];
            $auteur = $_SESSION["filtres"]["auteur"];
            $editeur = $_SESSION["filtres"]["editeur"];
            $disponible = $_SESSION["filtres"]["disponible"];
        }
        $_SESSION["page"] = $_SESSION["page"] + 1;
        $page = $_SESSION["page"];
    } else if ($_POST["hiddenInput"] == "precedent") {
        if (isset($_SESSION["filtres"])) {
            $titre = $_SESSION["filtres"]["titre"];
            $auteur = $_SESSION["filtres"]["auteur"];
            $editeur = $_SESSION["filtres"]["editeur"];
            $disponible = $_SESSION["filtres"]["disponible"];
        }
        $_SESSION["page"] = $_SESSION["page"] - 1;
        $page = $_SESSION["page"];
    }

    $offset = $page * 20;

    if ($offset <= 0) {
        $offset = 0;
        $hidePrecedent = true;
    }

    $livres = getBooks($titre, $auteur, $editeur, $disponible, $offset);


    $havingFiltres = isset($_SESSION["filtres"]);

    if (count($livres) < 20) {
        $hideSuivant = true;
    }
    if (count($livres) > 0) {
        echo $twig->render('Accueil.twig', [
            'title' => 'Accueil',
            'livres' => $livres,
            'havingFiltres' => $havingFiltres,
            'hidePrecedent' => $hidePrecedent,
            'hideSuivant' => $hideSuivant,
            'filtres' => $havingFiltres ? $_SESSION["filtres"] : null
        ]);
    } else {
        echo $twig->render('Accueil.twig', [
            'title' => 'Accueil',
            'message' => 'Aucun livre trouvé.',
            'havingFiltres' => true,
            'hidePrecedent' => true,
            'hideSuivant' => true,
            'filtres' => $havingFiltres ? $_SESSION["filtres"] : null
        ]);
    }
} else {
    unset($_SESSION["page"]);
    unset($_SESSION["filtres"]);

    $livres = getBooks();
    echo $twig->render('Accueil.twig', [
        'title' => 'Accueil',
        'livres' => $livres,
        'hidePrecedent' => true,
    ]);
}



