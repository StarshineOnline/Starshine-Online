ALTER TABLE `motd` ADD `date` INT(11) NOT NULL DEFAULT '0';

ALTER TABLE `echange` ADD `nouveau` BOOLEAN NOT NULL DEFAULT FALSE ;

-- On distingue les sorts ayant le même effets
UPDATE sort_combat SET type = 'degat_feu-1' WHERE nom LIKE 'Toucher de feu%';
UPDATE sort_combat SET type = 'degat_feu-2' WHERE nom LIKE 'Trait de feu%';
UPDATE sort_combat SET type = 'degat_feu-3' WHERE nom LIKE 'Boule de feu%';
UPDATE sort_combat SET type = 'degat_feu-4' WHERE nom LIKE 'Fournaise%';
UPDATE sort_combat SET type = 'degat_mort-1' WHERE nom LIKE 'Trait de mort%';
UPDATE sort_combat SET type = 'degat_mort-2' WHERE nom LIKE 'Destruction Mentale%';
UPDATE sort_combat SET type = 'degat_nature-1' WHERE nom LIKE 'Feuilles tranchantes%';
UPDATE sort_combat SET type = 'degat_nature-2' WHERE nom LIKE 'Epines Géantes%';

-- On désactive la possibilité d'avoir son propre css dans la description du personnage (bonus shine)
UPDATE `starshine_preprod`.`bonus` SET `id_categorie` = '0' WHERE `bonus`.`id_bonus` = 27;

-- Options
ALTER TABLE `options` CHANGE `valeur` `valeur` MEDIUMINT(16) UNSIGNED NOT NULL;