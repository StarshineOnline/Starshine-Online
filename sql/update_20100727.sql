-- Nostalgie de karn
INSERT INTO `sort_combat` (
`id` ,
`nom` ,
`description` ,
`mp` ,
`type` ,
`comp_assoc` ,
`carac_assoc` ,
`carac_requis` ,
`incantation` ,
`comp_requis` ,
`effet` ,
`effet2` ,
`duree` ,
`cible` ,
`requis` ,
`prix` ,
`difficulte` ,
`lvl_batiment` ,
`etat_lie`
)
VALUES (
NULL , 'Nostalgie de Karn', 'Affecte la RM (division par 1.%effet2%) dégâts + %effet%', '0', 'nostalgie_karn', 'sort_element', 'puissance', '0', '0', '1', '10', '50', '0', '4', '', '0', '1', '99', ''
);

update `monstre` set `action` = '#09=3@~179;#14<3µ#10°posture@~101;#11°appel_tenebreµ#09<15@~70;#14<3@~173;#14<3@~44;#14<3@~92;#14<3@~128;#14<3@~122' where id = 145;
