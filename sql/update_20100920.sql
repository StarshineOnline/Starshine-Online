INSERT INTO `bataille_repere_type` (`id`, `nom`, `description`, `ajout_groupe`, `image`) VALUES
(5, 'Attaque', 'Ordonner à un ou plusieurs groupe d''attaquer une position, des ennemis, des batiments...', 0, ''),
(6, 'Défense', 'Ordonner à un ou plusieurs groupe de défendre une position, des alliés, des bâtiments...', 0, ''),
(7, 'Déplacement', 'Ordonner à un ou plusieurs groupe de se rendre à la position indiquée.', 0, ''),
(8, 'Réparation', 'Ordonner à un ou plusieurs groupe de réparer un bâtiment.', 0, ''),
(9, 'Construction', 'Ordonner à un ou plusieurs groupe de construire un batiment, une arme 

ALTER TABLE `bataille_groupe` ADD `id_thread` INT NOT NULL