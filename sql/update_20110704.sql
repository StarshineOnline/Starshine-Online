-- buff du sniper: charisme
INSERT INTO `comp_jeu` (
`id` ,
`nom` ,
`description` ,
`mp` ,
`pa` ,
`type` ,
`comp_assoc` ,
`carac_assoc` ,
`carac_requis` ,
`comp_requis` ,
`arme_requis` ,
`effet` ,
`effet2` ,
`duree` ,
`cible` ,
`requis` ,
`prix` ,
`lvl_batiment`
)
VALUES (
NULL , 'Charisme', 'Augmente de %effet% le nombre de buffs disponibles. Dure une semaine.', '20', '5', 'buff_charisme', 'distance', 'dexterite', '0', '100', 'arc', '2', '0', '604800', '8', '999', '0', '9'
);

