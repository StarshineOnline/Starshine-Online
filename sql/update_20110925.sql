ALTER TABLE `point_victoire_action` ADD `duree` INT( 10 ) UNSIGNED NOT NULL ;

UPDATE `point_victoire_action` SET `duree` = '2678400' WHERE `nom` ='Moral';

UPDATE `point_victoire_action` SET `duree` = '2678400' WHERE `nom` ='Moral II';


INSERT INTO `point_victoire_action` (
`id` ,
`nom` ,
`type` ,
`action` ,
`cout` ,
`type_buff` ,
`effet` ,
`description` ,
`duree`
)
VALUES (
NULL , 'Rune royale', 'buff', '0', '20', 'buff_rune', '2', 'Augmente la RM de 2', '604800'
);

INSERT INTO `point_victoire_action` (
`id` ,
`nom` ,
`type` ,
`action` ,
`cout` ,
`type_buff` ,
`effet` ,
`description` ,
`duree`
)
VALUES (
NULL , 'Rush', 'buff', '0', '20', 'buff_rush', '1', 'Diminue le cout en PA des deplacements de 1', '86400'
);
