-- Modification des sorts de combat
UPDATE sort_combat SET effet = 25 WHERE id = 105;
UPDATE sort_combat SET effet = 40 WHERE id = 106;
UPDATE sort_combat SET effet = 50 WHERE id = 107;
UPDATE sort_combat SET incantation = 370, comp_requis = 175, prix = 2738, difficulte = 740 WHERE id = 163;
UPDATE sort_combat SET incantation = 490, comp_requis = 245, prix = 4802, difficulte = 980 WHERE id = 164;
UPDATE sort_combat SET incantation = 10, comp_requis = 5, prix = 2, difficulte = 20 WHERE id = 111;

-- Nouveaux sorts de combat
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, cible, prix, difficulte, lvl_batiment) VALUES ('Feuilles tranchantes', 'Lance une salve de feuilles tranchantes sur la cible (Dégats : +%effet%)', 4, 'degat_nature', sort_vie, puissance, 0, 0, 0, 4, 0, 3, 1);
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, cible, prix, difficulte, lvl_batiment) VALUES ('Feuilles tranchantes 2', 'Lance une salve de feuilles tranchantes sur la cible (Dégats : +%effet%)', 4, 'degat_nature', sort_vie, puissance, 50, 25, 1, 4, 50, 100, 1);
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, cible, prix, difficulte, lvl_batiment) VALUES ('Feuilles tranchantes 3', 'Lance une salve de feuilles tranchantes sur la cible (Dégats : +%effet%)', 4, 'degat_nature', sort_vie, puissance, 100, 50, 2, 4, 200, 200, 2);
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, cible, prix, difficulte, lvl_batiment) VALUES ('Feuilles tranchantes 4', 'Lance une salve de feuilles tranchantes sur la cible (Dégats : +%effet%)', 4, 'degat_nature', sort_vie, puissance, 150, 75, 3, 4, 200, 300, 3);
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, cible, prix, difficulte, lvl_batiment) VALUES ('Feuilles tranchantes 5', 'Lance une salve de feuilles tranchantes sur la cible (Dégats : +%effet%)', 4, 'degat_nature', sort_vie, puissance, 250, 125, 5, 4, 1250, 500, 4);
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, cible, prix, difficulte, lvl_batiment) VALUES ('Feuilles tranchantes 6', 'Lance une salve de feuilles tranchantes sur la cible (Dégats : +%effet%)', 4, 'degat_nature', sort_vie, puissance, 450, 225, 6, 4, 4050, 900, 6);
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, cible, prix, difficulte, lvl_batiment) VALUES ('Feuilles tranchantes 7', 'Lance une salve de feuilles tranchantes sur la cible (Dégats : +%effet%)', 4, 'degat_nature', sort_vie, puissance, 750, 375, 8, 4, 11250, 1500, 6);

-- Modification des sorts hors combat
UPDATE sort_jeu SET effet = 20, effet2 = 20 WHERE id = 72;
UPDATE sort_jeu SET effet = 35, effet2 = 30 WHERE id = 73;
UPDATE sort_jeu SET effet = 50, effet2 = 40 WHERE id = 74;
UPDATE sort_jeu SET effet = 65, effet2 = 50 WHERE id = 88;

-- Modification des compétences de combat
UPDATE comp_combat SET effet3 = 9 WHERE id = 52;
UPDATE comp_combat SET effet3 = 11 WHERE id = 82;
UPDATE comp_combat SET effet3 = 13 WHERE id = 82;
UPDATE comp_combat SET effet2 = 10 WHERE id = 42;
UPDATE comp_combat SET effet2 = 15 WHERE id = 43;
UPDATE comp_combat SET effet2 = 20 WHERE id = 76;
UPDATE comp_combat SET effet2 = 25 WHERE id = 77;
UPDATE comp_combat SET description  = "Dégats normaux, pendant %duree% tours les chances de toucher et de lancer des sorts de l'adversaire sont réduits de %effet%%", effet = 10, effet2 = 0, mp = 3 WHERE id = 59;
UPDATE comp_combat SET description  = "Dégats normaux, pendant %duree% tours les chances de toucher et de lancer des sorts de l'adversaire sont réduits de %effet%%", effet = 15, effet2 = 0, mp = 3 WHERE id = 60;
UPDATE comp_combat SET description  = "Dégats normaux, pendant %duree% tours les chances de toucher et de lancer des sorts de l'adversaire sont réduits de %effet%%", effet = 20, effet2 = 0, mp = 3 WHERE id = 61;
UPDATE comp_combat SET description  = "Dégats normaux, pendant %duree% tours les chances de toucher et de lancer des sorts de l'adversaire sont réduits de %effet%%", effet = 25, effet2 = 0, mp = 3 WHERE id = 88;
UPDATE comp_combat SET description  = "Dégats normaux, pendant %duree% tours les chances de toucher et de lancer des sorts de l'adversaire sont réduits de %effet%%", effet = 30, effet2 = 0, mp = 3, duree = 3 WHERE id = 89;
UPDATE comp_combat SET effet = 15 WHERE id = 20;
UPDATE comp_combat SET effet = 30 WHERE id = 106;
UPDATE comp_combat SET effet = 70 WHERE id = 110;
UPDATE comp_combat SET effet = 1 WHERE id = 37;

-- Modification des compétences hors combat
UPDATE comp_jeu SET effet = 15, mp = 40, comp_requis = 40, prix = 64 WHERE id = 22;
UPDATE comp_jeu SET effet = 30, mp = 45 WHERE id = 23;
UPDATE comp_jeu SET effet = 40, mp = 45 WHERE id = 24;
UPDATE comp_jeu SET effet = 55, mp = 50 WHERE id = 64;
UPDATE comp_jeu SET effet = 65, mp = 50 WHERE id = 65;
UPDATE comp_jeu SET effet = 40, mp = 35, comp_requis = 100, prix = 400 WHERE id = 25;
UPDATE comp_jeu SET effet = 55, mp = 40 WHERE id = 26;
UPDATE comp_jeu SET effet = 70, mp = 45 WHERE id = 27;
UPDATE comp_jeu SET effet = 85, mp = 50 WHERE id = 66;
UPDATE comp_jeu SET effet = 100, mp = 55 WHERE id = 67;
UPDATE comp_jeu SET duree = 43200, mp = 55 WHERE type LIKE 'buff_forteresse';
UPDATE comp_jeu SET duree = 172800 WHERE id = 48;
UPDATE comp_jeu SET effet = 15, duree = 172800 WHERE id = 49;
UPDATE comp_jeu SET effet = 20, duree = 172800 WHERE id = 50;
UPDATE comp_jeu SET effet = 25, duree = 172800 WHERE id = 77;
UPDATE comp_jeu SET effet = 30, duree = 172800 WHERE id = 78;