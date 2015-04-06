ALTER TABLE `hotel` DROP `nombre`;
ALTER TABLE `hotel` ADD `type` ENUM('vente', 'achat') NOT NULL DEFAULT 'vente' AFTER `id_vendeur`;

UPDATE `classe` SET `nom` = 'Dresseur de l''ombre' WHERE `classe`.`id` = 27;
UPDATE `classe` SET `nom` = 'Archer d''élite' WHERE `classe`.`id` = 11;

ALTER TABLE `taverne` CHANGE `ID` `id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT;

-- Rang des capitales
ALTER TABLE `royaume` ADD `rang` TINYINT(4) NOT NULL AFTER `alchimie`;
UPDATE `royaume` SET `rang` = 4 WHERE id > 0;

-- Dresseur
INSERT INTO `batiment_ville` (`id`, `nom`, `cout`, `entretien`, `type`, `level`, `hp`) VALUES (30, 'Dresseur', 0, 0, 'dresseur', 1, 1000);
INSERT INTO `batiment_ville` (`nom`, `cout`, `entretien`, `type`, `level`, `hp`) VALUES ('Dresseur cuivre', 500, 5, 'dresseur', 2, 2000);
INSERT INTO `batiment_ville` (`nom`, `cout`, `entretien`, `type`, `level`, `hp`) VALUES ('Dresseur bronze', 1000, 10, 'dresseur', 3, 4000);
INSERT INTO `batiment_ville` (`nom`, `cout`, `entretien`, `type`, `level`, `hp`) VALUES ('Dresseur argent', 2000, 15, 'dresseur', 4, 6000);
INSERT INTO `batiment_ville` (`nom`, `cout`, `entretien`, `type`, `level`, `hp`) VALUES ('Dresseur or', 4000, 20, 'dresseur', 5, 8000);
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (1, 30, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (2, 30, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (3, 30, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (4, 30, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (6, 30, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (7, 30, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (8, 30, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (9, 30, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (10, 30, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (11, 30, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (12, 30, 'actif', '0', '1000', '0');

-- Enchanteur
INSERT INTO `batiment_ville` (`id`, `nom`, `cout`, `entretien`, `type`, `level`, `hp`) VALUES (35, 'Enchanteur', 0, 0, 'enchanteur', 1, 1000);
INSERT INTO `batiment_ville` (`nom`, `cout`, `entretien`, `type`, `level`, `hp`) VALUES ('Enchanteur bronze', 500, 5, 'enchanteur', 1, 2000);
INSERT INTO `batiment_ville` (`nom`, `cout`, `entretien`, `type`, `level`, `hp`) VALUES ('Enchanteur argent', 1000, 10, 'enchanteur', 1, 4000);
INSERT INTO `batiment_ville` (`nom`, `cout`, `entretien`, `type`, `level`, `hp`) VALUES ('Enchanteur or', 2000, 20, 'enchanteur', 1, 6000);
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (1, 35, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (2, 35, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (3, 35, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (4, 35, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (6, 35, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (7, 35, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (8, 35, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (9, 35, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (10, 35, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (11, 35, 'actif', '0', '1000', '0');
INSERT INTO `construction_ville` (`id_royaume`, `id_batiment`, `statut`, `dette`, `hp`, `date`) VALUES (12, 35, 'actif', '0', '1000', '0');

-- Rumeurs du bar
CREATE TABLE IF NOT EXISTS `rumeurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(30) NOT NULL,
  `royaumes` smallint(5) unsigned DEFAULT '65535',
  `etape_quete` int(11) NOT NULL DEFAULT '0',
  `texte` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=135 ;

--
-- Contenu de la table `rumeurs`
--

INSERT INTO `rumeurs` (`id`, `type`, `royaumes`, `etape_quete`, `texte`) VALUES
(1, 'perso-star', 65535, 0, '- Il parait que %nom% est riche.'),
(2, 'perso-melee', 65535, 0, '- Il parait que %nom% est un bon escrimeur.'),
(3, 'perso-esquive', 65535, 0, '- Il parait que %nom% est difficile à toucher.'),
(4, 'perso-blocage', 65535, 0, '- Il parait que %nom% manie bien le bouclier.'),
(5, 'perso-distance', 65535, 0, '- Il parait que %nom% vise très bien.'),
(6, 'perso-incantation', 65535, 0, '- Il parait que %nom% est un des meilleurs magiciens que l''on connaisse.'),
(7, 'perso-element', 65535, 0, '- Il parait que %nom% est un puissant sorcier'),
(8, 'perso-vie', 65535, 0, '- Il parait que %nom% parle directement avec les dieux.'),
(9, 'perso-mort', 65535, 0, '- Il parait que %nom% contrôle la Mort elle-même.'),
(10, 'perso-survie', 65535, 0, '- Il parait que %nom% peut survivre n''importe où.'),
(11, 'perso-dressage', 65535, 0, '- Il parait que %nom% est un excellent dresseur.'),
(12, 'perso-artisanat', 65535, 0, '- Il parait que %nom% est un très bon artisan.'),
(13, 'perso-honneur', 65535, 0, '- Tiens, tu sais quoi ? J''ai encore entendu parlé des exploits de %nom%. Un vrai aventurier celui-là !'),
(14, 'perso-reputation', 65535, 0, '- C''est vraiment quelqu''un d’impressionnant ce %nom% !'),
(15, 'perso-niveau', 65535, 0, '- Il parait que %nom% est impressionnant sur un champ de bataille.'),
(16, 'perso-pvp', 65535, 0, '- Il parait que %nom% est redoutable en temps de guerre, il a beaucoup de morts à son compteur.'),
(17, 'perso-suicide', 65535, 0, '- Il parait que %nom% est mort beaucoup de fois.'),
(18, 'perso-lieu', 65535, 0, '- Il parait qu''on a vu %nom% près de %lieu%.'),
(19, 'royaume-stock-pierre', 65535, 0, '- Il parait que %nom% a beaucoup de pierre.'),
(20, 'royaume-stock-bois', 65535, 0, '- Il parait que %nom% a beaucoup de bois.'),
(21, 'royaume-stock-eau', 65535, 0, '- Il parait que %nom% a beaucoup d''eau.'),
(22, 'royaume-stock-sable', 65535, 0, '- Il parait que %nom% a beaucoup de sable.'),
(23, 'royaume-stock-charbon', 65535, 0, '- Il parait que %nom% a beaucoup de charbon.'),
(24, 'royaume-stock-essence', 65535, 0, '- Il parait que %nom% a beaucoup d''essence magique.'),
(25, 'royaume-stock-nourriture', 65535, 0, '- Il parait que %nom% a beaucoup de nourriture.'),
(26, 'royaume-fstock-nourriture', 65535, 0, '- Il parait que %nom% a peu de nourriture.'),
(27, 'royaume-fstock-essence', 65535, 0, '- Il parait que %nom% a peu d''essence magique.'),
(28, 'royaume-fstock-charbon', 65535, 0, '- Il parait que %nom% a peu de charbon.'),
(29, 'royaume-fstock-sable', 65535, 0, '- Il parait que %nom% a peu de sable.'),
(30, 'royaume-fstock-eau', 65535, 0, '- Il parait que %nom% a peu d''eau.'),
(31, 'royaume-fstock-bois', 65535, 0, '- Il parait que %nom% a peu de bois.'),
(32, 'royaume-fstock-pierre', 65535, 0, '- Il parait que %nom% a peu de pierre.'),
(33, 'royaume-prod-nourriture', 65535, 0, '- Il parait que %nom% produit beaucoup de nourriture.'),
(34, 'royaume-prod-essence', 65535, 0, '- Il parait que %nom% produit beaucoup d''essence magique.'),
(35, 'royaume-prod-charbon', 65535, 0, '- Il parait que %nom% produit beaucoup de charbon.'),
(36, 'royaume-prod-sable', 65535, 0, '- Il parait que %nom% produit beaucoup de sable.'),
(37, 'royaume-prod-eau', 65535, 0, '- Il parait que %nom% produit beaucoup d''eau.'),
(38, 'royaume-prod-bois', 65535, 0, '- Il parait que %nom% produit beaucoup de bois.'),
(39, 'royaume-prod-pierre', 65535, 0, '- Il parait que %nom% produit beaucoup de pierre.'),
(40, 'royaume-fprod-nourriture', 65535, 0, '- Il parait que %nom% produit peu de nourriture.'),
(41, 'royaume-fprod-essence', 65535, 0, '- Il parait que %nom% produit peu d''essence magique.'),
(42, 'royaume-fprod-charbon', 65535, 0, '- Il parait que %nom% produit peu de charbon.'),
(43, 'royaume-fprod-sable', 65535, 0, '- Il parait que %nom% produit peu de sable.'),
(44, 'royaume-fprod-eau', 65535, 0, '- Il parait que %nom% produit peu d''eau.'),
(45, 'royaume-fprod-bois', 65535, 0, '- Il parait que %nom% produit peu de bois.'),
(46, 'royaume-fprod-pierre', 65535, 0, '- Il parait que %nom% produit peu de pierre.'),
(47, 'royaume-pv', 65535, 0, '- Il parait que %nom% est très puissant.'),
(48, 'royaume-pv-peu', 65535, 0, '- Il parait que %nom% est faible.'),
(49, 'royaume-alchimie', 65535, 0, '- Il parait que %nom% a de bons alchimiste.'),
(50, 'royaume-alchimie-peu', 65535, 0, '- Il parait que %nom% n''a pas de grandes capacités en alchimie.'),
(51, 'royaume-batint', 65535, 0, '- Il parait que %lieu% est une grande ville.'),
(52, 'royaume-batint-peu', 65535, 0, '- Il parait que %lieu% est une petite ville.'),
(53, 'royaume-bourg', 65535, 0, '- Il parait que %nom% est un royaume très urbain.'),
(54, 'royaume-bourg-peu', 65535, 0, '- Il parait que %nom% un royaume très rural.'),
(55, 'royaume-conso_food', 65535, 0, '- Il parait que %nom% consomme beaucoup de nourriture.'),
(56, 'royaume-conso_food-peu', 65535, 0, '- Il parait que %nom% consomme peu de nourriture.'),
(57, 'royaume-entretien', 65535, 0, '- Il parait que %nom% dépense beaucoup en entretien.'),
(58, 'royaume-entretien-peu', 65535, 0, '- Il parait que %nom% dépense peu en entretien.'),
(59, 'royaume-batint-bon', 65535, 0, '- Il parait que %nom% de %lieu% est en bon état.'),
(60, 'royaume-batint-mauvais', 65535, 0, '- Il parait que %nom% de %lieu% est en mauvais état.'),
(61, 'royaum-taxe', 65535, 0, '- Il parait que %nom% a de fortes taxes.'),
(62, 'royaum-taxe-peu', 65535, 0, '- Il parait que %nom% a de faibles taxes.'),
(63, 'royaume-conso-monte', 65535, 0, '- Il parait que la consommation en nourriture de %nom% devrait augmenter.'),
(64, 'royaume-conso-baisse', 65535, 0, '- Il parait que la consommation en nourriture de %nom% devrait diminuer.'),
(65, 'royaume-conso-stable', 65535, 0, '- Il parait que la consommation en nourriture de %nom% devrait rester stable.'),
(66, 'royaume-entretien-stable', 65535, 0, '- Il parait que l''entretien de %nom% devrait rester stable.'),
(67, 'royaume-entretien-monte', 65535, 0, '- Il parait que l''entretien de %nom% devrait augmenter.'),
(68, 'royaume-entretien-baisse', 65535, 0, '- Il parait que l''entretien de %nom% devrait diminuer.'),
(69, 'perso-crime', 65535, 0, '- Il parait que %nom% est un vrai criminel.'),
(70, 'groupe-honneur', 65535, 0, '- Tu connais "%nom%" ? Il parait qu''ils ont fait beaucoup d''exploits ces derniers temps.'),
(71, 'groupe-reputation', 65535, 0, '- Tu connais "%nom%" ? Il parait qu''ils sont très réputés.'),
(72, 'groupe-niveau', 65535, 0, '- Tu connais "%nom%" ? Il parait qu''ils sont très forts.'),
(73, 'groupe-star', 65535, 0, '- Tu connais "%nom%" ? Il parait qu''ils sont très riches.'),
(74, 'groupe-pvp', 65535, 0, '- Tu connais "%nom%" ? Il parait qu''ils sont tués beaucoup d''ennemis.'),
(75, 'groupe-suicide', 65535, 0, '- Tu connais "%nom%" ? Il parait qu''ils sont souvent morts.'),
(76, 'groupe-criminel', 65535, 0, '- Tu connais "%nom%" ? Il parait que se sont des criminels.'),
(77, 'groupe-survie', 65535, 0, '- Tu connais "%nom%" ? Il parait qu''ils peuvent survivre n''importe où.'),
(78, 'groupe-artisanat', 65535, 0, '- Tu connais "%nom%" ? Il parait que se sont de très bons artisans.'),
(79, 'groupe-vie', 65535, 0, '- Tu connais "%nom%" ? Il parait qu''ils sont difficiles à tuer.'),
(80, 'groupe-mana', 65535, 0, '- Tu connais "%nom%" ? Il parait qu''ils ont de bonnes capacités magiques.'),
(81, 'royaume-point_victoire', 65535, 0, '- Il parait que %nom% a beaucoup de ressources.'),
(82, 'royaume-point_victoire-peu', 65535, 0, '- Il parait que %nom% a peu de ressources.'),
(83, 'royaume-case', 65535, 0, '- Il parait que %nom% est très grand.'),
(84, 'royaume-case-peu', 65535, 0, '- Il parait que %nom% est assez petit.'),
(85, 'royaume-niveau', 65535, 0, '- Il parait que les %race% sont très expérimentés.'),
(86, 'royaume-niveau-peu', 65535, 0, '- Il parait que les %race% sont peu expérimentés.'),
(87, 'royaume-honneur', 65535, 0, '- Ils parait que les %race% ont fait beaucoup d''exploits ces derniers temps.'),
(88, 'royaume-honneur-peu', 65535, 0, '- Ils parait que les %race% n''ont pas fait beaucoup d''exploits ces derniers temps.'),
(89, 'royaume-reputation', 65535, 0, '- Il parait que les %race% sont très valeureux.'),
(90, 'royaume-reputation-peu', 65535, 0, '- Il parait que les %race% n''ont jamais fait grand chose'),
(91, 'royaume-pvp', 65535, 0, '- Il parait que les %race% sont redoutables, ils ont beaucoup de victimes à leur actifs.'),
(92, 'royaume-pvp-peu', 65535, 0, '- Il parait que les %race% ne sont pas très dangereux, ils n''ont pas fait de mal à grand monde.'),
(93, 'royaume-suicide', 65535, 0, '- Il parait que les %race% sont tombent comme des mouches.'),
(94, 'royaume-suicide-peu', 65535, 0, '- Il parait que les %race% sont intuables.'),
(95, 'royaume-crime', 65535, 0, '- Il parait que %nom% est rempli de criminels.'),
(96, 'royaume-crime-peu', 65535, 0, '- Il parait que les %race% sont intègres.'),
(97, 'stats-classe', 65535, 0, '- Il parait qu''il y a beaucoup de %nom%.'),
(98, 'stats-classe-peu', 65535, 0, '- Il parait qu''il y a peu de %nom%.'),
(99, 'stats-race', 65535, 0, '- Il parait que les %race% sont nombreux.'),
(100, 'stats-race-peu', 65535, 0, '- Il parait que les %race% sont peu nombreux.'),
(101, 'stats-star', 65535, 0, '- Il parait que %nom% est riche.'),
(102, 'stats-star-peu', 65535, 0, '- Il parait que %nom% est pauvre.'),
(103, 'diplomatie', 65535, 0, '- Il parait que %nom% et %nom2% sont %diplo%.'),
(104, 'conversation', 65535, 0, '[i]Vous ne comprenez rien à travers tout ce brouhaha.[/i]'),
(105, 'monstre-plaine', 65535, 0, '- Il parait qu''il y a beaucoup de %nom% en plaine.'),
(106, 'monstre-plaine-peu', 65535, 0, '- Il parait qu''il y a peu de %nom% en plaine.'),
(107, 'monstre-foret', 65535, 0, '- Il parait qu''il y a beaucoup de %nom% en forêt.'),
(108, 'monstre-foret-peu', 65535, 0, '- Il parait qu''il y a peu de %nom% en forêt.'),
(109, 'monstre-desert', 65535, 0, '- Il parait qu''il y a beaucoup de %nom% en désert.'),
(110, 'monstre-desert-peu', 65535, 0, '- Il parait qu''il y a peu de %nom% en désert.'),
(111, 'monstre-glace', 65535, 0, '- Il parait qu''il y a beaucoup de %nom% sur la banquise.'),
(112, 'monstre-glace-peu', 65535, 0, '- Il parait qu''il y a peu de %nom% sur la banquise.'),
(113, 'monstre-montagne', 65535, 0, '- Il parait qu''il y a beaucoup de %nom% en montagne.'),
(114, 'monstre-montagne-peu', 65535, 0, '- Il parait qu''il y a peu de %nom% en montagne.'),
(115, 'monstre-marais', 65535, 0, '- Il parait qu''il y a beaucoup de %nom% dans les marais.'),
(116, 'monstre-marais-peu', 65535, 0, '- Il parait qu''il y a peu de %nom% dans les marais.'),
(117, 'monstre-terre_maudite', 65535, 0, '- Il parait qu''il y a beaucoup de %nom% en terre maudite.'),
(118, 'monstre-terre_maudite-peu', 65535, 0, '- Il parait qu''il y a peu de %nom% en terre maudite.'),
(119, 'monstre', 65535, 0, '- Il parait qu''il y a beaucoup de %nom%.'),
(120, 'monstre-peu', 65535, 0, '- Il parait qu''il y a peu de %nom%.'),
(121, 'monstre-royaume', 65535, 0, '- Il parait qu''il y a beaucoup de %nom% dans le royaume.'),
(122, 'monstre-royaume-peu', 65535, 0, '- Il parait qu''il y a peu de %nom% dans le royaume.'),
(123, 'monstre-prop', 65535, 0, '- Il parait que les %nom% sont très présents dans leur territoire.'),
(124, 'monstre-prop-peu', 65535, 0, '- Il parait que les %nom% sont peu présents dans leur territoire.'),
(125, 'royaume-fort', 65535, 0, '- Il parait que %nom% est un royaume très fortifié.'),
(126, 'royaume-fort-peu', 65535, 0, '- Il parait que %nom% un royaume très peu fortifié.'),
(127, 'royaume-arme_de_siege', 65535, 0, '- Il parait que %nom% est un royaume très belliqueux.'),
(128, 'royaume-arme_de_siege-peu', 65535, 0, '- Il parait que %nom% un royaume plutôt pacifique.'),
(129, 'royaume-mur', 65535, 0, '- Il parait que %nom% est un royaume très défensif.'),
(130, 'royaume-mur-peu', 65535, 0, '- Il parait que %nom% un royaume mal défendu.'),
(131, 'royaume-tour', 65535, 0, '- Il parait que %nom% a de bons éclaireurs.'),
(132, 'royaume-tour-peu', 65535, 0, '- Il parait que %nom% ne sait jamais ce qu''il se passe sur son territoire.'),
(133, 'royaume-mine', 65535, 0, '- Il parait que %nom% a une bonne économie.'),
(134, 'royaume-mine-peu', 65535, 0, '- Il parait que %nom% ne produit pas grand chose.');
