INSERT INTO platforme VALUES (1, 'PC', 0), (2, 'PlayStation', 1), (3, 'Xbox', 1), (4, 'Nintendo', 1), (5, 'Mobilna', 0);

INSERT INTO tip_korisnika VALUES (1, 'obican'), (2, 'administrator');

INSERT INTO korisnici VALUES (1, 'obican', 1, '5555555555554444', 'obican', 'obican@gmail.com'), (2, 'admin', 2, '5105105105105100', 'admin', 'admin@gmail.com');

INSERT INTO zanrovi VALUES (1, 'Open-world'), (2, 'FPS, TPS'), (3, 'Strategy'), (4, 'Multiplayer'), (5, 'Action-adventure'), (6, 'Survival Horror'), (7, 'Platformer'),
(8, '2D'), (9,'3D'), (10, 'RPG'), (11, 'Sports'), (12, 'Indie');


INSERT INTO igre VALUES 
	(1, 'Stray', '2022-07-19', 'BlueTwelve Studio', null),
	(2, 'Horizon Zero Dawn', '2017-02-28', 'Guerrilla Games', null), (3, 'Horizon Forbidden West', '2022-02-18', 'Guerrilla Games', 2),
    (4, 'Horizon Call of the Mountain', '2023-02-22', 'Guerrilla Games', 2), 
    (5, 'Metal Gear', '1987-07-07', 'Konami', null), (6, 'Metal Gear 2 Solid Snake', '1990-07-20', 'Konami', 4), 
    (7, 'Metal Gear Solid', '1998-09-03', 'Konami', 4), (8, 'Metal Gear Solid 2: Sons of Liberty', '2001-11-13', 'Konami', 4),
    (9, 'Rollerdrome', '2022-08-16', 'Roll7', null), (10, 'Hollow Knight', '2017-02-24', 'Team Cherry', null), (11, 'Minecraft', '2011-11-18', 'Mojang Studios', null),
    (12, 'Marvel Midnight Suns', '2022-12-02', 'Firaxis Games', null), (13, 'Hogwarts Legacy', '2023-02-10', 'Avalanche Software', null);

INSERT INTO zanrovi_igre VALUES 
	(5,1), (7,1), (9,1), (12,1),
    (1,2), (5,2), (9,2), (10,2), (1,3), (5,3), (9,3), (10,3), (1,4), (5,4), (9,4), (10,4),
    (5,5), (5,6), (5,7), (5,8),
    (2,9), (7,9), (11,9), (12,9),
    (7,10), (8,10), (12,10), 
    (4,11), (12,11),
    (10,12);

INSERT INTO prethodnice VALUES (3,2), (4,3), (6,5), (7,6), (8,7);

INSERT INTO platforme_igre VALUES 
	(2,1,'2022-07-19'), (1,1,'2022-07-19'), (3,1,'2023-06-01'),
	(2,2,'2017-02-28'), (1,2,'2020-08-07'),
    (5,12, '2022-12-02');
    

DROP TRIGGER IF EXISTS dodana_igra_u_listu_zelja;

DELIMITER //
CREATE TRIGGER dodana_igra_u_listu_zelja 
    BEFORE INSERT ON lista_zelja
    FOR EACH ROW 
BEGIN
	-- DECLARE igra_id INT;
    DECLARE datum_izlaska_igre DATE;
	DECLARE izasla BOOLEAN DEFAULT 0;
    
    -- SELECT id_igre INTO igra_id FROM igre WHERE id_igre = new.id_igre;
    SELECT datum_izlaska INTO datum_izlaska_igre FROM igre WHERE id_igre = new.id_igre;
    SELECT (now() >= datum_izlaska_igre) INTO izasla;
    IF izasla THEN
		SET new.kupljena = 1;
		INSERT INTO katalog_igara SELECT new.id_igre, new.id_korisnici, now();
	ELSE
		SET new.kupljena = 0;
	END IF; 
END //
DELIMITER ;

INSERT INTO lista_zelja VALUES (1,1, now(), null); -- okidač - ispunjen uvjet da je igra izašla
INSERT INTO lista_zelja VALUES (13,1, now(), null); -- okidač - nije ispunjen uvjet da je igra izašla


DROP TRIGGER IF EXISTS obrisana_igra_liste_zelja;

DELIMITER //
CREATE TRIGGER obrisana_igra_liste_zelja 
    BEFORE DELETE ON lista_zelja
    FOR EACH ROW 
BEGIN
    IF old.kupljena = 1 THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'S liste želja možete brisati samo igre koje postoje u njoj i koje nisu kupljene!';
	END IF; 
END //
DELIMITER ;

DELETE FROM lista_zelja WHERE id_igre = 1; -- nece proći kod okidača obrisana_igra_liste_zelja
DELETE FROM lista_zelja WHERE id_igre = 13; -- prolazi kod ovog okidača

SELECT ig2.naslov AS 'Igra', ig1.naslov AS 'Direktni nastavak' FROM igre ig1 JOIN prethodnice p ON ig1.id_igre = p.id_igre JOIN igre ig2 ON p.id_prethodnice = ig2.id_igre;

SELECT z.naziv AS 'Žanr s trenutno najviše igara', COUNT(*) AS 'Broj igara u žanru' FROM zanrovi z JOIN zanrovi_igre zi ON z.id_zanrovi = zi.id_zanrovi
JOIN igre i ON zi.id_igre = i.id_igre GROUP BY z.naziv ORDER BY COUNT(*) DESC LIMIT 1;

SELECT p.naziv AS 'Najpopularnija platforma' FROM platforme p JOIN platforme_igre pi ON p.id_platforme = pi.id_platforme
JOIN igre i ON pi.id_igre = i.id_igre GROUP BY p.naziv ORDER BY COUNT(*) DESC LIMIT 1;


DROP VIEW IF EXISTS Ekskluzive;

CREATE VIEW Ekskluzive AS
SELECT pi.id_igre, COUNT(*) AS 'broj' FROM platforme p JOIN platforme_igre pi ON p.id_platforme = pi.id_platforme
JOIN igre i ON pi.id_igre = i.id_igre GROUP BY pi.id_igre HAVING broj = 1;


-- SELECT * FROM Ekskluzive;
SELECT i.naslov, p.naziv FROM Ekskluzive e 
	JOIN igre i ON i.id_igre = e.id_igre 
    JOIN platforme_igre pi ON pi.id_igre = i.id_igre 
    JOIN platforme p ON pi.id_platforme = p.id_platforme;



    