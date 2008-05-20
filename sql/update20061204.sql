UPDATE `map` SET ID = ( (
FLOOR( ID /100 ) *1000 ) + ( ID - ( FLOOR( ID /100 ) *100 ) )
) WHERE ID < 105001 ;
 ALTER TABLE `construction_ville` ADD `statut` ENUM( 'actif', 'inactif' ) NOT NULL DEFAULT 'actif',
ADD `dette` INT UNSIGNED NOT NULL ;
INSERT INTO `sort_combat` (`id`, `nom`, `description`, `mp`, `type`, `comp_assoc`, `carac_assoc`, `carac_requis`, `incantation`, `comp_requis`, `effet`, `effet2`, `duree`, `cible`, `requis`, `prix`, `difficulte`, `lvl_batiment`) VALUES 
('', 'Pacte de sang', 'Vous prend %effet2%% de vos points de vie max, et inflige des dégâts à l''adversaire (dégats %effet%).', 0, 'pacte_sang', 'sort_mort', 'puissance', 0, 100, 50, '3', 2, 0, 4, '', 600, 200, 1);
INSERT INTO `sort_combat` ( `id` , `nom` , `description` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `incantation` , `comp_requis` , `effet` , `effet2` , `duree` , `cible` , `requis` , `prix` , `difficulte` , `lvl_batiment` )
VALUES (
NULL , 'Pacte de sang 2', 'Vous prend %effet2%% de vos points de vie max, et inflige des dégâts à l''adversaire (dégats %effet%).', '0', 'pacte_sang', 'sort_mort', 'puissance', '0', '200', '100', '5', '2.5', '', '4', '', '1800', '400', '3'
), (
NULL , 'Pacte de sang 3', 'Vous prend %effet2%% de vos points de vie max, et inflige des dégâts à l''adversaire (dégats %effet%).', '0', 'pacte_sang', 'sort_mort', 'puissance', '0', '300', '150', '7', '3', '', '4', '', '3000', '600', '3'
);
INSERT INTO `sort_combat` ( `id` , `nom` , `description` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `incantation` , `comp_requis` , `effet` , `effet2` , `duree` , `cible` , `requis` , `prix` , `difficulte` , `lvl_batiment` )
VALUES (
NULL , 'Pacte de sang 4', 'Vous prend %effet2%% de vos points de vie max, et inflige des dégâts à l''adversaire (dégats %effet%).', '0', 'pacte_sang', 'sort_mort', 'puissance', '0', '400', '200', '10', '3.5', '', '4', '', '5400', '800', '3'
), (
NULL , 'Pacte de sang 5', 'Vous prend %effet2%% de vos points de vie max, et inflige des dégâts à l''adversaire (dégats %effet%).', '0', 'pacte_sang', 'sort_mort', 'puissance', '0', '520', '260', '13', '4', '', '4', '', '16000', '1040', '3'
);
INSERT INTO `sort_combat` ( `id` , `nom` , `description` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `incantation` , `comp_requis` , `effet` , `effet2` , `duree` , `cible` , `requis` , `prix` , `difficulte` , `lvl_batiment` )
VALUES (
NULL , 'Pacte de sang 6', 'Vous prend %effet2%% de vos points de vie max, et inflige des dégâts à l''adversaire (dégats %effet%).', '0', 'pacte_sang', 'sort_mort', 'puissance', '0', '700', '350', '16', '4.5', '', '4', '', '45000', '1400', '3'
);
INSERT INTO `sort_combat` ( `id` , `nom` , `description` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `incantation` , `comp_requis` , `effet` , `effet2` , `duree` , `cible` , `requis` , `prix` , `difficulte` , `lvl_batiment` )
VALUES (
NULL , 'Trait de feu 5', 'Lance un trait de feu sur la cible (Dégats : %effet%)', '4', 'degat_feu', 'sort_element', 'puissance', '0', '240', '120', '8', '', '', '4', '', '2000', '480', '3'
), (
NULL , 'Trait de feu 6', 'Lance un trait de feu sur la cible (Dégats : %effet%)', '4', 'degat_feu', 'sort_element', 'puissance', '0', '440', '220', '10', '', '', '4', '', '9000', '880', '3'
);
INSERT INTO `sort_combat` ( `id` , `nom` , `description` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `incantation` , `comp_requis` , `effet` , `effet2` , `duree` , `cible` , `requis` , `prix` , `difficulte` , `lvl_batiment` )
VALUES (
NULL , 'Trait de feu 7', 'Lance un trait de feu sur la cible (Dégats : %effet%)', '4', 'degat_feu', 'sort_element', 'puissance', '0', '700', '350', '11', '', '', '4', '', '45000', '1400', '3'
);
INSERT INTO `sort_combat` ( `id` , `nom` , `description` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `incantation` , `comp_requis` , `effet` , `effet2` , `duree` , `cible` , `requis` , `prix` , `difficulte` , `lvl_batiment` )
VALUES (
NULL , 'Boule de feu 5', 'Lance une boule de feu sur la cible (Dégats : %effet%)', '6', 'degat_feu', 'sort_element', 'puissance', '0', '320', '160', '13', '', '', '4', '', '3800', '640', '3'
), (
NULL , 'Boule de feu 6', 'Lance une boule de feu sur la cible (Dégats : %effet%)', '6', 'degat_feu', 'sort_element', 'puissance', '0', '600', '300', '15', '', '', '4', '', '25000', '1200', '3'
);
INSERT INTO `sort_combat` ( `id` , `nom` , `description` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `incantation` , `comp_requis` , `effet` , `effet2` , `duree` , `cible` , `requis` , `prix` , `difficulte` , `lvl_batiment` )
VALUES (
NULL , 'Toucher de feu 5', 'Touche la cible avec une main en feu (Dégats : %effet%)', '2', 'degat_feu', 'sort_element', 'puissance', '0', '600', '300', '6', '', '', '4', '', '25000', '1200', '3'
);
UPDATE `arme` SET `forcex` = '12' WHERE `arme`.`id` =42 LIMIT 1 ;

UPDATE `arme` SET `forcex` = '12',
`melee` = '10' WHERE `arme`.`id` =43 LIMIT 1 ;

UPDATE `arme` SET `forcex` = '12',
`melee` = '20' WHERE `arme`.`id` =44 LIMIT 1 ;

UPDATE `arme` SET `forcex` = '12',
`melee` = '40' WHERE `arme`.`id` =45 LIMIT 1 ;

UPDATE `arme` SET `forcex` = '12',
`melee` = '60' WHERE `arme`.`id` =46 LIMIT 1 ;

UPDATE `arme` SET `forcex` = '12',
`melee` = '100' WHERE `arme`.`id` =47 LIMIT 1 ;

UPDATE `arme` SET `forcex` = '12',
`melee` = '150' WHERE `arme`.`id` =48 LIMIT 1 ;

UPDATE `arme` SET `forcex` = '12',
`melee` = '220' WHERE `arme`.`id` =49 LIMIT 1 ;
