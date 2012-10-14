-- armes de sièges
ALTER TABLE construction ADD rattrapage INT UNSIGNED NOT NULL AFTER AFTER rechargement;
-- Dégâts des trébuchets
UPDATE  batiment_bonus SET valeur=1000 WHERE id_batiment = 19 AND bonus LIKE 'degats_bat';
-- Modification des extracteurs
UPDATE batiment SET hp=5000, pp=2000, pm=1000 WHERE type LIKE 'mine' AND entretien = 50;
UPDATE batiment SET hp=7500, pp=3000, pm=1500 WHERE type LIKE 'mine' AND entretien = 100;
UPDATE batiment SET hp=9000, pp=4000, pm=2000 WHERE type LIKE 'mine' AND entretien = 200;
-- Modification  des tours
INSERT INTO batiment_bonus VALUES (9, 'distance_arc', 1);
INSERT INTO batiment_bonus VALUES (8, 'distance_baton', 1);
UPDATE batiment SET description='Attaque :<br />+15% toucher a distance, +25% lancement sort, +1 à la distance d\'attaque avec un bâton ou une baguette.' WHERE id = 8;
UPDATE batiment SET description='Attaque :<br />+30% toucher a distance, +15% lancement sort, +1 à la distance d\'attaque avec un arc.' WHERE id = 9;
-- Modification des drapeaux
UPDATE batiment SET pp=1000, pm=300 WHERE id = 21;
UPDATE batiment SET pp=1500, pm=1200 WHERE id = 22;
UPDATE batiment SET pp=2000, pm=1600 WHERE id = 23;
-- Monstres qui passent au bâton
UPDATE monstre SET arme='baton' WHERE id IN ('elementaire_eau', 'elementaire_feu', 'cockatrice', 'djinn', 'basilic', 'demon_cercle_5', 'liches', 'elem_noble');