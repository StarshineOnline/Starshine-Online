-- corrections pop mobs plan de karn
update monstre set spawn = 0, terrain = '15;25' where id = 147; -- elementaire de Karn, il peut pop dans les murs
update monstre set terrain = '15;25' where id = 144; -- demon chronophage, il peut pop dans les murs
