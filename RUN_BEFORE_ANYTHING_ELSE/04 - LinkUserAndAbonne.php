<?php
require_once "../src/functions/functions.php";

$pdo = connectToDatabase();

// Create the gestionnaire
$sqlAdmin = "INSERT INTO utilisateur (email, password, role) VALUES (:email, :password, 'gestionnaire')";
$stmt = $pdo->prepare($sqlAdmin);
$stmt->bindValue(":email", "gestionnaire@sqlmds.fr");
$stmt->bindValue(":password", password_hash("gestionnaire", PASSWORD_BCRYPT));
$stmt->execute();


// Get the first two abonnes
$sqlFirstAbonne = "SELECT id FROM abonne ORDER BY id LIMIT 2";
$stmt = $pdo->prepare($sqlFirstAbonne);
$stmt->execute();
$first2Abonne = $stmt->fetchAll(PDO::FETCH_ASSOC);


$sqlUpdateAbonne = "
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

// Update the first abonne
$stmt = $pdo->prepare($sqlUpdateAbonne);
$stmt->bindValue(":nom", "Bouriche");
$stmt->bindValue(":prenom", "Alexandre");
$stmt->bindValue(":date_naissance", "1999-09-09");
$stmt->bindValue(":adresse", "Chateau de Duloc");
$stmt->bindValue(":code_postal", "00000");
$stmt->bindValue(":ville", "FORT FORT LOINTAIN");
$stmt->bindValue(":date_inscription", "1999-09-09");
$stmt->bindValue(":date_fin_abo", "2099-09-09");
$stmt->bindValue(":id", $first2Abonne[0]["id"]);
$stmt->execute();

// Update the second abonne
$stmt = $pdo->prepare($sqlUpdateAbonne);
$stmt->bindValue(":nom", "Abonne");
$stmt->bindValue(":prenom", "Abonne");
$stmt->bindValue(":date_naissance", "1970-01-01");
$stmt->bindValue(":adresse", "128 Avenue de Fes");
$stmt->bindValue(":code_postal", "34090");
$stmt->bindValue(":ville", "MONTPELLIER");
$stmt->bindValue(":date_inscription", "1970-01-01");
$stmt->bindValue(":date_fin_abo", "2070-01-01");
$stmt->bindValue(":id", $first2Abonne[1]["id"]);
$stmt->execute();

// Get all the abonnes
$sql = "SELECT id, nom, prenom FROM abonne";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$abonnes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// For each abonne, we create a utilisateur
// The offset is here to avoid duplicate email
$offset = 0;
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

// Update the password and mail of the abonne "abonne"
$updatePassword = "UPDATE utilisateur SET password = :password, email = :newemail WHERE email = :email";
$stmt = $pdo->prepare($updatePassword);
$stmt->bindValue(":password", password_hash("abonne", PASSWORD_BCRYPT));
$stmt->bindValue(":newemail", "abonne@sqlmds.fr");
$stmt->bindValue(":email", "abonneabonne@sqlmds.fr");
$stmt->execute();

// Close the connection
$pdo = null;