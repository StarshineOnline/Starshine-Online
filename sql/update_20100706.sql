-- maj des spawn berseker gob pour nouvelles salles draconides
UPDATE `monstre` SET `spawn_loc` = 'p36-284;p33-285;p35-285;p36-285;p14-285;p13-286;p14-286;p13-287;p12-288;p13-288;p27-290;p28-290;p27-291;p28-291;p28-291;p31-293;p32-292;p34-293;p35-293;p36-293;p37-293;p38-293;p38-291;p39-291;p40-291;p39-293;p39-292;p39-294;p43-293;p43-292;p43-294;p44-292;p44-294;p34-286;p35-286;p36-283;p36-286;p37-283;p37-284;p37-285;p37-286;p12-286;p12-287;p16-285' WHERE `monstre`.`id` =122 LIMIT 1 ;

update map_monstre set x=14 where x=13 and y=285;


insert into monstre select null, 'necro_serviteur', 'Serviteur d''Adénnaïos', type, floor(hp * 1.2), floor(pp * 1.2), floor(pm * 1.2), forcex + 1, dexterite + 1, puissance + 1, volonte + 1, energie + 1, melee + 100, esquive + 100, incantation + 100, sort_vie + 100, sort_mort + 100, dressage, sort_dressage, sort_element + 100, arme, action, level + 1, floor(xp * 1.2), floor(star * 1.2), drops, spawn, 'p6-221;p7-221;p8-221;p9-221;p10-221;p11-221;p12-221;p12-222;p12-223;p12-224;p12-225;p12-226;p12-227;p11-222;p11-225;p11-226;p13-225;p13-226;p14-227;p15-227', terrain, affiche, '' from monstre where lib = 'spectre_myriandre';
