ALTER TABLE `titre_honorifique` ADD `niveau` INT(10) NOT NULL DEFAULT 0 AFTER `titre`;
CREATE TABLE `achievement_perso_variable` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
`id_perso` INT,
`variable` VARCHAR(20),
`compteur` INT
);