---- technique
-- calendrier: ajout d'une colonne d'id logique pour s'y retrouver
-- dans les entrées manuelles, et ne plus se baser sur le sql ou l'eval
ALTER TABLE `calendrier` ADD `id_manuel` VARCHAR( 50 ) NULL DEFAULT NULL COMMENT 'identificateur pour les entrées manuelles' ,
ADD UNIQUE (
`id_manuel`
);
-- cet id doit être unique. Le laissser à NULL pour les entrées auto

-- je considère que personne n'a utilisé le calendrier avant moi pour les id <9
UPDATE `calendrier` SET `id_manuel` = 'pop maraudeur geolier 1' WHERE `calendrier`.`id` = 1; 
UPDATE `calendrier` SET `id_manuel` = 'kill maraudeur geolier 1' WHERE `calendrier`.`id` = 2; 
UPDATE `calendrier` SET `id_manuel` = 'pop maraudeur geolier 2' WHERE `calendrier`.`id` = 3; 
UPDATE `calendrier` SET `id_manuel` = 'kill maraudeur geolier 2' WHERE `calendrier`.`id` = 4; 
UPDATE `calendrier` SET `id_manuel` = 'pop maraudeur geolier 3' WHERE `calendrier`.`id` = 5; 
UPDATE `calendrier` SET `id_manuel` = 'kill maraudeur geolier 3' WHERE `calendrier`.`id` = 6; 
UPDATE `calendrier` SET `id_manuel` = 'pop maraudeur geolier 4' WHERE `calendrier`.`id` = 7; 
UPDATE `calendrier` SET `id_manuel` = 'kill maraudeur geolier 4' WHERE `calendrier`.`id` = 8; 
-- ici je l'ai au moins utilisé sur mon serveur de test
UPDATE `calendrier` SET `id_manuel` = 'depart Leonard Sileyn' WHERE `calendrier`.`sql` like 'update pnj set x=30, y=320 %';
UPDATE `calendrier` SET `id_manuel` = 'retour Leonard Sileyn' WHERE `calendrier`.`sql` like 'update pnj set x=30, y=326 %';

---- I.A
-- Geolier 3: uniquement les lundi, mardi, et mercredi
UPDATE `calendrier` SET `eval` = '// date du jour
$d = date(''w'');
// on calcule les jours à ajouter au jour paramétré
switch ($d)
{
  case 0: // dimanche
    $a = 0; // cas improbable
    break;
  case 1: // lundi
  case 2: // mardi
    $a = 0; // on refait le lendemain
    break;
  case 3: // mercredi
    $a = 4; // on saute 4 jours : lundi prochain
    break;
  case 4: // jeudi
    $a = 3; // cas improbable
    break;
  case 5: // vendredi
    $a = 2; // cas improbable
    break;
  case 6: // samedi
    $a = 1; // cas improbable
    break;
}
if ($a > 0) {
  $sql = ''update `calendrier` set `date` = DATE_ADD(`date`, interval ''.
    $a.'' day) where id = ''.$row->id;
  if (!$mysqli->query($sql)) die($mysqli->error);
}' WHERE `calendrier`.`id_manuel` = 'pop maraudeur geolier 3';

---- I.C
-- projection au nord des joueurs qui seraient sur la porte
UPDATE `calendrier` SET `sql` = 'update pnj set x=30, y=326 where nom = ''Leonard Sileyn''; update map set decor = 4663, info = 46 where x=30 and y=325;
update perso set y = y - 1, hp = hp - 10 where x=30 and y=326;' WHERE `calendrier`.`id_manuel` = 'retour Leonard Sileyn';
-- retour au sud des joueurs qui seraient 'stockés' dans le passage
UPDATE `calendrier` SET `sql` = 'update pnj set x=30, y=320 where nom = ''Leonard Sileyn''; update map set decor = 10676, info = 106 where x=30 and y=325;
update perso set y = y + 1 where x=30 and y=325' WHERE `calendrier`.`id_manuel` = 'depart Leonard Sileyn';


---- II
-- creation du morceau de canalisation
INSERT INTO `objet` (`nom`, `type`, `prix`, `achetable`, `stack`, `utilisable`, `effet`, `description`, `pa`, `mp`) VALUES ('Morceau de canalisation', 'repaation_canalisation', '0', 'n', '0', 'n', '0', 'Un morceau de canalisation permettant de réparer le réseau du sous-sol ... enfin, c''est ce que vous a promis le vendeur.', '0', '0');
-- id: 57

-- Amélioration des relations diplomatiques: id 88
-- Exploration des strates inférieures: id 89
-- vente de tuyaux par brizen
UPDATE `pnj` SET `texte` = '<em>(gratte gratte)

(gratte oreille droite)

(lèche patte gauche)</em>

<em>Ho ! des clients !</em>

Bonjour !
Je vends des tuyaux, vous en voulez ? Seulement 10000 stars, et tellement indispensables pour se rendre dans certaines parties des grottes, que vous ne pourrez pas vous en passer !

[ID:1]Oui, j''en prendrai un, merci[/ID:1]
[quete_finie:88][ID:2]On m''a dit que vous vous intéressiez à l''amélioration des relations diplomatiques ...[/ID:2][/quete_finie:88]
[quete_finie:89][ID:3]Vous saviez que j''ai exploré les strates inférieures ? ...[/ID:3][/quete_finie:89]
*****
<em>(rire narquois)</em>
Merci bien !
[vendsitem:o57:10000]
*****
On vous a bien renseigné, je vois.
Bon, puisque vous avez fait avancer les relations, je vous fais moitié prix, ok ?
[ID:4]Tope là ![/ID:4]
[quete_finie:89][ID:5]Vous saviez que j''ai exploré les strates inférieures ? ...[/ID:5][/quete_finie:89]
*****
Très bien, très bien. Je consent donc à vous faire moitié prix ; Ça fera 5000 stars.
[ID:4]Tope là ![/ID:4]
[quete_finie:88][ID:5]Mais j''ai aussi amélioré des relations diplomatiques ![/ID:5][/quete_finie:88]
*****
Merci bien !
[vendsitem:o57:5000]
*****
Ha ?? Ça aussi ?
Hum ...

C''est du vol, j''y perds des plumes, mais je peux vous faire 25% de réduction supplémentaire. Ça vous fera -- pour vous seulement -- 2500 stars !
[ID:6]Voici une bonne proposition ![/ID:6]
*****
Heureusement que tous les clients ne négocient pas aussi bien que vous, j''y perdrais ma chemise !
[vendsitem:o57:2500]' WHERE `pnj`.`id` = 33;
