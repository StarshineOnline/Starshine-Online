INSERT INTO `achievement_type` (`id`, `nom`, `description`, `value`, `variable`, `secret`, `strong`, `color`) VALUES ('87', 'Piétaille', 'Être rang 2', '0', 'rang_2', '0', '0', NULL), ('88', 'Vétéran', 'Être rang 3', '0', 'rang_3', '0', '0', NULL), ('89', 'Spec ops', 'Être rang 4', '0', 'rang_4', '0', '0', NULL);


INSERT INTO `achievement` (`id_perso`, `id_achiev`) SELECT `ID`, 87 FROM `perso` WHERE ( `classe_id` > 2 );

INSERT INTO `achievement` (`id_perso`, `id_achiev`) SELECT `ID`, 88 FROM `perso` WHERE ( `classe_id` > 8 AND `classe_id` < 17 AND `classe_id` <> 15);
INSERT INTO `achievement` (`id_perso`, `id_achiev`) SELECT `ID`, 88 FROM `perso` WHERE ( `classe_id` > 27 AND `classe_id` < 40 );

INSERT INTO `achievement` (`id_perso`, `id_achiev`) SELECT `ID`, 89 FROM `perso` WHERE ( `classe_id` > 33 AND `classe_id` < 40 );
INSERT INTO `achievement` (`id_perso`, `id_achiev`) SELECT `ID`, 89 FROM `perso` WHERE ( `classe_id` > 16 AND `classe_id` < 24 );
