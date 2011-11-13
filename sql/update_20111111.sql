-- modif quête leonard
UPDATE `quete` SET `objectif` = 'a:1:{i:0;O:8:"stdClass":3:{s:5:"cible";s:1:"Q";s:6:"nombre";s:1:"1";s:6:"requis";s:0:"";}}' WHERE `quete`.`nom` = 'Exploration des strates inférieures';

-- modif tp quête leonard
UPDATE `map_event` SET `code` = 'checkTpValidQuest($joueur, 89, 34, 300, true); /* ou false si on ne peut pas redescendre */ ' WHERE `map_event`.`x` =24 AND `map_event`.`y` =399;

-- pnj pute: sous forme d'event map, ce sera plus simple
-- 300 stars, 0 honneur (repaire ^^), 120 HP/PV
INSERT INTO `map_event` (`x`, `y`, `titre`, `description`, `action`, `code`, `sql`) VALUES
(40, 338, 'Prostituée', 'Un prostituée en plein travail, vous pouvez user de ses services pour 300 stars', 'Acheter une passe', 'usePute($joueur, 300, 0, 120, true);', NULL);
