-- -*- sql -*-
INSERT INTO `objet` (
`id` ,
`nom` ,
`type` ,
`prix` ,
`achetable` ,
`stack` ,
`utilisable` ,
`effet` ,
`description` ,
`pa` ,
`mp`
)
VALUES (
-- herbe
NULL , 'Herbe d''arbrubozis néfaste', 'fabrication', '750', 'n', '10', 'n', '0', 'Ingrédient servant a la fabrication d''objets.', '0', '0'
), (
-- potion
NULL , 'Potion de protection magique', 'potion_pm', '2000', 'n', '0', 'y', '75', 'Augmente la PM de %effet% %', '5', '5'
), (
-- sel
NULL , 'Sel de spiral jetty', 'fabrication', '0', 'n', '0', 'n', '0', 'Ingredient servant a la fabrication d''objets.', '0', '0'
);

-- une chance sur 5 de drop l'herbe
UPDATE `monstre` SET `drops` = (select concat('o', id, '-5') from objet where nom = 'Herbe d''arbrubozis néfaste') where nom = 'arbrubozis nefaste';

-- la recette
INSERT INTO `recette` 
SELECT 
			 null id, 
			 'Préparation de potion de protection magique' nom, 
			 concat((SELECT concat(`id`, '-2') FROM `objet` WHERE `nom` = 'Herbe d''arbrubozis néfaste'),
			 				';',
							(SELECT concat(id, '-1') FROM `objet` WHERE `nom` = 'Sel de spiral jetty')
			 ) ingredient,
			 (SELECT concat('o', `id`, '-1') FROM `objet` WHERE `nom` = 'Potion de protection magique') resultat,
			 999 difficulte,
			 4 pa;
