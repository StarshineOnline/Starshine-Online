-- -*- coding: latin-1-unix -*-
UPDATE  `sort_combat` SET  `nom` =  'Entraves de Nysin' WHERE  `sort_combat`.`type` = 'debuff_nysin_femelle';
insert into sort_combat
(nom, description, mp,
 type, comp_assoc, carac_assoc, carac_requis, incantation, comp_requis, effet,
 effet2, duree, cible, requis, prix, difficulte, lvl_batiment)
select 'Hérésie de Nysin', description, 5, 
 type, comp_assoc, carac_assoc, carac_requis, incantation, comp_requis, effet,
 720, duree, cible, requis, prix, difficulte, lvl_batiment
from `sort_combat` where `sort_combat`.`type` = 'heresie_divine' limit 1;
