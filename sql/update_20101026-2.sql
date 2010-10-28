-- Avec cet update, on pourra en finir avec ces stupides bonus1, bonus2, ... 7
-- qui ne veulent pas dire la même chose suivant le type de bâtiment auquel
-- ils s'appliquent. Ce sera de plus bien plus lisible

-- création de la table
DROP TABLE IF EXISTS `batiment_bonus`;
CREATE TABLE `batiment_bonus` (
`id_batiment` INT NOT NULL COMMENT 'id du bâtiment',
`bonus` VARCHAR( 30 ) NOT NULL COMMENT 'nom du bonus',
`valeur` INT NULL COMMENT 'valeur éventuelle du bonus',
PRIMARY KEY ( `id_batiment` , `bonus` )
) ENGINE = MYISAM COMMENT = 'Gère les boni des bâtiments à l''aide d''une relation 1-n' ;
delete from `batiment_bonus`;

-- remplissage
-- gestion du royaume
insert into `batiment_bonus`
select id, 'royaume', null from batiment where (bonus7 = 1 and type = 'bourg') or (type = 'fort' and id not like 1);

-- Téléport
insert into `batiment_bonus`
select id, 'teleport', null from batiment where (bonus6 = 1 and type = 'fort') or type = 'bourg';

-- Poste
insert into `batiment_bonus`
select id, 'poste', null from batiment where (bonus6 = 1 and type = 'fort') or (bonus7 = 1 and type = 'bourg');

-- Taverne
insert into `batiment_bonus`
select id, 'taverne', null from batiment where (type = 'fort') or (bonus7 = 1 and type = 'bourg');

-- Alchimiste
insert into `batiment_bonus`
select id, 'alchimiste', null from batiment where (bonus7 = 1 and type = 'fort');

-- Écurie
insert into `batiment_bonus`
select id, 'ecurie', null from batiment where (bonus7 = 1 and type = 'bourg') or type = 'fort';

-- Bureau des quêtes
insert into `batiment_bonus`
select id, 'quete', null from batiment where type = 'bourg' or type = 'fort';

-- bonus esquive
insert into `batiment_bonus`
select id, 'batiment_esquive', bonus1 from batiment where type = 'fort';

-- bonus pp
insert into `batiment_bonus`
select id, 'batiment_pp', bonus2 from batiment where type = 'fort';

-- bonus pm
insert into `batiment_bonus`
select id, 'batiment_pm', bonus3 from batiment where type = 'fort';

-- bonus distance
insert into `batiment_bonus`
select id, 'batiment_distance', bonus1 from batiment where type = 'tour';

-- bonus incant
insert into `batiment_bonus`
select id, 'batiment_incantation', bonus2 from batiment where type = 'tour';

-- bonus de vue
insert into `batiment_bonus`
select id, 'batiment_vue', bonus4 from batiment where type = 'tour';

-- point de rez
insert into `batiment_bonus`
select id, 'rez', bonus4 from batiment where bonus4 > 0 and type != 'arme_de_siege';

-- dégâts armes de siège vs bâtiments
insert into `batiment_bonus`
select id, 'degats_bat', bonus1 from batiment where type = 'arme_de_siege';

-- dégâts armes de siège vs armes de siège
insert into `batiment_bonus`
select id, 'degats_siege', bonus2 from batiment where type = 'arme_de_siege';

-- rechargement armes de siège
insert into `batiment_bonus`
select id, 'rechargement', bonus3 from batiment where type = 'arme_de_siege';

-- portée armes de siège
insert into `batiment_bonus`
select id, 'portee', bonus4 from batiment where type = 'arme_de_siege';

-- précision armes de siège
insert into `batiment_bonus`
select id, 'precision', bonus5 from batiment where type = 'arme_de_siege';

-- rang pour manipuler armes de siège
insert into `batiment_bonus`
select id, 'rang_manip', bonus6 from batiment where type = 'arme_de_siege';

-- boost production
insert into `batiment_bonus`
select id, 'production', bonus1 from batiment where type = 'mine';

-- production spécialisée
insert into `batiment_bonus`
select id, 'specialite', bonus2 from batiment where type = 'mine';

-- On vire les anciennes colonnes, mais on les garde quelque fois que
 ALTER TABLE `batiment` 
CHANGE `bonus1` `oldbonus1` MEDIUMINT( 9 ) NOT NULL DEFAULT '0',
CHANGE `bonus2` `oldbonus2` MEDIUMINT( 9 ) NOT NULL DEFAULT '0',
CHANGE `bonus3` `oldbonus3` MEDIUMINT( 9 ) NOT NULL DEFAULT '0',
CHANGE `bonus4` `oldbonus4` MEDIUMINT( 9 ) NOT NULL DEFAULT '0',
CHANGE `bonus5` `oldbonus5` MEDIUMINT( 9 ) NOT NULL DEFAULT '0',
CHANGE `bonus6` `oldbonus6` MEDIUMINT( 9 ) NOT NULL DEFAULT '0',
CHANGE `bonus7` `oldbonus7` MEDIUMINT( 9 ) NOT NULL DEFAULT '0' ;
