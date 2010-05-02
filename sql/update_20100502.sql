UPDATE TABLE `map_zone`
ADD `ordre` TINYINT NOT NULL DEFAULT '0'
COMMENT 'Priorité du calque par rapport aux autre (si chevauchment)';
