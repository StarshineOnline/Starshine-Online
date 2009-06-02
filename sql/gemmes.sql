-- anciennes gemmes

delete from gemme;

INSERT INTO `gemme` (`id`, `nom`, `type`, `niveau`, `partie`, `enchantement_nom`, `description`, `enchantement_type`, `enchantement_effet`) VALUES
(1, 'Gemme d arme', 'arme', 1, '', 'caillou', 'Augmente les dégats de 1', 'degat', 1),
(2, 'Gemme d arme éclatante', 'arme', 2, '', 'pierre', 'augmente les dégats de 2', 'degat', 2),
(3, 'Gemme d arme parfaite', 'arme', 3, '', 'rocher', 'augmente les dégats de 4', 'degat', 4),
(4, 'Gemme d inspiration', 'accessoire', 1, '', 'Inspiration', 'Augmente la réserve de mana de 1', 'reserve', 1),
(5, 'Gemme de transe', 'accessoire', 2, '', 'Transe', 'Augmente la réserve de mana de 2', 'reserve', 2),
(6, 'Gemme de transcendance', 'accessoire', 3, '', 'transcendance', 'Augmente la réserve de mana de 4', 'reserve', 4),
(7, 'Gemme de la tortue', 'armure', 1, '', 'tortue', 'Augmente la protection physique de 50', 'pp', 50),
(8, 'Gemme de tortue affamée', 'armure', 2, '', 'tortue affamée', 'Augmente la protection physique de 100', 'pp', 100),
(9, 'Gemme de tortue royale', 'armure', 3, '', 'tortue royale', 'Augmente la protection physique de 250', 'pp', 250),
(10, 'Gemme du sage', 'armure', 1, '', 'sage', 'Augmente la protection magique de 20', 'pm', 20),
(11, 'gemme de l''érudit', 'armure', 2, '', 'érudit', 'Augmente la protection magique de 75', 'pm', 75),
(12, 'gemme de l archimage', 'armure', 3, '', 'archimage', 'Augmente la protection magique de 200', 'pm', 200);

-- nouvelles gemmes
INSERT INTO `gemme` (
`nom` ,
`type` ,
`niveau` ,
`partie` ,
`enchantement_nom` ,
`description` ,
`enchantement_type` ,
`enchantement_effet`
)
VALUES (
'Gemme du roc', 'arme', '1', 'bouclier', 'roc', '+1 d''absorbtion des dégats', 'bouclier', '1'
), (
'Gemme de l''épervier', 'arme', '1', 'bouclier', 'epervier', '+10% d''augmentation de la parade', 'blocage', '10'
), (
'Gemme du serpent', 'arme', '1', '', 'serpent', '10% de chance d''empoisonner l''ennemi et lui infliger 1 pt de dégats par round pendant 5 rounds', 'poison', '10;1'
), (
'Gemme d''épine', 'arme', '1', 'bouclier', 'epine', '1 dégat effectué à chaque parade réussie', 'bouclier_epine', '1'
), (
'Gemme du vampire', 'arme', '1', '', 'vampire', '30% de chance de drainer 1 pv sur une attaque réussie ', 'vampire', '1'
), (
'Gemme du drake', 'arme', '1', '', 'drake', '+1 aux dégats des sorts', 'degat_magie', '1'
), (
'Gemme du singe', 'armure', '1', '', 'singe', '+10 à la compétence esquive', 'esquive', '10'
), (
'Gemme de la fabrique', 'armure', '1', '', 'fabrique', '+15% de chances de réussir la fabrication d''objet', 'forge', '15'
), (
'Gemme de la pierre', 'armure', '1', '', 'pierre', '+10 à la compétence blocage', 'blocage', '10'
), (
'Gemme de l''ours', 'armure', '1', '', 'ours', '+5% en PP', 'pourcent_pp', '5'
), (
'Gemme du nain', 'armure', '1', '', 'nain', '+5% en PM', 'pourcent_pm', '5'
), (
'Gemme du troll', 'armure', '1', '', 'troll', '- 15 min sur le temps de régénératin', 'regeneration', '15'
), (
'Gemme de vie', 'armure', '1', '', 'vie', '+10 PV', 'hp', '10'
), (
'Gemme de mana', 'armure', '1', '', 'mana', '+10 PM', 'mp', '10'
), (
'Gemme du mage', 'accessoire', '1', '', 'mage', '+10 en incantation', 'incantation', '10'
), (
'Gemme du combattant', 'accessoire', '1', '', 'combattant', '+10 en mêlée', 'melee', '10'
), (
'Gemme de la baliste', 'accessoire', '1', '', 'baliste', '+10 en tir à distance', 'distance', '10'
), 

(
'Gemme du roc dur', 'arme', '2', 'bouclier', 'roc', '+2 d''absorbtion des dégats', 'bouclier', '2'
), (
'Gemme de l''épervier affamé', 'arme', '2', 'bouclier', 'epervier', '+15% d''augmentation de la parade', 'blocage', '15'
), (
'Gemme du serpent affamé', 'arme', '2', '', 'serpent', '15% de chance d''empoisonner l''ennemi et lui infliger 3 pt de dégats par round pendant 5 rounds', 'poison', '15;3'
), (
'Gemme d''épine coupante', 'arme', '2', 'bouclier', 'epine', '3 dégat effectué à chaque parade réussie', 'bouclier_epine', '3'
), (
'Gemme du vampire affamé', 'arme', '2', '', 'vampire', '30% de chance de drainer 3 pv sur une attaque réussie ', 'vampire', '3'
), (
'Gemme du drake affamé', 'arme', '2', '', 'drake', '+2 aux dégats des sorts', 'degat_magie', '2'
), (
'Gemme du singe malin', 'armure', '2', '', 'singe', '+20 à la compétence esquive', 'esquive', '20'
), (
'Gemme de la manufacture', 'armure', '2', '', 'fabrique', '+25% de chances de réussir la fabrication d''objet', 'forge', '25'
), (
'Gemme de la pierre polie', 'armure', '2', '', 'pierre', '+20 à la compétence blocage', 'blocage', '20'
), (
'Gemme de l''ours affamé', 'armure', '2', '', 'ours', '+10% en PP', 'pourcent_pp', '10'
), (
'Gemme taillée du nain', 'armure', '2', '', 'nain', '+10% en PM', 'pourcent_pm', '10'
), (
'Gemme taillée du troll', 'armure', '2', '', 'troll', '- 30 min sur le temps de régénératin', 'regeneration', '30'
), (
'Gemme de grande vie', 'armure', '2', '', 'vie', '+25 PV', 'hp', '25'
), (
'Gemme de grand mana', 'armure', '2', '', 'mana', '+25 PM', 'mp', '25'
), (
'Gemme du sorcier', 'accessoire', '2', '', 'mage', '+20 en incantation', 'incantation', '20'
), (
'Gemme du guerrier', 'accessoire', '2', '', 'combattant', '+20 en mêlée', 'melee', '20'
), (
'Gemme de l''archer', 'accessoire', '2', '', 'baliste', '+20 en tir à distance', 'distance', '20'
), 

(
'Gemme du roc parfait', 'arme', '3', 'bouclier', 'roc', '+5 d''absorbtion des dégats', 'bouclier', '5'
), (
'Gemme de l''épervier royal', 'arme', '3', 'bouclier', 'epervier', '+25% d''augmentation de la parade', 'blocage', '25'
), (
'Gemme du serpent royal', 'arme', '3', '', 'serpent', '20% de chance d''empoisonner l''ennemi et lui infliger 5 pt de dégats par round pendant 5 rounds', 'poison', '20;5'
), (
'Gemme de ronce', 'arme', '3', 'bouclier', 'epine', '5 dégat effectué à chaque parade réussie', 'bouclier_epine', '5'
), (
'Gemme du vampire royal', 'arme', '3', '', 'vampire', '30% de chance de drainer 5 pv sur une attaque réussie ', 'vampire', '5'
), (
'Gemme du drake royal', 'arme', '3', '', 'drake', '+5 aux dégats des sorts', 'degat_magie', '5'
), (
'Gemme du singe royal', 'armure', '3', '', 'singe', '+50 à la compétence esquive', 'esquive', '50'
), (
'Gemme de réussite', 'armure', '3', '', 'fabrique', '+50% de chances de réussir la fabrication d''objet', 'forge', '50'
), (
'Gemme de la pierre parfaite', 'armure', '3', '', 'pierre', '+50 à la compétence blocage', 'blocage', '50'
), (
'Gemme de l''ours royal', 'armure', '3', '', 'ours', '+20% en PP', 'pourcent_pp', '20'
), (
'Gemme du roi nain', 'armure', '3', '', 'nain', '+20% en PM', 'pourcent_pm', '20'
), (
'Gemme du roi troll', 'armure', '3', '', 'troll', '- 1 h sur le temps de régénératin', 'regeneration', '60'
), (
'Gemme de vie infinie', 'armure', '3', '', 'vie', '+75 PV', 'hp', '75'
), (
'Gemme de mana infini', 'armure', '3', '', 'mana', '+75 PM', 'mp', '75'
), (
'Gemme du sorcier royal', 'accessoire', '3', '', 'mage', '+50 en incantation', 'incantation', '50'
), (
'Gemme du guerrier royal', 'accessoire', '3', '', 'combattant', '+50 en mêlée', 'melee', '50'
), (
'Gemme de l''archer royal', 'accessoire', '3', '', 'baliste', '+50 en tir à distance', 'distance', '50'
), 

(
'Gemme d''esquive', 'armure', '3', 'bottes', 'esquive', '50% de chances d''esquiver totalement la 2 ème attaque', 'evasion', '50'
), (
'Gemme de parade totale', 'arme', '3', 'bouclier', 'parade', '10% de chance de parer totalement un coup en cas de parade réussie', 'parade', '10'
), (
'Gemme des 7 lieues', 'armure', '3', 'bottes', '7 lieues', '-1 pa de déplacement (2 minimum)', 'course', '50'
), (
'Gemme divine', 'accessoire', '3', '', 'divin', '-1 en cout de réserve de mana pour les compétences ', 'divin', '1'
)
;
