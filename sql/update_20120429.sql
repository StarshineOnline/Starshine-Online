-- Ajout de la possibilité d'avoir un bouclier pour les monstres
ALTER TABLE monstre ADD blocage INT NOT NULL, ADD bouclier INT NOT NULL;

-- Modification des monstres
UPDATE monstre SET incantation = 30, sort_vie = 25, sort_mort = 25, sort_element = 25, sort_combat = "3;4;78;11" WHERE id = 32; -- renard gris
UPDATE monstre SET blocage = 50, bouclier = 1 WHERE id = 17;  -- Scarabée Géant
UPDATE monstre SET incantation = 75, sort_mort = 65, sort_combat = "86;135;27;46" WHERE id = 3; -- serpent
UPDATE monstre SET arme = "dague", comp_combat = "19;20;21;22;8;50;44;26;29" WHERE id = 34; -- puma
UPDATE monstre SET incantation = 70, sort_vie = 60, sort_mort = 60, sort_element = 60, sort_combat = "24;102;123;129;111;112;113" WHERE id = 16;  -- worg
UPDATE monstre SET arme = "arc", incantation = 90, sort_vie = 80, sort_mort = 0, sort_element = 0, sort_combat = "17;78;111;112;113", comp_combat = "7" WHERE id = 19;  -- dryade
UPDATE monstre SET action = "#09=10@_53;#10°berzeker@_9;#14<5@_47;!", comp_combat = "19;20;21;22;8;47;9;5;53" WHERE id = 5; -- ours noir
UPDATE monstre SET blocage = 100, bouclier = 2, incantation = 110, sort_element = 90, sort_combat = "1;2;5;11;12;51;52" WHERE id = 51; -- waja
UPDATE monstre SET arme = "arc", comp_combat = "19;20;22;25;7;1;32;59;68", action = "#14<5@_1;#14<3@_68;_32;!" WHERE id = 35; -- goblin
UPDATE monstre SET melee = 230, incantation = 110, sort_vie = 80, sort_mort = 80, sort_element = 80, sort_combat = "51;52;78;86;102;135;165;3;4;15" WHERE id = 39; -- Araignée de Glace
UPDATE monstre SET comp_combat = "21;22;25;8;47;9;5;53;10;6;48;54" WHERE id = 5; -- ours noir
UPDATE monstre SET arme = "arc", comp_combat = "7;19;21;25;32;33;59;60;62;63;71;72", action = "#10°posture@_21;#14<2@_63;#14<5@_33;_7" WHERE id = 23; -- Fourmi géante soldat
UPDATE monstre SET comp_combat = '19;20;25;50;44;41;45;51;42' WHERE id =55; -- Vautour géant
UPDATE monstre SET blocage = 250, bouclier = 3, comp_combat = '20;22;25;8;47;9;5;10;6;48;17' WHERE id = 53; -- Ours polaire
UPDATE monstre SET incantation = 130, sort_vie = 100, sort_mort = 100, sort_element = 100, sort_combat = "20;27;78;79;86;135;159;105;108;1;2;5;6", comp_combat = '20;11;12' WHERE id = 6; -- Araignée Géante
UPDATE monstre SET blocage = 350, bouclier = 2 WHERE id = 41; -- 	Hydre à 3 têtes
UPDATE monstre SET blocage = 275, bouclier = 3, comp_combat = "19;25;8;47;9;5;53;10;6;48;54;38;39" WHERE id = 40; -- Roc
UPDATE monstre SET arme = "arc", comp_combat = "7;21;1;2;71;65" WHERE id = 13; -- Cactus Géant
UPDATE monstre SET comp_combat = "19;22;8;47;9;5;53;10;6;48;54" WHERE id = 41; -- Hydre à 3 têtes
UPDATE monstre SET incantation = 200, sort_vie = 150, sort_mort = 150, sort_element = 150, sort_combat = "1;2;5;6;3;4;7;8;9;16;21;30;31;55;56;61;62" WHERE id = 29;  -- Ogre
UPDATE monstre SET blocage = 275, bouclier = 3 WHERE id = 52; -- Golem Des Sables
UPDATE monstre SET comp_combat = "19;20;22;25;8;47;9;5;53;6;48;54;41;42", action = "#10°berzeker@_10;#09=3@_54;#09=4@_48;#09=5@_6;#09=6@_42" WHERE id = 4; -- Loup-garou
UPDATE monstre SET comp_combat = "11;12;41;42;25;20" WHERE id = 25; -- Tigre
UPDATE monstre SET comp_combat = "50;51;44;45;41;42;19;21" WHERE id = 11; -- Elémentaire d'air
UPDATE monstre SET action = "#10°posture@_22;#14<5@_48", comp_combat = "20;21;22;8;47;5;53;6;48;54" WHERE id = 50; -- 	Elémentaire de Terre
UPDATE monstre SET sort_vie = 50, sort_mort = 0, sort_element = 180, sort_combat = "99;100;101;7;8;9;10;147;148;149;11;12;13;1;2;5;6" WHERE id = 18; -- Elémentaire de feu
UPDATE monstre SET arme = "arc", comp_combat = "7;62;65;68;56" WHERE id = 42; -- Succube Mineure
UPDATE monstre SET esquive = 150, blocage = 300, bouclier = 3, comp_combat = "21;25;47;5;6;48;17;18" WHERE id = 54; -- Treant
UPDATE monstre SET arme = "arc", incantation = 200, sort_vie = 130, sort_mort = 0, sort_element = 0, sort_combat = "72;111,112,113,114", comp_combat = "7;20;22;25;21;1;2;3;15;16;23;35;32;33;34;68;69;70;59;60;61", action = "#10°posture@_22;#14<5@_3;#14<5@_70;_34" WHERE id = 78; -- Centaure
UPDATE monstre SET blocage = 350, bouclier = 4 WHERE id = 43; -- Cyclope
UPDATE monstre SET blocage = 400, bouclier = 4 WHERE id = 46; -- Yeti
UPDATE monstre SET sort_vie = 0, sort_element = 110, sort_combat = "66;3;4;15;16;34;35;19;27;1;2;5;6;7;8;9;11;12;13;99;100;141;142;143" WHERE id = 14; -- Gargouille
UPDATE monstre SET melee = 400, blocage = 350, bouclier = 4, action = "#10°berzeker@_10;#14<5@_6;#14<5@_78", comp_combat="20;22;25;106;108;111;8;9;5;53;10;6;54;98;101;55;80;44;45;46;78" WHERE id = 80; -- Hobgoblin
UPDATE monstre SET arme = "arc", comp_combat = "7;19;20;22;106;107;108;1;2;3;4;23;32;33;34;35;36;65;66;67;71;72;73;92;116", action = "#11°posture@_22;#14<5@_4;#14<5@_92;_116" WHERE id = 81; -- 	Centaure des neiges
UPDATE monstre SET incantation = 400, sort_vie = 275, sort_element = 0, sort_mort = 0, sort_combat = "72;73;78;79;111;112;113;114" WHERE id = 84; -- Myrmarque Formien
UPDATE monstre SET blocage = 500, bouclier = 5 WHERE id = 15; -- Griffon
UPDATE monstre SET esquive = 400, blocage = 400, bouclier = 5, action = "#11°posture@_110;#14<3µ#11°poison@~20;#14<5@_14;#>@_43", comp_combat = "20;29;38;11;12;24;13;14;30;31;39;40;106;110;41;42;43" WHERE id = 87; -- Scorpion Géant
UPDATE monstre SET arme = "arc", comp_combat = "7;25;21;106;111;1;2;3;4;15;16;62;63;64;68;69;70;92;94;104;105", action = "#14<1@_90;#14<5@_4;#14<5@_94" WHERE id = 86; -- Chasseur de Peau
UPDATE monstre SET comp_combat = "29;30;31;11;12;13;14;26;20;25;106;111" WHERE id = 85; -- Onikage
UPDATE monstre SET comp_combat = "20;11;12;13;14;41;42;43;26;27;28;24;110;112" WHERE id = 87; -- Scorpion Géant
UPDATE monstre SET sort_vie = 300, sort_combat = "66;67;68;69;30;31;91;92;118;119;120;121;3;4;15;16;84;85;94;27;28;29;123;130;131;132;133;134;123;124;125;126;127;128;17;18;21;72;73;74;75;87" WHERE id = 90; -- Banshee
UPDATE monstre SET blocage = 400, bouclier = 6, sort_element = 240, sort_mort = 0, sort_combat = "1;2;5;6;7;8;9;10;11;12;13;40;43;51;52;53;54;108;109;171;165;166;167" WHERE id = 89; -- Wyverne
UPDATE monstre SET arme = "arc", comp_combat = "7;21;22;107;108;1;2;3;4;32;33;34;59;60;61;62;63;64;88;89;90;91;100;116;117", action = "#10°posture@_107;#14<5@_100;#14<5@_117" WHERE id = 91; -- Elfe Noir
UPDATE monstre SET blocage = 425, bouclier = 6 WHERE id = 92; -- Chitineux
UPDATE monstre SET comp_combat = "8;5;6;50;51;52;20;22;105;108;101;102;82;41;42;43;46;77" WHERE id = 95; -- Serpent à Plumes
UPDATE monstre SET arme = "arc", comp_combat = "7;15;16;23;35;36;56;57;65;66;67;71;72;73;92;93;96;104;105;109" WHERE id = 96; -- Succube
UPDATE monstre SET comp_combat = "49;46;47;48;44;45;53;54;55;78;80;79;84;81" WHERE id = 97; -- Abomination des Tempêtes
UPDATE monstre SET blocage = 450, bouclier = 7 WHERE id = 48; -- Manticore
UPDATE monstre SET sort_combat = "32;146;126" WHERE id = 99; -- Gorgone
UPDATE monstre SET sort_combat = "32;67", comp_combat = "110;103;81;80;49;48;47;50;51;52;82;83;8;5;6;101;102;22;25;108;111", action = "#11°paralysieµ#14<1@~32;#11°appel_tenebreµ#14<1@~67;#10°posture@_110;#14<5@_83;#14<5@_81" WHERE id = 100; -- Hurleur
UPDATE monstre SET arme = "dague", sort_combat = "110;164", comp_combat = "22;24;108;110;11;12;13;14;103;41;42;43;76;77;29;30;31;", action = "#10°posture@~110;#14<3@~164;#14<5@_77;114;115;#14<5@_103" WHERE id = 101; -- Lézard Voltaïque
UPDATE monstre SET arme = "arc", blocage = 500, bouclier = 8, comp_combat = "7;1;2;3;4;23;32;33;34;62;63;64;68;69;70;90;91;94;95;100;109;116;117", action = "#10°posture@_109;#14<5@_100;#14<5@_95;_117" WHERE id = 102; -- Harpie Vengeresse
UPDATE monstre SET blocage = 600, bouclier = 8 WHERE id = 106; -- Wyrm
UPDATE monstre SET sort_combat = '99;100;101;108;190;110;7;8;9;10;43;171;172;11;12;13;14;1;2;5;6;40;109;51;52;53;54' WHERE id =28; -- Djinn
UPDATE monstre SET comp_combat = '19;20;21;22;25;8;47;9;5;53;10;6;48;54;49;95;101;55;80;99;102;84;98' WHERE id =44; -- Ogre à deux têtes
UPDATE monstre SET sort_combat = '66;67;68;69;30;31;91;92;118;119;120;121;3;4;15;16;84;85;94;27;28;29;123;130;131;132;133;134;123;124;125;126;127;128;17;18;21;72;73;74;75;87;129' WHERE id =90; -- Banshee
UPDATE monstre SET sort_combat = '102;103;104;153;154;155;24;25;26;61;62;63;159;160;165;166;167;168' WHERE id =12; -- Élémentaire d''eau


-- Encombrement psychique
UPDATE sort_combat SET effet = 0, nom = 'Encombrement psychique 2' WHERE type LIKE 'encombrement_psy';
INSERT INTO sort_combat (nom, description, mp, type, comp_assoc, carac_assoc, incantation, comp_requis, effet, effet2, cible, difficulte, lvl_batiment) VALUES ('Encombrement psychique', 'Réduit le nombre de buff maximum à 2 pour %effet2% jours.', 6, 'encombrement_psy', 'sort_mort', 'volonte', 300, 200, 1, 3, 1, 150, 9);

-- Péages
UPDATE batiment SET cout = 999999999 WHERE id IN (32, 40, 45);

-- Joueurs
CREATE TABLE IF NOT EXISTS joueur (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  login varchar(50) NOT NULL DEFAULT '',
  mdp varchar(50) DEFAULT NULL,
  pseudo varchar(50) DEFAULT '',
  droits INT NOT NULL DEFAULT '0',
  email varchar(50) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY login (login));
ALTER TABLE perso ADD id_joueur INT(11) DEFAULT NULL;