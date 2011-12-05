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
`prix` ,
`lvl_batiment` ,
`etat_lie`
)
VALUES (
NULL , 'Vol à la tire', 'dégâts +%effet%, et vol de 1d%effet2% stars', '5', 'vol_a_la_tire', 'esquive', 'dexterite', '0', '200', 'epee', '5', '20', '0', '0', '4', '', '0', '9', ''
);
