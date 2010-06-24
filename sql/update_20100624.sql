-- -*- mode: sql -*-
-- machins pour les quêtes du donjon gob

INSERT INTO `quete` (
`id` ,
`nom` ,
`description` ,
`fournisseur` ,
`objectif` ,
`exp` ,
`honneur` ,
`star` ,
`reward` ,
`repete` ,
`mode` ,
`royaume` ,
`niveau_requis` ,
`honneur_requis` ,
`quete_requis` ,
`star_royaume` ,
`lvl_joueur` ,
`achat`
)
VALUES (
85 , 'Nouvelle carte', 'Ma carte commence à se faire vieille, il faudrait que je la renouvelle, mais je n''ai pas d''inspiration. Si vous pouviez me trouver un livre de recettes original, je vous donnerais une carte de membre de l''amicale des Taverniers.', 'taverne', 'a:1:{i:0;O:8:"stdClass":3:{s:5:"cible";s:4:"Oo52";s:6:"nombre";i:1;s:6:"requis";s:0:"";}}', '0', '0', '0', '', 'n', 's', '', '5', '0', '', '1', '0', 'non'
);

INSERT INTO `objet` (
`id` ,
`nom` ,
`type` ,
`prix` ,
`achetable` ,
`stack` ,
`utilisable` ,
`effet` ,
`description` ,
`pa` ,
`mp`
)
VALUES (
52 , 'Livre de recettes', 'objet_quete', '0', 'n', '0', 'y', '0', 'Un livre de recettes gobelines', '0', '0'
);

INSERT INTO `pnj` (
`id` ,
`nom` ,
`image` ,
`x` , 
`y` ,
`texte`
)
VALUES (
14 , 'Cusinier gobelin', 'goblin_cuisinier', '11', '280', '<em>Un gobelin en costume de cuisinier vous regarde d''un air apeuré, cherchant à se cacher derrière son fourneau.</em>
[ISQUETE:85][ID:1]Donne-moi ton livre de recettes, ou je te fais la peau ![/ID:1]
[ID:2]Bonjour, je cherche un livre de recettes originales, vous auriez ca ?[/ID:2][/ISQUETE:85][QUETEFINI85:3]Vous m''avez déjà pris mon livre ... je n''ai plus rien pour vous ...[/QUETEFINI85:3]
[retour]
*****
[donneitem:o52][verifinventaire:85]<em>Le petit être vous donne son livre</em>
*****
Oui, mes ce sont mes secrets de fabrication ...

[ID:1]Donne-moi ton livre de recettes, où je te fais la peau ![/ID:1]
*****
C''est vrai ...
[retour]'
);

ALTER TABLE `taverne` ADD `requis` VARCHAR( 250 ) NULL ;
INSERT INTO `taverne` (
`ID` ,
`nom` ,
`nom_f` ,
`pa` ,
`honneur` ,
`honneur_pc` ,
`hp` ,
`hp_pc` ,
`mp` ,
`mp_pc` ,
`star` ,
`pute` ,
`requis`
)
VALUES (
NULL , 'Repas d''ami', 'Repas d''amie', '12', '0', '', '60', '7', '45', '0', '70', '0', 'q85'
);
