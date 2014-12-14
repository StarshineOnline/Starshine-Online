ALTER TABLE `hotel` DROP `nombre`;
ALTER TABLE `hotel` ADD `type` ENUM('vente', 'achat') NOT NULL DEFAULT 'vente' AFTER `id_vendeur`;

UPDATE `classe` SET `nom` = 'Dresseur de l''ombre' WHERE `classe`.`id` = 27;
UPDATE `classe` SET `nom` = 'Archer d''élite' WHERE `classe`.`id` = 11;

ALTER TABLE `taverne` CHANGE `ID` `id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT;

-- Rang des capitales
ALTER TABLE `royaume` ADD `rang` TINYINT(4) NOT NULL AFTER `alchimie`;
UPDATE `royaume` SET `rang` = 4 WHERE id > 0;

-- Dresseur
INSERT INTO `batiment_ville` (`nom`, `cout`, `entretien`, `type`, `level`, `hp`) VALUES ('Dresseur', 0, 0, 'dresseur', 1, 1000);
INSERT INTO `batiment_ville` (`nom`, `cout`, `entretien`, `type`, `level`, `hp`) VALUES ('Dresseur cuivre', 500, 5, 'dresseur', 2, 2000);
INSERT INTO `batiment_ville` (`nom`, `cout`, `entretien`, `type`, `level`, `hp`) VALUES ('Dresseur bronze', 1000, 10, 'dresseur', 3, 4000);
INSERT INTO `batiment_ville` (`nom`, `cout`, `entretien`, `type`, `level`, `hp`) VALUES ('Dresseur argent', 2000, 15, 'dresseur', 4, 6000);
INSERT INTO `batiment_ville` (`nom`, `cout`, `entretien`, `type`, `level`, `hp`) VALUES ('Dresseur or', 4000, 20, 'dresseur', 5, 8000);