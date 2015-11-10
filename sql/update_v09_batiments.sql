-- Modification de la table des buffs
ALTER TABLE `buff_batiment` ADD `id` INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;
ALTER TABLE `buff_batiment` CHANGE `date_fin` `fin` INT(11) NOT NULL;
ALTER TABLE `buff_batiment` ADD `effet2` INT(11) NOT NULL DEFAULT '0' , ADD `nom` VARCHAR(50) NOT NULL , ADD `description` TEXT NOT NULL , ADD `debuff` BOOLEAN NOT NULL;
ALTER TABLE `buff_batiment` ADD `id_perso` INT NOT NULL DEFAULT '0';

-- Modification de la compétence de rang 4
UPDATE comp_jeu SET cible = 7 WHERE type LIKE 'sabotage';

-- Définitions des (de)buffs de bâtiments
CREATE TABLE IF NOT EXISTS `buff_batiment_def` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `type` varchar(20) NOT NULL,
  `effet` int(11) NOT NULL,
  `effet2` int(11) NOT NULL DEFAULT '0',
  `duree` int(11) NOT NULL,
  `description` text NOT NULL,
  `debuff` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- nombre de mines en bonus
INSERT INTO batiment_bonus VALUES (10, 'mines', 1);
INSERT INTO batiment_bonus VALUES (11, 'mines', 2);
INSERT INTO batiment_bonus VALUES (12, 'mines', 4);

-- définitions des (de)buffs de bâtiments
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (1, "Assiégé", "assiege", 0, 0, 259200, "Empêche la suppression du bâtiment tant qu''il est assiégé.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (2, "Protection physique", "buff_bouclier", 20, 0, 259200, "Augmente la PP de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (3, "Protection physique améliorée", "buff_bouclier", 40, 0, 259200, "Augmente la PP de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (4, "Protection physique supérieure", "buff_bouclier", 60, 0, 259200, "Augmente la PP de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (5, "Protection physique suprême", "buff_bouclier", 80, 0, 259200, "Augmente la PP de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (6, "Protection magique", "buff_barriere", 10, 0, 259200, "Augmente la PM de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (7, "Protection magique améliorée", "buff_barriere", 20, 0, 259200, "Augmente la PM de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (8, "Protection magique supérieure", "buff_barriere", 30, 0, 259200, "Augmente la PM de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (9, "Protection magique suprême", "buff_barriere", 40, 0, 259200, "Augmente la PM de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (10, "Évitement", "buff_evasion", 10, 0, 259200, "Augmente les chances d\'éviter les effets d\'une attaque physique de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (11, "Évitement amélioré", "buff_evasion", 20, 0, 259200, "Augmente les chances d\'éviter les effets d\'une attaque physique de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (12, "Évitement supérieur", "buff_evasion", 30, 0, 259200, "Augmente les chances d\'éviter les effets d\'une attaque physique de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (13, "Évitement suprême", "buff_evasion", 40, 0, 259200, "Augmente les chances d\'éviter les effets d\'une attaque physique de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (14, "Protection physique détériorée", "debuff_pp", 20, 0, 259200, "Diminue la PP de 1,%effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (15, "Protection physique réduite", "debuff_pp", 40, 0, 259200, "Diminue la PP de 1,%effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (16, "Protection physique inférieure", "debuff_pp", 60, 0, 259200, "Diminue la PP de 1,%effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (17, "Protection physique excécrable", "debuff_pp", 80, 0, 259200, "Diminue la PP de 1,%effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (18, "Protection magique détériorée", "debuff_desespoir", 10, 0, 259200, "Diminue la PM de 1,%effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (19, "Protection magique réduite", "debuff_desespoir", 20, 0, 259200, "Diminue la PM de 1,%effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (20, "Protection magique inférieure", "debuff_desespoir", 30, 0, 259200, "Diminue la PM de 1,%effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (21, "Protection magique excécrable", "debuff_desespoir", 40, 0, 259200, "Diminue la PM de 1,%effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (22, "Évitement détériorée", "debuff_esquive", 10, 0, 259200, "Diminue les chances d\'éviter les effets d\'une attaque physique de 1%effet%,.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (23, "Évitement réduite", "debuff_esquive", 20, 0, 259200, "Diminue les chances d\'éviter les effets d\'une attaque physique de 1,%effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (24, "Évitement inférieure", "debuff_esquive", 30, 0, 259200, "Diminue les chances d\'éviter les effets d\'une attaque physique de 1,%effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (25, "Évitement excécrable", "debuff_esquive", 40, 0, 259200, "Diminue les chances d\'éviter les effets d\'une attaque physique de 1,%effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (26, "Production améliorée", "buff_prod", 15, 0, 7, "Augmente la production de ressources de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (27, "Production augmentée", "buff_prod", 30, 0, 604800, "Augmente la production de ressources de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (28, "Production supérieure", "buff_prod", 45, 0, 604800, "Augmente la production de ressources de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (29, "Production suprême", "buff_prod", 60, 0, 604800, "Augmente la production de ressources de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (30, "Production détériorée", "debuff_prod", 15, 0, 604800, "Diminue la production de ressources de 1,%effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (31, "Production réduite", "debuff_prod", 30, 0, 604800, "Diminue la production de ressources de 1,%effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (32, "Production inférieure", "debuff_prod", 45, 0, 604800, "Diminue la production de ressources de 1,%effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (33, "Production excécrable", "debuff_prod", 60, 0, 604800, "Diminue la production de ressources de 1,%effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (34, "Projectiles améliorés", "buff_degats_bat", 10, 0, 259200, "Augmente les dégâts contre les bâtiments (hors armes de siège) de %effet%.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (35, "Projectiles augmentés", "buff_degats_bat", 20, 0, 259200, "Augmente les dégâts contre les bâtiments (hors armes de siège) de %effet%.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (36, "Projectiles supérieurs", "buff_degats_bat", 30, 0, 259200, "Augmente les dégâts contre les bâtiments (hors armes de siège) de %effet%.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (37, "Projectiles suprêmes", "buff_degats_bat", 40, 0, 259200, "Augmente les dégâts contre les bâtiments (hors armes de siège) de %effet%.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (38, "Projectiles spécialisés améliorés", "buff_degats_siege", 10, 0, 259200, "Diminue les dégâts contre les bâtiments (hors armes de siège) de %effet%.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (39, "Projectiles spécialisés augmentés", "buff_degats_siege", 20, 0, 259200, "Diminue les dégâts contre les bâtiments (hors armes de siège) de %effet%.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (40, "Projectiles spécialisés supérieurs", "buff_degats_siege", 30, 0, 259200, "Diminue les dégâts contre les bâtiments (hors armes de siège) de %effet%.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (41, "Projectiles spécialisés suprêmes", "buff_degats_siege", 40, 0, 259200, "Diminue les dégâts contre les bâtiments (hors armes de siège) de %effet%.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (42, "Projectiles détériorés", "debuff_degats_bat", 10, 0, 259200, "Augmente les dégâts contre les armes de siège de %effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (43, "Projectiles réduits", "debuff_degats_bat", 20, 0, 259200, "Augmente les dégâts contre les armes de siège de %effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (44, "Projectiles inférieurs", "debuff_degats_bat", 30, 0, 259200, "Augmente les dégâts contre les armes de siège de %effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (45, "Projectiles excécrables", "debuff_degats_bat", 40, 0, 259200, "Augmente les dégâts contre les armes de siège de %effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (46, "Projectiles spécialisés détériorés", "debuff_degats_siege", 10, 0, 259200, "Diminue les dégâts contre les armes de siège de %effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (47, "Projectiles spécialisés réduits", "debuff_degats_siege", 20, 0, 259200, "Diminue les dégâts contre les armes de siège de %effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (48, "Projectiles spécialisés inférieurs", "debuff_degats_siege", 30, 0, 259200, "Diminue les dégâts contre les armes de siège de %effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (49, "Projectiles spécialisés excécrables", "debuff_degats_siege", 40, 0, 259200, "Diminue les dégâts contre les armes de siège de %effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (50, "Téléportation améliorée", "buff_cout_tp", 10, 0, 604800, "Réduit le coût (en stars) d\'une téléportation de 1,1.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (51, "Téléportation augmentée", "buff_cout_tp", 20, 0, 604800, "Réduit le coût (en stars) d\'une téléportation de 1,2.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (52, "Téléportation supérieure", "buff_cout_tp", 30, 0, 604800, "Réduit le coût (en stars) d\'une téléportation de 1,3.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (53, "Téléportation suprême", "buff_cout_tp", 40, 0, 604800, "Réduit le coût (en stars) d\'une téléportation de 1,4.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (54, "Téléportation détériorée", "debuff_cout_tp", 10, 0, 604800, "Augmente le coût (en stars) d\'une téléportation de %effet% %.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (55, "Téléportation réduite", "debuff_cout_tp", 20, 0, 604800, "Augmente le coût (en stars) d\'une téléportation de %effet% %.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (56, "Téléportation inférieure", "debuff_cout_tp", 30, 0, 604800, "Augmente le coût (en stars) d\'une téléportation de %effet% %.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (57, "Téléportation excécrable", "debuff_cout_tp", 40, 0, 604800, "Augmente le coût (en stars) d\'une téléportation de %effet% %.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (58, "Longue vue", "buff_vision", 1, 0, 604800, "Augmente la vision d\'une tour de %effet%.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (59, "Longue vue améliorée", "buff_vision", 2, 0, 604800, "Augmente la vision d\'une tour de %effet%.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (60, "Longue vue supérieure", "buff_vision", 3, 0, 604800, "Augmente la vision d\'une tour de %effet%.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (61, "Longue vue suprême", "buff_vision", 4, 0, 604800, "Augmente la vision d\'une tour de %effet%.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (62, "Vision détériorée", "debuff_vision", 2, 0, 604800, "Diminue la vision d\'une tour de %effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (63, "Vision réduite", "debuff_vision", 4, 0, 604800, "Diminue la vision d\'une tour de %effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (64, "Vision inférieure", "debuff_vision", 6, 0, 604800, "Diminue la vision d\'une tour de %effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (65, "Vision excécrable", "debuff_vision", 8, 0, 604800, "Diminue la vision d\'une tour de %effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (66, "Précision améliorée", "buff_cri_bataille", 10, 0, 259200, "Augmente la précision des armes de siège de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (67, "Précision augmentée", "buff_cri_bataille", 20, 0, 259200, "Augmente la précision des armes de siège de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (68, "Précision supérieure", "buff_cri_bataille", 30, 0, 259200, "Augmente la précision des armes de siège de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (69, "Précision suprême", "buff_cri_bataille", 40, 0, 259200, "Augmente la précision des armes de siège de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (70, "Précision détériorée", "debuff_aveuglement", 10, 0, 259200, "Réduit la précision des armes de siège de 1,1.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (71, "Précision réduite", "debuff_aveuglement", 20, 0, 259200, "Réduit la précision des armes de siège de 1,2.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (72, "Précision inférieure", "debuff_aveuglement", 30, 0, 259200, "Réduit la précision des armes de siège de 1,3.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (73, "Précisin excécrable", "debuff_aveuglement", 40, 0, 259200, "Réduit la précision des armes de siège de 1,4.", 1);

-- objets correspondant aux (de)buffs de bâtiments
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Protection physique", 0, 2, "buff", 2, 50, 25, 0, 25, 0, 0, 1, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Protection physique améliorée", 0, 2, "buff", 3, 100, 50, 0, 50, 0, 0, 2, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Protection physique supérieure", 0, 259200, "buff", 4, 200, 100, 0, 100, 0, 0, 3, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Protection physique suprême", 0, 4, "buff", 5, 400, 200, 0, 200, 0, 0, 4, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Protection magique", 0, 2, "buff", 6, 0, 0, 10, 0, 5, 25, 1, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Protection magique améliorée", 0, 2, "buff", 7, 0, 0, 20, 0, 10, 50, 2, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Protection magique supérieure", 0, 259200, "buff", 8, 0, 0, 40, 0, 20, 100, 3, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Protection magique suprême", 0, 4, "buff", 9, 0, 0, 80, 0, 40, 200, 4, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Évitement", 0, 1, "buff", 10, 25, 25, 0, 50, 0, 0, 1, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Évitement amélioré", 0, 2, "buff", 11, 50, 50, 0, 100, 0, 0, 2, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Évitement supérieur", 0, 259200, "buff", 12, 100, 100, 0, 200, 0, 0, 3, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Évitement suprême", 0, 4, "buff", 13, 200, 200, 0, 400, 0, 0, 4, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Protection physique détériorée", 5, 2, "debuff", 14, 0, 50, 0, 10, 25, 0, 1, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Protection physique réduite", 10, 2, "debuff", 15, 0, 100, 0, 20, 50, 0, 2, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Protection physique inférieure", 20, 3, "debuff", 16, 0, 200, 0, 40, 100, 0, 3, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Protection physique excécrable", 40, 4, "debuff", 17, 0, 400, 0, 80, 200, 0, 4, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Protection magique détériorée", 0, 2, "debuff", 18, 5, 5, 0, 5, 10, 10, 1, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Protection magique réduite", 0, 2, "debuff", 19, 10, 10, 0, 10, 20, 20, 2, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Protection magique inférieure", 0, 259200, "debuff", 20, 20, 20, 0, 20, 40, 40, 3, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Protection magique excécrable", 0, 4, "debuff", 21, 40, 40, 0, 40, 80, 80, 4, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Évitement détériorée", 0, 1, "debuff", 22, 10, 25, 25, 0, 25, 10, 1, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Évitement réduite", 0, 2, "debuff", 23, 20, 50, 50, 0, 50, 20, 2, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Évitement inférieure", 0, 259200, "debuff", 24, 40, 100, 100, 0, 100, 40, 3, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Évitement excécrable", 0, 4, "debuff", 25, 80, 200, 200, 0, 200, 80, 4, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Production améliorée", 50, 2, "buff", 26, 0, 10, 25, 0, 0, 0, 1, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Production augmentée", 100, 3, "buff", 27, 0, 20, 50, 0, 0, 0, 2, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Production supérieure", 200, 4, "buff", 28, 0, 40, 100, 0, 0, 0, 3, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Production suprême", 400, 5, "buff", 29, 0, 50, 200, 0, 0, 0, 4, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Production détériorée", 0, 2, "debuff", 30, 50, 0, 0, 25, 10, 0, 1, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Production réduite", 0, 259200, "debuff", 31, 100, 0, 0, 50, 20, 0, 2, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Production inférieure", 0, 4, "debuff", 32, 200, 0, 0, 100, 40, 0, 3, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Production excécrable", 0, 5, "debuff", 33, 400, 0, 0, 200, 50, 0, 4, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Projectiles améliorés", 0, 2, "buff", 34, 10, 0, 0, 0, 5, 3, 1, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Projectiles augmentés", 0, 2, "buff", 35, 20, 0, 0, 0, 10, 5, 2, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Projectiles supérieurs", 0, 259200, "buff", 36, 40, 0, 0, 0, 20, 8, 3, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Projectiles suprêmes", 0, 259200, "buff", 37, 80, 0, 0, 0, 40, 10, 4, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Projectiles spécialisés améliorés", 0, 1, "buff", 38, 10, 3, 0, 0, 10, 0, 1, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Projectiles spécialisés augmentés", 0, 1, "buff", 39, 20, 5, 0, 0, 20, 0, 2, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Projectiles spécialisés supérieurs", 0, 2, "buff", 40, 30, 8, 0, 0, 40, 0, 3, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Projectiles spécialisés suprêmes", 0, 2, "buff", 41, 40, 10, 0, 0, 80, 0, 4, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Projectiles détériorés", 3, 2, "debuff", 42, 0, 0, 5, 10, 0, 0, 1, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Projectiles réduits", 5, 2, "debuff", 43, 0, 0, 10, 20, 0, 0, 2, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Projectiles inférieurs", 8, 3, "debuff", 44, 0, 0, 20, 40, 0, 0, 3, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Projectiles excécrables", 10, 3, "debuff", 45, 0, 0, 40, 80, 0, 0, 4, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Projectiles spécialisés détériorés", 3, 1, "debuff", 46, 0, 0, 10, 5, 0, 0, 1, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Projectiles spécialisés réduits", 5, 1, "debuff", 47, 0, 0, 20, 10, 0, 0, 2, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Projectiles spécialisés inférieurs", 8, 2, "debuff", 48, 0, 0, 40, 20, 0, 0, 3, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Projectiles spécialisés excécrables", 10, 3, "debuff", 49, 0, 0, 80, 40, 0, 0, 4, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Téléportation améliorée", 50, 2, "buff", 50, 10, 10, 5, 5, 5, 100, 1, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Téléportation augmentée", 100, 3, "buff", 51, 20, 20, 10, 10, 10, 200, 2, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Téléportation supérieure", 200, 4, "buff", 52, 40, 40, 20, 20, 20, 400, 3, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Téléportation suprême", 400, 5, "buff", 53, 80, 80, 40, 40, 40, 800, 4, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Téléportation détériorée", 10, 2, "debuff", 54, 5, 50, 10, 5, 100, 50, 1, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Téléportation réduite", 20, 3, "debuff", 55, 10, 100, 20, 10, 200, 100, 2, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Téléportation inférieure", 40, 4, "debuff", 56, 20, 200, 40, 20, 400, 200, 3, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Téléportation excécrable", 80, 5, "debuff", 57, 40, 400, 80, 40, 800, 400, 4, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Longue vue", 50, 2, "buff", 58, 25, 25, 0, 0, 25, 25, 1, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Longue vue améliorée", 100, 3, "buff", 59, 50, 50, 20, 0, 50, 50, 2, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Longue vue supérieure", 200, 4, "buff", 60, 100, 100, 40, 0, 100, 100, 3, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Longue vue suprême", 400, 5, "buff", 61, 200, 200, 80, 0, 200, 200, 4, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Vision détériorée", 50, 2, "debuff", 62, 0, 25, 25, 0, 25, 25, 1, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Vision réduite", 100, 3, "debuff", 63, 0, 50, 50, 20, 50, 50, 2, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Vision inférieure", 200, 4, "debuff", 64, 0, 100, 100, 40, 100, 100, 3, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Vision excécrable", 400, 5, "debuff", 65, 0, 200, 200, 80, 200, 200, 4, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Précision améliorée", 5, 1, "buff", 66, 0, 10, 0, 0, 0, 3, 1, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Précision augmentée", 10, 2, "buff", 67, 0, 20, 0, 0, 0, 5, 2, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Précision supérieure", 20, 3, "buff", 68, 0, 40, 0, 0, 0, 8, 3, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Précision suprême", 40, 4, "buff", 69, 0, 80, 0, 0, 0, 10, 4, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Précision détériorée", 0, 1, "debuff", 70, 3, 0, 10, 5, 0, 0, 1, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Précision réduite", 0, 2, "debuff", 71, 5, 0, 20, 10, 0, 0, 2, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Précision inférieure", 0, 259200, "debuff", 72, 8, 0, 40, 20, 0, 0, 3, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Précisin excécrable", 0, 4, "debuff", 73, 10, 0, 80, 40, 0, 0, 4, 5);



-- Modification de la table bâtiment (pour les quêtes)
ALTER TABLE `batiment` ADD `quete` INT NULL DEFAULT NULL ;

-- Bâtiments pour les quêtes
INSERT INTO batiment (id, nom, description, type, hp, pp, pm, carac, image, point_victoire, quete) VALUES
(53, 'Tour abandonnée', 'Une vielle tour en ruine', 'tour', 500, 100, 125, 10, 'tour_de_guet', 1, 142),
(54, 'Tour abandonnée', 'Une vielle tour en ruine', 'tour', 800, 200, 250, 11, 'tour_de_guet', 1, 143),
(55, 'Tour abandonnée', 'Une vielle tour en ruine', 'tour', 900, 400, 400, 11, 'tour_de_guet', 1, 144),
(56, 'Tour abandonnée', 'Une vielle tour en ruine', 'tour', 1000, 800, 600, 11, 'tour_de_guet', 1, 145),
(57, 'Tour abandonnée', 'Une vielle tour en ruine', 'tour', 1300, 1600, 800, 11, 'tour_de_guet', 1, 146),
(58, 'Tour abandonnée', 'Une vielle tour en ruine', 'tour', 1500, 3500, 1000, 12, 'tour_de_guet', 1, 147),
(59, 'Tour abandonnée', 'Une vielle tour en ruine', 'tour', 1750, 4000, 1200, 12, 'tour_de_guet', 1, 148),
(60, 'Tour abandonnée', 'Une vielle tour en ruine', 'tour', 2000, 8000, 1400, 12, 'tour_de_guet', 1, 149),
(61, 'Tour abandonnée', 'Une vielle tour en ruine', 'tour', 2400, 10000, 1400, 12, 'tour_de_guet', 1, 150),
(62, 'Tour abandonnée', 'Une vielle tour en ruine', 'tour', 3000, 6000, 1600, 12, 'tour_de_guet', 1, 151),
(63, 'Tour abandonnée', 'Une vielle tour en ruine', 'tour', 3400, 6000, 1600, 12, 'tour_de_guet', 1, 152),
(64, 'Tour abandonnée', 'Une vielle tour en ruine', 'tour', 3600, 7000, 1600, 12, 'tour_de_guet', 1, 153),
(65, 'Tour abandonnée', 'Une vielle tour en ruine', 'tour', 3850, 9000, 1600, 12, 'tour_de_guet', 1, 154),
(66, 'Tour abandonnée', 'Une vielle tour en ruine', 'tour', 4000, 10000, 1800, 12, 'tour_de_guet', 1, 155),
(67, 'Tour abandonnée', 'Une vielle tour en ruine', 'tour', 4250, 10000, 1800, 12, 'tour_de_guet', 1, 156),
(68, 'Tour abandonnée', 'Une vielle tour en ruine', 'tour', 4500, 10000, 1800, 13, 'tour_de_guet', 1, 157),
(69, 'Tour abandonnée', 'Une vielle tour en ruine', 'tour', 4750, 10000, 1900, 13, 'tour_de_guet', 1, 158),
(70, 'Tour abandonnée', 'Une vielle tour en ruine', 'tour', 5000, 10000, 1900, 13, 'tour_de_guet', 1, 159),
(71, 'Tour abandonnée', 'Une vielle tour en ruine', 'tour', 5250, 9000, 1800, 13, 'tour_de_guet', 1, 160),
(72, 'Tour abandonnée', 'Une vielle tour en ruine', 'tour', 5500, 8000, 1700, 13, 'tour_de_guet', 1, 161),
(73, 'Poste avancé abandonné', 'Un vieux poste avancé en ruine', 'fort', 400, 50, 100, 20, 'poste_avance', 1, 162),
(74, 'Poste avancé abandonné', 'Un vieux poste avancé en ruine', 'fort', 600, 100, 230, 20, 'poste_avance', 2, 163),
(75, 'Poste avancé abandonné', 'Un vieux poste avancé en ruine', 'fort', 850, 300, 300, 20, 'poste_avance', 2, 164),
(76, 'Poste avancé abandonné', 'Un vieux poste avancé en ruine', 'fort', 1050, 500, 420, 20, 'poste_avance', 2, 165),
(77, 'Poste avancé abandonné', 'Un vieux poste avancé en ruine', 'fort', 1250, 750, 600, 20, 'poste_avance', 2, 166),
(78, 'Fortin indigène', 'Un vieux fortin récupéré par des indigènes', 'fort', 1500, 1000, 800, 21, 'fortin', 2, 167),
(79, 'Fortin indigène', 'Un vieux fortin récupéré par des indigènes', 'fort', 1700, 1200, 1000, 21, 'fortin', 2, 168),
(81, 'Fortin indigène', 'Un vieux fortin récupéré par des indigènes', 'fort', 1900, 1400, 1200, 21, 'fortin', 2, 169),
(82, 'Fortin indigène', 'Un vieux fortin récupéré par des indigènes', 'fort', 2200, 1600, 1300, 21, 'fortin', 2, 170),
(83, 'Fortin indigène', 'Un vieux fortin récupéré par des indigènes', 'fort', 2500, 1800, 1700, 21, 'fort', 3, 171),
(84, 'Fort des bandits', 'Un vieux fort récupéré par des bandits', 'fort', 2700, 2000, 1800, 22, 'fort', 3, 172),
(85, 'Fort des bandits', 'Un vieux fort récupéré par des bandits', 'fort', 3000, 2500, 1900, 22, 'fort', 3, 173),
(86, 'Fort des bandits', 'Un vieux fort récupéré par des bandits', 'fort', 3400, 3500, 1900, 22, 'fort', 3, 174),
(87, 'Fort des bandits', 'Un vieux fort récupéré par des bandits', 'fort', 3800, 4000, 2000, 22, 'fort', 3, 175),
(88, 'Fort des bandits', 'Un vieux fort récupéré par des bandits', 'fort', 4100, 4000, 2000, 22, 'fort', 3, 176),
(89, 'Forteresse ennemie', 'Une vielle forteresse récupérée par un peuple ennemi', 'fort', 4500, 5000, 1900, 23, 'forteresse', 3, 177),
(90, 'Forteresse ennemie', 'Une vielle forteresse récupérée par un peuple ennemi', 'fort', 4800, 5000, 1900, 23, 'forteresse', 3, 178),
(91, 'Forteresse ennemie', 'Une vielle forteresse récupérée par un peuple ennemi', 'fort', 5100, 4500, 1800, 23, 'forteresse', 3, 179),
(92, 'Forteresse ennemie', 'Une vielle forteresse récupérée par un peuple ennemi', 'fort', 5300, 4500, 1800, 23, 'forteresse', 3, 180),
(93, 'Forteresse ennemie', 'Une vielle forteresse récupérée par un peuple ennemi', 'fort', 5500, 4000, 1800, 23, 'forteresse', 3, 181),
(94, 'Village abandonné', 'Un ancien village en ruine', 'bourg', 1400, 2000, 1000, 25, 'bourgade', 4, 182),
(95, 'Village abandonné', 'Un ancien village en ruine', 'bourg', 1500, 2000, 1000, 25, 'bourgade', 4, 183),
(96, 'Village abandonné', 'Un ancien village en ruine', 'bourg', 1600, 2000, 1000, 25, 'bourgade', 4, 184),
(97, 'Village abandonné', 'Un ancien village en ruine', 'bourg', 1700, 2000, 1000, 25, 'bourgade', 4, 185),
(98, 'Village abandonné', 'Un ancien village en ruine', 'bourg', 1800, 2000, 1000, 26, 'bourgade', 4, 186),
(99, 'Bourgade indigène', 'Une bourgade peuplée par des indigènes', 'bourg', 2500, 3000, 1000, 26, 'bourgade', 4, 187),
(100, 'Bourgade indigène', 'Une bourgade peuplée par des indigènes', 'bourg', 3000, 3000, 1000, 26, 'bourgade', 4, 188),
(101, 'Bourgade indigène', 'Une bourgade peuplée par des indigènes', 'bourg', 3500, 3000, 1000, 27, 'bourgade', 4, 189),
(102, 'Bourgade indigène', 'Une bourgade peuplée par des indigènes', 'bourg', 4000, 3000, 1000, 27, 'bourgade', 4, 190),
(103, 'Bourgade indigène', 'Une bourgade peuplée par des indigènes', 'bourg', 4500, 3000, 1000, 27, 'bourgade', 4, 191),
(104, 'Bourg rebèle', 'Un petit bourg habité par des bandits qui ont formé leur propre royaume', 'bourg', 5000, 4000, 1000, 28, 'petit_bourg', 5, 192),
(105, 'Bourg rebèle', 'Un petit bourg habité par des bandits qui ont formé leur propre royaume', 'bourg', 5250, 4000, 1000, 28, 'petit_bourg', 5, 193),
(106, 'Bourg rebèle', 'Un petit bourg habité par des bandits qui ont formé leur propre royaume', 'bourg', 5500, 4000, 1000, 28, 'petit_bourg', 5, 194),
(107, 'Bourg rebèle', 'Un petit bourg habité par des bandits qui ont formé leur propre royaume', 'bourg', 5750, 4000, 1000, 29, 'petit_bourg', 5, 195),
(108, 'Bourg rebèle', 'Un petit bourg habité par des bandits qui ont formé leur propre royaume', 'bourg', 6000, 4000, 1000, 29, 'petit_bourg', 5, 196),
(109, 'Cité ennemie', 'Une cité ennemie', 'bourg', 6200, 5000, 1000, 29, 'bourg', 5, 197),
(110, 'Cité ennemie', 'Une cité ennemie', 'bourg', 6400, 5000, 1000, 30, 'bourg', 5, 198),
(111, 'Cité ennemie', 'Une cité ennemie', 'bourg', 6600, 5000, 1000, 30, 'bourg', 5, 199),
(112, 'Cité ennemie', 'Une cité ennemie', 'bourg', 6800, 5000, 1000, 30, 'bourg', 5, 200),
(113, 'Cité ennemie', 'Une cité ennemie', 'bourg', 7000, 5000, 1000, 30, 'bourg', 5, 201),
(114, 'Mine abandonnée', 'Une vielle mine abandonné', 'mine', 500, 50, 100, 15, 'mine', 1, 202),
(115, 'Mine abandonnée', 'Une vielle mine abandonné', 'mine', 800, 100, 200, 15, 'mine', 1, 203),
(116, 'Mine abandonnée', 'Une vielle mine abandonné', 'mine', 1000, 200, 300, 15, 'mine', 1, 204),
(117, 'Mine abandonnée', 'Une vielle mine abandonné', 'mine', 1400, 250, 350, 15, 'mine', 1, 205),
(118, 'Mine abandonnée', 'Une vielle mine abandonné', 'mine', 1700, 400, 500, 15, 'mine', 1, 206),
(119, 'Mine abandonnée', 'Une vielle mine abandonné', 'mine', 2000, 600, 700, 16, 'mine', 1, 207),
(120, 'Mine abandonnée', 'Une vielle mine abandonné', 'mine', 2200, 800, 850, 16, 'mine', 1, 208),
(121, 'Mine abandonnée', 'Une vielle mine abandonné', 'mine', 2400, 1000, 1000, 16, 'mine', 1, 209),
(122, 'Mine abandonnée', 'Une vielle mine abandonné', 'mine', 2600, 1300, 1250, 16, 'mine', 1, 210),
(123, 'Mine abandonnée', 'Une vielle mine abandonné', 'mine', 2900, 1600, 1700, 16, 'mine', 1, 211),
(124, 'Mine abandonnée', 'Une vielle mine abandonné', 'mine', 3200, 1900, 1800, 17, 'mine', 1, 212),
(125, 'Mine abandonnée', 'Une vielle mine abandonné', 'mine', 3400, 2200, 1900, 17, 'mine', 1, 213),
(126, 'Mine abandonnée', 'Une vielle mine abandonné', 'mine', 3600, 2500, 1900, 17, 'mine', 1, 214),
(127, 'Mine abandonnée', 'Une vielle mine abandonné', 'mine', 3800, 3000, 2000, 17, 'mine', 1, 215),
(128, 'Mine abandonnée', 'Une vielle mine abandonné', 'mine', 40000, 4000, 2100, 17, 'mine', 1, 216),
(129, 'Mine abandonnée', 'Une vielle mine abandonné', 'mine', 4300, 6000, 2100, 18, 'mine', 1, 217),
(130, 'Mine abandonnée', 'Une vielle mine abandonné', 'mine', 4600, 6000, 2100, 18, 'mine', 1, 218),
(131, 'Mine abandonnée', 'Une vielle mine abandonné', 'mine', 4900, 6000, 2000, 18, 'mine', 1, 219),
(132, 'Mine abandonnée', 'Une vielle mine abandonné', 'mine', 5100, 6000, 2000, 18, 'mine', 1, 220),
(133, 'Mine abandonnée', 'Une vielle mine abandonné', 'mine', 5200, 6000, 2000, 18, 'mine', 1, 221),
(134, 'Meule abandonnée', 'Une vielle meule à charbon abandonné', 'mine', 500, 50, 100, 15, 'mine', 1, 222),
(135, 'Meule abandonnée', 'Une vielle meule à charbon abandonné', 'mine', 800, 100, 200, 15, 'mine', 1, 223),
(136, 'Meule abandonnée', 'Une vielle meule à charbon abandonné', 'mine', 1000, 200, 300, 15, 'mine', 1, 224),
(137, 'Meule abandonnée', 'Une vielle meule à charbon abandonné', 'mine', 1400, 250, 350, 15, 'mine', 1, 225),
(138, 'Meule abandonnée', 'Une vielle meule à charbon abandonné', 'mine', 1700, 400, 500, 15, 'mine', 1, 226),
(139, 'Meule abandonnée', 'Une vielle meule à charbon abandonné', 'mine', 2000, 600, 700, 16, 'mine', 1, 227),
(140, 'Meule abandonnée', 'Une vielle meule à charbon abandonné', 'mine', 2200, 800, 850, 16, 'mine', 1, 228),
(141, 'Meule abandonnée', 'Une vielle meule à charbon abandonné', 'mine', 2400, 1000, 1000, 16, 'mine', 1, 229),
(142, 'Meule abandonnée', 'Une vielle meule à charbon abandonné', 'mine', 2600, 1300, 1250, 16, 'mine', 1, 230),
(143, 'Meule abandonnée', 'Une vielle meule à charbon abandonné', 'mine', 2900, 1600, 1700, 16, 'mine', 1, 231),
(144, 'Meule abandonnée', 'Une vielle meule à charbon abandonné', 'mine', 3200, 1900, 1800, 17, 'mine', 1, 232),
(145, 'Meule abandonnée', 'Une vielle meule à charbon abandonné', 'mine', 3400, 2200, 1900, 17, 'mine', 1, 233),
(146, 'Meule abandonnée', 'Une vielle meule à charbon abandonné', 'mine', 3600, 2500, 1900, 17, 'mine', 1, 234),
(147, 'Meule abandonnée', 'Une vielle meule à charbon abandonné', 'mine', 3800, 3000, 2000, 17, 'mine', 1, 235),
(148, 'Meule abandonnée', 'Une vielle meule à charbon abandonné', 'mine', 40000, 4000, 2100, 17, 'mine', 1, 236),
(149, 'Meule abandonnée', 'Une vielle meule à charbon abandonné', 'mine', 4300, 6000, 2100, 18, 'mine', 1, 237),
(150, 'Meule abandonnée', 'Une vielle meule à charbon abandonné', 'mine', 4600, 6000, 2100, 18, 'mine', 1, 238),
(151, 'Meule abandonnée', 'Une vielle meule à charbon abandonné', 'mine', 4900, 6000, 2000, 18, 'mine', 1, 239),
(152, 'Meule abandonnée', 'Une vielle meule à charbon abandonné', 'mine', 5100, 6000, 2000, 18, 'mine', 1, 240),
(153, 'Meule abandonnée', 'Une vielle meule à charbon abandonné', 'mine', 5200, 6000, 2000, 18, 'mine', 1, 241),
(154, 'Puit abandonné', 'Un vieux puits abandonné', 'mine', 500, 50, 100, 15, 'mine', 1, 242),
(155, 'Puit abandonné', 'Un vieux puits abandonné', 'mine', 800, 100, 200, 15, 'mine', 1, 243),
(156, 'Puit abandonné', 'Un vieux puits abandonné', 'mine', 1000, 200, 300, 15, 'mine', 1, 244),
(157, 'Puit abandonné', 'Un vieux puits abandonné', 'mine', 1400, 250, 350, 15, 'mine', 1, 245),
(158, 'Puit abandonné', 'Un vieux puits abandonné', 'mine', 1700, 400, 500, 15, 'mine', 1, 246),
(159, 'Puit abandonné', 'Un vieux puits abandonné', 'mine', 2000, 600, 700, 16, 'mine', 1, 247),
(160, 'Puit abandonné', 'Un vieux puits abandonné', 'mine', 2200, 800, 850, 16, 'mine', 1, 248),
(161, 'Puit abandonné', 'Un vieux puits abandonné', 'mine', 2400, 1000, 1000, 16, 'mine', 1, 249),
(162, 'Puit abandonné', 'Un vieux puits abandonné', 'mine', 2600, 1300, 1250, 16, 'mine', 1, 250),
(163, 'Puit abandonné', 'Un vieux puits abandonné', 'mine', 2900, 1600, 1700, 16, 'mine', 1, 251),
(164, 'Puit abandonné', 'Un vieux puits abandonné', 'mine', 3200, 1900, 1800, 17, 'mine', 1, 252),
(165, 'Puit abandonné', 'Un vieux puits abandonné', 'mine', 3400, 2200, 1900, 17, 'mine', 1, 253),
(166, 'Puit abandonné', 'Un vieux puits abandonné', 'mine', 3600, 2500, 1900, 17, 'mine', 1, 254),
(167, 'Puit abandonné', 'Un vieux puits abandonné', 'mine', 3800, 3000, 2000, 17, 'mine', 1, 255),
(168, 'Puit abandonné', 'Un vieux puits abandonné', 'mine', 40000, 4000, 2100, 17, 'mine', 1, 256),
(169, 'Puit abandonné', 'Un vieux puits abandonné', 'mine', 4300, 6000, 2100, 18, 'mine', 1, 257),
(170, 'Puit abandonné', 'Un vieux puits abandonné', 'mine', 4600, 6000, 2100, 18, 'mine', 1, 258),
(171, 'Puit abandonné', 'Un vieux puits abandonné', 'mine', 4900, 6000, 2000, 18, 'mine', 1, 259),
(172, 'Puit abandonné', 'Un vieux puits abandonné', 'mine', 5100, 6000, 2000, 18, 'mine', 1, 260),
(173, 'Puit abandonné', 'Un vieux puits abandonné', 'mine', 5200, 6000, 2000, 18, 'mine', 1, 261),
(174, 'Ferme abandonnée', 'Une vielle ferme abandonnée', 'mine', 500, 50, 100, 15, 'mine', 1, 262),
(175, 'Ferme abandonnée', 'Une vielle ferme abandonnée', 'mine', 800, 100, 200, 15, 'mine', 1, 263),
(176, 'Ferme abandonnée', 'Une vielle ferme abandonnée', 'mine', 1000, 200, 300, 15, 'mine', 1, 264),
(177, 'Ferme abandonnée', 'Une vielle ferme abandonnée', 'mine', 1400, 250, 350, 15, 'mine', 1, 265),
(178, 'Ferme abandonnée', 'Une vielle ferme abandonnée', 'mine', 1700, 400, 500, 15, 'mine', 1, 266),
(179, 'Ferme abandonnée', 'Une vielle ferme abandonnée', 'mine', 2000, 600, 700, 16, 'mine', 1, 267),
(180, 'Ferme abandonnée', 'Une vielle ferme abandonnée', 'mine', 2200, 800, 850, 16, 'mine', 1, 268),
(181, 'Ferme abandonnée', 'Une vielle ferme abandonnée', 'mine', 2400, 1000, 1000, 16, 'mine', 1, 269),
(182, 'Ferme abandonnée', 'Une vielle ferme abandonnée', 'mine', 2600, 1300, 1250, 16, 'mine', 1, 270),
(183, 'Ferme abandonnée', 'Une vielle ferme abandonnée', 'mine', 2900, 1600, 1700, 16, 'mine', 1, 271),
(184, 'Ferme abandonnée', 'Une vielle ferme abandonnée', 'mine', 3200, 1900, 1800, 17, 'mine', 1, 272),
(185, 'Ferme abandonnée', 'Une vielle ferme abandonnée', 'mine', 3400, 2200, 1900, 17, 'mine', 1, 273),
(186, 'Ferme abandonnée', 'Une vielle ferme abandonnée', 'mine', 3600, 2500, 1900, 17, 'mine', 1, 274),
(187, 'Ferme abandonnée', 'Une vielle ferme abandonnée', 'mine', 3800, 3000, 2000, 17, 'mine', 1, 275),
(189, 'Ferme abandonnée', 'Une vielle ferme abandonnée', 'mine', 40000, 4000, 2100, 17, 'mine', 1, 276),
(190, 'Ferme abandonnée', 'Une vielle ferme abandonnée', 'mine', 4300, 6000, 2100, 18, 'mine', 1, 277),
(191, 'Ferme abandonnée', 'Une vielle ferme abandonnée', 'mine', 4600, 6000, 2100, 18, 'mine', 1, 278),
(192, 'Ferme abandonnée', 'Une vielle ferme abandonnée', 'mine', 4900, 6000, 2000, 18, 'mine', 1, 279),
(193, 'Ferme abandonnée', 'Une vielle ferme abandonnée', 'mine', 5100, 6000, 2000, 18, 'mine', 1, 280),
(194, 'Ferme abandonnée', 'Une vielle ferme abandonnée', 'mine', 5200, 6000, 2000, 18, 'mine', 1, 281),
(195, 'Scierie abandonnée', 'Une vielle scierie abandonnée', 'mine', 500, 50, 100, 15, 'mine', 1, 282),
(196, 'Scierie abandonnée', 'Une vielle scierie abandonnée', 'mine', 800, 100, 200, 15, 'mine', 1, 283),
(197, 'Scierie abandonnée', 'Une vielle scierie abandonnée', 'mine', 1000, 200, 300, 15, 'mine', 1, 284),
(198, 'Scierie abandonnée', 'Une vielle scierie abandonnée', 'mine', 1400, 250, 350, 15, 'mine', 1, 285),
(199, 'Scierie abandonnée', 'Une vielle scierie abandonnée', 'mine', 1700, 400, 500, 15, 'mine', 1, 286),
(200, 'Scierie abandonnée', 'Une vielle scierie abandonnée', 'mine', 2000, 600, 700, 16, 'mine', 1, 287),
(201, 'Scierie abandonnée', 'Une vielle scierie abandonnée', 'mine', 2200, 800, 850, 16, 'mine', 1, 288),
(202, 'Scierie abandonnée', 'Une vielle scierie abandonnée', 'mine', 2400, 1000, 1000, 16, 'mine', 1, 289),
(203, 'Scierie abandonnée', 'Une vielle scierie abandonnée', 'mine', 2600, 1300, 1250, 16, 'mine', 1, 290),
(204, 'Scierie abandonnée', 'Une vielle scierie abandonnée', 'mine', 2900, 1600, 1700, 16, 'mine', 1, 291),
(205, 'Scierie abandonnée', 'Une vielle scierie abandonnée', 'mine', 3200, 1900, 1800, 17, 'mine', 1, 292),
(206, 'Scierie abandonnée', 'Une vielle scierie abandonnée', 'mine', 3400, 2200, 1900, 17, 'mine', 1, 293),
(207, 'Scierie abandonnée', 'Une vielle scierie abandonnée', 'mine', 3600, 2500, 1900, 17, 'mine', 1, 294),
(208, 'Scierie abandonnée', 'Une vielle scierie abandonnée', 'mine', 3800, 3000, 2000, 17, 'mine', 1, 295),
(209, 'Scierie abandonnée', 'Une vielle scierie abandonnée', 'mine', 4000, 4000, 2100, 17, 'mine', 1, 296),
(210, 'Scierie abandonnée', 'Une vielle scierie abandonnée', 'mine', 4300, 6000, 2100, 18, 'mine', 1, 297),
(211, 'Scierie abandonnée', 'Une vielle scierie abandonnée', 'mine', 4600, 6000, 2100, 18, 'mine', 1, 298),
(212, 'Scierie abandonnée', 'Une vielle scierie abandonnée', 'mine', 4900, 6000, 2000, 18, 'mine', 1, 299),
(213, 'Scierie abandonnée', 'Une vielle scierie abandonnée', 'mine', 5100, 6000, 2000, 18, 'mine', 1, 300),
(214, 'Scierie abandonnée', 'Une vielle scierie abandonnée', 'mine', 5200, 6000, 2000, 18, 'mine', 1, 301),
(215, 'Puit à essence abandonné', 'Un vieux puits à essence abandonné', 'mine', 500, 50, 100, 15, 'mine', 1, 302),
(216, 'Puit à essence abandonné', 'Un vieux puits à essence abandonné', 'mine', 800, 100, 200, 15, 'mine', 1, 303),
(217, 'Puit à essence abandonné', 'Un vieux puits à essence abandonné', 'mine', 1000, 200, 300, 15, 'mine', 1, 304),
(218, 'Puit à essence abandonné', 'Un vieux puits à essence abandonné', 'mine', 1400, 250, 350, 15, 'mine', 1, 305),
(219, 'Puit à essence abandonné', 'Un vieux puits à essence abandonné', 'mine', 1700, 400, 500, 15, 'mine', 1, 306),
(220, 'Puit à essence abandonné', 'Un vieux puits à essence abandonné', 'mine', 2000, 600, 700, 16, 'mine', 1, 307),
(221, 'Puit à essence abandonné', 'Un vieux puits à essence abandonné', 'mine', 2200, 800, 850, 16, 'mine', 1, 308),
(222, 'Puit à essence abandonné', 'Un vieux puits à essence abandonné', 'mine', 2400, 1000, 1000, 16, 'mine', 1, 309),
(223, 'Puit à essence abandonné', 'Un vieux puits à essence abandonné', 'mine', 2600, 1300, 1250, 16, 'mine', 1, 310),
(224, 'Puit à essence abandonné', 'Un vieux puits à essence abandonné', 'mine', 2900, 1600, 1700, 16, 'mine', 1, 311),
(225, 'Puit à essence abandonné', 'Un vieux puits à essence abandonné', 'mine', 3200, 1900, 1800, 17, 'mine', 1, 312),
(226, 'Puit à essence abandonné', 'Un vieux puits à essence abandonné', 'mine', 3400, 2200, 1900, 17, 'mine', 1, 313),
(227, 'Puit à essence abandonné', 'Un vieux puits à essence abandonné', 'mine', 3600, 2500, 1900, 17, 'mine', 1, 314),
(228, 'Puit à essence abandonné', 'Un vieux puits à essence abandonné', 'mine', 3800, 3000, 2000, 17, 'mine', 1, 315),
(229, 'Puit à essence abandonné', 'Un vieux puits à essence abandonné', 'mine', 40000, 4000, 2100, 17, 'mine', 1, 316),
(230, 'Puit à essence abandonné', 'Un vieux puits à essence abandonné', 'mine', 4300, 6000, 2100, 18, 'mine', 1, 317),
(231, 'Puit à essence abandonné', 'Un vieux puits à essence abandonné', 'mine', 4600, 6000, 2100, 18, 'mine', 1, 318),
(232, 'Puit à essence abandonné', 'Un vieux puits à essence abandonné', 'mine', 4900, 6000, 2000, 18, 'mine', 1, 319),
(233, 'Puit à essence abandonné', 'Un vieux puits à essence abandonné', 'mine', 5100, 6000, 2000, 18, 'mine', 1, 320),
(234, 'Puit à essence abandonné', 'Un vieux puits à essence abandonné', 'mine', 5200, 6000, 2000, 18, 'mine', 1, 321),
(235, 'Carrière de sable abandonnée', 'Une vielle carrière de sable abandonnée', 'mine', 500, 50, 100, 15, 'mine', 1, 322),
(236, 'Carrière de sable abandonnée', 'Une vielle carrière de sable abandonnée', 'mine', 800, 100, 200, 15, 'mine', 1, 323),
(237, 'Carrière de sable abandonnée', 'Une vielle carrière de sable abandonnée', 'mine', 1000, 200, 300, 15, 'mine', 1, 324),
(238, 'Carrière de sable abandonnée', 'Une vielle carrière de sable abandonnée', 'mine', 1400, 250, 350, 15, 'mine', 1, 325),
(239, 'Carrière de sable abandonnée', 'Une vielle carrière de sable abandonnée', 'mine', 1700, 400, 500, 15, 'mine', 1, 326),
(240, 'Carrière de sable abandonnée', 'Une vielle carrière de sable abandonnée', 'mine', 2000, 600, 700, 16, 'mine', 1, 327),
(241, 'Carrière de sable abandonnée', 'Une vielle carrière de sable abandonnée', 'mine', 2200, 800, 850, 16, 'mine', 1, 328),
(242, 'Carrière de sable abandonnée', 'Une vielle carrière de sable abandonnée', 'mine', 2400, 1000, 1000, 16, 'mine', 1, 329),
(243, 'Carrière de sable abandonnée', 'Une vielle carrière de sable abandonnée', 'mine', 2600, 1300, 1250, 16, 'mine', 1, 330),
(244, 'Carrière de sable abandonnée', 'Une vielle carrière de sable abandonnée', 'mine', 2900, 1600, 1700, 16, 'mine', 1, 331),
(245, 'Carrière de sable abandonnée', 'Une vielle carrière de sable abandonnée', 'mine', 3200, 1900, 1800, 17, 'mine', 1, 332),
(246, 'Carrière de sable abandonnée', 'Une vielle carrière de sable abandonnée', 'mine', 3400, 2200, 1900, 17, 'mine', 1, 333),
(247, 'Carrière de sable abandonnée', 'Une vielle carrière de sable abandonnée', 'mine', 3600, 2500, 1900, 17, 'mine', 1, 334),
(248, 'Carrière de sable abandonnée', 'Une vielle carrière de sable abandonnée', 'mine', 3800, 3000, 2000, 17, 'mine', 1, 335),
(249, 'Carrière de sable abandonnée', 'Une vielle carrière de sable abandonnée', 'mine', 40000, 4000, 2100, 17, 'mine', 1, 336),
(250, 'Carrière de sable abandonnée', 'Une vielle carrière de sable abandonnée', 'mine', 4300, 6000, 2100, 18, 'mine', 1, 337),
(251, 'Carrière de sable abandonnée', 'Une vielle carrière de sable abandonnée', 'mine', 4600, 6000, 2100, 18, 'mine', 1, 338),
(252, 'Carrière de sable abandonnée', 'Une vielle carrière de sable abandonnée', 'mine', 4900, 6000, 2000, 18, 'mine', 1, 339),
(188, 'Carrière de sable abandonnée', 'Une vielle carrière de sable abandonnée', 'mine', 5100, 6000, 2000, 18, 'mine', 1, 340),
(80, 'Carrière de sable abandonnée', 'Une vielle carrière de sable abandonnée', 'mine', 5200, 6000, 2000, 18, 'mine', 1, 341);