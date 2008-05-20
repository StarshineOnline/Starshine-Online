INSERT INTO `sort_jeu` ( `id` , `nom` , `description` , `pa` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `incantation` , `comp_requis` , `effet` , `duree` , `cible` , `portee` , `requis` , `prix` , `lvl_batiment` )
VALUES (
NULL , 'Ralentissement', 'Augmente le cout en PA pour attaquer de 1.', '4', '40', 'debuff_ralentissement', 'sort_mort', 'puissance', '0', '100', '50', '1', '3600', '4', '1', '', '430', '2'
), (
NULL , 'Ralentissement 2', 'Augmente le cout en PA pour attaquer de 1.', '4', '35', 'debuff_ralentissement', 'sort_mort', 'puissance', '0', '180', '90', '1', '3600', '4', '1', '', '1700', '2'
);
INSERT INTO `sort_jeu` ( `id` , `nom` , `description` , `pa` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `incantation` , `comp_requis` , `effet` , `duree` , `cible` , `portee` , `requis` , `prix` , `lvl_batiment` )
VALUES (
NULL , 'Ralentissement 3', 'Augmente le cout en PA pour attaquer de 1.', '4', '30', 'debuff_ralentissement', 'sort_mort', 'puissance', '0', '300', '150', '1', '3600', '4', '1', '', '4000', '3'
);
INSERT INTO `sort_jeu` ( `id` , `nom` , `description` , `pa` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `incantation` , `comp_requis` , `effet` , `duree` , `cible` , `portee` , `requis` , `prix` , `lvl_batiment` )
VALUES (
NULL , 'Enracinement', 'Empèche la cible de se déplacer. (durée %effet% minutes)', '4', '40', 'debuff_enracinement', 'sort_vie', 'energie', '0', '200', '100', '20', '1200', '4', '1', '', '1600', '2'
), (
NULL , 'Enracinement 2', 'Empèche la cible de se déplacer. (durée %effet% minutes)', '4', '45', 'debuff_enracinement', 'sort_vie', 'energie', '0', '260', '130', '40', '2400', '4', '1', '', '3300', '3'
);
INSERT INTO `sort_jeu` ( `id` , `nom` , `description` , `pa` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `incantation` , `comp_requis` , `effet` , `duree` , `cible` , `portee` , `requis` , `prix` , `lvl_batiment` )
VALUES (
NULL , 'Enracinement 3', 'Empèche la cible de se déplacer. (durée %effet% minutes)', '4', '50', 'debuff_enracinement', 'sort_vie', 'energie', '0', '320', '160', '60', '3600', '4', '1', '', '5000', '3'
);
UPDATE arme SET melee = melee * 1.2 WHERE type = 'epee';
UPDATE arme SET melee = melee * 1.4 WHERE TYPE = 'dague';
UPDATE arme SET melee = melee * 0.9 WHERE TYPE = 'hache';