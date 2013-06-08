-- Pour éviter qu'on puisse l'acheter sans magie de la vie
UPDATE `sort_combat` SET `comp_requis` = '1' WHERE `sort_combat`.`nom` = 'Feuilles tranchantes';
