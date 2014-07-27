ALTER TABLE `hotel` DROP `nombre`;
ALTER TABLE `hotel` ADD `type` ENUM('vente', 'achat') NOT NULL DEFAULT 'vente' AFTER `id_vendeur`;