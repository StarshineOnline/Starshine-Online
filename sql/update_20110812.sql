-- -- bestioles à invoquer (sorts rang 4 dresseurs)
-- comptence d'invocation
INSERT INTO `comp_jeu` 
(`id`, `nom`, `description`, `mp`, `pa`, `type`, `comp_assoc`, `carac_assoc`, `carac_requis`, `comp_requis`, `arme_requis`, `effet`, `effet2`, `duree`, `cible`, `requis`, `prix`, `lvl_batiment`) 
VALUES 
(NULL, 'Invocation', 'Invoque une créature dépendante du lanceur.', '150', '10', 'invocation_pet', 'dressage', 'dexterite', '0', '0', '', '0', '0', '0', '1', '999', '0', '9');

-- don de la comp
insert into `classe_comp_permet` (id_classe, competence, type) 
select id, (select id from comp_jeu where nom = 'invocation'), 'comp_jeu' 
from classe where id in (34, 35, 36, 37);

-- les bêbêtes
-- base de gros monstre un peu boosté, puis HP / 5
INSERT INTO `monstre` (`id`, `lib`, `nom`, `type`, `hp`, `pp`, `pm`, `forcex`, `dexterite`, `puissance`, `volonte`, `energie`, `melee`, `esquive`, `incantation`, `sort_vie`, `sort_mort`, `dressage`, `sort_dressage`, `sort_element`, `arme`, `action`, `level`, `xp`, `star`, `drops`, `spawn`, `spawn_loc`, `terrain`, `affiche`, `description`, `sort_combat`, `comp_combat`) VALUES 
-- base de minautore
(NULL, 'lapin', 'Mammouth', 'bete', 3000 / 5, '2800', '1000', '84', '12', '15', '10', '15', '620', '500', '0', '0', '0', '99999', 's87', '0', 'epee', '#14<5@_6;#14<5@_52', '0', '0', '0', '', '0', '', '0', 'y', 'Un mammouth', '', '5;6;9;10;21;22;25;44;45;46;47;48;49;53;54;80;81;98;99;101;102;108'),
-- base de gorgone
(NULL, 'lapin', 'Ange', 'magique', 2000/ 5, '2000', '2000', '70', '30', '30', '30', '20', '600', '600', '550', '550', '300', '99999', 's89', '300', 'epee', '#14<4@_81;#14<4@_79;#14<4@_85', '0', '0', '0', '', '0', '', '0', 'y', '', '--TODO--', '--TODO--'),
-- base de banshee
(NULL, 'lapin', 'Démon majeur', 'magique', 1500 / 5, '3000', '3000', '40', '40', '30', '30', '20', '400', '400', '400', '100', '500', '99999', 's74', '400', 'epee', '#11°appel_tenebre@~68;#14<5@~133;#>@~94', '0', '0', '0', '', '0', '', '0', 'y', '', '--TODO--', '5;6;9;10;21;22;25;44;45;46;47;48;49;53'),
-- base d'Elémentaire de feu beaucoup boosté
(NULL, 'lapin', 'Élémentaire noble', 'magique', 1800 / 5, '700', '1000', '35', '35', '32', '32', '32', '400', '400', '500', '300', '300', '99999', 's77', '400', 'epee', '#14<4@~10;#14<3@~149;#>@~6;#>@~13', '0', '0', '0', '', '0', '', '0', 'y', '', '', '');


-- -- Sorts de rang 4 pour derviche et prédateur
INSERT INTO `sort_jeu` (`id`, `nom`, `description`, `pa`, `mp`, `type`, `comp_assoc`, `carac_assoc`, `carac_requis`, `incantation`, `comp_requis`, `effet`, `effet2`, `duree`, `cible`, `portee`, `requis`, `difficulte`, `prix`, `lvl_batiment`, `special`) VALUES 
-- voir pour les difficultés/incantation ...
(NULL, 'Libération', '(Personnel) Supprime un buff, et vous redonne %effet%% de vos PV.', '5', '15', 'liberation', 'sort_element', 'volonte', '200', '1', '0', '7', '0', '0', '1', '2', 'classe:derviche', '1', '0', '99', '1');

