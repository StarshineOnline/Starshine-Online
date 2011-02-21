INSERT INTO `achievement_type` (`id`, `nom`, `description`, `value`, `variable`, `secret`, `strong`, `color`) VALUES (null, 'Piétaille', 'Être rang 2', '0', 'rang_2', '0', '0', NULL), (null, 'Vétéran', 'Être rang 3', '0', 'rang_3', '0', '0', NULL), (null, 'Spec ops', 'Être rang 4', '0', 'rang_4', '0', '0', NULL);

INSERT INTO `achievement` (`id_perso`, `id_achiev`) SELECT perso.`ID`, (select id from achievement_type where `variable` = 'rang_2') id_achiev FROM `perso`, classe WHERE perso.classe = classe.nom and classe.rang > 1;

INSERT INTO `achievement` (`id_perso`, `id_achiev`) SELECT perso.`ID`, (select id from achievement_type where `variable` = 'rang_3') id_achiev FROM `perso`, classe WHERE perso.classe = classe.nom and classe.rang > 2;

INSERT INTO `achievement` (`id_perso`, `id_achiev`) SELECT perso.`ID`, (select id from achievement_type where `variable` = 'rang_4') id_achiev FROM `perso`, classe WHERE perso.classe = classe.nom and classe.rang > 3;
