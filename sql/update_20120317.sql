-- forts
UPDATE batiment SET carac = 35, pp = 2000, hp = 20000 WHERE id = 2;
UPDATE batiment SET carac = 40, pp = 6000, hp = 30000 WHERE id = 3;
UPDATE batiment SET carac = 45, pp = 7500, hp = 40000 WHERE id = 4;
UPDATE batiment SET carac = 45, pm = 300 WHERE id = 1;
INSERT batiment_bonus VALUES (1, 'royaume', NULL);
-- bourgs
UPDATE batiment SET carac = 25, pm = 200, pp = 500, hp = 4000 WHERE id = 10;
UPDATE batiment SET carac = 27, pm = 300, pp = 1000, hp = 10000 WHERE id = 11;
UPDATE batiment SET carac = 30, pm = 400, pp = 1500, hp = 20000 WHERE id = 12;
-- tours
UPDATE batiment SET pm = 150, pp = 500, hp = 1500 WHERE id = 6;
UPDATE batiment SET carac = 15, pm = 300, pp = 1000, hp = 3000 WHERE id = 7;
UPDATE batiment SET carac = 15, pm = 1500, pp = 200, hp = 3000 WHERE id = 8;
-- armes de sièges
UPDATE batiment SET carac = 10, pm = 150, pp = 1000, hp = 2500 WHERE id = 17;
UPDATE batiment SET carac = 15, pm = 500, pp = 1000  WHERE id = 18;
UPDATE batiment SET carac = 20, pm = 700 WHERE id = 19;
UPDATE batiment SET carac = 10, pm = 100, pp = 1000, hp = 2000 WHERE id = 20;
UPDATE batiment_bonus SET valeur = 15000 WHERE id_batiment = 17 AND bonus LIKE 'precision';
UPDATE batiment_bonus SET valeur = 8000 WHERE id_batiment = 18 AND bonus LIKE 'precision';
UPDATE batiment_bonus SET valeur = 5000 WHERE id_batiment = 19 AND bonus LIKE 'precision';
UPDATE batiment_bonus SET valeur = 200 WHERE id_batiment = 17 AND bonus LIKE 'degats_bat';
UPDATE batiment_bonus SET valeur = 400 WHERE id_batiment = 18 AND bonus LIKE 'degats_bat';
UPDATE batiment_bonus SET valeur = 700 WHERE id_batiment = 19 AND bonus LIKE 'degats_bat';

-- Ajustement des HP des batiment construits et en construction
UPDATE construction SET hp = FLOOR(hp * 5 / 3) WHERE id_batiment = 2;
UPDATE placement SET hp = FLOOR(hp * 5 / 3) WHERE id_batiment = 2;
UPDATE construction SET hp = FLOOR(hp * 25 / 9) WHERE id_batiment = 3;
UPDATE placement SET hp = FLOOR(hp * 25 / 9) WHERE id_batiment = 3;
UPDATE construction SET hp = FLOOR(hp * 35 / 12) WHERE id_batiment = 4;
UPDATE placement SET hp = FLOOR(hp * 35 / 12) WHERE id_batiment = 4;
UPDATE construction SET hp = FLOOR(hp / 2 ) WHERE id_batiment = 1;
UPDATE placement SET hp = FLOOR(hp / 2 ) WHERE id_batiment = 1;
UPDATE construction SET hp = FLOOR(hp * 4 ) WHERE id_batiment = 10;
UPDATE placement SET hp = FLOOR(hp * 4 ) WHERE id_batiment = 10;
UPDATE construction SET hp = FLOOR(hp * 5 ) WHERE id_batiment = 11;
UPDATE placement SET hp = FLOOR(hp * 5 ) WHERE id_batiment = 11;
UPDATE construction SET hp = FLOOR(hp * 5 ) WHERE id_batiment = 12;
UPDATE placement SET hp = FLOOR(hp * 5 ) WHERE id_batiment = 12;
UPDATE construction SET hp = FLOOR(hp * 15 / 14 ) WHERE id_batiment = 6;
UPDATE placement SET hp = FLOOR(hp * 15 / 14 ) WHERE id_batiment = 6;
UPDATE construction SET hp = FLOOR(hp * 3 / 2 ) WHERE id_batiment = 7;
UPDATE placement SET hp = FLOOR(hp * 3 / 2 ) WHERE id_batiment = 7;
UPDATE construction SET hp = FLOOR(hp * 5 / 4 ) WHERE id_batiment = 8;
UPDATE placement SET hp = FLOOR(hp * 5 / 4 ) WHERE id_batiment = 8;
UPDATE construction SET hp = FLOOR(hp * 5 / 4 ) WHERE id_batiment = 17;
UPDATE placement SET hp = FLOOR(hp * 5 / 4 ) WHERE id_batiment = 17;
UPDATE construction SET hp = FLOOR(hp * 2 ) WHERE id_batiment = 20;
UPDATE placement SET hp = FLOOR(hp * 2 ) WHERE id_batiment = 20;