-- Ajout des gemmes de dressage
INSERT INTO `gemme` (
`nom` ,
`type` ,
`niveau` ,
`partie` ,
`enchantement_nom` ,
`description` ,
`enchantement_type` ,
`enchantement_effet` ,
`enchantement_effet2`
)
VALUES (
'Gemme d''amitié animale', 'arme', '1', '', 'amitié animale', 'Augmente le nombre de créatures contrôlées de 1', 'max_pet', '1', '0'
), (
'Gemme de grande amitié animale', 'arme', '2', '', 'amitié animale', 'Augmente le nombre de créatures contrôlées de 2', 'max_pet', '2', '0'
), (
'Gemme d''amitié animale supérieure', 'arme', '3', '', 'amitié animale', 'Augmente le nombre de créatures contrôlées de 3', 'max_pet', '3', '0'
), (
'Gemme de contrôle animalier', 'accessoire', '2', '', 'contrôle animal', 'Augmente le niveau maximal des monstres dressés de 1', 'max_dresse', '1', '0'
), (
'Gemme de contrôle animalier supérieur', 'accessoire', '3', '', 'contrôle animal', 'Augmente le niveau maximal des monstres dressés de 2', 'max_dresse', '2', '0'
);
