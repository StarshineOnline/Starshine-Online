ALTER TABLE `classe_comp_permet` ADD UNIQUE (
`id_classe` ,
`competence` ,
`type`
);

ALTER TABLE `classe_comp_permet` CHANGE `type` `type` ENUM( 'comp_combat', 'comp_jeu', 'sort_combat', 'sort_jeu' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'comp_combat';

insert into `classe_comp_permet` (`id_classe`, `competence`, `type`) select c.id, s.id, 'sort_jeu' from classe c, sort_jeu s where s.requis = concat('classe:', c.nom) ; 
