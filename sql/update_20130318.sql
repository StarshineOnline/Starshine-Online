-- Champ pour stocker le hash du mot de passe pour le forum
ALTER TABLE `joueur` ADD `mdp_forum` varchar(40) 	 NOT NULL DEFAULT '';
-- On rajoute un champ pour mémoriser la date de création du personnage
ALTER TABLE `perso` ADD `date_creat` INT( 10 ) NOT NULL DEFAULT '0';
-- Champs pour le lissage du facteur d'entretien et de la consommation de nourriture
ALTER TABLE `royaume` ADD `facteur_entretien` INT( 10 ) NOT NULL DEFAULT '1';
ALTER TABLE `royaume` ADD conso_food INT( 10 ) NOT NULL DEFAULT '0';
