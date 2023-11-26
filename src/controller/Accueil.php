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

    $titre = $_POST["titre"];
    $auteur = $_POST["auteur"];
    $editeur = $_POST["editeur"];
    $disponibilite = $_POST["disponible"];

    $sql = "
    SELECT
        titre,
        auteur,
        editeur,
        dernieremprunt,
        emprunt,
        retour,
        disponibilite
    FROM (
        SELECT
            li.titre AS titre,
            au.nom AS auteur,
            ed.nom AS editeur,
            IFNULL(MAX(em.date_emprunt), NULL) AS dernieremprunt,
            MAX(em.date_emprunt) AS emprunt,
            MAX(em.date_retour) AS retour,
            IF(MAX(em.date_emprunt) IS NULL OR MAX(em.date_retour) < CURDATE(), TRUE, FALSE) AS disponibilite
        FROM livre AS li
        JOIN auteur au ON li.id_auteur = au.id
        JOIN editeur ed ON li.id_editeur = ed.id
        LEFT JOIN emprunt em ON li.id = em.id_livre
        WHERE
            (NULLIF(:titre, '') IS NULL OR li.titre = :titre)
            AND (NULLIF(:auteur, '') IS NULL OR au.nom = :auteur)
            AND (NULLIF(:editeur, '') IS NULL OR ed.nom = :editeur)
        GROUP BY li.id
    ) AS subquery
    WHERE (NULLIF(:disponibilite, '') IS NULL OR disponibilite = :disponibilite)
    ORDER BY titre
    
    ";
    $sth = $bdh->prepare($sql);
    $sth->bindValue(':titre', $titre);
    $sth->bindValue(':auteur', $auteur);
    $sth->bindValue(':editeur', $editeur);
    $sth->bindValue(':disponibilite', $disponibilite);
    $sth->execute();
    $livres = $sth->fetchAll(PDO::FETCH_ASSOC);

    if (count($livres) > 0) {
        echo $twig->render('Accueil.twig', [
            'title' => 'Accueil',
            'livres' => $livres
        ]);
    } else {
        echo $twig->render('Accueil.twig', [
            'title' => 'Accueil',
            'message' => 'Aucun résultat trouvé.',
        ]);
    }

} else {
    echo $twig->render('Accueil.twig', [
        'title' => 'Accueil',
        'message' => 'Renseignez vos filtres puis appuyez sur Rechercher'
    ]);
}



