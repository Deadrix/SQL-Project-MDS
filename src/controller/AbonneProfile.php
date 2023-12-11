<?php
require_once '../../vendor/autoload.php';
require_once '../functions/functions.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader('../templates');
$twig = new Environment($loader);

session_start();
$pdo = connectToDatabase();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = htmlspecialchars($_POST["id"]);
    $nom = htmlspecialchars(ucwords(strtolower($_POST["nom"])));
    $prenom = htmlspecialchars(ucwords(strtolower($_POST["prenom"])));
    $date_naissance = htmlspecialchars($_POST["naissance"]);
    $adresse = htmlspecialchars(ucwords(strtolower($_POST["adresse"])));
    $code_postal = htmlspecialchars($_POST["codePostal"]);
    $ville = htmlspecialchars(ucwords(strtolower($_POST["ville"])));
    $date_inscription = htmlspecialchars($_POST["debutAbonnement"]);
    $date_fin_abo = htmlspecialchars($_POST["finAbonnement"]);

    $sql = "
            UPDATE abonne
            SET nom = :nom, prenom = :prenom, date_naissance = :date_naissance, adresse = :adresse, code_postal = :code_postal, ville = :ville, date_inscription = :date_inscription, date_fin_abo = :date_fin_abo
            WHERE id = :id
        ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':nom', $nom);
    $stmt->bindParam(':prenom', $prenom);
    $stmt->bindParam(':date_naissance', $date_naissance);
    $stmt->bindParam(':adresse', $adresse);
    $stmt->bindParam(':code_postal', $code_postal);
    $stmt->bindParam(':ville', $ville);
    $stmt->bindParam(':date_inscription', $date_inscription);
    $stmt->bindParam(':date_fin_abo', $date_fin_abo);
    $result = $stmt->execute();
    if ($result !== false) {
        echo "true";
    } else {
        echo "false";
    }

} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $sql = "SELECT * FROM abonne WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $_GET['id']]);
    $abonne = $stmt->fetch(PDO::FETCH_ASSOC);

    $sql = "SELECT li.titre, em.date_emprunt AS date
            FROM livre li
                JOIN emprunt em ON em.id_livre = li.id
            WHERE em.id_abonne = :id
            ORDER BY em.date_emprunt DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $_GET['id']);
    $stmt->execute();
    $emprunts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sqltest = "SELECT li.categorie
        FROM livre li
        JOIN emprunt em ON em.id_livre = li.id
        WHERE id_abonne = :id
            AND li.categorie IS NOT NULL  -- Exclure les catégories null de la sous-requête
        GROUP BY li.categorie
        ORDER BY COUNT(*) DESC
        LIMIT 1";
    $stmt = $pdo->prepare($sqltest);
    $stmt->bindValue(':id', $_GET['id']);
    $stmt->execute();
    $test = $stmt->fetch(PDO::FETCH_ASSOC);
    var_dump($test);

    if ($emprunts) {
        $sqlRecommandation = "SELECT li.titre, li.categorie
FROM emprunt em
JOIN livre li ON em.id_livre = li.id
WHERE id_abonne = :id
    AND li.categorie IS NOT NULL  -- Exclure les catégories null des résultats 
    AND li.categorie = :topCategorie
    AND em.date_emprunt >= DATE_SUB(NOW(), INTERVAL 1 YEAR)
    AND li.dispo = 1
GROUP BY li.titre
ORDER BY COUNT(*) DESC
LIMIT 5;";
        $stmt = $pdo->prepare($sqlRecommandation);
        $stmt->bindValue(':id', $_GET['id']);
        $stmt->bindValue(':topCategorie', $test['categorie']);
        $stmt->execute();
        $recommandations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $emprunts["message"] = "L'utilisateur n'a jamais emprunté de livre.";
        $recommandations["message"] = "Impossible de créer des recommandations personnalisée.";
    }

    if ($abonne) {
        echo $twig->render('AbonneProfile.twig', [
            'title' => 'Fiche Abonné',
            'abonne' => $abonne,
            'emprunts' => $emprunts,
            'recommandations' => $recommandations
        ]);
    } else {
        echo $twig->render('AbonneProfile.twig', [
            'title' => 'Fiche Abonné',
            'message' => 'Une erreur est survenue, veuillez ressayer plus tard.'
        ]);
    }
}