-- maj des spawn berseker gob pour nouvelles salles draconides
UPDATE `monstre` SET `spawn_loc` = 'p36-284;p33-285;p35-285;p36-285;p14-285;p13-286;p14-286;p13-287;p12-288;p13-288;p27-290;p28-290;p27-291;p28-291;p28-291;p31-293;p32-292;p34-293;p35-293;p36-293;p37-293;p38-293;p38-291;p39-291;p40-291;p39-293;p39-292;p39-294;p43-293;p43-292;p43-294;p44-292;p44-294;p34-286;p35-286;p36-283;p36-286;p37-283;p37-284;p37-285;p37-286;p12-286;p12-287;p16-285' WHERE `monstre`.`id` =122 LIMIT 1 ;

update map_monstre set x=14 where x=13 and y=285;

