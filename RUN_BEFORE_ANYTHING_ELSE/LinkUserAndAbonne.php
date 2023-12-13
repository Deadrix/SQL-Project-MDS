<?php
 require_once "../src/functions/functions.php";

    $pdo = connectToDatabase();
    $sql = "SELECT id, nom, prenom FROM abonne";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $abonnes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // For each abonne, we create a utilisateur
    foreach ($abonnes as $abonne) {
        $sql = "INSERT INTO utilisateur (email, password, role, id_abonne) VALUES (:email, :password, 'abonne', :id_abonne)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":email", str_replace([" ","'"], "",(strtolower($abonne['prenom'] . $abonne['nom']))) . "@hotmail.fr");
        $stmt->bindValue(":password", password_hash(str_replace([" ","'"], "",(strtolower($abonne['prenom'] . $abonne['nom']))), PASSWORD_BCRYPT));
        $stmt->bindValue(":id_abonne", $abonne["id"]);
        $stmt->execute();
    }