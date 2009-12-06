-- Crée la table pour les arènes
drop table if exists arenes;
 CREATE TABLE `arenes` (
`x` INT NOT NULL COMMENT 'origine X',
`y` INT NOT NULL COMMENT 'origine Y',
`size` INT NOT NULL COMMENT 'taille',
`nom` VARCHAR( 255 ) NOT NULL COMMENT 'nom de l''arène (affiché)',
`file` VARCHAR( 255 ) NOT NULL COMMENT 'nom du fichier généré',
PRIMARY KEY ( `nom` ) ,
UNIQUE (
`file`
)
) ENGINE = MYISAM COMMENT = 'Arènes' ;

INSERT INTO `arenes` (
`x` ,
`y` ,
`size` ,
`nom` ,
`file`
)
VALUES (
'201', '2', '10', 'Arène 1', 'arene_1.xml'
);
