
ALTER TABLE `classe_permet` ADD `categorie` TINYINT UNSIGNED NULL DEFAULT '1';
UPDATE classe_permet SET categorie = 2 WHERE competence = 'dressage';
UPDATE classe_permet SET categorie = 2 WHERE competence IN ('incantation', 'sort_element', 'sort_mort', 'sort_vie') AND id_classe IN (16, 23, 32, 33, 38, 39);
UPDATE classe_permet SET categorie = 0 WHERE competence = 'esquive' AND id_classe NOT IN (3, 4, 8, 16, 32, 33, 23, 38, 39);
UPDATE classe_permet SET categorie = NULL WHERE competence IN ('blocage', 'maitrise_bouclier', 'facteur_magie', 'max_pet', 'sort_groupe', 'sort_groupe_sort_mort', 'sort_groupe_sort_element', 'survie_bete', 'survie_humanoide', 'survie_magique', 'maitrise_epee', 'maitrise_hache');
UPDATE classe_permet SET permet = 300 WHERE id_classe = 4 AND competence = 'blocage';
UPDATE classe_permet SET permet = 400 WHERE id_classe IN 38 AND competence = 'blocage';
INSERT INTO classe_permet (id_classe, competence, permet, new, categorie) VALUES (17, 'blocage', 400, 'no', 0);
UPDATE classe_permet SET permet = 350 WHERE competence IN ('sort_element', 'sort_mort', 'sort_vie') AND id_classe IN (23, 38, 39);
INSERT INTO classe_permet (id_classe, competence, permet, new, categorie) VALUES (17, 'blocage', 400, 'no', 0);
UPDATE classe_permet SET permet = 400 WHERE competence = 'incantation' AND id_classe IN (23, 38, 39);
UPDATE classe_permet SET categorie = 1 WHERE competence = 'esquive' AND id_classe = 1;
UPDATE classe_permet SET categorie = NULL WHERE competence IN ('sort_element', 'sort_mort', 'sort_vie') AND permet = 100;


ALTER TABLE `classe_requis` ADD `categorie` TINYINT UNSIGNED NULL DEFAULT '1';
UPDATE classe_requis SET categorie = 2 WHERE competence = 'dressage';
UPDATE classe_requis SET categorie = 2 WHERE competence IN ('incantation', 'sort_element', 'sort_mort', 'sort_vie') AND id_classe IN (16, 23, 32, 33, 38, 39);
UPDATE classe_requis SET categorie = 0 WHERE competence = 'esquive' AND id_classe NOT IN (3, 4, 8, 16, 32, 33, 23, 38, 39);
UPDATE classe_requis SET requis = 80 WHERE competence = 'dressage' AND requis = 75;
UPDATE classe_requis SET requis = 150 WHERE competence = 'incantation' AND requis = 135;