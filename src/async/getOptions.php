<?php
$user = 'SQLMDS';
$pass = 'SQLMDS';
$dbname = 'sqlmds';
$host = 'localhost';
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

try {
    $bdh = new PDO($dsn, $user, $pass);
    $bdh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérez la valeur du paramètre 'field' passé dans l'URL
    $field = isset($_GET['field']) ? $_GET['field'] : '';

    // Utilisez une structure conditionnelle pour déterminer la requête SQL
    $sql = '';
    if ($field === 'titre') {
        $sql = "SELECT DISTINCT titre FROM livre ORDER BY titre";
    } elseif ($field === 'auteur') {
        $sql = "SELECT DISTINCT nom FROM auteur ORDER BY nom";
    } elseif ($field === 'editeur') {
        $sql = "SELECT DISTINCT nom FROM editeur ORDER BY nom";
    } else {
        // Gérer d'autres cas si nécessaire
        die("Champ inconnu : " . htmlspecialchars($field));
    }

    $sth = $bdh->prepare($sql);
    $sth->execute();
    $options = $sth->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode($options);
} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}
