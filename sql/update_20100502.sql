ALTER TABLE `map_zone`
ADD `ordre` TINYINT NOT NULL DEFAULT '0'
COMMENT 'Priorité du calque par rapport aux autre (si chevauchment)';

ALTER TABLE `options` add UNIQUE KEY `id_perso` (`id_perso`,`nom`) ;
