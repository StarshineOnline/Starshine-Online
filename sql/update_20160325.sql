-- Modifications des sorts de combat
UPDATE sort_combat SET comp_requis = 20, incantation = 40, difficulte = 60, prix =  WHERE id = 99; -- Aura de feu
UPDATE sort_combat SET effet = 1 WHERE id = 11; -- Toucher de feu
UPDATE sort_combat SET effet = 2 WHERE id = 12; -- Toucher de feu 2
UPDATE sort_combat SET comp_requis = 95, incantation = 190, difficulte = 380, effet = 4, prix =  WHERE id = 13; -- Toucher de feu 3
UPDATE sort_combat SET comp_requis = 135, incantation = 270, difficulte = 540, effet = 6, prix =  WHERE id = 14; -- Toucher de feu 4
UPDATE sort_combat SET comp_requis = 170, incantation = 340, difficulte = 680, effet = 8, prix =  WHERE id = 45;  -- Toucher de feu 5
UPDATE sort_combat SET comp_requis = 35, incantation = 70, difficulte = 140, prix =  WHERE id = 5; -- Trait de feu 3
UPDATE sort_combat SET comp_requis = 80, incantation = 160, difficulte = 320, prix =  WHERE id = 6; -- Trait de feu 4
UPDATE sort_combat SET comp_requis = 115, incantation = 230, difficulte = 460, effet = 9, prix =  WHERE id = 40; -- Trait de feu 5
UPDATE sort_combat SET comp_requis = 150, incantation = 300, difficulte = 600, effet = 12, prix =  WHERE id = 41; -- Trait de feu 6
UPDATE sort_combat SET comp_requis = 190, incantation = 380, difficulte = 760, effet = 14, prix =  WHERE id = 42; -- Trait de feu 7
UPDATE sort_combat SET effet = 10 WHERE id = 7; -- Boule de feu
UPDATE sort_combat SET effet = 11 WHERE id = 8; -- Boule de feu 2
UPDATE sort_combat SET comp_requis = 55, incantation = 110, difficulte = 220, prix =  WHERE id = 9; -- Boule de feu 3
UPDATE sort_combat SET comp_requis = 90, incantation = 180, difficulte = 360, effet = 14, prix =  WHERE id = 10; -- Boule de feu 4
UPDATE sort_combat SET comp_requis = 120, incantation = 240, difficulte = 480, prix =  WHERE id = 43; -- Boule de feu 5
UPDATE sort_combat SET comp_requis = 160, incantation = 320, difficulte = 640, prix =  WHERE id = 44; -- Boule de feu 6
UPDATE sort_combat SET effet = 16 WHERE id = 171; -- Fournaise
UPDATE sort_combat SET comp_requis = 165, incantation = 330, difficulte = 660, effet = 18, prix =  WHERE id = 171; -- Fournaise 2
UPDATE sort_combat SET comp_requis = 190, incantation = 380, difficulte = 760, effet = 21, prix =  WHERE id = 172; -- Fournaise 3
UPDATE sort_combat SET effet = 1 WHERE id = 61; -- Frappe tellurique
UPDATE sort_combat SET effet = 2 WHERE id = 62; -- Frappe tellurique 2
UPDATE sort_combat SET comp_requis = 140, incantation = 280, difficulte = 560, effet = 5, prix =  WHERE id = 63; -- Frappe tellurique 3
UPDATE sort_combat SET effet = 7, duree = 4 WHERE id = 64; -- Frappe tellurique 4
UPDATE sort_combat SET comp_requis = 295, incantation = 590, difficulte = 1180, effet = 9, prix =  WHERE id = 65; -- Frappe tellurique 5 
UPDATE sort_combat SET comp_requis = 45, incantation = 90, difficulte = 180, prix =  WHERE id = 147; -- Embrasement
UPDATE sort_combat SET comp_requis = 85, incantation = 170, difficulte = 340, prix =  WHERE id = 148; -- Embrasement 2
UPDATE sort_combat SET comp_requis = 115, incantation = 230, difficulte = 460, effet = 8, prix =  WHERE id = 149; -- Embrasement 3
UPDATE sort_combat SET comp_requis = 145, incantation = 290, difficulte = 580, effet = 10, prix =  WHERE id = 150; -- Embrasement 4
UPDATE sort_combat SET comp_requis = 180, incantation = 360, difficulte = 720, effet = 12, prix =  WHERE id = 151; -- Embrasement 5
UPDATE sort_combat SET comp_requis = 230, incantation = 460, difficulte = 920, effet = 15, prix =  WHERE id = 152; -- Embrasement 6
UPDATE sort_combat SET comp_requis = 5, incantation = 10, difficulte = 20, effet = 1, prix =  WHERE id = 141; -- Brisement d'os 
UPDATE sort_combat SET comp_requis = 45, incantation = 90, difficulte = 180, effet = 2, prix =  WHERE id = 142; -- Brisement d'os 2
UPDATE sort_combat SET comp_requis = 100, incantation = 200, difficulte = 400, prix =  WHERE id = 143; -- Brisement d'os 3
UPDATE sort_combat SET comp_requis = 190, incantation = 380, difficulte = 760, prix =  WHERE id = 145; -- Brisement d'os 5
UPDATE sort_combat SET comp_requis = 255, incantation = 510, difficulte = 1020, prix =  WHERE id = 146; -- Brisement d'os 6
UPDATE sort_combat SET effet = 5 WHERE id = 118; -- Déstruction mentale
UPDATE sort_combat SET effet = 7 WHERE id = 119; -- Déstruction mentale 2
UPDATE sort_combat SET effet = 9 WHERE id = 120; -- Déstruction mentale 3
UPDATE sort_combat SET comp_requis = 180, incantation = 360, difficulte = 720, prix =  WHERE id = 121; -- Déstruction mentale 4
UPDATE sort_combat SET comp_requis = 260, incantation = 520, difficulte = 1040, prix =  WHERE id = 122; -- Déstruction mentale 5
UPDATE sort_combat SET comp_requis = 10, incantation = 20, difficulte = 40, prix =  WHERE id = 46; -- Trait de mort 1
UPDATE sort_combat SET comp_requis = 55, incantation = 110, difficulte = 220, prix =  WHERE id = 47; -- Trait de mort 2
UPDATE sort_combat SET comp_requis = 195, incantation = 390, difficulte = 780, effet = 9, prix =  WHERE id = 50; -- Trait de mort 5
UPDATE sort_combat SET comp_requis = 135, incantation = 270, difficulte = 540, prix =  WHERE id = 137; -- ex Putréfaction 3
UPDATE sort_combat SET nom = 'Pourrissement', type = 'putrefaction-1' WHERE id = 135; -- ex Putréfaction
UPDATE sort_combat SET nom = 'Pourrissement 2', type = 'putrefaction-1' WHERE id = 136; -- ex Putréfaction 2
UPDATE sort_combat SET nom = 'Pourrissement 3', type = 'putrefaction-1' WHERE id = 137; -- ex Putréfaction 3
UPDATE sort_combat SET nom = 'Pourrissement 4', type = 'putrefaction-1' WHERE id = 138; -- ex Putréfaction 4
UPDATE sort_combat SET nom = 'Pourrissement 5', type = 'putrefaction-1', effet = 9 WHERE id = 139; -- ex Putréfaction 5
UPDATE sort_combat SET nom = 'Pourrissement 6', type = 'putrefaction-1', effet = 11 WHERE id = 140; -- ex Putréfaction 6
UPDATE sort_combat SET effet = 3 WHERE id = 86; -- Poison
UPDATE sort_combat SET comp_requis = 160, incantation = 320, difficulte = 640, prix =  WHERE id = 175; -- Poison 4
UPDATE sort_combat SET comp_requis = 220, incantation = 440, difficulte = 880, prix =  WHERE id = 176; -- Poison 5
UPDATE sort_combat SET effet = 8 WHERE id = 36; -- Pacte de sang 3
UPDATE sort_combat SET comp_requis = 280, incantation = 560, difficulte = 1120, prix =  WHERE id = 38; -- Pacte de sang 5
UPDATE sort_combat SET comp_requis = 365, incantation = 730, difficulte = 1460, effet = 15, prix =  WHERE id = ; -- Pacte de sang 6
UPDATE sort_combat SET comp_requis = 220, incantation = 440, difficulte = 600, effet = 10, prix =  WHERE id = 133; -- Vortex de mana 5
UPDATE sort_combat SET comp_requis = 260, incantation = 520, difficulte = 780, effet = 12, prix =  WHERE id = 134; -- Vortex de mana 6
UPDATE sort_combat SET description = 'Empèche l''ennemi de lancer des sorts ou compétences et réduit son esquive et son blocage, dure %duree% rounds.' WHERE id = 27; -- Silence
UPDATE sort_combat SET description = 'Empèche l''ennemi de lancer des sorts ou compétences et réduit son esquive et son blocage, dure %duree% rounds.' WHERE id = 28; -- Silence 2
UPDATE sort_combat SET description = 'Empèche l''ennemi de lancer des sorts ou compétences et réduit son esquive et son blocage, dure %duree% rounds.' WHERE id = 29; -- Silence 3
UPDATE sort_combat SET comp_requis = 210, incantation = 420, difficulte = 840, prix =  WHERE id = 23; -- Récupération 3
UPDATE sort_combat SET comp_requis = 260, incantation = 520, difficulte = 1040, prix =  WHERE id = 96; -- Récupération 4

-- Modification des compétences de combat
UPDATE comp_combat SET comp_requis = 110, effet = 20, effet2 = 15, prix =  WHERE id = 41; -- Feinte
UPDATE comp_combat SET comp_requis = 190, effet = 25, effet2 = 20, prix =  WHERE id = 42; -- Feinte 2
UPDATE comp_combat SET effet = 30, effet2 = 25 WHERE id = 43; -- Feinte 3
UPDATE comp_combat SET effet = 35, effet2 = 30 WHERE id = 76; -- Feinte 4
UPDATE comp_combat SET comp_requis = 640, effet = 40, effet2 = 35, prix =  WHERE id = 77; -- Feinte 5
UPDATE comp_combat SET comp_requis = 90, prix =  WHERE id = 53; -- Frappe de la dernière chance
UPDATE comp_combat SET comp_requis = 270, prix =  WHERE id = 54; -- Frappe de la dernière chance 2
UPDATE comp_combat SET comp_requis = 560, prix =  WHERE id = 84; -- Frappe de la dernière chance 4
UPDATE comp_combat SET comp_requis = 750, prix =  WHERE id = 85; -- Frappe de la dernière chance 5
UPDATE comp_combat SET comp_requis = 180, prix =  WHERE id = 25; -- Posture du lion 
UPDATE comp_combat SET comp_requis = 560, prix =  WHERE id = 99; -- Berserker 4
UPDATE comp_combat SET comp_requis = 110, prix =  WHERE id = 47; -- Attaque brutale
UPDATE comp_combat SET comp_requis = 190, prix =  WHERE id = 48; -- Attaque brutale 2
UPDATE comp_combat SET comp_requis = 270, prix =  WHERE id = 49; -- Attaque brutale 3
UPDATE comp_combat SET comp_requis = 560, prix =  WHERE id = 80; -- Attaque brutale 4
UPDATE comp_combat SET comp_requis = 770, prix =  WHERE id = 81; -- Attaque brutale 5
UPDATE comp_combat SET comp_requis = 100, prix =  WHERE id = 44; -- Attaque de côté
UPDATE comp_combat SET comp_requis = 180, prix =  WHERE id = 45; -- Attaque de côté 2
UPDATE comp_combat SET comp_requis = 260, prix =  WHERE id = 46; -- Attaque de côté 3
UPDATE comp_combat SET comp_requis = 590, prix =  WHERE id = 79; -- Attaque de côté 5
UPDATE comp_combat SET comp_requis = 60, prix =  WHERE id = 50; -- Attaque rapide
UPDATE comp_combat SET comp_requis = 170, prix =  WHERE id = 51; -- Attaque rapide 2
UPDATE comp_combat SET effet = 40 WHERE id = 52; -- Attaque rapide 3
UPDATE comp_combat SET comp_requis = 480, effet = 60, prix =  WHERE id = 82; -- Attaque rapide 4
UPDATE comp_combat SET comp_requis = 700, effet = 80, prix =  WHERE id = 83; -- Attaque rapide 5
UPDATE comp_combat SET comp_requis = 60, effet = 50, prix =  WHERE id = 124; -- Botte de l'aigle
UPDATE comp_combat SET comp_requis = 175, effet = 60, prix =  WHERE id = 125; -- Botte de l'aigle 2
UPDATE comp_combat SET effet = 70 WHERE id = 126; -- Botte de l'aigle 3
UPDATE comp_combat SET effet = 75 WHERE id = 127; -- Botte de l'aigle 4
UPDATE comp_combat SET effet = 80 WHERE id = 128; -- Botte de l'aigle 5
UPDATE comp_combat SET effet = 50 WHERE id = 119; -- Botte du scorpion
UPDATE comp_combat SET effet = 55 WHERE id = 120; -- Botte du scorpion 2
UPDATE comp_combat SET effet = 60 WHERE id = 121; -- Botte du scorpion 3
UPDATE comp_combat SET effet = 65 WHERE id = 122; -- Botte du scorpion 4
UPDATE comp_combat SET comp_requis = 550, effet = 70, prix =  WHERE id = 123; -- Botte du scorpion 5
UPDATE comp_combat SET comp_requis = 650, prix =  WHERE id = 102; -- Frappe de l'ours 2
UPDATE comp_combat SET comp_requis = 215, effet = 60, prix =  WHERE id = 11; -- Coup sournois
UPDATE comp_combat SET comp_requis = 215, effet = 65, prix =  WHERE id = 12; -- Coup sournois 2
UPDATE comp_combat SET comp_requis = 310, effet = 70, prix =  WHERE id = 13; -- Coup sournois 3
UPDATE comp_combat SET comp_requis = 410, effet = 75, prix =  WHERE id = 14; -- Coup sournois 4
UPDATE comp_combat SET effet = 65, effet2 = 70 WHERE id = 29; -- Coup mortel
UPDATE comp_combat SET comp_requis = 200, effet = 63, effet2 = 75, prix =  WHERE id = 30; -- Coup mortel 2
UPDATE comp_combat SET comp_requis = 350, effet = 61, effet2 = 80, prix =  WHERE id = 31; -- Coup mortel 3
UPDATE comp_combat SET comp_requis = 410, effet = 60, effet2 = 85, prix =  WHERE id = 114; -- Coup fatal
UPDATE comp_combat SET effet = 60 WHERE id = 26; -- Dissimulation
UPDATE comp_combat SET effet = 70 WHERE id = 27; -- Dissimulation 2
UPDATE comp_combat SET effet = 80 WHERE id = 28; -- Dissimulation 3
UPDATE comp_combat SET effet = 85 WHERE id = 112; -- Manteau de l'ombre
UPDATE comp_combat SETeffet = 90 WHERE id = 113; -- Manteau de l'ombre 2
UPDATE comp_combat SET effet = 30 WHERE id = 20; -- Posture du scorpion
UPDATE comp_combat SET effet = 40 WHERE id = 106; -- Posture de l'assassin
UPDATE comp_combat SET comp_requis = 340, prix =  WHERE id = 104; -- Tir visé 3
UPDATE comp_combat SET comp_requis = 360, prix =  WHERE id = 94; -- Flèche sanglante 4
UPDATE comp_combat SET comp_requis = 420, prix =  WHERE id = 92; -- Flèche rapide 4
UPDATE comp_combat SET comp_requis = 510, prix =  WHERE id = 93; -- Flèche rapide 5
UPDATE comp_combat SET comp_requis = 350, prix =  WHERE id = 90; -- Flèche empoisonnée 4
UPDATE comp_combat SET effet = 15 WHERE id = 71; -- Flèche débilitante
UPDATE comp_combat SET effet = 30 WHERE id = 72; -- Flèche débilitante 2
UPDATE comp_combat SET effet = 45 WHERE id = 73; -- Flèche débilitante 3
UPDATE comp_combat SET effet = 60 WHERE id = 96; -- Flèche débilitante 4
UPDATE comp_combat SET effet = 75 WHERE id = 97; -- Flèche débilitante 5
UPDATE comp_combat SET effet = 25 WHERE id = 149; -- Flèche enflammée
UPDATE comp_combat SET effet2 = 20 WHERE id = 59; -- Flèche de sable
UPDATE comp_combat SET effet = 20, effet2 = 20 WHERE id = 60; -- Flèche de sable 2
UPDATE comp_combat SET effet = 30, effet2 = 20 WHERE id = 61; -- Flèche de sable 3
UPDATE comp_combat SET effet = 40, effet2 = 20 WHERE id = 88; -- Flèche de sable 4
UPDATE comp_combat SET effet = 50, effet2 = 20 WHERE id = 89; -- Flèche de sable 5
UPDATE comp_combat SET rm = 2, description = 'Dégâts normaux, a environ 30% de chance de réduire un buff de 1 niveau', effet = 10 WHERE id = 56; -- Flèche magnétique
UPDATE comp_combat SET rm = 2, description = 'Dégâts normaux, a environ 50% de chance de réduire un buff de 1 niveau', effet = 16 WHERE id = 57; -- Flèche magnétique 2
UPDATE comp_combat SET rm = 2, description = 'Dégâts normaux, a environ 50% de chance de réduire un buff de 2 niveau', effet = 16 WHERE id = 58; -- Flèche magnétique 3
UPDATE comp_combat SET rm = 2, description = 'Dégâts normaux, a environ 60% de chance de réduire un buff de 2 niveau', effet = 18 WHERE id = 86; -- Flèche magnétique 4
UPDATE comp_combat SET rm = 2, description = 'Dégâts normaux, a environ 80% de chance de réduire un buff de 2 niveau', effet = 20 WHERE id = 87; -- Flèche magnétique 5


-- Nouveaux sorts de combats
INSERT INTO sort_combat (nom, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, duree, cible, requis, prix, difficulte, lvl_batiment, etat_lie) VALUES ('Aura de feu 4', 1, 'aura_feu', 'sort_element', 'puissance', 350, 175, 40, 20, 1, '101', , 525, , 'vne-posture');
INSERT INTO sort_combat (nom, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, duree, cible, requis, prix, difficulte, lvl_batiment, etat_lie) VALUES ('Aura de feu 5', 1, 'aura_feu', 'sort_element', 'puissance', 550, 275, 50, 20, 1, CONCAT(LAST_INSERT_ID()), , 825, , 'vne-posture');
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, cible, requis, prix, difficulte, lvl_batiment, etat_lie) VALUES ('Brisement d''os 7', 'Vous infligez +%effet% dégats. Si l''énnemi est paralysé alors les dégats sont multipliés par 1.6.', 3, 'brisement_os', 'sort_mort', 'puissance', 660, 330, 10, 4, '146', , 1320, , 'ae-paralysie');
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, cible, requis, prix, difficulte, lvl_batiment) VALUES ('Toucher de feu 6', 'Touche la cible avec une main en feu (dégat +%effet%)', 2, 'degat_feu-1', 'sort_element', 'puissance', 420, 210, 10, 4, '45', , 840, );
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, cible, requis, prix, difficulte, lvl_batiment) VALUES ('Toucher de feu 7', 'Touche la cible avec une main en feu (dégat +%effet%)', 2, 'degat_feu-1', 'sort_element', 'puissance', 530, 265, 12, 4, CONCAT(LAST_INSERT_ID()), , 1060, );
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, cible, requis, prix, difficulte, lvl_batiment) VALUES ('Toucher de feu 8', 'Touche la cible avec une main en feu (dégat +%effet%)', 2, 'degat_feu-1', 'sort_element', 'puissance', 680, 340, 14, 4, CONCAT(LAST_INSERT_ID()), , 1360, );
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, cible, requis, prix, difficulte, lvl_batiment) VALUES ('Trait de feu 8', 'Lance un trait de feu sur la cible (dégat +%effet%)', 4, 'degat_feu-2', 'sort_element', 'puissance', 470, 235, 16, 4, '42', , 940, );
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, cible, requis, prix, difficulte, lvl_batiment) VALUES ('Trait de feu 9', 'Lance un trait de feu sur la cible (dégat +%effet%)', 4, 'degat_feu-2', 'sort_element', 'puissance', 560, 280, 18, 4, CONCAT(LAST_INSERT_ID()), , 1120, );
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, cible, requis, prix, difficulte, lvl_batiment) VALUES ('Trait de feu 10', 'Lance un trait de feu sur la cible (dégat +%effet%)', 4, 'degat_feu-2', 'sort_element', 'puissance', 700, 350, 20, 4, CONCAT(LAST_INSERT_ID()), , 1400, );
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, cible, requis, prix, difficulte, lvl_batiment) VALUES ('Boule de feu 7', 'Lance une boule de feu sur la cible (dégat +%effet%)', 6, 'degat_feu-3', 'sort_element', 'puissance', 520, 260, 23, 4, '44', , 1040, );
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, cible, requis, prix, difficulte, lvl_batiment) VALUES ('Boule de feu 8', 'Lance une boule de feu sur la cible (dégat +%effet%)', 6, 'degat_feu-3', 'sort_element', 'puissance', 640, 320, 26, 4, CONCAT(LAST_INSERT_ID()), , 1280, );
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, cible, requis, prix, difficulte, lvl_batiment) VALUES ('Fournaise 4', 'Vous infligez +%effet% dégats', 8, 'degat_feu-4', 'sort_element', 'puissance', 500, 250, 23, 4, '173', , 1000, );
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, cible, requis, prix, difficulte, lvl_batiment) VALUES ('Fournaise 5', 'Vous infligez +%effet% dégats', 8, 'degat_feu-4', 'sort_element', 'puissance', 570, 285, 26, 4, CONCAT(LAST_INSERT_ID()), , 1140, );
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, cible, requis, prix, difficulte, lvl_batiment) VALUES ('Fournaise 6', 'Vous infligez +%effet% dégats', 8, 'degat_feu-4', 'sort_element', 'puissance', 620, 310, 28, 4, CONCAT(LAST_INSERT_ID()), , 1240, );
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, cible, requis, prix, difficulte, lvl_batiment) VALUES ('Fournaise 7', 'Vous infligez +%effet% dégats', 8, 'degat_feu-4', 'sort_element', 'puissance', 780, 390, 30, 4, CONCAT(LAST_INSERT_ID()), , 1560, );
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, cible, requis, prix, difficulte, lvl_batiment) VALUES ('Trait de mort 6', 'Lance un trait de mort sur la cible (dégats +%effet%)', 4, 'degat_mort-1', 'sort_mort', 'puissance', 480, 240, 11, 4, '50', , 960, );
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, cible, requis, prix, difficulte, lvl_batiment) VALUES ('Trait de mort 7', 'Lance un trait de mort sur la cible (dégats +%effet%)', 4, 'degat_mort-1', 'sort_mort', 'puissance', 580, 290, 13, 4, CONCAT(LAST_INSERT_ID()), , 1160, );
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, cible, requis, prix, difficulte, lvl_batiment) VALUES ('Trait de mort 8', 'Lance un trait de mort sur la cible (dégats +%effet%)', 4, 'degat_mort-1', 'sort_mort', 'puissance', 740, 370, 15, 4, CONCAT(LAST_INSERT_ID()), , 1480, );
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, cible, requis, prix, difficulte, lvl_batiment) VALUES ('Destruction Mentale 6', 'Détruit l''esprit de la cible (Dégats : +%effet%)', 6, 'degat_mort-2', 'sort_mort', 'puissance', 650, 325, 18, 4, '122', , 1300, );
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, cible, requis, prix, difficulte, lvl_batiment) VALUES ('Destruction Mentale 7', 'Détruit l''esprit de la cible (Dégats : +%effet%)', 6, 'degat_mort-2', 'sort_mort', 'puissance', 760, 380, 20, 4, CONCAT(LAST_INSERT_ID()), , 1520, );
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, effet2, duree, cible, requis, prix, difficulte, lvl_batiment, etat_lie) VALUES ('Frappe téllurique 6', 'Inflige des dégats élémentaires à la cible (dégats +%effet%), et donne un bonus (cumulable) de dégats aux prochains sorts élémentaires de %effet2%.', 4, 'degat_terre', 'sort_element', 'puissance', 740, 370, 10, 2, 8, 4, '65', , 1520, , 'vne-tellurique');
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, effet2, duree, cible, requis, prix, difficulte, lvl_batiment, etat_lie) VALUES ('Embrasement 7', 'Vous infligez +%effet% dégats, l''adversaire s''embrase pendant 5 rounds faisant 1 de dégats par round', 6, 'embrasement', 'sort_element', 'puissance', 580, 290, 17, 1, 5, 4, '152', , 1160, , 'ane-embraser');
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, effet2, duree, cible, requis, prix, difficulte, lvl_batiment, etat_lie) VALUES ('Embrasement 8', 'Vous infligez +%effet% dégats, l''adversaire s''embrase pendant 5 rounds faisant 1 de dégats par round', 6, 'embrasement', 'sort_element', 'puissance', 750, 375, 19, 1, 5, 4, CONCAT(LAST_INSERT_ID()), , 1500, , 'ane-embraser');
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, cible, requis, prix, difficulte, lvl_batiment) VALUES ('Pourrissement 7', 'Vous infligez +%effet% dégats. Si l''énnemi est empoisonné alors les dégats dût au poison pour ce tour sont doublés.', 6, 'putrefaction-1', 'sort_mort', 'puissance', 590, 295, 15, 4, '140', , 1180, , 'ae-poison');
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, cible, requis, prix, difficulte, lvl_batiment) VALUES ('Pourrissement 8', 'Vous infligez +%effet% dégats. Si l''énnemi est empoisonné alors les dégats dût au poison pour ce tour sont doublés.', 6, 'putrefaction-1', 'sort_mort', 'puissance', 680, 340, 18, 4, CONCAT(LAST_INSERT_ID()), , 1360, , 'ae-poison');
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, cible, prix, difficulte, lvl_batiment, etat_lie) VALUES ('Putréfaction', 'Vous infligez +%effet% dégats. Si l''énnemi est empoisonné alors les dégats dût au poison pour ce tour sont doublés.', 8, 'putrefaction-2', 'sort_mort', 'puissance', 60, 30, 4, 4, , 120, , 'ae-poison');
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, cible, requis, prix, difficulte, lvl_batiment, etat_lie) VALUES ('Putréfaction 2', 'Vous infligez +%effet% dégats. Si l''énnemi est empoisonné alors les dégats dût au poison pour ce tour sont doublés.', 8, 'putrefaction-2', 'sort_mort', 'puissance', 210, 105, 6, 4, CONCAT(LAST_INSERT_ID()), , 410, , 'ae-poison');
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, cible, requis, prix, difficulte, lvl_batiment, etat_lie) VALUES ('Putréfaction 3', 'Vous infligez +%effet% dégats. Si l''énnemi est empoisonné alors les dégats dût au poison pour ce tour sont doublés.', 8, 'putrefaction-2', 'sort_mort', 'puissance', 310, 155, 9, 4, CONCAT(LAST_INSERT_ID()), , 620, , 'ae-poison');
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, cible, requis, prix, difficulte, lvl_batiment, etat_lie) VALUES ('Putréfaction 4', 'Vous infligez +%effet% dégats. Si l''énnemi est empoisonné alors les dégats dût au poison pour ce tour sont doublés.', 8, 'putrefaction-2', 'sort_mort', 'puissance', 400, 200, 11, 4, CONCAT(LAST_INSERT_ID()), , 800, , 'ae-poison');
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, cible, requis, prix, difficulte, lvl_batiment, etat_lie) VALUES ('Putréfaction 5', 'Vous infligez +%effet% dégats. Si l''énnemi est empoisonné alors les dégats dût au poison pour ce tour sont doublés.', 8, 'putrefaction-2', 'sort_mort', 'puissance', 480, 240, 14, 4, CONCAT(LAST_INSERT_ID()), , 960, , 'ae-poison');
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, cible, requis, prix, difficulte, lvl_batiment, etat_lie) VALUES ('Putréfaction 6', 'Vous infligez +%effet% dégats. Si l''énnemi est empoisonné alors les dégats dût au poison pour ce tour sont doublés.', 8, 'putrefaction-2', 'sort_mort', 'puissance', 640, 320, 17, 4, CONCAT(LAST_INSERT_ID()), , 1280, , 'ae-poison');
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, cible, requis, prix, difficulte, lvl_batiment, etat_lie) VALUES ('Putréfaction 7', 'Vous infligez +%effet% dégats. Si l''énnemi est empoisonné alors les dégats dût au poison pour ce tour sont doublés.', 8, 'putrefaction-2', 'sort_mort', 'puissance', 710, 355, 20, 4, CONCAT(LAST_INSERT_ID()), , 1420, , 'ae-poison');
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, cible, requis, prix, difficulte, lvl_batiment) VALUES ('Vortex de mana 7', 'Vous infligez +%effet% dégats, et gagnez 20% en RM', 7, 'vortex_mana', 'sort_mort', 'puissance', 640, 320, 18, 4, '134', , 2180, );
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, cible, requis, prix, difficulte, lvl_batiment) VALUES ('Vortex de mana 8', 'Vous infligez +%effet% dégats, et gagnez 20% en RM', 7, 'vortex_mana', 'sort_mort', 'puissance', 720, 360, 18, 4, CONCAT(LAST_INSERT_ID()), , 1440, );

-- Nouvelles compétences de combat
INSERT INTO comp_combat (nom, description, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, duree, cible, requis, prix, lvl_batiment) VALUES ('Botte du scolopendre 2', '', 2, 'botte_scolopendre', 'melee', 'force', 160, 'dague;epee', 20, 1, 4, '144', , );
INSERT INTO comp_combat (nom, description, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, duree, cible, requis, prix, lvl_batiment) VALUES ('Botte du scolopendre 3', '', 2, 'botte_scolopendre', 'melee', 'force', 270, 'dague;epee', 30, 1, 4, CONCAT(LAST_INSERT_ID()), , );
INSERT INTO comp_combat (nom, description, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, duree, cible, requis, prix, lvl_batiment) VALUES ('Botte du scolopendre 4', '', 2, 'botte_scolopendre', 'melee', 'force', 410, 'dague;epee', 45, 1, 4, CONCAT(LAST_INSERT_ID()), , );
INSERT INTO comp_combat (nom, description, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, duree, cible, requis, prix, lvl_batiment) VALUES ('Botte du scolopendre 5', '', 2, 'botte_scolopendre', 'melee', 'force', 540, 'dague;epee', 60, 1, 4, CONCAT(LAST_INSERT_ID()), , );
INSERT INTO comp_combat (nom, description, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, duree, cible, requis, prix, lvl_batiment) VALUES ('Botte du scolopendre 6', '', 2, 'botte_scolopendre', 'melee', 'force', 730, 'dague;epee', 80, 1 , 4, CONCAT(LAST_INSERT_ID()), , );
INSERT INTO comp_combat (nom, description, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, duree, cible, requis, prix, lvl_batiment) VALUES ('Botte de la tortue 2', '', 4, 'botte_tortue', 'melee', 'force', 190, 'epee', 20, 1, 4, '145', , );
INSERT INTO comp_combat (nom, description, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, duree, cible, requis, prix, lvl_batiment) VALUES ('Botte de la tortue 3', '', 4, 'botte_tortue', 'melee', 'force', 380, 'epee', 30, 1, 4, CONCAT(LAST_INSERT_ID()), , );
INSERT INTO comp_combat (nom, description, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, duree, cible, requis, prix, lvl_batiment) VALUES ('Botte de la tortue 4', '', 4, 'botte_tortue', 'melee', 'force', 555, 'epee', 40, 1, 4, CONCAT(LAST_INSERT_ID()), , );
INSERT INTO comp_combat (nom, description, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, duree, cible, requis, prix, lvl_batiment) VALUES ('Botte de la tortue 5', '', 4, 'botte_tortue', 'melee', 'force', 650, 'epee', 50, 1, 4, CONCAT(LAST_INSERT_ID()), , );
INSERT INTO comp_combat (nom, description, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, duree, cible, requis, prix, lvl_batiment) VALUES ('Botte de la tortue 6', '', 4, 'botte_tortue', 'melee', 'force', 750, 'epee', 60, 1, 4, CONCAT(LAST_INSERT_ID()), , );
INSERT INTO comp_combat (nom, description, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, duree, cible, requis, prix, lvl_batiment) VALUES ('Botte du tigre 2', '', 2, 'botte_tigre', 'melee', 'force', 240, 'hache', 20, 1, 4, '147', , );
INSERT INTO comp_combat (nom, description, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, duree, cible, requis, prix, lvl_batiment) VALUES ('Botte du tigre 3', '', 2, 'botte_tigre', 'melee', 'force', 420, 'hache', 30, 1, 4, CONCAT(LAST_INSERT_ID()), , );
INSERT INTO comp_combat (nom, description, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, duree, cible, requis, prix, lvl_batiment) VALUES ('Botte du tigre 4', '', 2, 'botte_tigre', 'melee', 'force', 570, 'hache', 40, 1, 4, CONCAT(LAST_INSERT_ID()), , );
INSERT INTO comp_combat (nom, description, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, duree, cible, requis, prix, lvl_batiment) VALUES ('Botte du tigre 5', '', 2, 'botte_tigre', 'melee', 'force', 750, 'hache', 55, 1, 4, CONCAT(LAST_INSERT_ID()), , );
INSERT INTO comp_combat (nom, description, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, duree, cible, requis, prix, lvl_batiment) VALUES ('Botte de l''ours 2', '', 7, 'botte_ours', 'melee', 'force', 110, 'hache', 2, 1, 4, '148', , );
INSERT INTO comp_combat (nom, description, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, duree, cible, requis, prix, lvl_batiment) VALUES ('Botte de l''ours 3', '', 7, 'botte_ours', 'melee', 'force', 190, 'hache', 3, 1, 4, CONCAT(LAST_INSERT_ID()), , );
INSERT INTO comp_combat (nom, description, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, duree, cible, requis, prix, lvl_batiment) VALUES ('Botte de l''ours 4', '', 7, 'botte_ours', 'melee', 'force', 410, 'hache', 4, 1, 4, CONCAT(LAST_INSERT_ID()), , );
INSERT INTO comp_combat (nom, description, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, duree, cible, requis, prix, lvl_batiment) VALUES ('Botte de l''ours 5', '', 7, 'botte_ours', 'melee', 'force', 540, 'hache', 5, 1, 4, CONCAT(LAST_INSERT_ID()), , );
INSERT INTO comp_combat (nom, description, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, duree, cible, requis, prix, lvl_batiment) VALUES ('Botte de l''ours 6', '', 7, 'botte_ours', 'melee', 'force', 700, 'hache', 6, 1, 4, CONCAT(LAST_INSERT_ID()), , );
INSERT INTO comp_combat (nom, description, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, duree, cible, requis, prix, lvl_batiment) VALUES ('Botte de l''ours 7', '', 7, 'botte_ours', 'melee', 'force', 800, 'hache', 7, 1, 4, CONCAT(LAST_INSERT_ID()), , );
INSERT INTO comp_combat (nom, description, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, cible, requis, prix, lvl_batiment) VALUES ('Frappe de l''ours 3', 'Frappe puissante infligeant plus de dégats à l''adversaire (degats + %effet%).', 4, 'coup_violent', 'melee', 'force', 800, 'epee;hache', 5, 4, '102', , );
INSERT INTO comp_combat (nom, description, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, cible, requis, prix, lvl_batiment) VALUES ('Flèche barbelée 2', '', 4, 'fleche_barbelee', 'distance', 'force', 90, 'arc', 10, 4, '150', , );
INSERT INTO comp_combat (nom, description, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, cible, requis, prix, lvl_batiment) VALUES ('Flèche barbelée 3', '', 4, 'fleche_barbelee', 'distance', 'force', 180, 'arc', 20, 4, CONCAT(LAST_INSERT_ID()), , );
INSERT INTO comp_combat (nom, description, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, cible, requis, prix, lvl_batiment) VALUES ('Flèche barbelée 4', '', 4, 'fleche_barbelee', 'distance', 'force', 280, 'arc', 30, 4, CONCAT(LAST_INSERT_ID()), , );
INSERT INTO comp_combat (nom, description, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, cible, requis, prix, lvl_batiment) VALUES ('Flèche barbelée 5', '', 4, 'fleche_barbelee', 'distance', 'force', 370, 'arc', 40, 4, CONCAT(LAST_INSERT_ID()), , );
INSERT INTO comp_combat (nom, description, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, cible, requis, prix, lvl_batiment) VALUES ('Flèche barbelée 6', '', 4, 'fleche_barbelee', 'distance', 'force', 490, 'arc', 50, 4, CONCAT(LAST_INSERT_ID()), , );
INSERT INTO comp_combat (nom, description, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, cible, requis, prix, lvl_batiment) VALUES ('Flèche barbelée 7', '', 4, 'fleche_barbelee', 'distance', 'force', 600, 'arc', 60, 4, CONCAT(LAST_INSERT_ID()), , );
INSERT INTO comp_combat (nom, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, effet2, duree, cible, requis, prix, lvl_batiment) VALUES ('Flèche enflammée 2', 2, 'fleche_enflammee', 'distance', 'force', 120, 'arc', 50, 1, 1, 4, '149', , );
INSERT INTO comp_combat (nom, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, effet2, duree, cible, requis, prix, lvl_batiment) VALUES ('Flèche enflammée 3', 2, 'fleche_enflammee', 'distance', 'force', 180, 'arc', 75, 1, 1, 4, CONCAT(LAST_INSERT_ID()), , );
INSERT INTO comp_combat (nom, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, effet2, duree, cible, requis, prix, lvl_batiment) VALUES ('Flèche enflammée 4', 2, 'fleche_enflammee', 'distance', 'force', 230, 'arc', 50, 2, 1, 4, CONCAT(LAST_INSERT_ID()), , );
INSERT INTO comp_combat (nom, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, effet2, duree, cible, requis, prix, lvl_batiment) VALUES ('Flèche enflammée 5', 2, 'fleche_enflammee', 'distance', 'force', 310, 'arc', 75, 2, 1, 4, CONCAT(LAST_INSERT_ID()), , );
INSERT INTO comp_combat (nom, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, effet2, duree, cible, requis, prix, lvl_batiment) VALUES ('Flèche enflammée 6', 2, 'fleche_enflammee', 'distance', 'force', 390, 'arc', 50, 3, 1, 4, CONCAT(LAST_INSERT_ID()), , );
INSERT INTO comp_combat (nom, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, effet2, duree, cible, requis, prix, lvl_batiment) VALUES ('Flèche enflammée 7', 2, 'fleche_enflammee', 'distance', 'force', 470, 'arc', 50, 4, 1, 4, CONCAT(LAST_INSERT_ID()), , );
INSERT INTO comp_combat (nom, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, effet2, duree, cible, requis, prix, lvl_batiment) VALUES ('Flèche enflammée 8', 2, 'fleche_enflammee', 'distance', 'force', 600, 'arc', 75, 4, 1, 4, CONCAT(LAST_INSERT_ID()), , );
INSERT INTO comp_combat (nom, description, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, cible, requis, prix, lvl_batiment) VALUES ('Posture de la panthère', 'Augmente vos chances d''esquiver les attaques physiques de %effet%%.', 0, 'posture_esquive', 'esquive', 'dexterite', 400, '', 25, 4, '19', , );
INSERT INTO comp_combat (nom, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, cible, requis, prix, lvl_batiment) VALUES ('Botte du rhinocéros 2', , 'botte_rhinoceros', 'melee', 'force', , 'epee;hache', , 4, '146', , );

-- Modification des sorts hors combat
UPDATE sort_jeu SET comp_requis = 230, incantation = 460, difficulte = 920, prix =  WHERE id = 29; -- Force 3
UPDATE sort_jeu SET comp_requis = 140, incantation = 280, difficulte = 560, prix =  WHERE id = 73; -- Furie magique 2 
UPDATE sort_jeu SET comp_requis = 190, incantation = 380, difficulte = 760, prix =  WHERE id = 74; -- Furie magique 3
UPDATE sort_jeu SET comp_requis = 300, incantation = 600, difficulte = 1200, prix =  WHERE id = 88; -- Furie magique 4
UPDATE sort_jeu SET effet = 40 WHERE id = 44; -- Colère 2
UPDATE sort_jeu SET effet = 60 WHERE id = 45; -- Colère 3
UPDATE sort_jeu SET effet = 80 WHERE id = 85; -- Colère 4
UPDATE sort_jeu SET comp_requis = 155, incantation = 310, difficulte = 620, prix =  WHERE id = 19; -- Inspiration 2
UPDATE sort_jeu SET comp_requis = 200, incantation = 440, difficulte = 880, prix =  WHERE id = 20; -- Inspiration 3
UPDATE sort_jeu SET comp_requis = 90, incantation = 180, difficulte = 360, prix =  WHERE id = 47; -- Méditation 2 
UPDATE sort_jeu SET comp_requis = 230, incantation = 460, difficulte = 920, prix =  WHERE id = 48; -- Méditation 3
UPDATE sort_jeu SET comp_requis = 160, incantation = 320, difficulte = 640, prix =  WHERE id = 76; -- Surpuissance 2
UPDATE sort_jeu SET comp_requis = 275, incantation = 550, difficulte = 1100, prix =  WHERE id = 77; -- Surpuissance 3

-- Modification des compétences hors combat
UPDATE sort_jeu SET duree = 43200 WHERE nom LIKE 'Cri %'; -- Cris à 12h
UPDATE sort_jeu SET comp_requis = 220, prix =  WHERE id = 14; -- Cri de bataille 2
UPDATE sort_jeu SET comp_requis = 310, prix =  WHERE id = 15; -- Cri de bataille 3
UPDATE sort_jeu SET comp_requis = 480, prix =  WHERE id = 59; -- Cri de bataille 4
UPDATE sort_jeu SET comp_requis = 600, prix =  WHERE id = 60; -- Cri de bataille 5
UPDATE sort_jeu SET comp_requis = 280, prix =  WHERE id = 19; -- Cri de rage
UPDATE sort_jeu SET comp_requis = 500, prix =  WHERE id = 21; -- Cri de rage 3
UPDATE sort_jeu SET comp_requis = 600, prix =  WHERE id = 63; -- Cri de rage 4
UPDATE sort_jeu SET comp_requis = 260, prix =  WHERE id = 17; -- Cri de victoire 2
UPDATE sort_jeu SET comp_requis = 320, effet = 3, mp = 95, prix =  WHERE id = 18; -- Cri de victoire 3
UPDATE sort_jeu SET comp_requis = 520, prix =  WHERE id = 61; -- Cri de victoire 4
UPDATE sort_jeu SET comp_requis = 700, prix =  WHERE id = 62; -- Cri de victoire 5

-- Nouvelles descriptions
UPDATE sort_combat SET description = 'Inflige des dégats élémentaires à la cible (dégats +%effet%), et donne un bonus (cumulable) de dégats aux prochains sorts élémentaires et attaques physiques de %effet2%.' WHERE type = 'degat_terre';
UPDATE sort_combat SET description = 'Vous enveloppe d''une aura de feu qui vous permet d''augmenter vos chances de lancer un sort et de toucher de %effet%%.' WHERE type = 'aura_feu';
UPDATE comp_combat SET description = '%effet%% de chances de faire %effet2% dégât supplémentaire (doublé si l''ennemi est sous silence).' WHERE type = 'fleche_enflammee';
UPDATE comp_combat SET description = 'Augmente le multiplicateur en cas de critique de %effet%%.' WHERE type = 'fleche_barbelee';
UPDATE comp_combat SET description = 'Après un blocage, augmente les chances de critique de %effet%%.' WHERE type = 'botte_scolopendre';
UPDATE comp_combat SET description = 'Après un blocage, augmente les chances de toucher de %effet%%.' WHERE type = 'botte_tortue';
UPDATE comp_combat SET description = 'Après une attaque réussie, augmente les chances de toucher de %effet%%.' WHERE type = 'botte_tigre';
UPDATE comp_combat SET description = 'Après une attaque réussie, augmente les dégâts de %effet%.' WHERE type = 'botte_ours';
UPDATE comp_combat SET description = 'Après une attaque réussie, augmente les chances de bloquer de %effet%%.' WHERE type = 'botte_rhinoceros';
UPDATE comp_jeu SET description = 'Vos flèches et sorts font + %effet% dégats.' WHERE type = 'fleche_tranchante';
UPDATE comp_jeu SET description = 'Augmente de %effet%% les chances de toucher physiquement quand vous attaquez.' WHERE type = 'buff_cri_bataille';
UPDATE comp_jeu SET description = 'Augmente les dégâts physiques (+%effet% dégâts) quand vous attaquez.' WHERE type = 'buff_cri_victoire';
UPDATE comp_jeu SET description = 'Augmente les chances de critique physique de %effet%% quand vous attaquez.' WHERE type = 'buff_cri_rage';
UPDATE comp_jeu SET description = 'Augmente l''esquive de %effet%% quand vous attaquez.' WHERE type = 'buff_cri_detresse';
UPDATE comp_jeu SET description = 'Augmente la protection physique de %effet%% quand vous attaquez.' WHERE type = 'buff_cri_protecteur';

-- Compétences et sorts de rang 4
UPDATE sort_jeu SET incantation = , comp_requis = , requis = '79', difficulte = , lvl_batiment = , special = 0, prix =  WHERE id = 152; -- Retour en ville de groupe
UPDATE sort_jeu SET nom = 'Puissance divine', description = 'Multiplie par deux la durée des sorts de buff.', type = 'duree_buff', effet = 2 WHERE id = 153; -- ex Faveur divine
UPDATE sort_jeu SET description = 'Réduit le coût des sorts de debuffs de %effet% PA et %effet2% MP.' WHERE id = 155; -- Contagion
UPDATE sort_jeu SET nom = 'Sape', description = 'Désactive les bonus et effets passifs du bâtiment.', type = 'sape', mp = 80, pa = 10, effet = 1, duree = 172800, cible = 1 WHERE id = 94; -- ex Charisme
UPDATE comp_jeu SET comp_requis = , requis = '', lvl_batiment = , prix =  WHERE id = 95; -- Pour la gloire
UPDATE comp_jeu SET description = 'Empêche la réparation et l''accélération du bâtiment', mp = 50, pa = 10 WHERE id = 96; -- Sabotage
INSERT INTO comp_jeu (nom, description, mp, pa, type, comp_assoc, carac_assoc, effet, duree, cible, requis, lvl_batiment) VALUES ('Assaut', 'Empêche d''accéder au bâtiment et à ses services.', 30, 5, 'assaut', 'melee', 'force', 1, 86400, 1, 'classe:titan', 9);
INSERT INTO sort_jeu (nom, description, mp, pa, type, comp_assoc, carac_assoc, effet, duree, cible, requis, difficulte, lvl_batiment, special) VALUES ('Charisme', 'Augmente de %effet% le nombre de buffs disponibles. Dure une semaine.', 20, pa, 'buff_charisme', 'sort_element', 'puissance', 1 ,604800, 3, 'classe:sniper', 1, 9, 1);

-- Pré-requis des dagues
UPDATE arme SET melee = 260, coefficient = 2600 WHERE id = 8; -- Brise Lame
UPDATE arme SET melee = 410, coefficient = 4100 WHERE id = 9; -- Dague d'assassin
UPDATE arme SET melee = 650, coefficient = 6500 WHERE id = 74; -- Lame d assassin
UPDATE arme SET melee = 320, coefficient = 3200 WHERE id = 52; -- Dague [Main Gauche]
UPDATE arme SET melee = 606, coefficient = 6060 WHERE id = 53; -- Lame [Main Gauche]

-- Sorts de groupe
UPDATE classe_permet SET permet = 2 WHERE competence IN ('sort_groupe', 'sort_groupe_sort_element', 'sort_groupe_sort_mort');
UPDATE comp_perso SET valeur = 2 WHERE competence IN ('sort_groupe', 'sort_groupe_sort_element', 'sort_groupe_sort_mort');
INSERT INTO classe_permet (id_classe, competence, permet, new, categorie) VALUES (26, 'sort_groupe_sort_element', 1, 'yes', NULL);
INSERT INTO classe_permet (id_classe, competence, permet, new, categorie) VALUES (27, 'sort_groupe_sort_mort', 1, 'yes', NULL);
INSERT INTO classe_permet (id_classe, competence, permet, new, categorie) VALUES (25, 'sort_groupe_sort_vie', 1, 'yes', NULL);
INSERT INTO classe_permet (id_classe, competence, permet, new, categorie) VALUES (32, 'sort_groupe_sort_element', 1, 'yes', NULL);
INSERT INTO classe_permet (id_classe, competence, permet, new, categorie) VALUES (33, 'sort_groupe_sort_mort', 1, 'yes', NULL);
INSERT INTO classe_permet (id_classe, competence, permet, new, categorie) VALUES (16, 'sort_groupe_sort_vie', 1, 'yes', NULL);

-- Oublis sur les aptitudes
INSERT INTO classe_permet (id_classe, competence, permet, new, categorie) VALUES (17, 'maitrise_critique', 300, 'no', 1);

-- Buff pour les chances de debuffs des dresseurs de l'ombre et archer noirs
INSERT INTO sort_jeu (nom, description, mp, pa, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, duree, cible, requis, difficulte, lvl_batiment) VALUES ('', 'Augmente de %effet% les chances de debuff.', 20, 4, 'buff_', 'sort_mort', 'puissance', 100, 50, 10 ,86400, 1, '', 200, 9);