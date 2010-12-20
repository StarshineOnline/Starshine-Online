UPDATE `achievement_type` SET `variable` = 'capitale_detruite' WHERE `id` = 36;
UPDATE `achievement_type` SET `variable` = 'seigneur_loup_garou' WHERE `id` = 34;

INSERT INTO `achievement_type` (`id` ,`nom` ,`description` ,`value` ,`variable` ,`secret`)
VALUES 
(NULL , 'Copy ninja', 'Avoir le meme script de combat que l''adversaire.', '1', 'same_action', '1'), 
(NULL , 'Traitre', 'Tuer un co-équipier.', '1', 'kill_teammate', '1'),
(NULL , 'You mad ?', 'Etre tué à %value% PV près', '1', 'near_kill', '1');
