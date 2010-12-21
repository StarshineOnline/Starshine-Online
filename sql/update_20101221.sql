INSERT INTO `achievement_type` (`id` ,`nom` ,`description` ,`value` ,`variable` ,`secret`)
VALUES 
(NULL , 'Peace through superior firepower', 'Avoir %value% armes de siège dans son inventaire.', '10', 'nbr_arme_siege', '1'), 
(NULL , 'Arsenal', 'Avoir %value% armes de sièges dans son inventaire.', '15', 'nbr_arme_siege', '1'), 
(NULL , 'Mecha', 'Avoir %value% armes de sièges dans son inventaire.', '20', 'nbr_arme_siege', '1'),
(NULL, 'All your base are belong to us', 'Avoir posé %value% drapeaux.', '1000', 'pose_drapeaux', '0'), 
(NULL, 'Maginot', 'Poser %value% murs.', '100', 'pose_murs', '0'),
(NULL, 'Drogué', 'Avoir utilisé %value% potions.', '500', 'use_potion', '0'),
(NULL, 'Leet', 'Avoir %value% PV.', '1337', 'hp_1337', '1'), 
(NULL, 'God''s busy, can I help ya ?', 'Avoir %value% PV.', '666', 'hp_666', '1'), 
(NULL, 'La réponse ultime', 'Avoir %value% PV.', '42', 'hp_42', '1'), 
(NULL, 'Last stand', 'Avoir %value% PV.', '1', 'hp_1', '1');
