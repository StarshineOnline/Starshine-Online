-- Demon chronophage et son special
UPDATE `starshine`.`monstre` SET `spawn_loc` = 'p37-222;p39-221;p37-218;p39-217;p34-217;p32-223;p39-227;p32-216;p40-212;p42-231' WHERE `monstre`.`id` =144 LIMIT 1 ;

INSERT INTO `sort_combat` (
`id` ,
`nom` ,
`description` ,
`mp` ,
`type` ,
`comp_assoc` ,
`carac_assoc` ,
`carac_requis` ,
`incantation` ,
`comp_requis` ,
`effet` ,
`effet2` ,
`duree` ,
`cible` ,
`requis` ,
`prix` ,
`difficulte` ,
`lvl_batiment` ,
`etat_lie`
)
VALUES (
NULL , 'Absorbtion temporelle', 'Retire jusque %effet2% PA à la cible. Dégats + %effet%', '0', 'absorb_temporelle', 'sort_element', 'puissance', '0', '0', '1', '10', '10', '0', '4', '', '0', '1', '99', ''
);

update `monstre` set `action` = '#09=15@~180;#09=5@~180;#14<3µ#10°posture@~101;#11°appel_tenebreµ#09<15@~70;#14<3@~173;#14<3@~44;#14<3@~92;#14<3@~128;#14<3@~122' where id = 144;
