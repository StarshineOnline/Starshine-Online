INSERT INTO `perso` (`ID`, `mort`, `nom`, `password`, `email`, `exp`, `honneur`, `reputation`, `level`, `rang_royaume`, `vie`, `forcex`, `dexterite`, `puissance`, `volonte`, `energie`, `race`, `classe`, `classe_id`, `inventaire`, `inventaire_pet`, `inventaire_slot`, `pa`, `dernieraction`, `action_a`, `action_d`, `sort_jeu`, `sort_combat`, `comp_combat`, `comp_jeu`, `star`, `x`, `y`, `groupe`, `hp`, `hp_max`, `mp`, `mp_max`, `melee`, `distance`, `esquive`, `blocage`, `incantation`, `sort_vie`, `sort_element`, `sort_mort`, `identification`, `craft`, `alchimie`, `architecture`, `forge`, `survie`, `dressage`, `facteur_magie`, `facteur_sort_vie`, `facteur_sort_mort`, `facteur_sort_element`, `regen_hp`, `maj_hp`, `maj_mp`, `point_sso`, `quete`, `quete_fini`, `dernier_connexion`, `statut`, `fin_ban`, `frag`, `crime`, `amende`, `teleport_roi`, `cache_classe`, `cache_stat`, `cache_niveau`, `max_pet`, `beta`) VALUES
('', 0, 'Az', '13085a63a2b3e4beb7ab10ee395aefe4', '', 0, 100000, 0, 0, 6, 19, 18, 14, 7, 13, 9, 'nain', 'titan', 18, 'O:10:"inventaire":18:{s:4:"cape";N;s:4:"main";s:3:"p88";s:11:"main_droite";s:3:"a41";s:11:"main_gauche";s:4:"lock";s:5:"torse";s:3:"p90";s:4:"tete";s:3:"p89";s:8:"ceinture";s:3:"p85";s:5:"jambe";s:3:"p87";s:9:"chaussure";s:3:"p86";s:5:"liste";N;s:10:"slot_liste";N;s:5:"mains";N;s:6:"jambes";N;s:5:"pieds";N;s:3:"dos";s:4:"p122";s:5:"doigt";s:4:"p120";s:3:"cou";s:4:"p118";s:10:"accessoire";s:3:"m15";}', '', 'a:2:{i:0;s:3:"a73";i:1;s:3:"a77";}', 180, 1308928586, 0, 0, '9;11;15;1;13;2;10;12;3', '99;102;105;108', '7;8;5;9;21;22;47;48;10;6;49;80;98;101;53;54;55;107;108;24;25;111;38;39;40;74;75;17;18;81;44;45;46;78;79;50;51;52;82;83;99;102;41;42;43;76;77;84;85', '13;1;14;16;2;15;59;17;18;61;62;3;51;22;23;24;64;65;25;26;27;66;67;19;20;21;63;60;52', 1, 207, 8, 0, 5466, 5466, 430, 430, 800, 100, 450, 600, 100, 100, 100, 100, 500, 500, 500, 500, 500, 500, 100, 2, 0, 0, 0, 1308927982, 1308753382, 1308753382, 0, '', '', 1308927134, 'actif', 0, 0, 0, 0, 'false', 0, 0, 0, 1, 0),
('', 0, 'Galadion', '13085a63a2b3e4beb7ab10ee395aefe4', '', 0, 100000, 0, 0, 6, 12, 13, 12, 14, 13, 15, 'humainnoir', 'pestimancien', 21, 'O:10:"inventaire":18:{s:4:"cape";N;s:4:"main";s:3:"p82";s:11:"main_droite";s:3:"a49";s:11:"main_gauche";s:3:"a34";s:5:"torse";s:3:"p84";s:4:"tete";s:3:"p83";s:8:"ceinture";s:3:"p79";s:5:"jambe";s:3:"p81";s:9:"chaussure";s:3:"p80";s:5:"liste";N;s:10:"slot_liste";N;s:5:"mains";N;s:6:"jambes";N;s:5:"pieds";N;s:3:"dos";s:4:"p122";s:5:"doigt";s:4:"p120";s:3:"cou";s:4:"p118";s:10:"accessoire";s:3:"m10";}', '', 'a:1:{i:0;s:3:"a79";}', 180, 1308930575, 0, 0, '1;155;9;11;15;13;2;10;12;3;14;32;4;34;7;5;105;43;21;23;6;102;46;139;60;16;8;25;55;49;18;108;72;33;58;27;52;35;69;75;63;145;142;44;61;30;36;103;66;38;129;148;120;76;117;134;53;37;39;62;146;130;124;135;67;149;131;77;136;125;132;121;54;118;137;68;126;147;150;133;138;127;119;122;128;151', '1;111;3;11;7;78;141;2;46;112;4;99;102;105;108;30;8;51;123;17;5;24;113;61;15;86;27;129;135;118;142;31;9;12;47;52;34;55;165;18;6;25;114;62;16;147;79;20;159;124;100;103;106;109;119;19;153;130;136;56;166;148;66;143;91;13;48;53;160;35;84;174;137;57;125;120;28;131;32;144;36;58;49;175;67;145;85;138;59;126;132;146;92;37;60;139;121;94;176;127;133;68;95;140;29;38;128;134;69;50;177;70;122;39;71', '', '', 1, 204, 5, 0, 3658, 3658, 831, 831, 100, 100, 250, 300, 800, 100, 100, 550, 500, 500, 500, 500, 500, 500, 100, 1, 0, 0, 0, 1308929686, 1308929686, 1308929686, 0, '', '', 1308929696, 'actif', 0, 0, 0, 0, 'false', 0, 0, 0, 1, 0);

SET @lastid = LAST_INSERT_ID();

INSERT INTO `comp_perso` (`id`, `id_comp`, `competence`, `valeur`, `id_perso`) VALUES
('', 1, 'maitrise_epee', 300, @lastid),
('', 1, 'maitrise_hache', 300, @lastid),
('', 1, 'maitrise_bouclier', 200, @lastid);