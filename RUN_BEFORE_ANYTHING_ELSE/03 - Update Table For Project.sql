-- Change database charset and collate 
ALTER TABLE abonne CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE auteur CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE editeur CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE emprunt CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE genre CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE livre CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Add 2 months to all dates otherwise all books are available 
UPDATE IGNORE emprunt
SET date_retour = DATE_ADD(date_retour, INTERVAL 2 MONTH),
    date_emprunt = DATE_ADD(date_emprunt, INTERVAL 2 MONTH);

UPDATE IGNORE emprunt
SET date_retour = DATE_SUB(date_retour, INTERVAL 2 MONTH),
    date_emprunt = DATE_SUB(date_emprunt, INTERVAL 2 MONTH);
    
-- Add dispo column on livre table cause it's smarter like this
ALTER TABLE livre ADD COLUMN dispo tinyint;

-- Updates livre dispo field base on the last "date_retour"
UPDATE IGNORE livre l
SET l.dispo = CASE 
    WHEN (
        SELECT MAX(e.date_retour) 
        FROM emprunt e 
        WHERE e.id_livre = l.id
    ) < CURDATE()
    THEN TRUE
    ELSE FALSE
    END;

-- Create user table
CREATE TABLE utilisateur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) UNIQUE NOT NULL,
    role VARCHAR(50) NOT NULL,
    id_abonne INT(11) NULL,
    CONSTRAINT id_abonne FOREIGN KEY (id_abonne) REFERENCES abonne(id)
);