UPDATE arme SET melee = 1 WHERE id = 1; --- Couteau accessible dès le début pour le tutoriel
INSERT INTO `comp_jeu` (`nom`, `description`, `mp`, `pa`, `type`, `comp_assoc`, `carac_assoc`, `carac_requis`, `comp_requis`, `arme_requis`, `effet`, `effet2`, `duree`, `cible`, `requis`, `prix`, `lvl_batiment`) VALUES ('Cri du débutant', 'Augmente l''esquive de %effet%%.', 50, 4, 'buff_cri_detresse', 'melee', 'force', 0, 1, '', '5', 0, 1800, 3, '', 0, 1);

--- Point de victoires de bâtiments
UPDATE batiment SET point_victoire = 5 WHERE id = 2;
UPDATE batiment SET point_victoire = 10 WHERE id = 3;
UPDATE batiment SET point_victoire = 20 WHERE id = 4;
UPDATE batiment SET point_victoire = 4 WHERE id = 10;
UPDATE batiment SET point_victoire = 8 WHERE id = 11;
UPDATE batiment SET point_victoire = 12 WHERE id = 12;

--- Calqes pour le tutoriel humain
INSERT INTO map_type_calque (type, calque, nom) VALUES (386, 'tutorial/humains/calquesup_chat_croisement_haut.png', 'chat_croisement_haut'), (387, 'tutorial/humains/calquesup_chateau_horiz_pass_bas.png', 'chateau_horiz_pass_bas');
