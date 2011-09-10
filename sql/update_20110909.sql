---- I.C: Leonard Sylein
INSERT INTO `calendrier` (`date`, `sql`, `next`) VALUES 
-- À 8h, il se casse, le passage s'ouvre
('2011-09-09 08:00:00', 'update pnj set x=30, y=320 where nom = ''Leonard Sileyn''; update map set decor = 10676, info = 106 where x=30 and y=325', '0000-00-01 00:00:00'), 
-- À 10h, il revient, le passage se referme
('2011-09-09 10:00:00', 'update pnj set x=30, y=326 where nom = ''Leonard Sileyn''; update map set decor = 4663, info = 46 where x=30 and y=325', '0000-00-01 00:00:00');
-- NB: je n'ai pas fait le passage vu que je ne sais pas où il est :/


---- I.B: Lilly Plume
-- quête: nouveau type d'objectif: X (misc.), avec un champ 'info' le décrivant
INSERT INTO `quete` (`nom`, `description`, `fournisseur`, `objectif`, `exp`, `honneur`, `star`, `reward`, `repete`, `mode`, `royaume`, `niveau_requis`, `honneur_requis`, `quete_requis`, `star_royaume`, `lvl_joueur`, `achat`) VALUES
('Cache-cache', 'Vous devez trouver trois fois le petit Riky Tendre-flocon', '', 'a:1:{i:0;O:8:"stdClass":4:{s:5:"cible";s:1:"X";s:6:"nombre";i:3;s:6:"requis";s:0:"";s:4:"info";s:16:"Trouver l''enfant";}}', 10000, 100, 0, '', 'n', 's', '', 8, 1000, '', 0, 8, 'non');

-- pnj: riky
INSERT INTO `pnj` (`nom`, `image`, `x`, `y`, `texte`) VALUES
('Riky Tendre-flocon', 'riky', 37, 334, '<em>Vous découvrez un petit garçon dissimulé</em>\n[isquete:90][id:1]Ho ... je crois que j''ai trouvé un petit bout d''ombre ...[/id:1][/isquete:90][non_quete:90]Chut ! j''suis caché ![/non_quete:90][quetefini:90]Chut ! z''avez déjà gagné, vous, je joue avec d''autres maintenant, ne leur montrez pas ma cachette ![/quetefini:90]*****\n[run:cache_cache]');

-- maj de lily plume pour donner la quete
UPDATE `pnj` SET `texte` = '<em>Une petite fille, la frimousse remplie de taches de rousseur vous lance un regard malicieux.</em>

Hey !! Bonjour !! 

[ID:1](...) Bonjour petite fille.[/ID:1]
*****

Vous êtes nouveau ici ? oui j''ai cru voir ça à vot'' tenue. Mon nom c''est Lyly Plume ! et je suis orpheline. Zin Ormil m''a recueilli ici et le vampire qui boit trop la bas s''occupe de moi... il est cooooooOOOOL !!!! héhéh

[ID:2]Eh bien... 
<em>Pour vous même :</em> (bonjour l''éducation...)
(Raclement de gorge), bon aller bonne journée, et ne fais pas de bêtise ![/ID:2]
*****

Attendez ! ne partez pas si vite. c''est pas tous les jours qu''on a la chance de croiser des nouveaux par ici ! Tiens Riky arrive !! CoooooOOOOL! Je vous présente mon copain Riky Tendre-Flocon. Il sait se faufiler partout !!!!

Hein Riky !! ? 

[ID:3](...) (?)[/ID:3]
*****

<em>Un petit garçon l''air assez robuste s''approche de vous en répondant à Lyly :</em> ah ahah héhé oui je sais me camoufler n''importe quand et n''importe où !! et je peux vous le prouver, je serais une grande ombre plus tard, la plus grande ! 

<em>Riky s''adressant à Lyly :</em> Hey ! j''y pense Lyly, pourquoi ne pas jouer avec eux à cache cache ! ?

<em> Lyly renvoyant un regard espiègle à Riky :</em> Oh oui ça, c''est une très bonne idée ! Si vous voulez jouer avec nous, Riky va se cacher dans un endroit et si vous arrivez à le trouver plusieurs fois, disons 3 fois ! Eh bien on vous montrera un endroit que nous seul connaissons ici !

Ca vous tente ? !!  

<em>allez dites oui, allez dites oui, dites oui, dites oui, allez allez ! !! </em>

[ID:4](...) euh... nous n''avons pas trop de temps à perdre mais pourquoi pas ...[/ID:4]
[non_quete:90][ID:5](...) euh... désolé, nous n''avons pas de temps à perdre ...[/ID:5][/non_quete:90]
*****

Super !!!! YOUHOU ! !! Aller Riky va te cacher !!!

<em>vous acceptez de jouer avec Lyly Plume et Riky Tendre-Flocon</em>
[prendquete:90]
*****
Méchant !' WHERE `pnj`.`nom` = 'Lyly Plume';

---- I.A
-- correction geollier 4: juste les dimanches
UPDATE `calendrier` SET `date` = '2011-09-11 19:00:00',
`next` = '0000-00-07 00:00:00' WHERE `calendrier`.`sql` like 'insert into map_monstre %Maraudeur geolier 4%';

