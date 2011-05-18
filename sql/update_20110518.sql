-- mode: sql; codin: utf-8; -*-
-- Préparation de potion de protection magique dans les bonnes tables
INSERT INTO `craft_recette` (id, nom, pa, mp, `type`, difficulte, resultat, royaume_alchimie, prix)
			 SELECT null id, 'Préparation de potion de protection magique' nom, 0 pa, 0 mp, 'protection' type, 50 difficulte, (SELECT concat('o', `id`, '-1') FROM `objet` WHERE `nom` = 'Potion de protection magique') resultat, 99999999 royaume_alchimie, 99999999 prix;

insert into `craft_recette_instrument` (`id_recette`, `type`) select `id`, 'mortier' from `craft_recette` where `nom` = 'Préparation de potion de protection magique';

insert into `craft_recette_recipient` (id_recette, id_objet, resultat, prefixe)
			 select craft_recette.id, objet.id, craft_recette.resultat, objet.nom
			 from craft_recette, objet
			 where objet.`type` = 'fiole' and craft_recette.nom = 'Préparation de potion de protection magique';

insert into `craft_recette_ingredient` (id_recette, id_ingredient, nombre)
			 select craft_recette.id, objet.id, 2 from craft_recette, objet
			 where objet.`nom` = 'Sel de spiral jetty' and craft_recette.nom = 'Préparation de potion de protection magique'
union all select craft_recette.id, objet.id, 1 from craft_recette, objet
			where objet.`nom` = 'Herbe d''arbrubozis néfaste' and craft_recette.nom = 'Préparation de potion de protection magique';

