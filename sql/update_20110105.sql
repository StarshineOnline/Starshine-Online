-- -*- sql -*-

-- Quêtes du gobelin artiste
INSERT INTO `achievement_type` (
`id` ,
`nom` ,
`description` ,
`value` ,
`variable` ,
`secret`
)
VALUES (
NULL , 'Explorateur du Royaume Gobelin', 'Avoir validé la première quête du gobelin artiste', '1', 'quest_gob', '0'
), (
NULL , 'Sauveur de l''art Gobelin', 'Avoir validé la seconde quête du gobelin artiste', '2', 'quest_gob', '0'
);

-- Abomination
INSERT INTO `achievement_type` (
`id` ,
`nom` ,
`description` ,
`value` ,
`variable` ,
`secret`
)
VALUES (
NULL , 'Tueur du Roi Gobelin Zewak Rustog', 'Avoir tué le roi gobelin derrière l''abomination', '1', 'kill_gob_king', '0'
), (
NULL , 'Marqué par la Terreur Gobeline', 'Avoir été touché par la marque de l''abomination', '1', 'abomination_mark', '0'
), (
NULL , 'Survivant de l''Abominable', 'Avoir survécu à un combat contre l''abomination', '2', 'abomination_mark', '0'
);

