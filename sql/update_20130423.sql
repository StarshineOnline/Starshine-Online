-- modification des requs des quetes
UPDATE quete SET quete_requis = CONCAT('q', quete_requis) WHERE quete_requis NOT LIKE '';
UPDATE quete SET quete_requis = 'q38;q39;q40;q41' WHERE id = 44;

-- bélier d'entrainement pour le tutoriel
INSERT INTO batiment (nom, description, type, hp, pp, pm, carac, image, temps_construction, temps_construction_min) VALUES ('Bélier d\'entrainement', 'Arme de siège pour s\'entrainer.', 'arme_de_siege', 100000, 10000, 10000, 100, 'belier', 2000000000, 400000000);
INSERT INTO batiment_bonus (id_batiment, bonus, valeur) SELECT id, 'degats_bat', 1 FROM batiment WHERE carac=100;
INSERT INTO batiment_bonus (id_batiment, bonus, valeur) SELECT id, 'degats_siege', 1 FROM batiment WHERE carac=100;
INSERT INTO batiment_bonus (id_batiment, bonus, valeur) SELECT id, 'rechargement', 300 FROM batiment WHERE carac=100;
INSERT INTO batiment_bonus (id_batiment, bonus, valeur) SELECT id, 'portee', 1 FROM batiment WHERE carac=100;
INSERT INTO batiment_bonus (id_batiment, bonus, valeur) SELECT id, 'precision', 0 FROM batiment WHERE carac=100;
INSERT INTO batiment_bonus (id_batiment, bonus, valeur) SELECT id, 'rang_manip', 0 FROM batiment WHERE carac=100;
INSERT INTO batiment_bonus (id_batiment, bonus, valeur) SELECT id, 'lim_montee', 2 FROM batiment WHERE carac=100;

-- monstres visibles uniqueleny si on a une quête
ALTER TABLE monstre ADD quete INT(10) NULL DEFAULT NULL;

-- textures des tutoriels
UPDATE map SET decor = decor - 19767, info = 7 WHERE decor >= 20547 AND decor <= 20566;