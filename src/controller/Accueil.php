<?php

$twig = require_once '../functions/twigSetup.php';
require_once '../functions/functions.php';

$pdo = connectToDatabase();

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

    $sql = "
            SELECT li.titre as titre, au.nom AS auteur, ed.nom AS editeur, li.dispo AS disponibilite, (
                SELECT MAX(em.date_emprunt)
                FROM emprunt em
                WHERE em.id_livre = li.id
            ) AS dernieremprunt
            FROM livre li
            JOIN auteur au ON au.id = li.id_auteur
            JOIN editeur ed ON ed.id = li.id_editeur
            WHERE (NULLIF(:titre, '') IS NULL OR LOWER(li.titre) COLLATE utf8mb4_unicode_ci LIKE LOWER(:titre) COLLATE utf8mb4_unicode_ci)
              AND (NULLIF(:auteur, '') IS NULL OR LOWER(au.nom) COLLATE utf8mb4_unicode_ci LIKE LOWER(:auteur) COLLATE utf8mb4_unicode_ci)
              AND (NULLIF(:editeur, '') IS NULL OR LOWER(ed.nom) COLLATE utf8mb4_unicode_ci LIKE LOWER(:editeur) COLLATE utf8mb4_unicode_ci)
              AND (NULLIF(:disponibilite, '') IS NULL OR li.dispo = :disponibilite)
            ORDER BY titre
            LIMIT 20 OFFSET :offset;
        ";
    $sth = $pdo->prepare($sql);
    $sth->bindValue(':titre', "%" . $titre . "%");
    $sth->bindValue(':auteur', "%" . $auteur . "%");
    $sth->bindValue(':editeur', "%" . $editeur . "%");
    $sth->bindValue(':disponibilite', $disponible);
    $sth->bindValue(':offset', $offset, PDO::PARAM_INT);
    $sth->execute();
    $livres = $sth->fetchAll(PDO::FETCH_ASSOC);
    $pdo = null;
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

    $sql =
        "
            SELECT li.titre as titre, au.nom AS auteur, ed.nom AS editeur, li.dispo AS disponibilite, (
                SELECT MAX(em.date_emprunt)
                FROM emprunt em
                WHERE em.id_livre = li.id
            ) AS dernieremprunt
            FROM livre li
            JOIN auteur au ON au.id = li.id_auteur
            JOIN editeur ed ON ed.id = li.id_editeur
            ORDER BY titre ASC
            LIMIT 20;
        ";
    $sth = $pdo->prepare($sql);
    $sth->execute();
    $livres = $sth->fetchAll(PDO::FETCH_ASSOC);
    $pdo = null;
    echo $twig->render('Accueil.twig', [
        'title' => 'Accueil',
        'livres' => $livres,
        'hidePrecedent' => true,
    ]);
}



