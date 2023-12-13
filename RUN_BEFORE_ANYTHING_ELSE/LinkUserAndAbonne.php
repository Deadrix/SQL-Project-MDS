<?php
require_once "../src/functions/functions.php";

$pdo = connectToDatabase();


$sqlAdmin = "INSERT INTO utilisateur (email, password, role) VALUES (:email, :password, 'gestionnaire')";
$stmt = $pdo->prepare($sqlAdmin);
$stmt->bindValue(":email", "gestionnaire@sqlmds.fr");
$stmt->bindValue(":password", password_hash("gestionnaire", PASSWORD_BCRYPT));
$stmt->execute();

// We get all the abonnes
$sql = "SELECT id, nom, prenom FROM abonne";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$abonnes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$offset = 0;
// For each abonne, we create a utilisateur
foreach ($abonnes as $abonne) {
    try {
        $sql = "INSERT INTO utilisateur (email, password, role, id_abonne) VALUES (:email, :password, 'abonne', :id_abonne)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":email", str_replace([" ", "'"], "", (strtolower($abonne['prenom'] . $abonne['nom']))) . "@sqlmds.fr");
        $stmt->bindValue(":password", password_hash(str_replace([" ", "'"], "", (strtolower($abonne['prenom'] . $abonne['nom']))), PASSWORD_BCRYPT));
        $stmt->bindValue(":id_abonne", $abonne["id"]);
        $stmt->execute();
    } catch (PDOException $e) {
        echo $e->getMessage() . "\n";
        $sql = "INSERT INTO utilisateur (email, password, role, id_abonne) VALUES (:email, :password, 'abonne', :id_abonne)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":email", str_replace([" ", "'"], "", (strtolower($abonne['prenom'] . $abonne['nom']))) . $offset . "@sqlmds.fr");
        $stmt->bindValue(":password", password_hash(str_replace([" ", "'"], "", (strtolower($abonne['prenom'] . $abonne['nom']) . $offset)), PASSWORD_BCRYPT));
        $stmt->bindValue(":id_abonne", $abonne["id"]);
        $stmt->execute();
        $offset++;
    }
}

$pdo = null;