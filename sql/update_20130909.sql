-- sworling bis
INSERT INTO  `calendrier` (
`id` ,
`date` ,
`script` ,
`eval` ,
`sql` ,
`nextu` ,
`done` ,
`id_manuel`
)
VALUES (
NULL ,  '2013-09-09 00:00:00',  'calendrier/sworling_bis.php', NULL , NULL ,  '259200',  '0',  'sworling_bis'
);

ALTER TABLE `monstre` ADD  `id_manuel` VARCHAR(50) NULL ,
ADD UNIQUE ( `id_manuel` );

INSERT INTO `monstre` (`id_manuel`, `lib`, `nom`, `type`, `hp`, `pp`, `pm`, `forcex`, `dexterite`, `puissance`, `volonte`, `energie`, `melee`, `esquive`, `incantation`, `sort_vie`, `sort_mort`, `dressage`, `sort_dressage`, `sort_element`, `arme`, `action`, `level`, `xp`, `star`, `drops`, `spawn`, `spawn_loc`, `terrain`, `affiche`, `description`, `sort_combat`, `comp_combat`, `blocage`, `bouclier`, `quete`)
SELECT 
'sworling_bis', `lib`, `nom`, `type`, `hp`, `pp`, `pm`, `forcex`, `dexterite`, `puissance`, `volonte`, `energie`, `melee`, `esquive`, `incantation`, `sort_vie`, `sort_mort`, `dressage`, `sort_dressage`, `sort_element`, `arme`, `action`, `level`, `xp`, `star`, `drops`, `spawn`, `spawn_loc`, `terrain`, `affiche`, `description`, `sort_combat`, `comp_combat`, `blocage`, `bouclier`, `quete`
FROM `monstre` WHERE id = 56;
