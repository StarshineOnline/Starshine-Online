UPDATE `achievement_type` SET `description` = 'Avoir tué un developpeur !' WHERE `achievement_type`.`nom` ='Destructeur de monde';
UPDATE `achievement_type` SET `description` = 'Avoir tué %value% joueurs ayant des points de crime' WHERE `achievement_type`.`nom` ='Judge Dredd';
UPDATE `achievement_type` SET `description` = 'Avoir tué quelqu''un avec cet achievement' WHERE `achievement_type`.`nom` ='Bouche à oreille';
UPDATE `achievement_type` SET `description` = 'Être mort sans appartenir à un groupe' WHERE `achievement_type`.`nom` ='Divided we fall';
UPDATE `achievement_type` SET `description` = 'Avoir expulsé un membre de son groupe' WHERE `achievement_type`.`nom` ='Donald Trump';
UPDATE `achievement_type` SET `description` = 'Devenir le chef de son groupe' WHERE `achievement_type`.`nom` ='Envy';
UPDATE `achievement_type` SET `description` = 'Avoir envoyé %value% invitations pour entrer dans un groupe' WHERE `achievement_type`.`nom` ='Tu veux être mon gopain ?';
UPDATE `achievement_type` SET `description` = 'Avoir envoyé %value% invitations pour entrer dans un groupe' WHERE `achievement_type`.`nom` ='Gopain !';
UPDATE `achievement_type` SET `description` = 'Avoir envoyé %value% invitations pour entrer dans un groupe' WHERE `achievement_type`.`nom` ='I facebooked your mum';
UPDATE `achievement_type` SET `nom` = 'Hear me roar', `description` = 'Avoir lancé %value% cris',`titre` = 'ap-lion-1-lionne' WHERE `achievement_type`.`nom` ='Le lion';
UPDATE `achievement_type` SET `nom` = 'A la santé du colonel, tout particulièrement !', `description` = 'Avoir utilisé %value% potions', `titre` = 'ap assoifé-0-assoifée' WHERE `achievement_type`.`titre` ='ap-assoifÃ©-0-assoifÃ©e';
UPDATE `achievement_type` SET `description` = 'Avoir utilisé %value% potions' WHERE `achievement_type`.`titre` ='ap-boit sans soif-1';
UPDATE `achievement_type` SET `description` = 'Avoir la classe Rôdeur' WHERE `achievement_type`.`nom` ='Les yeux Bouh, il faut viser les yeux !';
UPDATE `achievement_type` SET `nom` = 'Arsène Lupin' WHERE `achievement_type`.`nom` ='Arsene Lupin';
UPDATE `achievement_type` SET `nom` = 'L''épée tranche la plume' WHERE `achievement_type`.`nom` ='L''Ã©pÃ©e tranche la plume';
UPDATE `achievement_type` SET `variable` = 'classe_druide oblaire' WHERE `achievement_type`.`nom` ='60 millions d''amis';
UPDATE `achievement_type` SET `nom` = 'Mais noir... Noir ?', `variable` = 'classe_archer noir' WHERE `achievement_type`.`nom` ='mais noir... Noir ?';
UPDATE `achievement_type` SET `description` = 'Avoir la classe Prêtre' WHERE `achievement_type`.`nom` ='Ave maria';
UPDATE `achievement_type` SET `description` = 'Avoir la classe Grand Nécromancien', `variable` = 'classe_grand necromancien' WHERE `achievement_type`.`nom` ='La mort n''est que le commencement';
UPDATE `achievement_type` SET `variable` = 'classe_dresseur de lombre' WHERE `achievement_type`.`nom` ='THe night is dark and full or terror';
UPDATE `achievement_type` SET `nom` = 'Promethée' WHERE `achievement_type`.`nom` ='PromethÃ©e';
UPDATE `achievement_type` SET `description` = 'Avoir la classe Archer d''élite',`variable` = 'classe_archer d élite' WHERE `achievement_type`.`nom` ='Hawkeye';
UPDATE `achievement_type` SET `description` = 'Avoir la classe Démoniste',`variable` = 'classe_demoniste' WHERE `achievement_type`.`nom` ='Diablo';
UPDATE `achievement_type` SET `description` = 'Avoir la classe Élémentaliste',`variable` = 'classe_elementaliste' WHERE `achievement_type`.`nom` ='Liloo';
UPDATE `achievement_type` SET `nom` = 'Le feu ça brûle' WHERE `achievement_type`.`nom` ='Le feu, ca brÃ»le';
UPDATE `achievement_type` SET `nom` = 'Vieux sénile' WHERE `achievement_type`.`nom` ='Vieux sÃ©nile';
UPDATE `achievement_type` SET `nom` = 'Peste et Choléra' WHERE `achievement_type`.`nom` ='Peste et CholÃ©ra';
UPDATE `achievement_type` SET `nom` = 'Croisé' WHERE `achievement_type`.`nom` ='CroisÃ©';
UPDATE `achievement_type` SET `nom` = 'Dresseur de Cerbère' WHERE `achievement_type`.`nom` ='Dresseur de CerbÃ¨re';
UPDATE `achievement_type` SET `nom` = 'Mieux vaut être accompagné que seul' WHERE `achievement_type`.`nom` ='Mieux vaut Ãªtre accompagnÃ© que seul';
UPDATE `achievement_type` SET `description` = 'Avoir la classe Nécromancien', `variable` = 'classe_nécromancien' WHERE `achievement_type`.`nom` ='I see dead people';
UPDATE `achievement_type` SET `description` = 'Avoir la classe Prédateur',`variable` = 'classe_predateur' WHERE `achievement_type`.`nom` ='If it bleed, you can kill it';
UPDATE `achievement_type` SET `description` = 'Avoir la classe Danseur Élémentaire',`variable` = 'classe_danseur elementaire' WHERE `achievement_type`.`nom` ='INAC';
UPDATE `achievement_type` SET `description` = 'Avoir posé %value% drapeaux sur du sable' WHERE `achievement_type`.`nom` ='Sand is overated, it''s juste tiny little rocks';
INSERT INTO `achievement_type` (nom, description, value, variable) VALUES ('A moi les glaces vanille, pistache, chocolat...', 'Avoir posé %value% drapeaux sur de la glace', 50, 'pose_drapeaux_glace');
INSERT INTO `achievement_type` (nom, description, value, variable) VALUES ('L''Everest ne me fait pas peur', 'Avoir posé %value% drapeaux en montagne', 50, 'pose_drapeaux_montagne');
INSERT INTO `achievement_type` (nom, description, value, variable) VALUES ('Agriculteur chevronné', 'Avoir posé %value% drapeaux sur de la plaine', 50, 'pose_drapeaux_plaine');
INSERT INTO `achievement_type` (nom, description, value, variable) VALUES ('Accrobranché', 'Avoir posé %value% drapeaux en forêt', 50, 'pose_drapeaux_foret');
INSERT INTO `achievement_type` (nom, description, value, variable) VALUES ('Ouh la gadoue, la gadoue, la gadoue', 'Avoir posé %value% drapeaux dans les marais', 50, 'pose_drapeaux_marais');
INSERT INTO `achievement_type` (nom, description, value, variable) VALUES ('Éleveur de moustiques', 'Avoir posé %value% drapeaux en terres maudites', 50, 'pose_drapeaux_terremaudite');
INSERT INTO `achievement_type` (nom, description, value, variable) VALUES ('King of the road', 'Avoir posé %value% drapeaux sur la route', 50, 'pose_drapeaux_route');
UPDATE `achievement_type` SET `titre` = 'ap-Kingslayer-1' WHERE `achievement_type`.`nom` ='Multi régiciviste';
UPDATE `achievement_type` SET `description` = 'Avoir tué son propre roi', `titre` = 'ap-assassin royal-0' WHERE `achievement_type`.`nom` ='Ravaillac';

