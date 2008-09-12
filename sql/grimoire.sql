-- La définition des grimoires
drop table if exists grimoire;
create table grimoire (
	id mediumint(8) auto_increment comment 'identifiant du grimoire',
	nom varchar(250) not null comment 'nom en jeu',
	comp_jeu mediumint(8) null comment 'donne une competence de jeu',
	comp_combat mediumint(8) null comment 'donne une competence de combat',
	comp_perso_id mediumint(8) null comment 'id de la comp_perso à améliorer',
	comp_perso_competence varchar(50) comment 'nom de la comp_perso à améliorer',
	comp_perso_valueadd smallint(3) comment 'valeur à ajouter à la comp_perso',
	primary key (id)
) comment 'Définit les grimoire qui peuvent enseigner une compétence, ou en améliorer une';

-- Quelques exemples
-- Competences jeu
INSERT INTO `starshine`.`grimoire` (
`nom` ,
`comp_jeu`
)
VALUES (
'Tome de fortification', (select id from comp_jeu where nom = 'Forteresse 2')
);

-- Competences de combat
INSERT INTO `starshine`.`grimoire` (
`nom` ,
`comp_combat`
)
VALUES (
'Tome de violence', (select id from comp_combat where nom = 'Coup violent 2')
);

-- Competences joueur
INSERT INTO `starshine`.`grimoire` (
`nom` ,
`comp_perso_id`,
`comp_perso_competence`,
`comp_perso_valueadd`
)
VALUES (
'Tome de protection', 1, 'maitrise_bouclier', 15
);

