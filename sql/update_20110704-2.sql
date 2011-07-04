-- debuff trêve olympique
INSERT INTO `sort_jeu` (
`id` ,
`nom` ,
`description` ,
`pa` ,
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
`portee` ,
`requis` ,
`difficulte` ,
`prix` ,
`lvl_batiment` ,
`special`
)
VALUES (
NULL , 'Trêve olympique', 'Empêche le RvR pendant les événements sportifs', '0', '0', 'debuff_rvr', 'sort_vie', 'puissance', '0', '0', '0', '0', '0', '86400', '0', '0', '999', '0', '0', '9', '1'
);
