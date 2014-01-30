UPDATE `placement` p INNER JOIN `batiment_bonus` bb ON p.`id_batiment` = bb.`id_batiment`
SET p.rez = bb.valeur
WHERE bb.bonus = 'rez';

UPDATE `construction` c INNER JOIN `batiment_bonus` bb ON c.`id_batiment` = bb.`id_batiment`
SET c.rez = bb.valeur
WHERE bb.bonus = 'rez';