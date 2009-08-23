 INSERT INTO `batiment_ville` (
`id` ,
`nom` ,
`cout` ,
`entretien` ,
`type` ,
`level` ,
`hp`
)
VALUES (
NULL , 'Pas de défense', '0', '0', 'mur', '0', '0'
), (
NULL , 'Palissade', '1000', '10', 'mur', '1', '1000'
), (
NULL , 'Mur', '2000', '20', 'mur', '2', '2000'
), (
NULL , 'Mur fortifié', '4000', '40', 'mur', '3', '4000'
), (
NULL , 'Muraille', '8000', '80', 'mur', '4', '8000'
);
INSERT INTO `construction_ville` (`id` ,`id_royaume` , `id_batiment` ,`statut` ,`dette` ,`hp`) VALUES (NULL , '1', '25', 'actif', '0', '1000');
INSERT INTO `construction_ville` (`id` ,`id_royaume` , `id_batiment` ,`statut` ,`dette` ,`hp`) VALUES (NULL , '2', '25', 'actif', '0', '1000');
INSERT INTO `construction_ville` (`id` ,`id_royaume` , `id_batiment` ,`statut` ,`dette` ,`hp`) VALUES (NULL , '3', '25', 'actif', '0', '1000');
INSERT INTO `construction_ville` (`id` ,`id_royaume` , `id_batiment` ,`statut` ,`dette` ,`hp`) VALUES (NULL , '4', '25', 'actif', '0', '1000');
INSERT INTO `construction_ville` (`id` ,`id_royaume` , `id_batiment` ,`statut` ,`dette` ,`hp`) VALUES (NULL , '6', '25', 'actif', '0', '1000');
INSERT INTO `construction_ville` (`id` ,`id_royaume` , `id_batiment` ,`statut` ,`dette` ,`hp`) VALUES (NULL , '7', '25', 'actif', '0', '1000');
INSERT INTO `construction_ville` (`id` ,`id_royaume` , `id_batiment` ,`statut` ,`dette` ,`hp`) VALUES (NULL , '8', '25', 'actif', '0', '1000');
INSERT INTO `construction_ville` (`id` ,`id_royaume` , `id_batiment` ,`statut` ,`dette` ,`hp`) VALUES (NULL , '9', '25', 'actif', '0', '1000');
INSERT INTO `construction_ville` (`id` ,`id_royaume` , `id_batiment` ,`statut` ,`dette` ,`hp`) VALUES (NULL , '10', '25', 'actif', '0', '1000');
INSERT INTO `construction_ville` (`id` ,`id_royaume` , `id_batiment` ,`statut` ,`dette` ,`hp`) VALUES (NULL , '11', '25', 'actif', '0', '1000');
INSERT INTO `construction_ville` (`id` ,`id_royaume` , `id_batiment` ,`statut` ,`dette` ,`hp`) VALUES (NULL , '12', '25', 'actif', '0', '1000');

ALTER TABLE `elections` CHANGE `date` `date` DATE NOT NULL;
ALTER TABLE `revolution` CHANGE `date` `date` DATE NOT NULL;
CREATE TABLE IF NOT EXISTS `vote_revolution` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_revolution` int(10) unsigned NOT NULL,
  `id_perso` int(10) unsigned NOT NULL default '0',
  `pour` tinyint(1) NOT NULL default '0',
  `poid_vote` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
);