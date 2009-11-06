CREATE TABLE IF NOT EXISTS `monstre_special` (
  `type` int(11) NOT NULL COMMENT 'type du monstre',
  `pop_type` int(11) default NULL COMMENT 'type du monstre a poper',
  `pop_x` int(11) default NULL COMMENT 'position du pop',
  `pop_y` int(11) default NULL COMMENT 'position du pop',
  `texte` varchar(255) default NULL COMMENT 'texte à afficher',
  `condition_sql` varchar(255) default NULL COMMENT 'requete SQL qui doit retourner au moins une ligne pour valider',
  `non_condition_sql` varchar(255) default NULL COMMENT 'requête SQL qui ne doit pas retourner de ligne pour valider',
  `eval_condition` varchar(255) default NULL COMMENT 'code PHP qui doit retourner vrai pour valider',
  `eval_action` varchar(255) default NULL COMMENT 'code PHP a executer en cas de validation',
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Gestion des kills de monstre speciaux';

--
-- Contenu de la table `monstre_special`
--

INSERT INTO `monstre_special` (`type`, `pop_type`, `pop_x`, `pop_y`, `texte`, `condition_sql`, `non_condition_sql`, `eval_condition`, `eval_action`) VALUES
(64, 65, 3, 212, 'Rha, tu me détruis aujourdhui mais le fossoyeur saura saisir ton âme... tu es déja mort !', NULL, NULL, NULL, NULL),
(65, 75, 24, 209, 'Tu ne fait que retarder l''inévitable, Le maître saura te faire payer ton insolence !', NULL, NULL, NULL, NULL),
(75, 116, 24, 209, 'Aaaargh VAINCU, JE SUIS VAINCU, comment est ce possible !!! Maître !! Maître venez à moi, vengez votre plus fidèle serviteur !!!', NULL, NULL, NULL, NULL),
(125, 123, 44, 293, 'Un bruit de mécanisme eveille votre attention, mais il vous est impossible de savoir d''où provient ce son.', NULL, 'SELECT type FROM map_monstre WHERE type IN (125, 126)', NULL, NULL),
(126, 123, 44, 293, 'Un bruit de mécanisme eveille votre attention, mais il vous est impossible de savoir d''où provient ce son.', NULL, 'SELECT type FROM map_monstre WHERE type IN (125, 126)', NULL, NULL);
