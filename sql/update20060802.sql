INSERT INTO `sort_jeu` ( `id` , `nom` , `description` , `pa` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `incantation` , `comp_requis` , `effet` , `duree` , `cible` , `requis` , `prix` , `lvl_batiment` )
VALUES (
NULL , 'Soin supérieur', 'Soigne la cible (puissance %effet%)', '4', '23', 'vie', 'sort_vie', 'energie', '11', '200', '100', '15', '0', '2', '8', '1600', '2'
), (
NULL , 'Soin majeur', 'Soigne la cible (puissance %effet%)', '4', '32', 'vie', 'sort_vie', 'energie', '11', '300', '150', '21', '0', '2', '39', '4000', '3'
);
INSERT INTO `sort_jeu` ( `id` , `nom` , `description` , `pa` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `incantation` , `comp_requis` , `effet` , `duree` , `cible` , `requis` , `prix` , `lvl_batiment` )
VALUES (
NULL , 'Soin puissant', 'Soigne la cible (puissance %effet%)', '4', '43', 'vie', 'sort_vie', 'energie', '11', '400', '200', '28', '0', '2', '40', '7000', '3'
);
UPDATE `sort_jeu` SET `effet` = '10' WHERE `id` =8 LIMIT 1 ;