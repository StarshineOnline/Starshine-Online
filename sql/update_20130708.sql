-- Modification de l'accès aux compétences hors combat en fonction du niveau de l'école
UPDATE comp_jeu SET lvl_batiment = 1 WHERE comp_requis <= 100 AND lvl_batiment < 9;
UPDATE comp_jeu SET lvl_batiment = 2 WHERE comp_requis > 100 AND comp_requis <= 200 AND lvl_batiment < 9;
UPDATE comp_jeu SET lvl_batiment = 3 WHERE comp_requis > 200 AND comp_requis <= 300 AND lvl_batiment < 9;
UPDATE comp_jeu SET lvl_batiment = 4 WHERE comp_requis > 300 AND comp_requis <= 400 AND lvl_batiment < 9;
UPDATE comp_jeu SET lvl_batiment = 5 WHERE comp_requis > 400 AND comp_requis <= 500 AND lvl_batiment < 9;

-- Modification de l'accès aux compétences de combat en fonction du niveau de l'école
UPDATE comp_combat SET lvl_batiment = 1 WHERE comp_requis <= 100 AND lvl_batiment < 9;
UPDATE comp_combat SET lvl_batiment = 2 WHERE comp_requis > 100 AND comp_requis <= 200 AND lvl_batiment < 9;
UPDATE comp_combat SET lvl_batiment = 3 WHERE comp_requis > 200 AND comp_requis <= 300 AND lvl_batiment < 9;
UPDATE comp_combat SET lvl_batiment = 4 WHERE comp_requis > 300 AND comp_requis <= 400 AND lvl_batiment < 9;
UPDATE comp_combat SET lvl_batiment = 5 WHERE comp_requis > 400 AND comp_requis <= 500 AND lvl_batiment < 9;

-- Modification de l'accès aux sorts hors combat en fonction du niveau de l'école
UPDATE sort_jeu SET lvl_batiment = 1 WHERE incantation <= 100 AND lvl_batiment < 9;
UPDATE sort_jeu SET lvl_batiment = 2 WHERE incantation > 100 AND incantation <= 200 AND lvl_batiment < 9;
UPDATE sort_jeu SET lvl_batiment = 3 WHERE incantation > 200 AND incantation <= 300 AND lvl_batiment < 9;
UPDATE sort_jeu SET lvl_batiment = 4 WHERE incantation > 300 AND incantation <= 400 AND lvl_batiment < 9;
UPDATE sort_jeu SET lvl_batiment = 5 WHERE incantation > 400 AND incantation <= 500 AND lvl_batiment < 9;

-- Modification de l'accès aux sorts de combat en fonction du niveau de l'école
UPDATE sort_combat SET lvl_batiment = 1 WHERE incantation <= 100 AND lvl_batiment < 9;
UPDATE sort_combat SET lvl_batiment = 2 WHERE incantation > 100 AND incantation <= 200 AND lvl_batiment < 9;
UPDATE sort_combat SET lvl_batiment = 3 WHERE incantation > 200 AND incantation <= 300 AND lvl_batiment < 9;
UPDATE sort_combat SET lvl_batiment = 4 WHERE incantation > 300 AND incantation <= 400 AND lvl_batiment < 9;
UPDATE sort_combat SET lvl_batiment = 5 WHERE incantation > 400 AND incantation <= 500 AND lvl_batiment < 9;

-- bourse des royaume
ALTER TABLE `bourse_royaume` CHANGE `fin_vente` `fin_vente` DATE NOT NULL;
