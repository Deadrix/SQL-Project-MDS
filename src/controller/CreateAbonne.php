<?php

$twig = require_once '../functions/twigSetup.php';
require_once '../functions/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

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
            'message' => 'permission error',
        ]);
        return;
    }

    $pdo = connectToDatabase();

    $nom = htmlspecialchars(ucwords(strtolower($_POST["nom"])));
    $prenom = htmlspecialchars(ucwords(strtolower($_POST["prenom"])));
    $date_naissance = htmlspecialchars($_POST["naissance"]);
    $adresse = htmlspecialchars(ucwords(strtolower($_POST["adresse"])));
    $code_postal = htmlspecialchars($_POST["codePostal"]);
    $ville = htmlspecialchars(ucwords(strtolower($_POST["ville"])));
    $email = htmlspecialchars($_POST["email"]);

    $sqlCreateAbonne = "
            INSERT INTO abonne
                    (nom, 
                    prenom,
                    date_naissance,
                    adresse,
                    code_postal,
                    ville,
                    date_inscription,
                    date_fin_abo)
            VALUES (:nom, 
                    :prenom,
                    :date_naissance,
                    :adresse,
                    :code_postal,
                    :ville,
                    CURDATE(),
                    DATE_ADD(CURDATE(), INTERVAL 1 YEAR))                                    
        ";

    $stmt = $pdo->prepare($sqlCreateAbonne);
    $stmt->bindParam(':nom', $nom);
    $stmt->bindParam(':prenom', $prenom);
    $stmt->bindParam(':date_naissance', $date_naissance);
    $stmt->bindParam(':adresse', $adresse);
    $stmt->bindParam(':code_postal', $code_postal);
    $stmt->bindParam(':ville', $ville);
    $result = $stmt->execute();

    if ($result !== false) {
        $abonneId = $pdo->lastInsertId();
        $sqlCreateUser = "INSERT INTO utilisateur (email, password, role, id_abonne) VALUES (:email, :password, 'abonne', :id_abonne)";
        $stmt = $pdo->prepare($sqlCreateUser);
        $stmt->bindValue(":email", $email);
        $stmt->bindValue(":password", password_hash(str_replace([" ", "'"], "", (strtolower($prenom . $nom))), PASSWORD_BCRYPT));
        $stmt->bindValue(":id_abonne", $abonneId);
        $result = $stmt->execute();
        if ($result !== false) {
            echo "true";
        } else {
            $sqlDeleteAbonne = "DELETE FROM abonne WHERE id = :id_abonne";
            $stmt = $pdo->prepare($sqlDeleteAbonne);
            $stmt->bindValue(":id_abonne", $abonneId);
            $stmt->execute();
            echo "false";
        }
    } else {
        echo "false";
    }
    $pdo = null;

} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {

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
            'message' => 'permission error',
        ]);
        return;
    }

    echo $twig->render('CreateAbonne.twig', [
        'title' => 'Nouvel abonné',
    ]);
}