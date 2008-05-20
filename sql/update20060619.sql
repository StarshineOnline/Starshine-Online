ALTER TABLE `sort_combat` ADD `effet2` TINYINT UNSIGNED NOT NULL AFTER `effet` ,
ADD `duree` TINYINT UNSIGNED NOT NULL AFTER `effet2` ;
INSERT INTO `sort_combat` ( `id` , `nom` , `description` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `incantation` , `comp_requis` , `effet` , `effet2` , `duree` , `cible` , `requis` , `prix` , `difficulte` )
VALUES (
NULL , 'Trait de glace', 'Frappe l''énnemie lui infligeant des dégats, et a une chance de le glacer pendant %duree% round. (effet %effet%)', '4', 'degat_froid', 'sort_element', 'puissance', '0', '60', '30', '1', '1', '1', '4', '', '300', '120'
), (
NULL , 'Trait de glace 2', 'Frappe l''énnemie lui infligeant des dégats, et a une chance de le glacer pendant %duree% round. (effet %effet%)', '4', 'degat_froid', 'sort_element', 'puissance', '0', '120', '60', '2', '2', '1', '4', '', '900', '240'
);
INSERT INTO `sort_combat` ( `id` , `nom` , `description` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `incantation` , `comp_requis` , `effet` , `effet2` , `duree` , `cible` , `requis` , `prix` , `difficulte` )
VALUES (
NULL , 'Trait de glace 3', 'Frappe l''énnemie lui infligeant des dégats, et a une chance de le glacer pendant %duree% round. (effet %effet%)', '4', 'degat_froid', 'sort_element', 'puissance', '0', '240', '120', '3', '3', '1', '4', '', '2000', '480'
);
INSERT INTO `sort_combat` ( `id` , `nom` , `description` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `incantation` , `comp_requis` , `effet` , `effet2` , `duree` , `cible` , `requis` , `prix` , `difficulte` )
VALUES (
NULL , 'Silence', 'Empèche l''énnemi de lancer des sorts ou compétences, dure %duree% rounds.', '3', 'silence', 'sort_mort', 'puissance', '0', '60', '30', '1', '1', '2', '4', '', '300', '150'
), (
NULL , 'Silence 2', 'Empèche l''énnemi de lancer des sorts ou compétences, dure %duree% rounds.', '3', 'silence', 'sort_mort', 'puissance', '0', '240', '120', '1', '1', '3', '4', '', '2000', '600'
);
INSERT INTO `sort_combat` ( `id` , `nom` , `description` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `incantation` , `comp_requis` , `effet` , `effet2` , `duree` , `cible` , `requis` , `prix` , `difficulte` )
VALUES (
NULL , 'Silence 3', 'Empèche l''''énnemi de lancer des sorts ou compétences, dure %duree% rounds.', '3', 'silence', 'sort_mort', 'puissance', '0', '500', '250', '1', '1', '4', '4', '', '15000', '900'
);
INSERT INTO `sort_combat` ( `id` , `nom` , `description` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `incantation` , `comp_requis` , `effet` , `effet2` , `duree` , `cible` , `requis` , `prix` , `difficulte` )
VALUES (
NULL , 'Brûlure de mana', 'Consume la réserve de mana de l''énnemi et lui inflige des dégats (effet %effet2%).', '4', 'brulure_mana', 'sort_mort', 'puissance', '0', '50', '25', '2', '1', '1', '4', '', '200', '100'
), (
NULL , 'Brûlure de mana 2', 'Consume la réserve de mana de l''énnemi et lui inflige des dégats (effet %effet2%).', '4', 'brulure_mana', 'sort_mort', 'puissance', '0', '100', '50', '2', '2', '1', '4', '', '600', '200'
);
INSERT INTO `comp_combat` ( `id` , `nom` , `description` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `comp_requis` , `arme_requis` , `effet` , `cible` , `requis` , `prix` )
VALUES (
NULL , 'Coup sournois', 'Augmente les chances de faire un coup critique (effet %effet%).', '3', 'coup_sournois', 'melee', 'dexterite', '0', '150', 'dague', '1', '4', '999', '5'
), (
NULL , 'Coup sournois 2', 'Augmente les chances de faire un coup critique (effet %effet%).', '3', 'coup_sournois', 'melee', 'dexterite', '0', '200', 'dague', '2', '4', '', '1000'
);
INSERT INTO `comp_combat` ( `id` , `nom` , `description` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `comp_requis` , `arme_requis` , `effet` , `cible` , `requis` , `prix` )
VALUES (
NULL , 'Coup sournois 3', 'Augmente les chances de faire un coup critique (effet %effet%).', '3', 'coup_sournois', 'melee', 'dexterite', '0', '300', 'dague', '3', '4', '', '2000'
), (
NULL , 'Coup sounrois 4', 'Augmente les chances de faire un coup critique (effet %effet%).', '3', 'coup_sournois', 'melee', 'dexterite', '0', '400', 'dague', '4', '4', '', '4000'
);
INSERT INTO `classe_comp_permet` ( `id` , `id_classe` , `competence` )
VALUES (                                                              
NULL , '3', '11'                                                      
);                                                                    