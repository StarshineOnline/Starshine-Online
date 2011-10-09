-- blouse du gobelin artiste
INSERT INTO `armure` (`id`, `nom`, `PP`, `PM`, `forcex`, `type`, `effet`, `prix`, `lvl_batiment`) VALUES (NULL, 'Blouse du gobelin', '80', '30', '10', 'torse', '13-gobelin', '50000', '9');

-- correction
UPDATE `armure` SET `nom` = 'Déguisement de maraudeur' WHERE `nom` like 'Déguisement de coyo%';

-- truite
INSERT INTO `objet` (`id`, `nom`, `type`, `prix`, `achetable`, `stack`, `utilisable`, `effet`, `description`, `pa`, `mp`) VALUES (NULL, 'Truite des hautes marées', 'objet_quete', '0', 'n', '0', 'n', '0', 'Une truite des hautes marées', '0', '0');
