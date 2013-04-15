UPDATE sort_jeu SET mp = 50 WHERE nom LIKE 'Bouclier de feu 3';
UPDATE sort_jeu SET effet = 50, mp = 35 WHERE nom LIKE 'Bouclier de feu 4';
UPDATE sort_jeu SET effet = 60, mp = 45 WHERE nom LIKE 'Bouclier de feu 5';
UPDATE sort_jeu SET effet = 35, description = 'Lors d\'un blocage, %effet%% de chances de glacer l\'adversaire (réduit fortement l\'attaque et la défense) pendant %effet2% round(s).' WHERE nom LIKE 'Bouclier d\'eau 1';
UPDATE sort_jeu SET effet = 40, description = 'Lors d\'un blocage, %effet%% de chances de glacer l\'adversaire (réduit fortement l\'attaque et la défense) pendant %effet2% round(s).' WHERE nom LIKE 'Bouclier d\'eau 2';
UPDATE sort_jeu SET effet = 45, effet2 = 1, description = 'Lors d\'un blocage, %effet%% de chances de glacer l\'adversaire (réduit fortement l\'attaque et la défense) pendant %effet2% round(s).' WHERE nom LIKE 'Bouclier d\'eau 3';
UPDATE sort_jeu SET effet = 50, effet2 = 1, description = 'Lors d\'un blocage, %effet%% de chances de glacer l\'adversaire (réduit fortement l\'attaque et la défense) pendant %effet2% round(s).' WHERE nom LIKE 'Bouclier d\'eau 4';
UPDATE sort_jeu SET effet = 55, effet2 = 1, description = 'Lors d\'un blocage, %effet%% de chances de glacer l\'adversaire (réduit fortement l\'attaque et la défense) pendant %effet2% round(s).' WHERE nom LIKE 'Bouclier d\'eau 5';