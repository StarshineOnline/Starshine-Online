INSERT INTO `bataille_repere_type` (`id`, `nom`, `description`, `ajout_groupe`, `image`) VALUES
(5, 'Attaque', 'Ordonner � un ou plusieurs groupe d''attaquer une position, des ennemis, des batiments...', 0, ''),
(6, 'D�fense', 'Ordonner � un ou plusieurs groupe de d�fendre une position, des alli�s, des b�timents...', 0, ''),
(7, 'D�placement', 'Ordonner � un ou plusieurs groupe de se rendre � la position indiqu�e.', 0, ''),
(8, 'R�paration', 'Ordonner � un ou plusieurs groupe de r�parer un b�timent.', 0, ''),
(9, 'Construction', 'Ordonner � un ou plusieurs groupe de construire un batiment, une arme 

ALTER TABLE `bataille_groupe` ADD `id_thread` INT NOT NULL