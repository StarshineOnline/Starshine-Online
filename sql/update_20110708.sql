-- sort rang 4 prédateur
-- définition
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
NULL , 'Transfert énergétique', 'Redonne %effet% MP.', '2', '30', 'transfert_energie', 'sort_mort', 'volonte', '0', '200', '0', '20', '0', '0', '2', '2', 'classe:prédateur', '1', '0', '99', '1'
);

-- don automatique
insert ignore into `classe_comp_permet` (`id_classe`, `competence`, `type`) select c.id, s.id, 'sort_jeu' from classe c, sort_jeu s where s.requis = concat('classe:', c.nom);

-- don manuel
update perso set sort_jeu = concat(sort_jeu, ';', (select id from sort_jeu where type = 'transfert_energie')) where classe = 'prédateur';

-- comp de rang 4 titan
-- définition
INSERT INTO `starshine`.`comp_jeu` (
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
NULL , 'Pour la gloire', 'Augmente les gains d''honneur de %effet%%.', '50', '4', 'buff_honneur', 'melee', 'force', '0', '200', '', '15', '0', '604800', '8', 'classe:titan', '0', '9'
);

-- don automatique
insert ignore into `classe_comp_permet` (`id_classe`, `competence`, `type`) select c.id, s.id, 'comp_jeu' from classe c, comp_jeu s where s.requis = concat('classe:', c.nom);

-- don manuel
update perso set comp_jeu = concat(comp_jeu, ';', (select id from comp_jeu where type = 'buff_honneur')) where classe = 'titan';
