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
NULL , 'Nostalgie de Karn', 'Affecte la RM (division par 1.%effet2%) Dégats + %effet%', '0', 'nostalgie_karn', 'sort_element', 'puissance', '0', '0', '1', '10', '50', '0', '4', '', '0', '1', '99', ''
);

update `monstre` set `action` = '#09=3@~179;#10°berzeker@_99;#09=20@_84;#14<5@_79;#14<5@_77' where id = 145;
