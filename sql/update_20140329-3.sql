UPDATE `batiment` SET nom = "Bélier d'entraînement" WHERE nom = "BÃ©lier d'entrainement" ;
UPDATE `batiment` SET description = "Arme de siège." WHERE description = "Arme de siège" ;
UPDATE `batiment` SET description = "Arme de siège pour s'entraîner." WHERE description = "Arme de siÃ¨ge pour s'entrainer." ;
UPDATE `batiment` SET nom = "Drapeau" WHERE nom = "drapeau" ;
UPDATE `batiment` SET description = "Permet de prendre le contrôle d'une case." WHERE description = "Permet de prendre le contrôle d'une case" ;
UPDATE `batiment` SET description = "Permet de prendre le contrôle d'une case." WHERE description = "Permet de prendre le contr" ;
UPDATE `batiment` SET nom = "Grand Drapeau" WHERE nom = "Grand drapeau" ;
UPDATE `batiment` SET nom = "Grand Etendard" WHERE nom = "Grand etendard" ;
UPDATE `batiment` SET nom = "Petit Bourg" WHERE nom = "Petit bourg" ;
UPDATE `batiment` SET nom = "Puits à essence" WHERE nom = "Puits a essence" ;
UPDATE `batiment` SET nom = "Puits à essence II" WHERE nom = "Puits a essence II" ;
UPDATE `batiment` SET nom = "Puits à essence III" WHERE nom = "Puits a essence III" ;
UPDATE `batiment` SET nom = "Tour d'archers" WHERE nom = "Tour d archers" ;

UPDATE `batiment` SET description = REPLACE(description, '<br />', '') WHERE 1 ;

UPDATE `batiment` SET `description` = 'Défense :\n+16% esquive, +10% PP\nAutres :\nRez à 5% HP/MP' WHERE nom = 'Fortin' ;
UPDATE `batiment` SET `description` = 'Défense :\n+24% esquive, +15% PP, +5% PM\nAutres :\nRez à 10% HP/MP' WHERE nom = 'Fort' ;
UPDATE `batiment` SET `description` = 'Défense :\n+32% esquive, +20% PP, +10% PM\nAutres :\nRez à 15% HP/MP' WHERE nom = 'Forteresse' ;
UPDATE `batiment` SET `description` = 'Attaque :\n+10% toucher à distance, +5% lancement sort' WHERE nom = 'Tour de guet' ;
UPDATE `batiment` SET `description` = 'Attaque :\n+20% toucher à distance, +10% lancement sort' WHERE nom = 'Tour de garde' ;
UPDATE `batiment` SET `description` = 'Attaque :\n+15% toucher à distance, +25% lancement sort, +1 à la distance d''attaque avec un bâton ou une baguette.' WHERE nom = 'Tour de mages' ;
UPDATE `batiment` SET `description` = 'Attaque :\n+30% toucher à distance, +15% lancement sort, +1 à la distance d''attaque avec un arc.' WHERE nom = 'Tour d''archers' ;
UPDATE `batiment` SET `description` = 'Multiplie les PA pour se déplacer par 4.\n+3% d''esquive.' WHERE nom = 'Palissade' ;
UPDATE `batiment` SET `description` = 'Multiplie les PA pour se déplacer par 5.\n+5% d''esquive.\n+2% de PP.' WHERE nom = 'Mur' ;
UPDATE `batiment` SET `description` = 'Multiplie les PA pour se déplacer par 6.\n+8% d''esquive.\n+5% de PP.' WHERE nom = 'Muraille' ;
UPDATE `batiment` SET `description` = 'Multiplie les PA pour se déplacer par 7.\n+16% d''esquive.\n+10% de PP\n+5% de PM' WHERE nom = 'Grande Muraille' ;

ALTER TABLE `objet_royaume` ADD INDEX ( `id_batiment` ) ;