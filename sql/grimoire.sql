-- La définition des grimoires
drop table if exists grimoire;
create table grimoire (
	id mediumint(8) auto_increment comment 'identifiant du grimoire',
	nom varchar(250) not null comment 'nom en jeu',
	comp_jeu mediumint(8) null comment 'donne une competence de jeu',
	comp_combat mediumint(8) null comment 'donne une competence de combat',
	comp_perso_id mediumint(8) null comment 'id de la comp_perso à améliorer',
	comp_perso_competence varchar(50) null comment 'nom de la comp_perso à améliorer',
	comp_perso_valueadd smallint(3) null comment 'valeur à ajouter à la comp_perso',
	classe_requis varchar(65000) null comment 'classes donnant accès au grimoire',
	primary key (id)
) comment 'Définit les grimoire qui peuvent enseigner une compétence, ou en améliorer une';

-- Quelques exemples
-- Competences jeu
INSERT INTO `grimoire` (
`nom` ,
`comp_jeu`,
`classe_requis`
)
VALUES (
'Tome de fortification', (select id from comp_jeu where nom = 'Forteresse 2'), null
);

-- Competences de combat
INSERT INTO `grimoire` (
`nom` ,
`comp_combat`,
`classe_requis`
)
VALUES (
'Tome de violence', (select id from comp_combat where nom = 'Coup violent 2'), null
);

-- Competences joueur
INSERT INTO `grimoire` (
`nom` ,
`comp_perso_id`,
`comp_perso_competence`,
`comp_perso_valueadd`,
`classe_requis`
)
VALUES (
'Tome de protection', 1, 'maitrise_bouclier', 15, null
),(
'Tome d\'archerie élémentaire', 1, 'maitrise_arc', 1, null
),(
'Tome d\'escrime élémentaire', 1, 'maitrise_epee', 1, null
),(
'Tome de hache élémentaire', 1, 'maitrise_hache', 1, null
),(
'Tome d\'escrime courtoise', 1, 'maitrise_epee', 5, 'paladin;paladin+'
);


-- Grimoires de debug (pas en jeu)
/*
INSERT INTO `grimoire` (
`nom` ,
`comp_combat`,
`classe_requis`
)
VALUES (
'Tome de puissance universelle', 1, 'maitrise_dommage', 2, null
),(
'Tome de sournoiserie combattante', (select id from comp_combat where nom = 'Coup sournois'), 'champion;paladin'
);
*/