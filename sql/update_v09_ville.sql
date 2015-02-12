ALTER TABLE `hotel` DROP `nombre`;
ALTER TABLE `hotel` ADD `type` ENUM('vente', 'achat') NOT NULL DEFAULT 'vente' AFTER `id_vendeur`;

UPDATE `classe` SET `nom` = 'Dresseur de l''ombre' WHERE `classe`.`id` = 27;
UPDATE `classe` SET `nom` = 'Archer d''élite' WHERE `classe`.`id` = 11;

ALTER TABLE `taverne` CHANGE `ID` `id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT;

-- Rang des capitales
ALTER TABLE `royaume` ADD `rang` TINYINT(4) NOT NULL AFTER `alchimie`;
UPDATE `royaume` SET `rang` = 4 WHERE id > 0;

-- Dresseur
INSERT INTO `batiment_ville` (`id`, `nom`, `cout`, `entretien`, `type`, `level`, `hp`) VALUES (30, 'Dresseur', 0, 0, 'dresseur', 1, 1000);
INSERT INTO `batiment_ville` (`nom`, `cout`, `entretien`, `type`, `level`, `hp`) VALUES ('Dresseur cuivre', 500, 5, 'dresseur', 2, 2000);
INSERT INTO `batiment_ville` (`nom`, `cout`, `entretien`, `type`, `level`, `hp`) VALUES ('Dresseur bronze', 1000, 10, 'dresseur', 3, 4000);
INSERT INTO `batiment_ville` (`nom`, `cout`, `entretien`, `type`, `level`, `hp`) VALUES ('Dresseur argent', 2000, 15, 'dresseur', 4, 6000);
INSERT INTO `batiment_ville` (`nom`, `cout`, `entretien`, `type`, `level`, `hp`) VALUES ('Dresseur or', 4000, 20, 'dresseur', 5, 8000);
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (1, 30, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (2, 30, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (3, 30, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (4, 30, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (6, 30, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (7, 30, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (8, 30, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (9, 30, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (10, 30, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (11, 30, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (12, 30, 'actif', '0', '1000', '0');

-- Enchanteurs
INSERT INTO `batiment_ville` (`id`, `nom`, `cout`, `entretien`, `type`, `level`, `hp`) VALUES (35, 'Enchanteur', 0, 0, 'enchanteur', 1, 1000);
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (1, 35, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (2, 35, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (3, 35, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (4, 35, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (6, 35, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (7, 35, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (8, 35, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (9, 35, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (10, 35, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (11, 35, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (12, 35, 'actif', '0', '1000', '0');