-- Riposte furtive
INSERT INTO `sort_combat` (`nom`, `description`, `mp`, `type`, `comp_assoc`, `carac_assoc`, `carac_requis`, `incantation`, `comp_requis`, `effet`, `effet2`, `duree`, `cible`, `requis`, `prix`, `difficulte`, `lvl_batiment`, `etat_lie`) VALUES
 ('Riposte furtive', 'Renvoie %effet%% des dégâts reçus', '0', 'riposte_furtive', 'sort_mort', 'volonte', '0', '100', '100', '200', '0', '8', '1', '', '0', '100', '9', '');


-- Boss: ombre
-- pas de pop automatisé puisqu'il change de place
UPDATE `monstre` SET `terrain` = '0' WHERE `nom` = 'Ombre Maudite Azgald';
INSERT INTO `monstre` select 
NULL, -- id
lib,nom,type,
100, -- hp
1, -- pp
1, -- pm
forcex,dexterite,puissance,volonte,energie,melee,esquive,incantation,sort_vie,
sort_mort,dressage,sort_dressage,sort_element,arme,
'', -- action
1, -- level
1, -- xp
0, -- star
'', -- drops
spawn,spawn_loc,terrain,affiche,description,sort_combat,comp_combat
from monstre WHERE `nom` = 'Ombre Maudite Azgald';
-- gestion du calendrier
INSERT INTO `calendrier` (`date`, `script`, `eval`, `sql`, `nextu`, `done`, `id_manuel`) VALUES ('2011-10-30 12:00:00', 'calendrier/illusioniste.php', NULL, NULL, '28800', '0', 'boss_illusioniste');
-- modif field 'affiche'
ALTER TABLE `monstre` CHANGE `affiche` `affiche` ENUM( 'y', 'n', 'h' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'y' COMMENT 'y: affiche tout - n: n''affiche pas la description - h: cache aussi level, hp, caracs, et autres infos survie';
-- set affiche à 'h' pour les ombres
update `monstre` set `affiche` = 'h' WHERE `nom` = 'Ombre Maudite Azgald';

