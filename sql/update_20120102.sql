UPDATE `comp_combat` SET `description` = 'Dégâts normaux, a environ 15% de chance de réduire un buff de 1 niveau',
`effet` = '5' WHERE `comp_combat`.`id` =56;

UPDATE `comp_combat` SET `description` = 'Dégâts normaux, a environ 25% de chance de réduire un buff de 1 niveau',
`effet` = '8',
`effet2` = '1' WHERE `comp_combat`.`id` =57;

UPDATE `comp_combat` SET `description` = 'Dégâts normaux, a environ 25% de chance de réduire un buff de 1 à %effet2% niveaux',
`effet` = '8' WHERE `comp_combat`.`id` =58;

UPDATE `comp_combat` SET `description` = 'Dégâts normaux, a environ 30% de chance de réduire un buff de 1 à %effet2% niveaux',
`effet` = '9' WHERE `comp_combat`.`id` =86;

UPDATE `comp_combat` SET `description` = 'Dégâts normaux, a environ 40% de chance de réduire un buff de 1 à %effet2% niveaux',
`effet` = '10' WHERE `comp_combat`.`id` =87;

UPDATE `monstre` SET `pp` = '260' WHERE `monstre`.`id` =38;
UPDATE `monstre` SET `pp` = '250' WHERE `monstre`.`id` =35;
UPDATE `monstre` SET `melee` = '150' WHERE `monstre`.`id` =5;
UPDATE `monstre` SET `melee` = '210' WHERE `monstre`.`id` =13;
UPDATE `monstre` SET `melee` = '200' WHERE `monstre`.`id` =50;
UPDATE `monstre` SET `melee` = '300' WHERE `monstre`.`id` =84;
UPDATE `monstre` SET `forcex` = '35' WHERE `monstre`.`id` =93;
UPDATE `monstre` SET `forcex` = '50', `melee` = '600', `esquive` = '500' WHERE `monstre`.`id` =104;
