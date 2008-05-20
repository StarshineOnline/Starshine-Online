INSERT INTO `comp_combat` ( `id` , `nom` , `description` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `comp_requis` , `arme_requis` , `effet` , `cible` , `requis` , `prix` , `lvl_batiment` )
VALUES (
NULL , 'Posture du chat', 'Augmente vos chances d''esquiver les attaques physiques (effet %effet%).', '0', 'posture_esquive', 'melee', 'dexterite', '0', '0', '', '1', '2', '999', '1', '1'
), (
NULL , 'Posture du scorpion', 'Augmente vos chances de faire un coup critique (effet %effet%).', '0', 'posture_critique', 'melee', 'dexterite', '0', '0', '', '1', '2', '999', '1', '1'
);
INSERT INTO `comp_combat` ( `id` , `nom` , `description` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `comp_requis` , `arme_requis` , `effet` , `cible` , `requis` , `prix` , `lvl_batiment` )
VALUES (
NULL , 'Posture du scarabée', 'Réduit les dégats physiques qui vous sont infligés.', '0', 'posture_defense', 'melee', 'force', '0', '0', '', '1', '2', '999', '1', '1'
), (
NULL , 'Posture du loup', 'Augmente les dégats que vous infligé.', '0', 'posture_degat', 'melee', 'force', '0', '0', '', '1', '2', '999', '1', '1'
);
INSERT INTO `classe_comp_permet` ( `id` , `id_classe` , `competence` )
VALUES (
NULL , '3', '19'
), (
NULL , '3', '20'
);
INSERT INTO `classe_comp_permet` ( `id` , `id_classe` , `competence` )
VALUES (
NULL , '8', '19'
), (
NULL , '8', '20'
);
INSERT INTO `classe_comp_permet` ( `id` , `id_classe` , `competence` )
VALUES (
NULL , '4', '21'
), (
NULL , '4', '22'
);
INSERT INTO `comp_combat` ( `id` , `nom` , `description` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `comp_requis` , `arme_requis` , `effet` , `cible` , `requis` , `prix` , `lvl_batiment` )
VALUES (
NULL , 'Posture de l''aigle', 'Vos coups ont une chance d''ignorer l''armure de l''adversaire (effet %effet%).', '0', 'posture_transperce', 'distance', 'dexterite', '0', '200', 'arc', '1', '2', '', '1000', '1'
), (
NULL , 'Posture du serpent', 'Vos coups critiques ont une chance de paralyser l''adversaire (effet %effet%).', '0', 'posture_paralyse', 'melee', 'dexterite', '0', '200', 'dague', '1', '2', '', '1000', '1'
);
INSERT INTO `comp_combat` ( `id` , `nom` , `description` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `comp_requis` , `arme_requis` , `effet` , `cible` , `requis` , `prix` , `lvl_batiment` )
VALUES (
NULL , 'Posture du lion', 'Augmente vos chances de toucher (effet %effet%).', '0', 'posture_touche', 'melee', 'dexterite', '0', '200', '', '1', '2', '', '1000', '1'
);
CREATE TABLE `titre_honorifique` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`id_perso` INT UNSIGNED NOT NULL ,
`titre` VARCHAR( 100 ) NOT NULL
);