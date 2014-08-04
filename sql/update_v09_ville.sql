ALTER TABLE `hotel` DROP `nombre`;
ALTER TABLE `hotel` ADD `type` ENUM('vente', 'achat') NOT NULL DEFAULT 'vente' AFTER `id_vendeur`;

UPDATE `starshine_preprod`.`classe` SET `nom` = 'Dresseur de l''ombre' WHERE `classe`.`id` = 27;
UPDATE `starshine_preprod`.`classe` SET `nom` = 'Archer d''élite' WHERE `classe`.`id` = 11;