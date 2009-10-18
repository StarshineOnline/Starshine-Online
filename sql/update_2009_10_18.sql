-- Modification de batiment : pour les mines, bonus7 devient ce en quoi on peut upgrade
-- ils se suivent
UPDATE `batiment` SET `bonus7` = '25' WHERE `id` =24 LIMIT 1 ;
UPDATE `batiment` SET `bonus7` = '26' WHERE `id` =25 LIMIT 1 ;

-- ils sont dans l'ordre
UPDATE `batiment` SET `bonus7` = `id` + 8 WHERE `id` >= 27 and `id` <= 34 ;

-- ils sont dans l'ordre inverse
UPDATE `batiment` SET `bonus7` = `id` + 2 * (43 - `id`) - 1 WHERE `id` >= 35 and `id` <= 42 ;
