<?php

require_once __DIR__ . "/../../config.php";

function connectToDatabase(): PDO
{
    try {
        $pdo = new PDO(DSN, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Erreur de base de données : " . $e->getMessage());
    }
}

function getBooks(string $titre = null, string $auteur = null, string $editeur = null, string $disponible = null, int $offset = 0): array
{
    $pdo = connectToDatabase();

    $query = "
    SELECT li.titre as titre, au.nom AS auteur, ed.nom AS editeur, li.dispo AS disponibilite, (
        SELECT MAX(em.date_emprunt)
        FROM emprunt em
        WHERE em.id_livre = li.id
    ) AS dernieremprunt
    FROM livre li
    JOIN auteur au ON au.id = li.id_auteur
    JOIN editeur ed ON ed.id = li.id_editeur
    ";

    $whereClause = "WHERE ";
    $firstClause = true;

    if (!empty($titre)) {
        $firstClause = false;
        $whereClause .= "LOWER(li.titre) COLLATE utf8mb4_unicode_ci LIKE LOWER(:titre) COLLATE utf8mb4_unicode_ci";
    }
    if (!empty($auteur)) {
        if (!$firstClause) {
            $whereClause .= " AND ";
        }
        $whereClause .= "LOWER(au.nom) COLLATE utf8mb4_unicode_ci LIKE LOWER(:auteur) COLLATE utf8mb4_unicode_ci";
        $firstClause = false;
    }
    if (!empty($editeur)) {
        if (!$firstClause) {
            $whereClause .= " AND ";
        }
        $whereClause .= "LOWER(ed.nom) COLLATE utf8mb4_unicode_ci LIKE LOWER(:editeur) COLLATE utf8mb4_unicode_ci";
        $firstClause = false;
    }
    if ($disponible !== null && $disponible !== "") {
        if (!$firstClause) {
            $whereClause .= " AND ";
        }
        $whereClause .= "li.dispo = :disponibilite";
        $firstClause = false;
    }

    if (!$firstClause) {
        $query .= $whereClause;
    }

    $query .= " ORDER BY titre LIMIT 20 OFFSET :offset";

    $stmt = $pdo->prepare($query);

    if (!empty($titre)) {
        $stmt->bindValue(':titre', '%' . $titre . '%', PDO::PARAM_STR);
    }
    if (!empty($auteur)) {
        $stmt->bindValue(':auteur', '%' . $auteur . '%', PDO::PARAM_STR);
    }
    if (!empty($editeur)) {
        $stmt->bindValue(':editeur', '%' . $editeur . '%', PDO::PARAM_STR);
    }
    if ($disponible !== null && $disponible !== "") {
        $stmt->bindValue(':disponibilite', $disponible, PDO::PARAM_INT);
    }

    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();
    $pdo = null;
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

