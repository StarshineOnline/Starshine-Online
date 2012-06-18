-- -*- coding: latin-1-unix -*-
insert into sort_combat
(nom, description, mp,
 type, comp_assoc, carac_assoc, carac_requis, incantation, comp_requis, effet,
 effet2, duree, cible, requis, prix, difficulte, lvl_batiment)
values
('Debuff Nysin Femelle', 'Lance le debuff debuff_bloque_deplacement_alea', 1,
 'debuff_nysin_femelle', 'sort_element', 'volonte', 0, 300, 200, 5,
 0, 0, 1, 9999, 0, 150, 9);