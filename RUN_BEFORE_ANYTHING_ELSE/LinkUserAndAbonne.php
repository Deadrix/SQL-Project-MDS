<?php
require_once "../src/functions/functions.php";

$pdo = connectToDatabase();


$sqlAdmin = "INSERT INTO utilisateur (email, password, role) VALUES (:email, :password, 'gestionnaire')";
$stmt = $pdo->prepare($sqlAdmin);
$stmt->bindValue(":email", "gestionnaire@sqlmds.fr");
$stmt->bindValue(":password", password_hash("gestionnaire", PASSWORD_BCRYPT));
$stmt->execute();

$sqlFirstAbonne = "SELECT id FROM abonne ORDER BY id LIMIT 1 ";
$stmt = $pdo->prepare($sqlFirstAbonne);
$stmt->execute();
$firstAbonne = $stmt->fetch(PDO::FETCH_ASSOC);

$sqlUpdateFirstAbonne = "
                        UPDATE abonne
                        SET nom = :nom, 
                            prenom = :prenom, 
                            date_naissance = :date_naissance, 
                            adresse = :adresse, 
                            code_postal = :code_postal, 
                            ville = :ville, 
                            date_inscription = :date_inscription, 
                            date_fin_abo = :date_fin_abo
                        WHERE id = :id
                        ";
$stmt = $pdo->prepare($sqlUpdateFirstAbonne);
$stmt->bindValue(":nom", "Bouriche");
$stmt->bindValue(":prenom", "Alexandre");
$stmt->bindValue(":date_naissance", "1999-09-09");
$stmt->bindValue(":adresse", "Place d'Armes");
$stmt->bindValue(":code_postal", "78000");
$stmt->bindValue(":ville", "VERSAILLES");
$stmt->bindValue(":date_inscription", "1999-09-09");
$stmt->bindValue(":date_fin_abo", "2099-09-09");
$stmt->bindValue(":id", $firstAbonne["id"]);
$stmt->execute();

// Get all the abonnes
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