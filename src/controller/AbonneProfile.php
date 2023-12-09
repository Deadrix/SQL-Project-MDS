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
    $nom = htmlspecialchars(strtoupper($_POST["nom"]));
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
    if (count($abonne) > 0) {
        echo $twig->render('AbonneProfile.twig', [
            'title' => 'Fiche Abonné',
            'abonne' => $abonne,
        ]);
    } else {
        echo $twig->render('AbonneProfile.twig', [
            'title' => 'Fiche Abonné',
            'message' => 'Une erreur est survenue, veuillez ressayer plus tard.'
        ]);
    }
}