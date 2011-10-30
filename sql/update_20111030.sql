-- sorts
INSERT INTO `sort_combat` 
(`nom`, `description`, `mp`, `type`, `comp_assoc`, `carac_assoc`, `carac_requis`, `incantation`, `comp_requis`, `effet`, `effet2`, `duree`, `cible`, `requis`, `prix`, `difficulte`, `lvl_batiment`, `etat_lie`) VALUES 
-- héresie divine
('Hérésie divine', 'Empêche la rez pour %effet2% heures', '4', 'heresie_divine', 'sort_mort', 'volonte', '0', '300', '300', '8', '72', '0', '1', '', '0', '200', '9', ''),
-- encombrement psychique
('Encombrement psychique', 'Réduit le nombre de buff maximum à 2 pour %effet2% jours.', '0', 'encombrement_psy', 'sort_mort', 'volonte', '0', '400', '300', '4', '2', '0', '1', '', '0', '200', '9', ''),
-- tsunami
('Tsunami', 'Inflige +%effet% dégâts, et projette la cible en arrière (direction aléatoire).
+%effet2% dégâts si la cible rencontre un mur.', '8', 'tsunami', 'sort_element', 'volonte', '0', '300', '200', '35', '8', '0', '1', '', '0', '150', '9', '');
-- correction energie monstres
update `monstre` set energie = `level` + 2 WHERE energie = 0 and id > 100;

