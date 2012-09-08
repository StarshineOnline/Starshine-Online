UPDATE sort_combat SET duree = 10 WHERE type LIKE 'appel_tenebre';
UPDATE comp_combat SET duree = 10 WHERE type LIKE 'berzeker';
UPDATE comp_combat SET duree = 2 WHERE type LIKE 'fleche_etourdissante';
UPDATE comp_combat SET duree = 20 WHERE type LIKE 'frappe_derniere_chance';
UPDATE comp_combat SET duree = 2 WHERE type LIKE 'dissimulation';
UPDATE comp_combat SET effet3 = effet2, effet2 = 0, description = 'Augmente vos chances de toucher de +%effet%%, réduits vos chances de critique de +%effet3%%' WHERE type LIKE 'attaque_rapide';