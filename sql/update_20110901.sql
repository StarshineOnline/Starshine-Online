ALTER TABLE `calendrier` ENGINE = InnoDB;
ALTER TABLE `calendrier` ADD `done` BOOLEAN NOT NULL DEFAULT '0' ;
ALTER TABLE `calendrier` ADD `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;

INSERT INTO `calendrier` (
`date` ,
`sql` ,
`next`
)
VALUES 
-- maraudeur 1
('2011-09-01 18:00:00', 'insert into map_monstre (type,x,y,hp,mort_naturelle) select id, 44, 366, hp, UNIX_TIMESTAMP(NOW()) + (3600 * 2) from monstre where nom = ''Maraudeur geolier''; ', '0-0-1 00:00:00'),
('2011-09-01 20:00:00', 'delete from map_monstre where x = 44 and y = 366;', '0-0-1 00:00:00'),

-- maraudeur 2
('2011-09-01 12:00:00', 'insert into map_monstre (type,x,y,hp,mort_naturelle) select id, 48, 366, hp, UNIX_TIMESTAMP(NOW()) + (3600 * 2) from monstre where nom = ''Maraudeur geolier 2''; ', '0-0-1 00:00:00'),
('2011-09-01 14:00:00', 'delete from map_monstre where x = 48 and y = 366;', '0-0-1 00:00:00'),

-- maraudeur 3
('2011-09-01 09:00:00', 'insert into map_monstre (type,x,y,hp,mort_naturelle) select id, 52, 366, hp, UNIX_TIMESTAMP(NOW()) + (3600 * 1) from monstre where nom = ''Maraudeur geolier 3''; ', '0-0-1 00:00:00'),
('2011-09-01 10:00:00', 'delete from map_monstre where x = 52 and y = 366;', '0-0-1 00:00:00'),

-- maraudeur 4
('2011-09-01 19:00:00', 'insert into map_monstre (type,x,y,hp,mort_naturelle) select id, 56, 366, hp, UNIX_TIMESTAMP(NOW()) + (3600 / 2) from monstre where nom = ''Maraudeur geolier 4''; ', '0-0-1 00:00:00'),
('2011-09-01 19:30:00', 'delete from map_monstre where x = 56 and y = 366;', '0-0-1 00:00:00')
;
