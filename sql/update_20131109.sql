-- On change le champ achetable par un champ lvl_batiment pour uniformiser
ALTER TABLE `objet` ADD `lvl_batiment` INT NOT NULL DEFAULT '9';
UPDATE objet SET lvl_batiment = 1 WHERE achetable = 'y';
ALTER TABLE `objet` DROP `achetable`;
UPDATE accessoire SET lvl_batiment = 0 WHERE achetable = 'n';
ALTER TABLE `accessoire` DROP `achetable`;

-- on modifie la table arme
ALTER TABLE `arme` ADD `coefficient` INT NOT NULL DEFAULT '9' AFTER `distance`;
update arme set coefficient = `forcex`*(melee+distance);
ALTER TABLE `arme` DROP `melee`;
ALTER TABLE `arme` DROP `distance`;
ALTER TABLE `arme` DROP `image`;

