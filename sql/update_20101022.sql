UPDATE `batiment` SET `PM` = `PM`*1.2 WHERE `type` = 'arme_de_siege' AND id != '20';
UPDATE `batiment` SET `PM` = `PM`*1.4 WHERE id = '20';
UPDATE `batiment` SET `PM` = `PM`*1.5 WHERE `type` = 'bourg' OR `type` = 'mine';
