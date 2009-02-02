<?php
include('menu.php');
?>
<h1>Quelles ressources ?</h1>

Il y a 8 ressources :<br />
- Pierre<br />
- Bois<br />
- Eau<br />
- Sable<br />
- Charbon<br />
- Essence magique<br />
- Nourriture<br />
- Stars<br />

<h1>A quoi servent ces ressources</h1>

<h2>La nourriture :</h2>
Elle sert à nourrir ses habitants. Tous les jours, un nombre de nourriture par habitant est indiqué dans l'interface des rois (il dépend de la nourriture produite).<br />
Si à la fin de la journée vous n'avez pas assez de nourriture pour vos habitants, toute la population de votre royaume subira une "famine", c'est un debuff qui fait perdre un pourcentage des HP et MP maximum.<br />
Le debuff ne peut pas excéder 5% par jour mais il est cumulable, c'est à dire que si en jour 1 vos citoyens ont un debuff de 3% et le lendemain un debuff de 4%, cela fera un debuff total de 7%.<br />
Toutefois, ce debuff ne peut pas atteindre plus de 50%.<br />
Par contre si le jour suivant vous arrivez à nourrir la population, le debuff sera réduit de 5% (ce qui fera au final 2% d'HP et MP en moins dans notre exemple).<br />
<br />
Les autres ressources servent à construire des bâtiments militaires.<br />

<h1>Comment gagner des ressources ?</h1>

- Le "pack de base" par type de terrain :<br />
<h2>Plaine</h2>
Pierre = 4<br />
Bois = 4<br />
Eau = 5<br />
Sable = 2<br />
Nourriture = 8<br />

<h2>Forêt</h2>
Pierre = 3<br />
Bois = 8<br />
Eau = 4<br />
Essence Magique = 3<br />
Nourriture = 5<br />

<h2>Désert</h2>
Pierre = 6<br />
Sable = 8<br />
Charbon = 2<br />
Essence Magique = 4<br />
Nourriture = 2<br />

<h2>Montagne</h2>
Pierre = 8<br />
Bois = 4<br />
Eau = 3<br />
Sable = 5<br />
Essence Magique = 1<br />
Nourriture = 2<br />

<h2>Marais</h2>
Bois = 1<br />
Eau = 1<br />
Sable = 3<br />
Charbon = 4<br />
Essence Magique = 8<br />
Nourriture = 2<br />

<h2>Terre Maudite</h2>
Pierre = 2<br />
Bois = 2<br />
Sable = 1<br />
Charbon = 8<br />
Essence Magique = 5<br />
Nourriture = 1<br />

<h2>Glace</h2>
Pierre = 1<br />
Eau = 8<br />
Charbon = 2<br />
Essence Magique = 3<br />
Nourriture = 2<br />

<h2>Route</h2>
Star = 30<br />
<br />
Maintenant que l'on connait les "pack de base", reste à savoir à quoi ça sert.<br />
Et bien c'est très simple, chaque 10 cases d'un type de terrain que vous contrôlez vous fait gagner 1 pack de base de ressource de ce terrain.<br />
<br />
Par exemple, si votre royaume contrôle en tout 523 cases de plaines, vous gagnerez chaque jour grâce à la plaine :<br />
Pierre = 4 * 52 = 208<br />
Bois = 4 * 52 = 208<br />
Eau = 5 * 52 = 260<br />
Sable = 2 * 52 = 104<br />
Nourriture = 8 * 52 = 416<br />

<h1>On peut gagner autrement des ressources qu'en contrôlant des cases ?</h1>
Oui !<br />
Vous pouvez placer des mines ou extracteurs sur les cases autour d'un bourg. (attention, le nombre de mine et extracteur est limité par la taille du bourg ! [1 pour une bourgade, 2 pour un petit bourg, et 4 pour un bourg].<br />
<br />
Ces mines vous permettront de gagner en plus des cases que vous contrôlez, un certain nombre de ressource.<br />
Par exemple, un extracteur permettra de gagner 5 x le pack de base du type de terrain sur lequel il se trouve.<br />
Donc si vous placez un extracteur sur une case de plaine, il vous donnera :<br />
Pierre = 4 * 5 = 20<br />
Bois = 4 * 5 = 20<br />
Eau = 5 * 5 = 25<br />
Sable = 2 * 5 = 10<br />
Nourriture = 8 * 5 = 40<br />
<br />
Pour les mines, cela dépend de leur type, par exemple, une ferme produira 20 x la nourriture de ce type de case est rien d'autre.<br />
Donc une ferme sur une plaine produire :<br />
Pierre = 4 * 0 = 0<br />
Bois = 4 * 0 = 0<br />
Eau = 5 * 0 = 0<br />
Sable = 2 * 0= 0<br />
Nourriture = 8 * 20 = 160<br />

<h1>Bourse inter royaume ?!</h1>

Vous pouvez a tout moment mettre sur la bourse inter royaume des ressources en vente, indiquez un type, un nombre et un prix total, et voila votre offre parti sur la bourse des royaumes.<br />
Votre enchère restera là pendant 1 semaine (plus si il y a des mises dans les dernières heures), et les mises se font par palier de 10%.<br />
<br />
Exemple, vous vendez 1000 bois pour 10000 stars.<br />
La première mise sera de 10000 stars, la deuxième de 10000 + 10% = 11000 stars, la troisième 12100, etc.<br />
<br />
Cette bourse est anonyme et sans diplomatie.<br />