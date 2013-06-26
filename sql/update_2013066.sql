-- correction des prérequis du paladin
UPDATE classe_requis SET requis = 300 WHERE id = 51;
DELETE FROM classe_requis WHERE id = 52;

-- on modifie le prérequis en incantation pour le rang 2 des mages.
UPDATE classe_requis SET requis = 135 WHERE id IN (10, 14, 47);

-- correction du prix de feuilles tranchantes 4
UPDATE sort_combat SET prix = 450 WHERE id = 195;