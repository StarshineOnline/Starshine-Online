CREATE TABLE IF NOT EXISTS `action_pet` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_joueur` int(10) unsigned NOT NULL DEFAULT '0',
  `type_monstre` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `nom` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `action` text COLLATE utf8_unicode_ci NOT NULL,
  `mode` enum('s','a') COLLATE utf8_unicode_ci NOT NULL DEFAULT 's',
  PRIMARY KEY (`id`)
);

ALTER TABLE `pet` ADD `action_a` INT( 10 ) NOT NULL DEFAULT '0',
ADD `action_d` INT( 10 ) NOT NULL DEFAULT '0'

ALTER TABLE `monstre` ADD `sort_combat` TEXT NOT NULL ,
ADD `comp_combat` TEXT NOT NULL 

UPDATE `monstre` SET `comp_combat` = '8' WHERE `id` = 1;
UPDATE `monstre` SET `comp_combat` = '8' WHERE `id` = 2;
UPDATE `monstre` SET `comp_combat` = '8' WHERE `id` = 3;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;25;8;47;9;5;53;6;48;54' WHERE `id` = 4;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;25;8;47;9;5;53;10;6' WHERE `id` = 5;
UPDATE `monstre` SET `comp_combat` = '8' WHERE `id` = 7;
UPDATE `monstre` SET `comp_combat` = '8' WHERE `id` = 8;
UPDATE `monstre` SET `comp_combat` = '8' WHERE `id` = 9;
UPDATE `monstre` SET `comp_combat` = '8' WHERE `id` = 10;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;25;50;44;41;45;51;42;46' WHERE `id` = 14;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;25;50;44;5;41;53;45;6;51;42;54;46;52;43;101;78;76;55' WHERE `id` = 15;
UPDATE `monstre` SET `comp_combat` = '8' WHERE `id` = 16;
UPDATE `monstre` SET `comp_combat` = '8' WHERE `id` = 17;
UPDATE `monstre` SET `comp_combat` = '8' WHERE `id` = 19;
UPDATE `monstre` SET `comp_combat` = '8' WHERE `id` = 20;
UPDATE `monstre` SET `comp_combat` = '8' WHERE `id` = 21;
UPDATE `monstre` SET `comp_combat` = '8' WHERE `id` = 22;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;25;50;44;41;45;51;42' WHERE `id` = 23;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;25;50;44;41;45;51;42' WHERE `id` = 26;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;25;8;47;5;6;48' WHERE `id` = 27;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;25;8;47;9;5;53;10;6;48;54' WHERE `id` = 29;
UPDATE `monstre` SET `comp_combat` = '8' WHERE `id` = 30;
UPDATE `monstre` SET `comp_combat` = '8' WHERE `id` = 31;
UPDATE `monstre` SET `comp_combat` = '8' WHERE `id` = 32;
UPDATE `monstre` SET `comp_combat` = '8' WHERE `id` = 33;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;8;50;44' WHERE `id` = 34;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;25;8;50;44;5;53;45;6' WHERE `id` = 35;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;8;44;5;41' WHERE `id` = 36;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;25;8;47;9;5;53;10;6;48;54' WHERE `id` = 38;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;25;8;47;9;5;53;10;6;48;54' WHERE `id` = 40;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;25;8;47;9;5;53;10;6;48;54' WHERE `id` = 41;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;25;8;47;9;5;53;10;6;48;54' WHERE `id` = 43;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;25;8;47;9;5;53;10;6;48;54;49;95;101;55;80;99;102;84' WHERE `id` = 44;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;25;8;47;9;5;53;10;6;48;54;49;98;101' WHERE `id` = 45;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;25;8;47;9;5;53;10;6;48;54' WHERE `id` = 46;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;25;8;5;6;101;102' WHERE `id` = 48;
UPDATE `monstre` SET `comp_combat` = '8' WHERE `id` = 49;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;25;8;47;9;5;53;6;48;54' WHERE `id` = 50;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;25;50;44;9;5;41;45;10;51;42' WHERE `id` = 52;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;25;8;47;9;5;53;10;6;48' WHERE `id` = 53;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;25;47;5;6;48' WHERE `id` = 54;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;25;50;44;41;45;51;42' WHERE `id` = 55;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;25;8;47;5;53;6;48;54;49;101' WHERE `id` = 76;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;25;50;44;41;45;51;42;46;52;43' WHERE `id` = 77;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;25;50;44;41;45;51;42;46;52;43' WHERE `id` = 78;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;25;50;44;41;45;51;42;46;52;43;78;76' WHERE `id` = 79;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;25;106;107;108;111;8;47;9;5;53;10;6;48;54;49;98;101;55;80' WHERE `id` = 80;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;25;106;107;108;111;8;50;5;6;51;52;101' WHERE `id` = 81;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;25;106;107;108;111;50;44;45;51;46;52;78;82' WHERE `id` = 84;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;25;106;107;108;111;8;47;9;5;53;10;6;48;54;49;98;101;55;80' WHERE `id` = 86;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;25;106;107;108;111;44;47;9;53;45;10;48;54;46;49;98;78;55;80' WHERE `id` = 88;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;25;106;107;108;111;47;9;10;48;54;49;98;55;80' WHERE `id` = 89;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;25;106;107;108;111;50;44;41;45;51;42;46;52;43;78;76;82;77;79' WHERE `id` = 91;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;25;106;107;108;111;50;47;53;51;48;54;49;52;55;80;82;84;81' WHERE `id` = 92;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;25;106;107;108;111;44;41;45;42;46;43;78;76;77;79' WHERE `id` = 93;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;25;106;107;108;111;47;41;53;48;52;54;49;43;76;55;80;77;84;81' WHERE `id` = 94;
UPDATE `monstre` SET `comp_combat` = '19;20;21;22;25;106;107;108;111;8;50;5;6;51;52;101;82;102' WHERE `id` = 98;
UPDATE `monstre` SET `comp_combat` = '50;51;44;45;41;42' WHERE `id` = 11;
UPDATE `monstre` SET `comp_combat` = '11;12;41;42' WHERE `id` = 25;
UPDATE `monstre` SET `comp_combat` = '11;12;41;42;26' WHERE `id` = 39;
UPDATE `monstre` SET `comp_combat` = '11;41' WHERE `id` = 51;
UPDATE `monstre` SET `comp_combat` = '30;29;26' WHERE `id` = 63;
UPDATE `monstre` SET `comp_combat` = '29;30;31;11;12;13;14;26' WHERE `id` = 85;
UPDATE `monstre` SET `sort_combat` = '102;103;104;153;154;155;24;25;26' WHERE `id` = 12;
UPDATE `monstre` SET `sort_combat` = '66;3;4;15;16;34;35;19;86;20;135;136;27;46;47;48' WHERE `id` = 14;
UPDATE `monstre` SET `sort_combat` = '99;100;101;7;8;9;10;147;148;149;11;12;13;1;2;5;6' WHERE `id` = 18;
UPDATE `monstre` SET `sort_combat` = '111;112;78' WHERE `id` = 19;
UPDATE `monstre` SET `sort_combat` = '30;31;91;19;32;129;130;131' WHERE `id` = 27;
UPDATE `monstre` SET `sort_combat` = '99;100;101;108;190;110;7;8;9;10;43;171;172;11;12;13;14;1;2;5;6;40' WHERE `id` = 28;
UPDATE `monstre` SET `sort_combat` = '66;67;3;4;15;16;84;85;55;56;57;58;59' WHERE `id` = 42;
UPDATE `monstre` SET `sort_combat` = '141;142;143;144;145;146;19;32;95' WHERE `id` = 48;
UPDATE `monstre` SET `sort_combat` = '17;18;87;21' WHERE `id` = 54;
UPDATE `monstre` SET `sort_combat` = '141;142;143;144;145;19;32;55;56;57;58;59;129;130;131;123;124;125' WHERE `id` = 65;
UPDATE `monstre` SET `sort_combat` = '66;67;68;118;119;120;121;34;35;36;37;135;136;137;138;139;140;55;56;57;58;59;60;46;47;48;49' WHERE `id` = 82;
UPDATE `monstre` SET `sort_combat` = '30;31;91;92;3;4;15;16;84;85;94;129;130;131;132;133;123;124;125;126;127' WHERE `id` = 83;
UPDATE `monstre` SET `sort_combat` = '66;67;68;69;30;31;91;92;118;119;120;121;3;4;15;16;84;85;94;27;28;29;123;130;131;132;133;134;123;124;125;126;127;128' WHERE `id` = 90;
UPDATE `monstre` SET `sort_combat` = '72;73;17;18;87;88;89;111;112;113;114;115;78;79;80;81;21;22;23' WHERE `id` = 93;
UPDATE `monstre` SET `sort_combat` = '86;135' WHERE `id` = 6;
UPDATE `monstre` SET `sort_combat` = '72;17;18;87;111;112;113;114;115;78;79;80;21' WHERE `id` = 13;
UPDATE `monstre` SET `sort_combat` = '141;30;3;4;46;123' WHERE `id` = 17;
UPDATE `monstre` SET `sort_combat` = '3;4;15;16;19;86;20;135;136' WHERE `id` = 24;
UPDATE `monstre` SET `sort_combat` = '141;142;143;144;145;118;119;120;34;35;36;19;32;86;20;174;175;27;28' WHERE `id` = 47;
UPDATE `monstre` SET `sort_combat` = '118;119;120;121;27;28;29;46;47;48;49' WHERE `id` = 84;
UPDATE `monstre` SET `sort_combat` = '19;32;86;20;174;175;27;28' WHERE `id` = 87;
UPDATE `monstre` SET `sort_combat` = '86;20;174;175' WHERE `id` = 89;
UPDATE `monstre` SET `sort_combat` = '3;4;15;16;84;85;94;55;56;57;58;59;60;129;130;131;132;133;34;123;124;125;126;127;128' WHERE `id` = 96;
