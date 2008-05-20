ALTER TABLE `comp_combat` ADD `level` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT '1';

UPDATE `comp_combat` set level=2 WHERE `nom` like '%2';
UPDATE `comp_combat` set level=3 WHERE `nom` like '%3';
UPDATE `comp_combat` set level=4 WHERE `nom` like '%4';
UPDATE `comp_combat` set level=5 WHERE `nom` like '%5';

-- Les postures que je ne sais pas gérer
UPDATE `comp_combat` SET `level` = '2' WHERE `comp_combat`.`id` =107 LIMIT 1 ;
UPDATE `comp_combat` SET `level` = '2' WHERE `comp_combat`.`id` =106 LIMIT 1 ;
UPDATE `comp_combat` SET `level` = '2' WHERE `comp_combat`.`id` =110 LIMIT 1 ;
UPDATE `comp_combat` SET `level` = '2' WHERE `comp_combat`.`id` =109 LIMIT 1 ;
UPDATE `comp_combat` SET `level` = '2' WHERE `comp_combat`.`id` =111 LIMIT 1 ;
UPDATE `comp_combat` SET `level` = '2' WHERE `comp_combat`.`id` =108 LIMIT 1 ;

-- Nouvelles compétences
INSERT INTO `comp_combat` (
`id` ,
`nom` ,
`description` ,
`mp` ,
`type` ,
`comp_assoc` ,
`carac_assoc` ,
`carac_requis` ,
`comp_requis` ,
`arme_requis` ,
`effet` ,
`effet2` ,
`effet3` ,
`duree` ,
`cible` ,
`requis` ,
`prix` , -- Mis grosso modo comme les autres
`lvl_batiment`,
`level`
)
VALUES 
-- Préparation
	(NULL , 'Préparation', 'Augmente vos chances d''esquiver les attaques physiques (effet %effet%).', '0', 'posture_esquive', 'esquive', 'dexterite', '0', '350', 'dague', '30', '0', '', '20', '1', '999', '1600', '4', 2),
	(NULL , 'Préparation 2', 'Augmente vos chances d''esquiver les attaques physiques (effet %effet%).', '0', 'posture_esquive', 'esquive', 'dexterite', '0', '400', 'dague', '40', '0', '', '20', '1', '1', '3600', '5', 3),
	(NULL , 'Préparation 3', 'Augmente vos chances d''esquiver les attaques physiques (effet %effet%).', '0', 'posture_esquive', 'esquive', 'dexterite', '0', '450', 'dague', '50', '0', '', '20', '1', '2', '6400', '6', 4),
-- Précision chirurgicale
	(NULL , 'Précision chirurgicale', 'Augmente vos chances de faire un coup critique (effet %effet%).', '0', 'posture_critique', 'maitrise_critique', 'dexterite', '0', '50', 'dague', '30', '0', '', '20', '1', '999', '3600', '5', 3),
	(NULL , 'Précision chirurgicale 2', 'Augmente vos chances de faire un coup critique (effet %effet%).', '0', 'posture_critique', 'maitrise_critique', 'dexterite', '0', '100', 'dague', '40', '0', '', '20', '1', '1', '6400', '6', 4),
	(NULL , 'Précision chirurgicale 3', 'Augmente vos chances de faire un coup critique (effet %effet%).', '0', 'posture_critique', 'maitrise_critique', 'dexterite', '0', '150', 'dague', '50', '0', '', '20', '1', '2', '10000', '6', 5)

;

UPDATE `comp_combat` SET `description` = 'Augmente vos chances de faire un coup critique (effet %effet%).' WHERE `comp_combat`.`type` = 'posture_critique'  ;
UPDATE `comp_combat` SET `description` = 'Augmente vos chances d''esquiver les attaques physiques (effet %effet%).' WHERE `comp_combat`.`type` = 'posture_esquive'  ;


-- Duelliste --
INSERT INTO `classe` (
`id` ,
`nom` ,
`description` ,
`rang` ,
`type`
)
VALUES (
17 , 'Duelliste', '', '3', 'guerrier'
);
INSERT INTO `classe_requis` (
`id` ,
`id_classe` ,
`competence` ,
`requis`
)
VALUES (
NULL , '17', 'esquive', '350'
), (
NULL , '17', 'melee', '300'
), (
NULL , '17', 'maitrise_dague', '50'
),
(NULL, 17, 'classe', (select id from classe where nom = 'Voleur'));

INSERT INTO `classe_permet` (
`id` ,
`id_classe` ,
`competence` ,
`permet` ,
`new`
)
VALUES (
NULL , '17', 'esquive', '450', 'no'
),(
NULL , '17', 'melee', '400', 'no'
),(
NULL , '17', 'maitrise_critique', '200', 'no'
),(
NULL , '17', 'maitrise_dague', '150', 'no'
),(
NULL , '17', 'art_critique', '100', 'no'
),(
NULL , '17', 'survie_humanoide', '100', 'no'
),(
NULL , '17', 'blocage', '300', 'no'
);
insert into classe_comp_permet (id_classe, competence, type) values (17, (select id from comp_combat where nom = 'Précision chirurgicale'), 'comp_combat'),(17, (select id from comp_combat where nom = 'Préparation'), 'comp_combat');

-- Correction des cibles
update comp_combat set cible=4 where type='feinte';
update comp_combat set cible=4 where type='frappe_derniere_chance';
update comp_combat set cible=4 where type like 'attaque_%';
update comp_combat set cible=4 where type like 'fleche_%';
update comp_combat set cible=4 where type like 'coup_%';

update comp_combat set cible=1 where type='berzeker';
update comp_combat set cible=1 where cible=2;

-- Correction de manteau de l'ombre
UPDATE `comp_combat` SET `level` = '4' WHERE nom='Manteau de l''ombre';
UPDATE `comp_combat` SET `type` = 'dissimulation', `level` = '5' WHERE nom='Manteau de l''ombre 2';
