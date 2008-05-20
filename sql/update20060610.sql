INSERT INTO `sort_jeu` ( `id` , `nom` , `description` , `pa` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `incantation` , `comp_requis` , `effet` , `duree` , `cible` , `requis` , `prix` )
VALUES (
NULL , 'Retour en ville', 'Vous permet de revenir à votre capitale.', '50', '50', 'teleport', 'sort_element', 'volonte', '0', '180', '90', '1', '', '1', '', '1700'
);
INSERT INTO `sort_combat` ( `id` , `nom` , `description` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `incantation` , `comp_requis` , `effet` , `cible` , `requis` , `prix` , `difficulte` )
VALUES (
NULL , 'Paralysie', 'Paralyse l''ennemi pendant %effet% round.', '3', 'paralysie', 'sort_mort', 'puissance', '0', '150', '75', '2', '4', '', '1100', '400'
), (
NULL , 'Poison', 'Empoisonne la cible pendant 3 rounds', '2', 'poison', 'sort_mort', 'puissance', '0', '120', '60', '1', '4', '', '900', '240'
);
ALTER TABLE `monstre` ADD `action` TEXT NOT NULL AFTER `esquive` ;
UPDATE `monstre` SET `action` = '#09=2@~20' WHERE `id` =6 LIMIT 1 ;
ALTER TABLE `monstre` ADD `incantation` MEDIUMINT UNSIGNED NOT NULL AFTER `esquive` ,
ADD `sort_vie` MEDIUMINT UNSIGNED NOT NULL AFTER `incantation` ,
ADD `sort_mort` MEDIUMINT UNSIGNED NOT NULL AFTER `sort_vie` ,
ADD `sort_element` MEDIUMINT UNSIGNED NOT NULL AFTER `sort_mort` ;
UPDATE `monstre` SET `incantation` = '100',
`sort_vie` = '50',
`sort_mort` = '50',
`sort_element` = '50' WHERE `id` =6 LIMIT 1 ;
UPDATE `monstre` SET `action` = '#10°berzeker@_10' WHERE `id` =4 LIMIT 1 ;
UPDATE `monstre` SET `incantation` = '210',
`sort_vie` = '50',
`sort_mort` = '110',
`sort_element` = '70' WHERE `id` =14 LIMIT 1 ;
UPDATE `monstre` SET `action` = '#09=2@~19' WHERE `id` =14 LIMIT 1 ;
INSERT INTO `monstre` ( `id` , `lib` , `nom` , `type` , `hp` , `pp` , `pm` , `forcex` , `dexterite` , `puissance` , `volonte` , `melee` , `esquive` , `incantation` , `sort_vie` , `sort_mort` , `sort_element` , `action` , `level` , `xp` , `star` , `drop` , `spawn` , `terrain` )
VALUES (
NULL , 'worg', 'Worg', 'bete', '200', '180', '180', '17', '15', '10', '10', '150', '175', '50', '25', '25', '25', '', '4', '1300', '90', '', '350', '4'
);
INSERT INTO `monstre` ( `id` , `lib` , `nom` , `type` , `hp` , `pp` , `pm` , `forcex` , `dexterite` , `puissance` , `volonte` , `melee` , `esquive` , `incantation` , `sort_vie` , `sort_mort` , `sort_element` , `action` , `level` , `xp` , `star` , `drop` , `spawn` , `terrain` )
VALUES (
NULL , 'scarabee_geant', 'Scarabée Géant', 'bete', '40', '100', '100', '10', '15', '10', '10', '50', '75', '50', '50', '50', '50', '', '3', '550', '35', '', '410', '3'
);
INSERT INTO `monstre` ( `id` , `lib` , `nom` , `type` , `hp` , `pp` , `pm` , `forcex` , `dexterite` , `puissance` , `volonte` , `melee` , `esquive` , `incantation` , `sort_vie` , `sort_mort` , `sort_element` , `action` , `level` , `xp` , `star` , `drop` , `spawn` , `terrain` )
VALUES (
NULL , 'elementaire_feu', 'Elémentaire de feu', 'monstre', '200', '350', '500', '19', '19', '16', '16', '200', '200', '250', '125', '125', '125', '~9;~6;~12;', '10', '10000', '1000', '', '100', '3'
);
INSERT INTO `monstre` ( `id` , `lib` , `nom` , `type` , `hp` , `pp` , `pm` , `forcex` , `dexterite` , `puissance` , `volonte` , `melee` , `esquive` , `incantation` , `sort_vie` , `sort_mort` , `sort_element` , `action` , `level` , `xp` , `star` , `drop` , `spawn` , `terrain` )
VALUES (
NULL , 'dryad', 'Dryade', 'monstre', '100', '90', '100', '10', '17', '10', '10', '80', '130', '50', '50', '50', '50', '', '4', '900', '55', '', '370', '2'
);