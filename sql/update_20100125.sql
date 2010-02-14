CREATE TABLE `pet` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
`id_joueur` INT UNSIGNED NOT NULL ,
`id_monstre` INT UNSIGNED NOT NULL ,
`nom` VARCHAR( 100 ) NOT NULL ,
`hp` MEDIUMINT UNSIGNED NOT NULL ,
`mp` MEDIUMINT UNSIGNED NOT NULL ,
`principale` TINYINT UNSIGNED NOT NULL ,
PRIMARY KEY ( `id` )
);
INSERT INTO `classe` (
`id` ,
`nom` ,
`description` ,
`rang` ,
`type`
)
VALUES (
NULL , 'Rodeur', '', '2', 'guerrier'
), (
NULL , 'Druide oblaire', '', '2', 'mage'
),
 (
NULL , 'Invocateur', '', '2', 'mage'
), (
NULL , 'Dresseur de lombre', '', '2', 'mage'
);
INSERT INTO `classe_permet` (
`id` ,
`id_classe` ,
`competence` ,
`permet` ,
`new`
)
VALUES (
NULL , '24', 'distance', '200', 'no'
), (
NULL , '24', 'esquive', '150', 'no'
), (
NULL , '24', 'survie_bete', '100', 'no'
);

ALTER TABLE `perso` ADD `dressage` MEDIUMINT UNSIGNED NOT NULL AFTER `survie` ;
ALTER TABLE `perso` ADD `max_pet` TINYINT UNSIGNED NOT NULL AFTER `cache_niveau`;
ALTER TABLE `monstre` ADD `dressage` MEDIUMINT UNSIGNED NOT NULL AFTER `sort_mort` ;

ALTER TABLE `pet` ADD `ecurie` TINYINT UNSIGNED NOT NULL;

INSERT INTO `terrain_batiment` (
`id` ,
`nom` ,
`description` ,
`type` ,
`effet` ,
`nb_case` ,
`prix` ,
`requis` ,
`point_structure`
)
VALUES (
NULL , 'Ecurie', 'Permet de garder 10 monstres sans avoir besoin de payer le dépôt.', 'ecurie', '10', '1', '0', '0', '5000'
), (
NULL , 'Ecurie 2', 'Permet de garder 25 monstres sans avoir besoin de payer le dépôt.', 'ecurie', '25', '2', '', '7', '15000'
);
ALTER TABLE `monstre` ADD `sort_dressage` VARCHAR( 5 ) NOT NULL AFTER `dressage`;
ALTER TABLE `perso` ADD `email` VARCHAR( 100 ) NOT NULL AFTER `password` ;