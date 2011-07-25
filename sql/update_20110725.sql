delete from `titre_honorifique` where id_perso like '%-%';
ALTER TABLE `titre_honorifique` CHANGE `id_perso` `id_perso` INT NOT NULL;      
