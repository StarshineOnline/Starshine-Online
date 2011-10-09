-- blouse du gobelin artiste
INSERT INTO `armure` (`id`, `nom`, `PP`, `PM`, `forcex`, `type`, `effet`, `prix`, `lvl_batiment`) VALUES (NULL, 'Blouse du gobelin', '80', '30', '10', 'torse', '13-gobelin', '50000', '9');

-- correction
UPDATE `armure` SET `nom` = 'Déguisement de maraudeur' WHERE `nom` like 'Déguisement de coyo%';

