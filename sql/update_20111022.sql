-- enracinnement en sort de mob
insert into sort_combat(
				nom,
				description,
				type,
				comp_assoc,
				carac_assoc,
				carac_requis,
				incantation,
				comp_requis,
				effet,
				effet2,
				duree,
				cible,
				requis,
				prix,
				difficulte,
				lvl_batiment,
				etat_lie
) select 
	nom,
	description,
	type,
	comp_assoc,
	carac_assoc,
	carac_requis,
	incantation,
	comp_requis,
	30, -- la durée du debuff
	24, -- difficulté du jet
	0,
	4,
	9999,
	0,
	difficulte,
	9,
	null 
from sort_jeu where nom = 'enracinement';

-- script des racines :
update monstre set action = 
			 concat(';#14<1@~', last_insert_id(), ';#10°posture@~101;#10°benediction@~90;#14<3µ#09<5@_6;#14<3@~65;#14<1@~152;#14<3@_102;#14<3@~173;#14<3@_77;#14<3@_80')
			 where nom = 'Racines hurlantes';
