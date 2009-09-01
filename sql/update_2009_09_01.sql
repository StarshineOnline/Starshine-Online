CREATE TABLE `point_victoire_action` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
`nom` VARCHAR( 100 ) NOT NULL ,
`type` VARCHAR( 50 ) NOT NULL ,
`action` INT UNSIGNED NOT NULL ,
PRIMARY KEY ( `id` )
);
ALTER TABLE `point_victoire_action` ADD `cout` MEDIUMINT UNSIGNED NOT NULL ;
INSERT INTO `point_victoire_action` (
`id` ,
`nom` ,
`type` ,
`action`,
`cout`
)
VALUES (
NULL , 'Festin', 'famine', '', '10'
);
ALTER TABLE `point_victoire_action` ADD `type_buff` VARCHAR( 50 ) NOT NULL ,
ADD `effet` MEDIUMINT UNSIGNED NOT NULL ,
ADD `description` TEXT NOT NULL ;
INSERT INTO `starshine_raz`.`point_victoire_action` (
`id` ,
`nom` ,
`type` ,
`action` ,
`cout` ,
`type_buff` ,
`effet` ,
`description`
)
VALUES (
NULL , 'Moral', 'buff', '', '20', 'moral', '10', 'Augmente l''honneur gagné de 10%.'
), (
NULL , 'Moral II', 'buff', '', '40', 'moral', '20', 'Augmente l''honneur gagné de 20%.'
);
UPDATE `point_victoire_action` SET `description` = 'Supprime la famine a tous les joueurs du royaume.' WHERE `point_victoire_action`.`id` =1 LIMIT 1 ;