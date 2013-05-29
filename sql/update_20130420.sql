-- Nouvelles compétences
INSERT INTO comp_combat (nom, description, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, duree, cible, requis, prix, lvl_batiment) VALUES ('Botte du scolopendre', 'Après un blocage, augmente les chances de critique de %effet%%', 3, 'botte_scolopendre', 'melee', 'force', 50, 'epee;dague', 10, 1, 4, '', 100, 1);
INSERT INTO comp_combat (nom, description, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, duree, cible, requis, prix, lvl_batiment) VALUES ('Botte de la tortue', 'Après un blocage, augmente les chances de toucher de %effet%%', 3, 'botte_tortue', 'melee', 'force', 75, 'epee', 10, 1, 4, '', 2250, 1);
INSERT INTO comp_combat (nom, description, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, duree, cible, requis, prix, lvl_batiment) VALUES ('Botte du rhinocéros ', 'Après une attaque réussie, augmente les chances de bloquer de %effet%%', 3, 'botte_rhinoceros', 'melee', 'force', 25, 'epee;hache', 15, 1, 4, '', 250, 1);
INSERT INTO comp_combat (nom, description, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, duree, cible, requis, prix, lvl_batiment) VALUES ('Botte du tigre', 'Après une attaque réussie, augmente les chances de toucher de %effet%%', 4, 'botte_tigre', 'melee', 'force', 90, 'hache', 10, 1, 4, '', 3240, 1);
INSERT INTO comp_combat (nom, description, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, duree, cible, requis, prix, lvl_batiment) VALUES ('Botte de l\'ours', 'Après une attaque réussie, augmente les dégâts de %effet%', 3, 'botte_ours', 'melee', 'force', 60, 'hache', 1, 1, 4, '', 1440, 1);
INSERT INTO comp_combat (nom, description, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, effet2, duree, cible, requis, prix, lvl_batiment) VALUES ('Flèche enflammée', '%effet%% de chances de faire %effet2% dégât supplémentaire', 2, 'fleche_enflammee', 'distance', 'dexterite', 60, 'arc', 20, 1, 1, 4, '', 1440, 1);
INSERT INTO comp_combat (nom, description, mp, type, comp_assoc, carac_assoc, comp_requis, arme_requis, effet, duree, cible, requis, prix, lvl_batiment) VALUES ('Flèche barbelée', 'augmente le multiplicateur en cas de critique de %effet%%', 3, 'fleche_barbelee', 'distance', 'dexterite', 30, 'arc', 5, 1, 4, '', 360, 1);

-- Modifications des compétences
UPDATE comp_combat SET comp_requis = 90 WHERE id = 59;
UPDATE comp_combat SET mp = 2 WHERE type like 'botte_scorpion';
UPDATE comp_combat SET mp = 4 WHERE type like 'botte_aigle';
UPDATE comp_combat SET effet = 30, mp = 3 WHERE id = 129;
UPDATE comp_combat SET effet = 45, mp = 4 WHERE id = 130;
UPDATE comp_combat SET effet = 45, mp = 4 WHERE id = 131;
UPDATE comp_combat SET effet = 60, mp = 5 WHERE id = 132;
UPDATE comp_combat SET effet = 60, mp = 5 WHERE id = 133;

-- Coût des bâtimenys de base en ville (pour calculer le co^t des réparations)
UPDATE batiment_ville SET cout = 500 WHERE id = 1;
UPDATE batiment_ville SET cout = 300 WHERE id = 2;
UPDATE batiment_ville SET cout = 700 WHERE id = 3;
UPDATE batiment_ville SET cout = 1000 WHERE id = 4;