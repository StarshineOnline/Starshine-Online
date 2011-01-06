-- Hurlement abominable
insert into sort_combat
 (id,nom,description,mp,type,comp_assoc,carac_assoc,carac_requis,incantation,comp_requis,effet,effet2,duree,cible,requis,prix,difficulte,lvl_batiment,etat_lie)
 select 181,'hurlement abominable', 'L''abomination rugit, terrorisant les spectateurs (jet de volonté DC %effet2% pour ne pas être terrorisé)',mp,
  'cri_abomination',comp_assoc,carac_assoc,carac_requis,incantation,comp_requis,
  0,effet2 - 5, -- pas d'effet, effet2 - 5 (plus facile de resister sans empalement)
duree,cible,requis,prix,difficulte,lvl_batiment,etat_lie from sort_combat where nom='Empalement abominable';

update monstre set action = concat('#09=1@~181;', action) where level = 100 and lib like 'abomination_gob%';
