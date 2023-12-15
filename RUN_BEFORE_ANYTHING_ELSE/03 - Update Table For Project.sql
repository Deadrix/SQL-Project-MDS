-- Change database charset and collate 
ALTER TABLE abonne
    CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE auteur
    CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE editeur
    CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE emprunt
    CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE genre
    CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE livre
    CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Add dispo column on livre table cause it's smarter like this
ALTER TABLE livre
    ADD COLUMN dispo tinyint;

-- Updates livre dispo field base on the last "date_retour"
UPDATE IGNORE livre l
SET l.dispo = IF((SELECT e.date_retour
                  FROM emprunt e
                  WHERE e.id_livre = l.id
                    AND e.date_emprunt = (
                        SELECT MAX(e2.date_emprunt)
                        FROM emprunt e2
                        WHERE e2.id_livre = l.id)
                  ) < CURDATE(), TRUE, IF((SELECT e.date_retour
                                           FROM emprunt e
                                           WHERE e.id_livre = l.id
                                             AND e.date_emprunt = (
                                               SELECT MAX(e3.date_emprunt)
                                               FROM emprunt e3
                                               WHERE e3.id_livre = l.id)
                                           ) IS NULL, TRUE, FALSE));

-- Create user table
CREATE TABLE utilisateur
(
    id        INT AUTO_INCREMENT PRIMARY KEY,
    email     VARCHAR(255) UNIQUE            NOT NULL,
    password  VARCHAR(255) UNIQUE            NOT NULL,
    role      ENUM ('abonne','gestionnaire') NOT NULL,
    id_abonne INT                            NULL,
    FOREIGN KEY (id_abonne) REFERENCES abonne (id)
) ENGINE = InnoDB;