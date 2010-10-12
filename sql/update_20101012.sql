UPDATE  `bataille_repere_type` SET `image` = 'icone_attack_bataille.png' WHERE `bataille_repere_type`.`id` =5;

UPDATE  `bataille_repere_type` SET `image` = 'icone_defense_bataille.png' WHERE `bataille_repere_type`.`id` =6;

UPDATE  `bataille_repere_type` SET `image` = 'icone_deplacement_bataille.png' WHERE `bataille_repere_type`.`id` =7;

UPDATE  `bataille_repere_type` SET `image` = 'icone_reparation_bataille.png' WHERE `bataille_repere_type`.`id` =8;

UPDATE  `bataille_repere_type` SET `description` = 'Ordonner à un ou plusieurs groupe de construire un bâtiment, une arme de siège, à la position indiquée.',
`image` = 'icone_construction_bataille.png' WHERE `bataille_repere_type`.`id` =9;
