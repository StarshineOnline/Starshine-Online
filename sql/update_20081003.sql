ALTER TABLE `perso`
  DROP `arme`,
  DROP `competence`,
  DROP `forge`,
  DROP `resistmagique`;

ALTER TABLE `perso` ADD `alchimie` MEDIUMINT UNSIGNED NOT NULL DEFAULT '1' AFTER `craft` ,
ADD `architecture` MEDIUMINT UNSIGNED NOT NULL DEFAULT '1' AFTER `alchimie` ,
ADD `forge` MEDIUMINT UNSIGNED NOT NULL DEFAULT '1' AFTER `architecture` ;