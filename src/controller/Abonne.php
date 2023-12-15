<?php

$twig = require_once '../functions/twigSetup.php';
require_once '../functions/functions.php';


if (!isset($_SESSION["currentUser"])) {
    echo $twig->render('Login.twig', [
        'title' => 'Login',
        'message' => 'authentication error',
    ]);
    return;
}

if ($_SESSION["currentUser"]["role"] !== "gestionnaire") {
    echo $twig->render('Login.twig', [
        'title' => 'Login',
        'message' => 'authentication error',
    ]);
    return;
}

$pdo = connectToDatabase();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom = '';
    $prenom = '';
    $ville = '';
    $abonnement = '';
    $page = 0;

    if (!isset($_SESSION["page"])) {
        $_SESSION["page"] = 0;
    }

    $hidePrecedent = false;
    $hideSuivant = false;

    if ($_POST["hiddenInput"] === "rechercher") {
        $nom = htmlspecialchars($_POST["nom"]);
        $prenom = htmlspecialchars($_POST["prenom"]);
        $ville = htmlspecialchars($_POST["ville"]);
        $abonnement = htmlspecialchars($_POST["abonnement"]);
        $_SESSION["filtres"] = [
            "nom" => $nom,
            "prenom" => $prenom,
            "ville" => $ville,
            "abonnement" => $abonnement
        ];
        $_SESSION["page"] = 0;
    } else if ($_POST["hiddenInput"] === "suivant") {
        if (isset($_SESSION["filtres"])) {
            $nom = $_SESSION["filtres"]["nom"];
            $prenom = $_SESSION["filtres"]["prenom"];
            $ville = $_SESSION["filtres"]["ville"];
            $abonnement = $_SESSION["filtres"]["abonnement"];
        }
        $_SESSION["page"] = $_SESSION["page"] + 1;
        $page = $_SESSION["page"];
    } else if ($_POST["hiddenInput"] === "precedent") {
        if (isset($_SESSION["filtres"])) {
            $nom = $_SESSION["filtres"]["nom"];
            $prenom = $_SESSION["filtres"]["prenom"];
            $ville = $_SESSION["filtres"]["ville"];
            $abonnement = $_SESSION["filtres"]["abonnement"];
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
            SELECT id, nom, prenom, ville, 
                IF(date_fin_abo < CURDATE(), TRUE, FALSE) AS abonnement
            FROM abonne ab
            WHERE (NULLIF(:nom, '') IS NULL OR LOWER(nom) COLLATE utf8mb4_unicode_ci LIKE LOWER(:nom) COLLATE utf8mb4_unicode_ci)
              AND (NULLIF(:prenom, '') IS NULL OR LOWER(prenom) COLLATE utf8mb4_unicode_ci LIKE LOWER(:prenom) COLLATE utf8mb4_unicode_ci)
              AND (NULLIF(:ville, '') IS NULL OR LOWER(ville) COLLATE utf8mb4_unicode_ci LIKE LOWER(:ville) COLLATE utf8mb4_unicode_ci)
              AND (
                    :abonnement = '' 
                    OR (
                        :abonnement = TRUE AND date_fin_abo <= CURDATE()
                    )
                    OR (
                        :abonnement = FALSE AND date_fin_abo > CURDATE()
                    )
                  )
            ORDER BY nom
            LIMIT 20 OFFSET :offset;

        ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':nom', "%" . $nom . "%");
    $stmt->bindValue(':prenom', "%" . $prenom . "%");
    $stmt->bindValue(':ville', "%" . $ville . "%");
    $stmt->bindValue(':abonnement', $abonnement);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $abonnes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $pdo = null;
    $havingFiltres = isset($_SESSION["filtres"]);

    if ($abonnes) {
        if (count($abonnes) < 20) {
            $hideSuivant = true;
        }
        echo $twig->render('Abonne.twig', [
            'title' => 'Abonnés',
            'abonnes' => $abonnes,
            'havingFiltres' => $havingFiltres,
            'hidePrecedent' => $hidePrecedent,
            'hideSuivant' => $hideSuivant,
            'filtres' => $havingFiltres ? $_SESSION["filtres"] : null
        ]);
    } else {
        echo $twig->render('Abonne.twig', [
            'title' => 'Abonnés',
            'message' => 'Aucun abonné trouvé.',
            'havingFiltres' => true,
            'hidePrecedent' => true,
            'hideSuivant' => true,
            'filtres' => $havingFiltres ? $_SESSION["filtres"] : null
        ]);
    }
} else {
    unset($_SESSION["page"]);
    unset($_SESSION["filtres"]);

    $sql = "
            SELECT id, nom, prenom, ville, 
                IF(date_fin_abo < CURDATE(), TRUE, FALSE) AS abonnement
            FROM abonne
            ORDER BY nom
            LIMIT 20;
        ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $abonnes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $pdo = null;
    if ($abonnes) {
        echo $twig->render('Abonne.twig', [
            'title' => 'Abonnés',
            'abonnes' => $abonnes,
            'hidePrecedent' => true,
        ]);
    } else {
        echo $twig->render('Abonne.twig', [
            'title' => 'Abonnés',
            'message' => 'Aucun abonné trouvé.',
            'hidePrecedent' => true,
        ]);
    }
}



