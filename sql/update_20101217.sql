--
-- Structure de la table `achievement`
--

DROP TABLE IF EXISTS `achievement`;
CREATE TABLE IF NOT EXISTS `achievement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_perso` int(11) NOT NULL,
  `id_achiev` int(11) NOT NULL,
  PRIMARY KEY (`id`)
);

--
-- Structure de la table `achievement_compteur`
--

DROP TABLE IF EXISTS `achievement_compteur`;
CREATE TABLE IF NOT EXISTS `achievement_compteur` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_perso` int(11) NOT NULL,
  `variable` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `compteur` int(11) NOT NULL,
  PRIMARY KEY (`id`)
);

--
-- Structure de la table `achievement_type`
--

DROP TABLE IF EXISTS `achievement_type`;
CREATE TABLE IF NOT EXISTS `achievement_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` int(11) NOT NULL,
  `variable` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
);

--
-- Contenu de la table `achievement_type`
--

INSERT INTO `achievement_type` (`id`, `nom`, `description`, `value`, `variable`) VALUES
(1, 'Vocation raté', 'En étant combattant, avoir acheté %value% affinités à l''école de magie', 3, 'type_magie'),
(2, 'Bob le bricoleur', 'Avoir réparé/construit plus de %value% points de structure', 50000, 'structure_hp'),
(3, 'Demolition Man', 'Avoir infligé %value% point de dégats a des bâtiments/arme de siège', 50, 'structure_degats'),
(4, 'La taille n''est rien', 'Avoir tué %value% joueurs de niveau plus élevé', 1000, 'kill_higher'),
(5, 'Racaille des bacs à sable', 'Avoir tué %value% joueurs de niveau plus faible', 1000, 'kill_lower'),
(6, 'Tuer n''est pas jouer', 'Avoir tué %value% joueurs de sa race', 50, 'kill_race'),
(7, 'Psychopathe', 'Avoir tué %value% joueurs de sa race', 200, 'kill_race'),
(8, 'Life Note', 'Avoir ressuscité %value% personnes', 1500, 'rez'),
(9, 'Docteur Jekyll', 'Avoir lancé %value% buff', 2000, 'buff'),
(10, 'Mister Hyde', 'Avoir lancé %value% debuff', 2000, 'debuff'),
(11, 'Pleasure man/woman', 'Avoir eu %value% bonus après être allé à la taverne', 250, 'taverne_bonus'),
(12, 'Mauvais coup', 'Avoir eu %value% malus après être allé à la taverne', 250, 'taverne_malus'),
(13, 'Soldat du roi', 'Avoir participé à %value% batailles', 100, 'bataille'),
(14, 'J''y vends di slips quasi-neufs', 'Avoir vendu %value% objets à l''hôtel des ventes', 1000, 'objets_vendus'),
(15, 'Commerçant', 'Avoir échangé  %value% objets avec d''autres joueurs', 1000, 'objets_echanges'),
(16, 'Contrebandier', 'Avoir échangé %value% objets avec des ennemis', 500, 'objets_echanges_ennemis'),
(17, 'Chimiste', 'Avoir crée %value% potions', 500, 'craft_potion'),
(18, 'Forgeron', 'Avoir slotté avec succès %value% objets', 1000, 'objets_slot'),
(19, 'Dufhandal', 'Avoir enchâssé avec succès %value% objets', 1000, 'objets_slotted'),
(20, 'Ennemi de Brigitte Bardot', 'Avoir tué %value% monstres', 10000, 'kill_monstres'),
(21, 'Catastrophe écologique', 'Avoir tué %value% monstres', 20000, 'kill_monstres'),
(22, 'Adrienne !', 'Avoir tué %value% joueurs à main nue', 100, 'kill_poing'),
(23, 'Come on get some', 'Avoir tué %value% joueurs en défense', 500, 'kill_defense'),
(24, 'My art is bang', 'S''être suicidé %value% fois en utilisant sacrifice morbide', 200, 'suicide_morbide'),
(25, 'Démocra-quoi ?', 'Avoir été roi %value% an', 1, 'duree_roi'),
(26, 'C''est un beau jour pour mourir', 'Etre mort plus de %value% fois dans la même journée', 10, 'mort_jour'),
(34, 'Couché le chien', 'Avoir tué un Seigneur Loup-Garou', 1, ''),
(35, 'Mon royaume pour une bière...j''ai eu ma bière', 'Avoir été roi', 1, ''),
(36, 'I love the smell of napalm in the morning', 'Avoir détruit une capitale', 1, ''),
(39, 'I have what it takes to be a citizen', 'Avoir tué %value% joueurs', 500, 'kill'),
(40, 'Killing Machine', 'Avoir tué %value% joueurs', 1000, 'kill'),
(41, 'Chuck Norris padawan', 'Avoir tué %value% joueurs.', 5000, 'kill'),
(42, 'Croque Mort', 'Etre mort %value% fois.', 500, 'mort'),
(43, 'La mort n''existe pas, il n''y a que la Force', 'Etre mort %value% fois.', 1000, 'mort'),
(44, 'Cresus', 'Avoir plus de %value% star', 100000, 'stars'),
(45, 'Haut Ratio', 'Ratio kill/mort > %value%', 5, 'ratio'),
(46, 'Actionnaire de la Poste', 'Avoir plus de %value% messages dans sa messagerie.', 500, 'messages');
