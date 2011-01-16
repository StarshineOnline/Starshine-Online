-- -*- mode: sql; coding: utf-8-unix; -*-
-- Ajout des sorts de rang 4

ALTER TABLE `sort_jeu` COMMENT = '1: self, 2: membre, 3: selfgroup, 4: fullgroup, 8: ennemi' ;

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
`lvl_batiment`
)
VALUES

(
NULL , 'Retour en ville de groupe', 'Téléporte l''ensemble du groupe à la capitale du lanceur', '25', '35', 'teleport', 'sort_element', 'volonte', '0', '0', '0', '0', '0', '0', '3', '2', 'classe:elémentaliste', '1', '0', '99'
), (
NULL , 'Faveur divine', 'Soigne la cible de 5% des PV.', '4', '30', 'vie_pourcent', 'sort_vie', 'energie', '0', '0', '0', '5', '0', '0', '2', '0', 'classe:sage', '1', '0', '99'
), (
NULL , 'Balance', 'Nivelle tous les points de vie des joueurs du groupe.', '5', '30', 'balance', 'sort_vie', 'volonte', '0', '0', '0', '0', '0', '0', '3', '2', 'classe:templier', '1', '0', '99'
), (
NULL , 'Contagion', 'Réduit le coût des sorts de maladie de %effet% PA et %effet2% MP.', '4', '20', 'buff_contagion', 'sort_mort', 'energie', '0', '0', '0', '2', '15', '86400', '1', '0', 'classe:pestimancien', '1', '0', '99'
)

;
