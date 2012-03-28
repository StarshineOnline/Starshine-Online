-- Le spécial
INSERT INTO `sort_combat` (
`id` ,
`nom` ,
`description` ,
`mp` ,
`type` ,
`comp_assoc` ,
`carac_assoc` ,
`carac_requis` ,
`incantation` ,
`comp_requis` ,
`effet` ,
`effet2` ,
`duree` ,
`cible` ,
`requis` ,
`prix` ,
`difficulte` ,
`lvl_batiment` ,
`etat_lie`
)
VALUES (
NULL , 'Empalement abominable', 'Empale la cible (dégâts +%effet% au minimum, cible réduite à 1 PV si elle survit), les spectateurs font un jet de volonté DC %effet2% pour ne pas être terrorisés.', '1', 'empalement_abomination', 'sort_mort', 'puissance', '100', '1', '1', '100', '28', '0', '4', '', '0', '1', '9', ''
);

-- La bebête ...
INSERT INTO `monstre` (`id`, `lib`, `nom`, `type`, `hp`, `pp`, `pm`, `forcex`, `dexterite`, `puissance`, `volonte`, `energie`, `melee`, `esquive`, `incantation`, `sort_vie`, `sort_mort`, `dressage`, `sort_dressage`, `sort_element`, `arme`, `action`, `level`, `xp`, `star`, `drops`, `spawn`, `spawn_loc`, `terrain`, `affiche`, `description`) VALUES
(null, 'abomination_gob_tete', 'Tête de l''abomination gobeline', 'magique', 1000000, 10000, 10000, 100, 100, 100, 100, 100, 1000, 1000, 1000, 1000, 1000, 999999, '', 1000, 'dague', '#09=20@~178;#10°berzeker@_99;#10°posture@_108;#14<4@~65;#14<2@~164;#14<4@~173;#11°lien_sylvestre@~83;#13+lien_sylvestreµ#14<4@_115;#14<4@_85', 100, 8388607, 10000, 'hg3-3;hg3-3;hg3-3;hg2-2;hg2-2;hg2-2;hg2-2;hg1-1;hg1-1;hg1-1;hg1-1;hg1-1;m13-10;p124-10;a41-10;p125-5;p126-5;a67-5;a68-3', 0, '', '15', 'y', 'Une indicible abomination');

-- ses autres parties ...
insert into monstre select null, 'abomination_gob_bas_ventre', 'Bas-ventre de l''abomination gobeline', type,hp,pp,pm,forcex,dexterite,puissance,volonte,energie,melee,esquive,incantation,sort_vie,sort_mort,dressage,sort_dressage,sort_element,arme,action,level,xp,star,drops,spawn,spawn_loc,terrain,affiche,description from monstre where lib='abomination_gob_tete';
insert into monstre select null, 'abomination_gob_bras_droit', 'Bras droit de l''abomination gobeline', type,hp,pp,pm,forcex,dexterite,puissance,volonte,energie,melee,esquive,incantation,sort_vie,sort_mort,dressage,sort_dressage,sort_element,arme,action,level,xp,star,drops,spawn,spawn_loc,terrain,affiche,description from monstre where lib='abomination_gob_tete';
insert into monstre select null, 'abomination_gob_bras_gauche', 'Bras gauche de l''abomination gobeline', type,hp,pp,pm,forcex,dexterite,puissance,volonte,energie,melee,esquive,incantation,sort_vie,sort_mort,dressage,sort_dressage,sort_element,arme,action,level,xp,star,drops,spawn,spawn_loc,terrain,affiche,description from monstre where lib='abomination_gob_tete';
insert into monstre select null, 'abomination_gob_patte_gauche', 'Patte gauche de l''abomination gobeline', type,hp,pp,pm,forcex,dexterite,puissance,volonte,energie,melee,esquive,incantation,sort_vie,sort_mort,dressage,sort_dressage,sort_element,arme,action,level,xp,star,drops,spawn,spawn_loc,terrain,affiche,description from monstre where lib='abomination_gob_tete';
insert into monstre select null, 'abomination_gob_patte_droite', 'Patte droite de l''abomination gobeline', type,hp,pp,pm,forcex,dexterite,puissance,volonte,energie,melee,esquive,incantation,sort_vie,sort_mort,dressage,sort_dressage,sort_element,arme,action,level,xp,star,drops,spawn,spawn_loc,terrain,affiche,description from monstre where lib='abomination_gob_tete';
insert into monstre select null, 'abomination_gob_tentacule_droite', 'Tentacule droite de l''abomination gobeline', type,hp,pp,pm,forcex,dexterite,puissance,volonte,energie,melee,esquive,incantation,sort_vie,sort_mort,dressage,sort_dressage,sort_element,arme,action,level,xp,star,drops,spawn,spawn_loc,terrain,affiche,description from monstre where lib='abomination_gob_tete';
insert into monstre select null, 'abomination_gob_tentacule_gauche', 'Tentacule gauche de l''abomination gobeline', type,hp,pp,pm,forcex,dexterite,puissance,volonte,energie,melee,esquive,incantation,sort_vie,sort_mort,dressage,sort_dressage,sort_element,arme,action,level,xp,star,drops,spawn,spawn_loc,terrain,affiche,description from monstre where lib='abomination_gob_tete';
insert into monstre select null, 'abomination_tentacule_haut_gauche', 'Tentacule gauche de l''abomination gobeline', type,hp,pp,pm,forcex,dexterite,puissance,volonte,energie,melee,esquive,incantation,sort_vie,sort_mort,dressage,sort_dressage,sort_element,arme,action,level,xp,star,drops,spawn,spawn_loc,terrain,affiche,description from monstre where lib='abomination_gob_tete';
insert into monstre select null, 'abomination_tentacule_haut_droite', 'Tentacule droite de l''abomination gobeline', type,hp,pp,pm,forcex,dexterite,puissance,volonte,energie,melee,esquive,incantation,sort_vie,sort_mort,dressage,sort_dressage,sort_element,arme,action,level,xp,star,drops,spawn,spawn_loc,terrain,affiche,description from monstre where lib='abomination_gob_tete';
insert into monstre select null, 'abomination_tentacule_haut_milieu', 'Tentacule de l''abomination gobeline', type,hp,pp,pm,forcex,dexterite,puissance,volonte,energie,melee,esquive,incantation,sort_vie,sort_mort,dressage,sort_dressage,sort_element,arme,action,level,xp,star,drops,spawn,spawn_loc,terrain,affiche,description from monstre where lib='abomination_gob_tete';
insert into monstre select null, 'abomination_gob_ventre', 'Ventre de l''abomination gobeline', type,hp,pp,pm,forcex,dexterite,puissance,volonte,energie,melee,esquive,incantation,sort_vie,sort_mort,dressage,sort_dressage,sort_element,arme,action,level,xp,star,drops,spawn,spawn_loc,terrain,affiche,description from monstre where lib='abomination_gob_tete';
insert into monstre select null, 'abomination_gob_tete_droite', 'Tête de l''abomination gobeline', type,hp,pp,pm,forcex,dexterite,puissance,volonte,energie,melee,esquive,incantation,sort_vie,sort_mort,dressage,sort_dressage,sort_element,arme,action,level,xp,star,drops,spawn,spawn_loc,terrain,affiche,description from monstre where lib='abomination_gob_tete';
insert into monstre select null, 'abomination_gob_tete_gauche', 'Tête de l''abomination gobeline', type,hp,pp,pm,forcex,dexterite,puissance,volonte,energie,melee,esquive,incantation,sort_vie,sort_mort,dressage,sort_dressage,sort_element,arme,action,level,xp,star,drops,spawn,spawn_loc,terrain,affiche,description from monstre where lib='abomination_gob_tete';

-- et ses tentacules baladeurs, basés sur le berzerker gob, en boosté
insert into monstre select null, 'tentacules_des_profondeurs', 'Tentacules des profondeurs', 'magique', hp * 2, FLOOR(pp * 1.5), FLOOR(pm * 1.5), forcex,dexterite,puissance,volonte,energie,melee,esquive,incantation,sort_vie,sort_mort,dressage,sort_dressage,sort_element,arme,action, level + 1, FLOOR(xp * 1.5), star * 2,drops,spawn,
'p15-297;p16-297;p17-297;p18-297;p15-298;p16-298;p17-298;p18-298;p17-299;p17-300;p17-301;p17-302;p18-302;p19-302;p20-302;p20-303;p20-304;p15-292;p15-293;p19-292;p19-293', -- ses spawns
terrain,affiche,'Tentacules mysterieux sortant du sol ...' from monstre where lib='berserker_goblin';

