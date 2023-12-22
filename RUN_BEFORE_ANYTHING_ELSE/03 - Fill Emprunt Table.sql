DROP PROCEDURE IF EXISTS generate_emprunts;
DELIMITER $$

CREATE PROCEDURE generate_emprunts()
BEGIN
    DECLARE fincurs1 BOOL DEFAULT 0;
    DECLARE dt_deb DATETIME;
    DECLARE dt_fin DATETIME;
    DECLARE dt_retour DATETIME;
    DECLARE dt_emprunt DATETIME;
    DECLARE id_abo INT;
    DECLARE id_liv INT;
    DECLARE i INT;
    DECLARE periode_jours INT;
    DECLARE jours INT;
    DECLARE duree INT;
    DECLARE max_livre BIGINT;
    DECLARE pos INT;

    DECLARE mycursor CURSOR FOR SELECT id, date_inscription, date_fin_abo
                                FROM abonne
                                WHERE date_inscription IS NOT NULL;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET fincurs1 := 1;

    DELETE FROM emprunt;

    OPEN mycursor;

    FETCH mycursor INTO id_abo, dt_deb, dt_fin;

    WHILE NOT fincurs1
        DO
            IF dt_fin IS NULL THEN
                SET dt_fin = DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY);
            END IF;
            IF dt_fin > CURRENT_DATE THEN
                SET dt_fin = DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY);
            END IF;
            SET periode_jours = DATEDIFF(dt_fin, dt_deb) - 10;
            SET i = 1;
            WHILE i <= 10
                DO
                    SET jours = FLOOR(RAND() * periode_jours);
                    SET duree = FLOOR(RAND() * 10);

                    SET dt_emprunt = DATE_ADD(dt_deb, INTERVAL jours DAY);
                    IF (RAND() * 100) < 2 THEN
                        SET dt_retour = NULL;
                    ELSE
                        SET dt_retour = DATE_ADD(dt_deb, INTERVAL (jours + duree) DAY);
                    END IF;

                    SELECT COUNT(*)
                    INTO max_livre
                    FROM livre
                    WHERE id NOT IN (SELECT id_livre
                                     FROM emprunt
                                     WHERE (date_emprunt >= dt_emprunt AND date_emprunt <= dt_retour)
                                        OR (date_retour >= dt_emprunt AND date_retour <= dt_retour));

                    SET pos = (RAND() * max_livre) + 1;
                    SELECT id
                    INTO id_liv
                    FROM livre
                    WHERE id NOT IN (SELECT id_livre
                                     FROM emprunt
                                     WHERE (date_emprunt >= dt_emprunt AND date_emprunt <= dt_retour)
                                        OR (date_retour >= dt_emprunt AND date_retour <= dt_retour))
                    LIMIT 1 OFFSET pos;

                    IF fincurs1 THEN
                        SET fincurs1 = FALSE;
                    END IF;

                    INSERT INTO emprunt(id_abonne, id_livre, date_emprunt, date_retour)
                    SELECT id_abo, id_liv, dt_emprunt, dt_retour
                    FROM dual
                    WHERE NOT EXISTS (SELECT 1
                                      FROM emprunt
                                      WHERE id_abonne = id_abo AND id_livre = id_liv AND date_emprunt = dt_emprunt);

                    SET i = i + 1;
                END WHILE;
            FETCH mycursor INTO id_abo, dt_deb, dt_fin;
        END WHILE;

    CLOSE mycursor;
END$$

CALL generate_emprunts();