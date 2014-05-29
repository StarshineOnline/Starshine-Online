UPDATE `sort_jeu` SET `description` = 'Effectuer un déplacement vous coutera %effet2% PA supplémentaire(s)' WHERE `type` ='debuff_enracinement';
Update `sort_jeu` SET `effet2` = 1 WHERE `type` ='debuff_enracinement';
Update `sort_jeu` SET `effet` = 240 WHERE `nom` ='Enracinement 2';
update `sort_jeu` SET `effet` = 240, `effet2` = 2 WHERE `nom` = 'Enracinement 3';
