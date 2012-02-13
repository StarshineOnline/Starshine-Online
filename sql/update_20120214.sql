UPDATE comp_jeu SET cible = 3 WHERE cible IN (5, 8);
UPDATE sort_jeu SET cible = 5 WHERE type LIKE 'maladie_%';
UPDATE classe_requis SET requis=75 WHERE competence LIKE 'sort_%' AND id_classe IN (5, 6, 15);
UPDATE classe_requis SET requis=50 WHERE competence LIKE 'sort_%' AND id_classe IN (25, 26, 27);
UPDATE comp_jeu SET arme_requis = 'arc' WHERE comp_assoc LIKE 'distance'