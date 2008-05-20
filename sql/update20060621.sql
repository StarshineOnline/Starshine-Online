INSERT INTO `monstre` ( `id` , `lib` , `nom` , `type` , `hp` , `pp` , `pm` , `forcex` , `dexterite` , `puissance` , `volonte` , `melee` , `esquive` , `incantation` , `sort_vie` , `sort_mort` , `sort_element` , `action` , `level` , `xp` , `star` , `drop` , `spawn` , `terrain` )
VALUES (
NULL , 'faucon', 'Faucon', 'bete', '40', '15', '5', '10', '16', '10', '10', '25', '40', '', '', '', '', '', '2', '250', '18', '', '700', '1'
), (
NULL , 'chameau', 'Chameau', 'bete', '55', '15', '10', '13', '13', '10', '12', '25', '30', '', '', '', '', '', '2', '230', '21', '', '850', '3'
);
INSERT INTO `monstre` ( `id` , `lib` , `nom` , `type` , `hp` , `pp` , `pm` , `forcex` , `dexterite` , `puissance` , `volonte` , `melee` , `esquive` , `incantation` , `sort_vie` , `sort_mort` , `sort_element` , `action` , `level` , `xp` , `star` , `drop` , `spawn` , `terrain` )
VALUES (
NULL , 'fourmi_geante_ouvrier', 'Fourmi géante ouvrière', 'bete', '35', '100', '75', '15', '15', '10', '10', '80', '70', '', '', '', '', '', '3', '650', '40', '', '400', '2'
), (
NULL , 'fourmi_geante_soldat', 'Fourmi géante soldat', 'bete', '300', '300', '150', '18', '16', '10', '10', '250', '200', '', '', '', '', '', '6', '2000', '150', '', '250', '2'
);
INSERT INTO `monstre` ( `id` , `lib` , `nom` , `type` , `hp` , `pp` , `pm` , `forcex` , `dexterite` , `puissance` , `volonte` , `melee` , `esquive` , `incantation` , `sort_vie` , `sort_mort` , `sort_element` , `action` , `level` , `xp` , `star` , `drop` , `spawn` , `terrain` )
VALUES (
NULL , 'cockatrice', 'cockatrice', 'monstre', '300', '260', '260', '19', '17', '16', '12', '200', '200', '170', '30', '130', '50', '#09=2@~19', '8', '4000', '300', '', '175', '1'
);
INSERT INTO `monstre` ( `id` , `lib` , `nom` , `type` , `hp` , `pp` , `pm` , `forcex` , `dexterite` , `puissance` , `volonte` , `melee` , `esquive` , `incantation` , `sort_vie` , `sort_mort` , `sort_element` , `action` , `level` , `xp` , `star` , `drop` , `spawn` , `terrain` )
VALUES (
NULL , 'tigre', 'Tigre', 'bete', '250', '350', '300', '22', '19', '10', '10', '300', '400', '', '', '', '', '', '10', '9000', '1000', '', '110', '2'
), (
NULL , 'tigre_blanc', 'Tigre blanc', 'bete', '270', '300', '250', '20', '18', '10', '10', '250', '300', '', '', '', '', '', '9', '7000', '600', '', '130', '4'
);
UPDATE `classe` SET `type` = 'guerrier' WHERE `id` =1 LIMIT 1 ;

UPDATE `classe` SET `type` = 'guerrier' WHERE `id` =3 LIMIT 1 ;

UPDATE `classe` SET `type` = 'guerrier' WHERE `id` =4 LIMIT 1 ;

UPDATE `classe` SET `type` = 'guerrier' WHERE `id` =7 LIMIT 1 ;

UPDATE `classe` SET `type` = 'guerrier' WHERE `id` =8 LIMIT 1 ;

UPDATE `classe` SET `type` = 'guerrier' WHERE `id` =9 LIMIT 1 ;

UPDATE `classe` SET `type` = 'guerrier' WHERE `id` =10 LIMIT 1 ;

UPDATE `classe` SET `type` = 'guerrier' WHERE `id` =11 LIMIT 1 ;
UPDATE `classe` SET `type` = 'mage' WHERE `id` =2 LIMIT 1 ;

UPDATE `classe` SET `type` = 'mage' WHERE `id` =5 LIMIT 1 ;

UPDATE `classe` SET `type` = 'mage' WHERE `id` =6 LIMIT 1 ;

UPDATE `classe` SET `type` = 'mage' WHERE `id` =12 LIMIT 1 ;

UPDATE `classe` SET `type` = 'mage' WHERE `id` =13 LIMIT 1 ;

UPDATE `classe` SET `type` = 'mage' WHERE `id` =14 LIMIT 1 ;
CREATE TABLE `gemme` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`nom` VARCHAR( 50 ) NOT NULL ,
`type` VARCHAR( 50 ) NOT NULL ,
`niveau` TINYINT UNSIGNED NOT NULL
);
INSERT INTO `gemme` ( `id` , `nom` , `type` , `niveau` )
VALUES (
NULL , 'Gemme d''arme', 'arme', '1'
), (
NULL , 'Gemme d''arme éclatante', 'arme', '2'
);
INSERT INTO `gemme` ( `id` , `nom` , `type` , `niveau` )
VALUES (
NULL , 'Gemme d''arme parfaite', 'arme', '3'
);
UPDATE `monstre` SET `drop` = 'g1-1000' WHERE `id` =1 LIMIT 1 ;
ALTER TABLE `monstre` CHANGE `drop` `drops` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ;
INSERT INTO `objet` ( `id` , `nom` , `type` , `prix` )
VALUES (
NULL , 'Matériel d''identification', 'identification', '10'
);
ALTER TABLE `perso` ADD `identification` MEDIUMINT UNSIGNED NOT NULL DEFAULT 1 AFTER `sort_mort` ,
ADD `forge` MEDIUMINT UNSIGNED NOT NULL DEFAULT 1 AFTER `identification` ;
UPDATE perso SET identification = 1, forge = 1;
DROP TABLE `groupe` ;
CREATE TABLE `groupe` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`partage` ENUM( 'r', 't', 'l' ) NOT NULL ,
`prochain_loot` INT UNSIGNED NOT NULL
);
CREATE TABLE `groupe_joueur` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`id_joueur` INT UNSIGNED NOT NULL ,
`id_groupe` INT UNSIGNED NOT NULL ,
`leader` ENUM( 'y', 'n' ) NOT NULL
);
UPDATE perso SET groupe =0;
ALTER TABLE `perso` ADD `frag` INT UNSIGNED NOT NULL AFTER `statut` ;
INSERT INTO `monstre` ( `id` , `lib` , `nom` , `type` , `hp` , `pp` , `pm` , `forcex` , `dexterite` , `puissance` , `volonte` , `melee` , `esquive` , `incantation` , `sort_vie` , `sort_mort` , `sort_element` , `action` , `level` , `xp` , `star` , `drops` , `spawn` , `terrain` )
VALUES (
NULL , 'momie', 'Momie', 'monstre', '450', '600', '800', '27', '19', '15', '18', '290', '440', '300', '50', '150', '50', '', '13', '17000', '1700', '', '80', '3'
), (
NULL , 'djinn', 'Djinn', 'monstre', '700', '800', '1000', '23', '23', '21', '21', '500', '500', '400', '50', '50', '200', '', '18', '30000', '3000', '', '30', '3'
);
ALTER TABLE `journal` ADD `valeur2` INT NOT NULL ;

ALTER TABLE `quete` CHANGE `repeat` `repete` ENUM( 'y', 'n' ) NOT NULL DEFAULT 'y';
UPDATE quete SET repete = 'y';
ALTER TABLE `perso` ADD `quete_fini` TEXT NOT NULL AFTER `quete` ;
ALTER TABLE `quete` ADD `quete_requis` TEXT NOT NULL AFTER `honneur_requis` ;
INSERT INTO `sort_jeu` ( `id` , `nom` , `description` , `pa` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `incantation` , `comp_requis` , `effet` , `duree` , `cible` , `requis` , `prix` , `lvl_batiment` )
VALUES (
NULL , 'Barrière magique', 'Augmente votre défense magique (effet %effet%).', '4', '20', 'buff_barriere', 'sort_vie', 'volonte', '0', '70', '35', '1', '86400', '2', '', '200', '1'
), (
NULL , 'Barrière magique 2', 'Augmente votre défense magique (effet %effet%).', '4', '25', 'buff_barriere', 'sort_vie', 'volonte', '0', '140', '70', '2', '86400', '2', '', '800', '2'
);
INSERT INTO `sort_jeu` ( `id` , `nom` , `description` , `pa` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `incantation` , `comp_requis` , `effet` , `duree` , `cible` , `requis` , `prix` , `lvl_batiment` )
VALUES (
NULL , 'Désespoir', 'Réduit la défense magique de la cible (effet %effet%).', '4', '20', 'debuff_desespoir', 'sort_mort', 'puissance', '0', '70', '35', '1', '3600', '4', '', '200', '1'
), (
NULL , 'Désespoir 2', 'Réduit la défense magique de la cible (effet %effet%).', '4', '25', 'debuff_desespoir', 'sort_mort', 'puissance', '0', '140', '70', '2', '3600', '4', '', '800', '2'
);

INSERT INTO `sort_jeu` ( `id` , `nom` , `description` , `pa` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `incantation` , `comp_requis` , `effet` , `duree` , `cible` , `requis` , `prix` , `lvl_batiment` )
VALUES (
NULL , 'Du corps à l esprit', 'Vous perdez des point de vie, pour gagner du mana (effet %effet%).', '4', '0', 'body_to_mind', 'sort_mort', 'puissance', '0', '200', '100', '20', '', '2', '', '2000', '2'
), (
NULL , 'Du corps à l esprit 2', 'Vous perdez des point de vie, pour gagner du mana (effet %effet%).', '4', '0', 'body_to_mind', 'sort_mort', 'puissance', '0', '300', '150', '30', '', '2', '', '4000', '3'
);
INSERT INTO `sort_jeu` ( `id` , `nom` , `description` , `pa` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `incantation` , `comp_requis` , `effet` , `duree` , `cible` , `requis` , `prix` , `lvl_batiment` )
VALUES (
NULL , 'Aveuglement 2', 'Réduit les chances de toucher de la cible (effet %effet%).', '4', '20', 'debuff_aveuglement', 'sort_mort', 'puissance', '0', '200', '100', '2', '3600', '4', '15', '400', '1'
), (
NULL , 'Aveuglement 3', 'Réduit les chances de toucher de la cible (effet %effet%).', '4', '25', 'debuff_aveuglement', 'sort_mort', 'puissance', '0', '300', '150', '3', '3600', '4', '37', '4000', '3'
);
INSERT INTO `sort_combat` ( `id` , `nom` , `description` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `incantation` , `comp_requis` , `effet` , `effet2` , `duree` , `cible` , `requis` , `prix` , `difficulte` , `lvl_batiment` )
VALUES (
NULL , 'Paralysie 2', 'Paralyse l''ennemi pendant %effet% round.', '3', 'paralysie', 'sort_mort', 'puissance', '0', '250', '125', '3', '', '', '4', '19', '2000', '600', '3'
), (
NULL , '', '', '0', '', '', '', '0', '0', '', '', '', '', '', '', '0', '', ''
);
INSERT INTO `classe_permet` ( `id` , `id_classe` , `competence` , `permet` , `new` )
VALUES (
NULL , '3', 'art_critique', '100', 'yes'
), (
NULL , '9', 'art_critique', '200', 'no'
);
INSERT INTO `comp_combat` ( `id` , `nom` , `description` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `comp_requis` , `arme_requis` , `effet` , `cible` , `requis` , `prix` , `lvl_batiment` )
VALUES (
NULL , 'Tir visé', 'Vous utilisez un round de combat pour prendre le temps de viser. Augmente la précision, les dégats et les chances de critique (effet %effet%).', '5', 'tir_vise', 'distance', 'force', '0', '100', 'arc', '1', '2', '', '200', '1'
), (
NULL , 'Tir visé 2', 'Vous utilisez un round de combat pour prendre le temps de viser. Augmente la précision, les dégats et les chances de critique (effet %effet%).', '5', 'tir_vise', 'distance', 'force', '0', '200', 'arc', '2', '2', '15', '1000', '1'
);
INSERT INTO `comp_combat` ( `id` , `nom` , `description` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `comp_requis` , `arme_requis` , `effet` , `cible` , `requis` , `prix` , `lvl_batiment` )
VALUES (
NULL , 'Coup de bouclier', 'Donne un coup de bouclier qui peut assomer l''adversaire (effet %effet%).', '4', 'coup_bouclier', 'melee', 'force', '0', '110', 'bouclier', '1', '4', '', '200', '1'
), (
NULL , 'Coup de bouclier 2', 'Donne un coup de bouclier qui peut assomer l''adversaire (effet %effet%).', '4', 'coup_bouclier', 'melee', 'force', '0', '220', 'bouclier', '2', '4', '17', '1100', '2'
);
UPDATE `quete` SET `nom` = 'Sérum à base de venin de serpent',
`description` = 'Vous m''avez l''air plus gaillard que la dernière fois, vous prenez de la bouteille ça se vois ! Ca tombe bien avec tous ces serpents qui trainent, on tombe a court de serum ces derniers temps, vous rendriez un fier service si vous pouviez ramener des glandes de poison de serpent afin de préparer plus de serum.',
`fournisseur` = 'magasin' WHERE `id` =2 LIMIT 1 ;
UPDATE `quete` SET `nom` = 'Chasse aux Guépards',
`description` = 'On a toujours eu des Guépards dans la région mais en géneral ils ne s''attaquent pas a nous. Ces derniers temps on a eu plusieurs rapports d''attaques sur des marchands ou des paysans, la garde patrouille mais on ne peux pas être partout, a mon avis ces bestioles ont trop pris confiance, il est temps de leur rappeler ou s''arrête leur territoire ! On m''a soufflé votre nom comme étant a la hauteur, on va bien voir ce que vous valez ... tuez moi 12 de ces saletés et vous ne le regretterez pas.',
`exp` = '3000',
`honneur` = '450',
`star` = '600',
`repete` = 'n' WHERE `id` =7 LIMIT 1 ;
INSERT INTO `quete` ( `id` , `nom` , `description` , `fournisseur` , `objectif` , `exp` , `honneur` , `star` , `repete` , `niveau_requis` , `honneur_requis` , `quete_requis` , `star_royaume` , `lvl_joueur` )
VALUES (
NULL , 'Chasse aux Worgs', 'On a toujours eu des Worgs dans la région mais en géneral ils ne s''attaquent pas a nous. Ces derniers temps on a eu plusieurs rapports d''attaques sur des marchands ou des paysans, la garde patrouille mais on ne peux pas être partout, a mon avis ces bestioles ont trop pris confiance, il est temps de leur rappeler ou s''arrête leur territoire ! On m''a soufflé votre nom comme étant a la hauteur, on va bien voir ce que vous valez... tuez moi 12 de ces saletés et vous ne le regretterez pas.', 'ecole_combat', 'a:1:{i:0;O:8:"stdClass":3:{s:5:"cible";s:3:"M16";s:6:"nombre";i:12;s:6:"requis";s:0:"";}}', '3000', '450', '600', 'n', '0', '0', '', '100', '4'
);
UPDATE `quete` SET `fournisseur` = 'ecole_combat',
`star_royaume` = '100' WHERE `id` =7 LIMIT 1 ;
UPDATE `quete` SET `description` = 'Comme je vous vois je vous dis, un Loup-garou je vous jure... un truc a vous faire dresser l''echine... je suis pas payé pour me faire tailler en rondelles moi ! Mais bon le capitaine là avec ses grand airs, il donne les ordres et il reste bien au chaud a l''abri... si vous êtes assez fou... heu... courageux, vous pourriez peut-être essayer de nous en débarasser... hein ça vous dis ?',
`objectif` = 'a:1:{i:0;O:8:"stdClass":3:{s:5:"cible";s:2:"M4";s:6:"nombre";i:1;s:6:"requis";s:0:"";}}',
`exp` = '1200',
`honneur` = '750',
`star` = '750',
`star_royaume` = '300' WHERE `id` =8 LIMIT 1 ;