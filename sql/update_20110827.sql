-- infra pour le debuff sabotage
CREATE TABLE `buff_batiment` (
`id_construction` INT NULL DEFAULT NULL COMMENT 'id du bâtiment s''il est construit',
`id_placement` INT NULL DEFAULT NULL COMMENT 'id du bâtiment s''il est en construction',
`date_fin` INT NOT NULL ,
`duree` INT NOT NULL ,
`type` VARCHAR( 50 ) NOT NULL ,
`effet` INT NOT NULL
) ENGINE = MYISAM ;

ALTER TABLE `buff_batiment` ADD UNIQUE `pk` ( `id_construction` , `id_placement` , `type` ) ;
ALTER TABLE `buff_batiment` ADD INDEX ( `date_fin` ) ;
ALTER TABLE `buff_batiment` ADD INDEX ( `id_construction` ) ;
ALTER TABLE `buff_batiment` ADD INDEX ( `id_placement` ) ;

-- création du debuff (compétence)
INSERT INTO `comp_jeu` (`nom`, `description`, `mp`, `pa`, `type`, `comp_assoc`, `carac_assoc`, `carac_requis`, `comp_requis`, `arme_requis`, `effet`, `effet2`, `duree`, `cible`, `requis`, `prix`, `lvl_batiment`) VALUES ('Sabotage', 'Empêche le soin et / ou l''accélération de construction', '30', '5', 'sabotage', 'esquive', 'dexterite', '0', '0', '', '1', '0', '86400', '1', 'classe:ombre', '0', '9');

-- don de la comp à la montée
insert into `classe_comp_permet` (id_classe, competence, type) 
select id, (select id from comp_jeu where nom = 'sabotage'), 'comp_jeu' 
from classe where nom = 'ombre';
-- don aux anciens
update `perso` set comp_jeu = concat(comp_jeu, ';', (select id from comp_jeu where nom = 'sabotage')) where classe = 'ombre';
