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
NULL , 'Charisme', 'Augmente de %effet% le nombre de buffs disponibles. Dure une semaine.', '20', '5', 'buff_charisme', 'distance', 'dexterite', '0', '100', 'arc', '2', '0', '604800', '8', 'classe:sniper', '0', '9'
);

-- don de la comp
update perso set comp_jeu = concat(comp_jeu, ';94') where classe = 'Sniper';

insert into `classe_comp_permet` (`id_classe`, `competence`, `type`) select c.id, s.id, 'comp_jeu' from classe c, comp_jeu s where s.requis = concat('classe:', c.nom);

