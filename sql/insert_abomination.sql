delete from map_monstre where type in (select id from monstre where lib like 'abomination_gob_%' OR lib like 'abomination_tentacule_haut_%');

insert into map_monstre values(null, (select id from monstre where lib = 'abomination_tentacule_haut_gauche'), 16, 293, 1000000, 2279168080);
insert into map_monstre values(null, (select id from monstre where lib = 'abomination_tentacule_haut_milieu'), 17, 293, 1000000, 2279168080);
insert into map_monstre values(null, (select id from monstre where lib = 'abomination_tentacule_haut_droite'), 18, 293, 1000000, 2279168080);
insert into map_monstre values(null, (select id from monstre where lib = 'abomination_gob_tete_gauche'), 16, 294, 1000000, 2279168080);
insert into map_monstre values(null, (select id from monstre where lib = 'abomination_gob_tete'), 17, 294, 1000000, 2279168080);
insert into map_monstre values(null, (select id from monstre where lib = 'abomination_gob_tete_droite'), 18, 294, 1000000, 2279168080);
insert into map_monstre values(null, (select id from monstre where lib = 'abomination_gob_tentacule_gauche'), 16, 295, 1000000, 2279168080);
insert into map_monstre values(null, (select id from monstre where lib = 'abomination_gob_ventre'), 17, 295, 1000000, 2279168080);
insert into map_monstre values(null, (select id from monstre where lib = 'abomination_gob_tentacule_droite'), 18, 295, 1000000, 2279168080);
insert into map_monstre values(null, (select id from monstre where lib = 'abomination_gob_bras_gauche'), 15, 296, 1000000, 2279168080);
insert into map_monstre values(null, (select id from monstre where lib = 'abomination_gob_patte_gauche'), 16, 296, 1000000, 2279168080);
insert into map_monstre values(null, (select id from monstre where lib = 'abomination_gob_bas_ventre'), 17, 296, 1000000, 2279168080);
insert into map_monstre values(null, (select id from monstre where lib = 'abomination_gob_patte_droite'), 18, 296, 1000000, 2279168080);
insert into map_monstre values(null, (select id from monstre where lib = 'abomination_gob_bras_droit'), 19, 296, 1000000, 2279168080);
