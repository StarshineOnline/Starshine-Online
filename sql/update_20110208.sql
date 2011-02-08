UPDATE `comp_combat` SET `description` = 'Réduit vos chances de toucher physiquement de %effet%%, multiplie les dégats par 3, et augmente les chances de critique de %effet2%%.', `effet2` = '60' WHERE `comp_combat`.`id` = 29; 
UPDATE `comp_combat` SET `description` = 'Réduit vos chances de toucher de %effet%%, multiplie les dégats par 3, et augmente les chances de critique de %effet2%%.', `effet2` = '60' WHERE `comp_combat`.`id` = 30; 
UPDATE `comp_combat` SET `description` = 'Réduit vos chances de toucher de %effet%%, multiplie les dégats par 3, et augmente les chances de critique de %effet2%%.', `effet2` = '60' WHERE `comp_combat`.`id` = 31; 
UPDATE `comp_combat` SET `description` = 'Réduit vos chances de toucher de %effet%%, multiplie les dégats par 3, et augmente les chances de critique de %effet2%%.(requiert coup mortel 3)', `effet2` = '75' WHERE `comp_combat`.`id` = 114; 
UPDATE `comp_combat` SET `description` = 'Réduit vos chances de toucher de %effet%%, multiplie les dégats par 3, et augmente les chances de critique de %effet2%%.', `effet2` = '90' WHERE `comp_combat`.`id` = 115;
UPDATE `objet_royaume` SET `pierre` = '25' WHERE `objet_royaume`.`id` =1;
UPDATE `objet_royaume` SET `prix` = '200',
`pierre` = '300',
`bois` = '300',
`eau` = '200',
`sable` = '200',
`charbon` = '500',
`essence` = '200' WHERE `objet_royaume`.`id` =10;
UPDATE `objet_royaume` SET `charbon` = '345',`essence` = '300' WHERE `objet_royaume`.`id` =15;
UPDATE `objet_royaume` SET `charbon` = '600',`essence` = '500' WHERE `objet_royaume`.`id` =16;
UPDATE `objet_royaume` SET `charbon` = '1150',`essence` = '1000' WHERE `objet_royaume`.`id` =17;
UPDATE `objet_royaume` SET `charbon` = '345',`essence` = '300' WHERE `objet_royaume`.`id` =18;
UPDATE `objet_royaume` SET `pierre` = '100' WHERE `objet_royaume`.`id` =19;
UPDATE `objet_royaume` SET `pierre` = '200' WHERE `objet_royaume`.`id` =20;
UPDATE `objet_royaume` SET `pierre` = '400' WHERE `objet_royaume`.`id` =21;
UPDATE `objet_royaume` SET `prix` = '200',
`pierre` = '0',
`bois` = '0',
`eau` = '0',
`sable` = '0',
`charbon` = '0',
`essence` = '0' WHERE `objet_royaume`.`id` =22;