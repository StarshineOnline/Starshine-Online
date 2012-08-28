ALTER TABLE `joueur` CHANGE `login` `login` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '', 
CHANGE `mdp` `mdp` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, 
CHANGE `pseudo` `pseudo` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, 
CHANGE `email` `email` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

ALTER TABLE `jabber_admin` CHANGE `nom` `nom` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, 
CHANGE `password` `password` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, 
CHANGE `statut` `statut` ENUM('admin','modo') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
