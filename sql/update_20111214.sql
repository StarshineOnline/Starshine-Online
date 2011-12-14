CREATE TABLE `joueur_loot` (
`id_joueur` INT NOT NULL ,
`id_monstre` INT NOT NULL
) ENGINE = MYISAM COMMENT = 'Table stockant les loots des boss';

ALTER TABLE `joueur_loot` ADD PRIMARY KEY ( `id_joueur` , `id_monstre` ) ;

CREATE TABLE `boss_loot` (
`id_monstre` INT NOT NULL ,
`item` VARCHAR( 20 ) NOT NULL ,
`chance` INT NOT NULL ,
`level` TINYINT NOT NULL DEFAULT '0' COMMENT '0 - item normal, 1 - item grosbill'
) ENGINE = MYISAM COMMENT = 'définit les loots unique des boss';

ALTER TABLE `boss_loot` ADD INDEX ( `id_monstre` ) ;

