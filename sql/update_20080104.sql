ALTER TABLE `motk` ADD `propagande` TEXT NOT NULL AFTER `message` ;

UPDATE sort_jeu SET lvl_batiment = 6;
UPDATE sort_jeu SET lvl_batiment = 5 WHERE incantation <= 400;
UPDATE sort_jeu SET lvl_batiment = 4 WHERE incantation <= 300;
UPDATE sort_jeu SET lvl_batiment = 3 WHERE incantation <= 200;
UPDATE sort_jeu SET lvl_batiment = 2 WHERE incantation <= 100;
UPDATE sort_jeu SET lvl_batiment = 1 WHERE incantation <= 50;

UPDATE sort_combat SET lvl_batiment = 6;
UPDATE sort_combat SET lvl_batiment = 5 WHERE incantation <= 400;
UPDATE sort_combat SET lvl_batiment = 4 WHERE incantation <= 300;
UPDATE sort_combat SET lvl_batiment = 3 WHERE incantation <= 200;
UPDATE sort_combat SET lvl_batiment = 2 WHERE incantation <= 100;
UPDATE sort_combat SET lvl_batiment = 1 WHERE incantation <= 50;

UPDATE comp_combat SET lvl_batiment = 6;
UPDATE comp_combat SET lvl_batiment = 5 WHERE comp_requis <= 400;
UPDATE comp_combat SET lvl_batiment = 4 WHERE comp_requis <= 300;
UPDATE comp_combat SET lvl_batiment = 3 WHERE comp_requis <= 200;
UPDATE comp_combat SET lvl_batiment = 2 WHERE comp_requis <= 100;
UPDATE comp_combat SET lvl_batiment = 1 WHERE comp_requis <= 50;

UPDATE comp_jeu SET lvl_batiment = 6;
UPDATE comp_jeu SET lvl_batiment = 5 WHERE comp_requis <= 400;
UPDATE comp_jeu SET lvl_batiment = 4 WHERE comp_requis <= 300;
UPDATE comp_jeu SET lvl_batiment = 3 WHERE comp_requis <= 200;
UPDATE comp_jeu SET lvl_batiment = 2 WHERE comp_requis <= 100;
UPDATE comp_jeu SET lvl_batiment = 1 WHERE comp_requis <= 50;

UPDATE `batiment_ville` SET `nom` = 'Ecole de combat Cuivre' WHERE `batiment_ville`.`id` =7 LIMIT 1 ;

UPDATE `batiment_ville` SET `nom` = 'Ecole de combat Bronze',
`cout` = '1000',
`entretien` = '30' WHERE `batiment_ville`.`id` =8 LIMIT 1 ;

UPDATE `batiment_ville` SET `nom` = 'Ecole de magie Bronze',
`cout` = '1500',
`entretien` = '45' WHERE `batiment_ville`.`id` =6 LIMIT 1 ;

UPDATE `batiment_ville` SET `nom` = 'Ecole de magie Cuivre' WHERE `batiment_ville`.`id` =5 LIMIT 1 ;