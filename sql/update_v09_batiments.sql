-- Modification de la table des buffs
ALTER TABLE `buff_batiment` ADD `id` INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;
ALTER TABLE `buff_batiment` CHANGE `date_fin` `fin` INT(11) NOT NULL;
ALTER TABLE `buff_batiment` ADD `effet2` INT(11) NOT NULL DEFAULT '0' , ADD `nom` VARCHAR(50) NOT NULL , ADD `description` TEXT NOT NULL , ADD `debuff` BOOLEAN NOT NULL;
ALTER TABLE `buff_batiment` ADD `id_perso` INT NOT NULL DEFAULT '0';

-- Modification de la compétence de rang 4
UPDATE comp_jeu SET cible = 7 WHERE type LIKE 'sabotage';

-- Modification de la table bâtiment (pour les quêtes)
ALTER TABLE `batiment` ADD `quete` INT NULL DEFAULT NULL ;

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

-- définitions des (de)buffs de bâtiments
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (2, "Protection physique", "buff_bouclier", 20, 0, 3, "Augmente la PP de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (3, "Protection physique améliorée", "buff_bouclier", 40, 0, 3, "Augmente la PP de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (4, "Protection physique supérieure", "buff_bouclier", 60, 0, 3, "Augmente la PP de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (5, "Protection physique suprême", "buff_bouclier", 80, 0, 3, "Augmente la PP de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (6, "Protection magique", "buff_barriere", 10, 0, 3, "Augmente la PM de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (7, "Protection magique améliorée", "buff_barriere", 20, 0, 3, "Augmente la PM de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (8, "Protection magique supérieure", "buff_barriere", 30, 0, 3, "Augmente la PM de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (9, "Protection magique suprême", "buff_barriere", 40, 0, 3, "Augmente la PM de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (10, "Évitement", "buff_evasion", 10, 0, 3, "Augmente les chances d\'éviter les effets d\'une attaque physique de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (11, "Évitement amélioré", "buff_evasion", 20, 0, 3, "Augmente les chances d\'éviter les effets d\'une attaque physique de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (12, "Évitement supérieur", "buff_evasion", 30, 0, 3, "Augmente les chances d\'éviter les effets d\'une attaque physique de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (13, "Évitement suprême", "buff_evasion", 40, 0, 3, "Augmente les chances d\'éviter les effets d\'une attaque physique de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (14, "Protection physique détériorée", "debuff_pp", 20, 0, 3, "Diminue la PP de 1,.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (15, "Protection physique réduite", "debuff_pp", 40, 0, 3, "Diminue la PP de 1,.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (16, "Protection physique inférieure", "debuff_pp", 60, 0, 3, "Diminue la PP de 1,.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (17, "Protection physique excécrable", "debuff_pp", 80, 0, 3, "Diminue la PP de 1,.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (18, "Protection magique détériorée", "debuff_desespoir", 10, 0, 3, "Diminue la PM de 1,.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (19, "Protection magique réduite", "debuff_desespoir", 20, 0, 3, "Diminue la PM de 1,.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (20, "Protection magique inférieure", "debuff_desespoir", 30, 0, 3, "Diminue la PM de 1,.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (21, "Protection magique excécrable", "debuff_desespoir", 40, 0, 3, "Diminue la PM de 1,.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (22, "Évitement détériorée", "debuff_esquive", 10, 0, 3, "Diminue les chances d\'éviter les effets d\'une attaque physique de 1,.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (23, "Évitement réduite", "debuff_esquive", 20, 0, 3, "Diminue les chances d\'éviter les effets d\'une attaque physique de 1,.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (24, "Évitement inférieure", "debuff_esquive", 30, 0, 3, "Diminue les chances d\'éviter les effets d\'une attaque physique de 1,.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (25, "Évitement excécrable", "debuff_esquive", 40, 0, 3, "Diminue les chances d\'éviter les effets d\'une attaque physique de 1,.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (26, "Production améliorée", "buff_prod", 15, 0, 7, "Augmente la production de ressources de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (27, "Production augmentée", "buff_prod", 30, 0, 7, "Augmente la production de ressources de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (28, "Production supérieure", "buff_prod", 45, 0, 7, "Augmente la production de ressources de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (29, "Production suprême", "buff_prod", 60, 0, 7, "Augmente la production de ressources de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (30, "Production détériorée", "debuff_prod", 15, 0, 7, "Diminue la production de ressources de 1,.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (31, "Production réduite", "debuff_prod", 30, 0, 7, "Diminue la production de ressources de 1,.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (32, "Production inférieure", "debuff_prod", 45, 0, 7, "Diminue la production de ressources de 1,.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (33, "Production excécrable", "debuff_prod", 60, 0, 7, "Diminue la production de ressources de 1,.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (34, "Projectiles améliorés", "buff_degats_bat", 10, 0, 3, "Augmente les dégâts contre les bâtiments (hors armes de siège) de %effet%.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (35, "Projectiles augmentés", "buff_degats_bat", 20, 0, 3, "Augmente les dégâts contre les bâtiments (hors armes de siège) de %effet%.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (36, "Projectiles supérieurs", "buff_degats_bat", 30, 0, 3, "Augmente les dégâts contre les bâtiments (hors armes de siège) de %effet%.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (37, "Projectiles suprêmes", "buff_degats_bat", 40, 0, 3, "Augmente les dégâts contre les bâtiments (hors armes de siège) de %effet%.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (38, "Projectiles spécialisés améliorés", "buff_degats_siege", 10, 0, 3, "Diminue les dégâts contre les bâtiments (hors armes de siège) de %effet%.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (39, "Projectiles spécialisés augmentés", "buff_degats_siege", 20, 0, 3, "Diminue les dégâts contre les bâtiments (hors armes de siège) de %effet%.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (40, "Projectiles spécialisés supérieurs", "buff_degats_siege", 30, 0, 3, "Diminue les dégâts contre les bâtiments (hors armes de siège) de %effet%.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (41, "Projectiles spécialisés suprêmes", "buff_degats_siege", 40, 0, 3, "Diminue les dégâts contre les bâtiments (hors armes de siège) de %effet%.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (42, "Projectiles détériorés", "debuff_degats_bat", 10, 0, 3, "Augmente les dégâts contre les armes de siège de %effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (43, "Projectiles réduits", "debuff_degats_bat", 20, 0, 3, "Augmente les dégâts contre les armes de siège de %effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (44, "Projectiles inférieurs", "debuff_degats_bat", 30, 0, 3, "Augmente les dégâts contre les armes de siège de %effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (45, "Projectiles excécrables", "debuff_degats_bat", 40, 0, 3, "Augmente les dégâts contre les armes de siège de %effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (46, "Projectiles spécialisés détériorés", "debuff_degats_siege", 10, 0, 3, "Diminue les dégâts contre les armes de siège de %effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (47, "Projectiles spécialisés réduits", "debuff_degats_siege", 20, 0, 3, "Diminue les dégâts contre les armes de siège de %effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (48, "Projectiles spécialisés inférieurs", "debuff_degats_siege", 30, 0, 3, "Diminue les dégâts contre les armes de siège de %effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (49, "Projectiles spécialisés excécrables", "debuff_degats_siege", 40, 0, 3, "Diminue les dégâts contre les armes de siège de %effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (50, "Téléportation améliorée", "buff_cout_tp", 10, 0, 7, "Réduit le coût (en stars) d\'une téléportation de 1,1.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (51, "Téléportation augmentée", "buff_cout_tp", 20, 0, 7, "Réduit le coût (en stars) d\'une téléportation de 1,2.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (52, "Téléportation supérieure", "buff_cout_tp", 30, 0, 7, "Réduit le coût (en stars) d\'une téléportation de 1,3.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (53, "Téléportation suprême", "buff_cout_tp", 40, 0, 7, "Réduit le coût (en stars) d\'une téléportation de 1,4.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (54, "Téléportation détériorée", "debuff_cout_tp", 10, 0, 7, "Augmente le coût (en stars) d\'une téléportation de %effet% %.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (55, "Téléportation réduite", "debuff_cout_tp", 20, 0, 7, "Augmente le coût (en stars) d\'une téléportation de %effet% %.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (56, "Téléportation inférieure", "debuff_cout_tp", 30, 0, 7, "Augmente le coût (en stars) d\'une téléportation de %effet% %.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (57, "Téléportation excécrable", "debuff_cout_tp", 40, 0, 7, "Augmente le coût (en stars) d\'une téléportation de %effet% %.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (58, "Longue vue", "buff_vision", 1, 0, 7, "Augmente la vision d\'une tour de %effet%.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (59, "Longue vue améliorée", "buff_vision", 2, 0, 7, "Augmente la vision d\'une tour de %effet%.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (60, "Longue vue supérieure", "buff_vision", 3, 0, 7, "Augmente la vision d\'une tour de %effet%.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (61, "Longue vue suprême", "buff_vision", 4, 0, 7, "Augmente la vision d\'une tour de %effet%.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (62, "Vision détériorée", "debuff_vision", 2, 0, 7, "Diminue la vision d\'une tour de %effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (63, "Vision réduite", "debuff_vision", 4, 0, 7, "Diminue la vision d\'une tour de %effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (64, "Vision inférieure", "debuff_vision", 6, 0, 7, "Diminue la vision d\'une tour de %effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (65, "Vision excécrable", "debuff_vision", 8, 0, 7, "Diminue la vision d\'une tour de %effet%.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (66, "Précision améliorée", "buff_cri_bataille", 10, 0, 3, "Augmente la précision des armes de siège de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (67, "Précision augmentée", "buff_cri_bataille", 20, 0, 3, "Augmente la précision des armes de siège de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (68, "Précision supérieure", "buff_cri_bataille", 30, 0, 3, "Augmente la précision des armes de siège de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (69, "Précision suprême", "buff_cri_bataille", 40, 0, 3, "Augmente la précision des armes de siège de %effet% %.", 0);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (70, "Précision détériorée", "debuff_aveuglement", 10, 0, 3, "Réduit la précision des armes de siège de 1,1.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (71, "Précision réduite", "debuff_aveuglement", 20, 0, 3, "Réduit la précision des armes de siège de 1,2.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (72, "Précision inférieure", "debuff_aveuglement", 30, 0, 3, "Réduit la précision des armes de siège de 1,3.", 1);
INSERT INTO buff_batiment_def (id, nom, type, effet, effet2, duree, description, debuff) VALUES (73, "Précisin excécrable", "debuff_aveuglement", 40, 0, 3, "Réduit la précision des armes de siège de 1,4.", 1);

-- objets correspondant aux (de)buffs de bâtiments
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Protection physique", 0, 2, "buff", 2, 50, 25, 0, 25, 0, 0, 1, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Protection physique améliorée", 0, 2, "buff", 3, 100, 50, 0, 50, 0, 0, 2, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Protection physique supérieure", 0, 3, "buff", 4, 200, 100, 0, 100, 0, 0, 3, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Protection physique suprême", 0, 4, "buff", 5, 400, 200, 0, 200, 0, 0, 4, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Protection magique", 0, 2, "buff", 6, 0, 0, 10, 0, 5, 25, 1, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Protection magique améliorée", 0, 2, "buff", 7, 0, 0, 20, 0, 10, 50, 2, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Protection magique supérieure", 0, 3, "buff", 8, 0, 0, 40, 0, 20, 100, 3, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Protection magique suprême", 0, 4, "buff", 9, 0, 0, 80, 0, 40, 200, 4, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Évitement", 0, 1, "buff", 10, 25, 25, 0, 50, 0, 0, 1, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Évitement amélioré", 0, 2, "buff", 11, 50, 50, 0, 100, 0, 0, 2, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Évitement supérieur", 0, 3, "buff", 12, 100, 100, 0, 200, 0, 0, 3, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Évitement suprême", 0, 4, "buff", 13, 200, 200, 0, 400, 0, 0, 4, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Protection physique détériorée", 5, 2, "debuff", 14, 0, 50, 0, 10, 25, 0, 1, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Protection physique réduite", 10, 2, "debuff", 15, 0, 100, 0, 20, 50, 0, 2, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Protection physique inférieure", 20, 3, "debuff", 16, 0, 200, 0, 40, 100, 0, 3, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Protection physique excécrable", 40, 4, "debuff", 17, 0, 400, 0, 80, 200, 0, 4, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Protection magique détériorée", 0, 2, "debuff", 18, 5, 5, 0, 5, 10, 10, 1, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Protection magique réduite", 0, 2, "debuff", 19, 10, 10, 0, 10, 20, 20, 2, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Protection magique inférieure", 0, 3, "debuff", 20, 20, 20, 0, 20, 40, 40, 3, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Protection magique excécrable", 0, 4, "debuff", 21, 40, 40, 0, 40, 80, 80, 4, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Évitement détériorée", 0, 1, "debuff", 22, 10, 25, 25, 0, 25, 10, 1, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Évitement réduite", 0, 2, "debuff", 23, 20, 50, 50, 0, 50, 20, 2, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Évitement inférieure", 0, 3, "debuff", 24, 40, 100, 100, 0, 100, 40, 3, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Évitement excécrable", 0, 4, "debuff", 25, 80, 200, 200, 0, 200, 80, 4, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Production améliorée", 50, 2, "buff", 26, 0, 10, 25, 0, 0, 0, 1, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Production augmentée", 100, 3, "buff", 27, 0, 20, 50, 0, 0, 0, 2, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Production supérieure", 200, 4, "buff", 28, 0, 40, 100, 0, 0, 0, 3, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Production suprême", 400, 5, "buff", 29, 0, 50, 200, 0, 0, 0, 4, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Production détériorée", 0, 2, "debuff", 30, 50, 0, 0, 25, 10, 0, 1, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Production réduite", 0, 3, "debuff", 31, 100, 0, 0, 50, 20, 0, 2, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Production inférieure", 0, 4, "debuff", 32, 200, 0, 0, 100, 40, 0, 3, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Production excécrable", 0, 5, "debuff", 33, 400, 0, 0, 200, 50, 0, 4, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Projectiles améliorés", 0, 2, "buff", 34, 10, 0, 0, 0, 5, 3, 1, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Projectiles augmentés", 0, 2, "buff", 35, 20, 0, 0, 0, 10, 5, 2, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Projectiles supérieurs", 0, 3, "buff", 36, 40, 0, 0, 0, 20, 8, 3, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Projectiles suprêmes", 0, 3, "buff", 37, 80, 0, 0, 0, 40, 10, 4, 5);
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
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Précision inférieure", 0, 3, "debuff", 72, 8, 0, 40, 20, 0, 0, 3, 5);
INSERT INTO objet_royaume (nom, prix, grade, type, id_batiment, pierre, bois, eau, sable, charbon, essence, rang_royaume, encombrement) VALUES ("Précisin excécrable", 0, 4, "debuff", 73, 10, 0, 80, 40, 0, 0, 4, 5);