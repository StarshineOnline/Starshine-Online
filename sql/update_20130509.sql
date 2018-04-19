INSERT INTO armure VALUES (NULL, 'Jambières de traqueur', 40, 40, 12, 'jambe', '', 25000, 9);
INSERT INTO armure VALUES (NULL, 'Peau de troll', 200, 100, 16, 'dos', '26-10', 25000, 9);
INSERT INTO arme VALUES (NULL, 'Baton de guérisseur', 'baton', '2', '0', '250', '0', '1', 'main_droite;main_gauche', '35', '27-1', '25000', '9', '');
INSERT INTO arme VALUES (NULL, 'Bouclier de pensée', 'bouclier', '0', '10', '175', '0', '0', 'main_gauche', '', '27', '25000', '9', '');
INSERT INTO arme VALUES (NULL, 'Arc Tung', 'arc', '10', '12', '0', '350', '1', 'main_droite;main_gauche', '', '28', '50000', '9', '');

ALTER TABLE `armure` ADD `puissance` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `forcex` ;
INSERT INTO armure VALUES (NULL, 'Bottes de Marcheur', '10', '0', '0', '14', 'pied', '29-10', '25000', '9');
