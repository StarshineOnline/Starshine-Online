-- Items PNJ
INSERT INTO `arme` (
`id` ,
`nom` ,
`type` ,
`degat` ,
`forcex` ,
`melee` ,
`distance` ,
`distance_tir` ,
`mains` ,
`var1` ,
`effet` ,
`prix` ,
`lvl_batiment` ,
`image`
)
VALUES (
NULL , 'Bouclier Tableau', 'bouclier', '5', '0', '200', '0', '0', 'main_gauche', '', '10-1', '30000', '255', 'arme81'
);

INSERT INTO `objet` (
`id` ,
`nom` ,
`type` ,
`prix` ,
`achetable` ,
`stack` ,
`utilisable` ,
`effet` ,
`description` ,
`pa` ,
`mp`
)
VALUES (
NULL , 'Huile de Karn', 'objet_quete', '1000', 'n', '0', 'n', '0', 'Un fragment du plan de Karn, rien de moins', '0', '0'
);

insert into monstre select null, 'element_de_karn', 'Elementaire de Karn', 'magique', hp, pp, FLOOR(pm * 1.2), forcex,dexterite,puissance,volonte,energie,melee,esquive,incantation,sort_vie,sort_mort,99999,sort_dressage,sort_element,arme,action, level, FLOOR(xp * 1.2), FLOOR(star * 0.8),'o53-80',5,'p34-222;p36-222;p38-220;p32-215;p39-216;p35-217',15,affiche,'Elementaire de Karn' from monstre where lib='chitineux';
