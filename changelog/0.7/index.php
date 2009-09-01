<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<title>Version07</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<style>
body { font:80% Verdana,Tahoma,Arial,sans-serif; }
h1, h2, h3, h4 {  font-family: Trebuchet MS,Georgia,"Times New Roman",serif; }
ul.toc { padding: 4px; margin-left: 0; }
ul.toc li { list-style-type:none; }
ul.toc li.heading2 { margin-left: 1em; }
ul.toc li.heading3 { margin-left: 2em; }
</style>
</head>
<body>
<h1 id="Version07">Version07<a href="#Version07" class="wiki-anchor">&para;</a></h1>


	<h2 id="Graphismes">Graphismes<a href="#Graphismes" class="wiki-anchor">&para;</a></h2>


	<p>Toutes les textures ont été refaites et de nouvelles ont été ajoutées.<br />
Tout ça pour que le jeu soit encore plus classe !</p>


	<h2 id="RvR">RvR<a href="#RvR" class="wiki-anchor">&para;</a></h2>


	<h3 id="Diplomatie">Diplomatie<a href="#Diplomatie" class="wiki-anchor">&para;</a></h3>


	<p>Chaque royaume ne peut avoir qu'un nombre limité de royaumes avec un statut diplomatique de rang élevé. (maxi 1 alliance fraternelle, 1 ennemi éternel, 3 (alliance + alliance fraternelle) et 3 (ennemis ennemi éternel)).</p>


	<h3 id="Capitale">Capitale<a href="#Capitale" class="wiki-anchor">&para;</a></h3>


	<p>On peut attaquer une capitale avec des armes de siège.<br />
Si on arrive a toucher la capitale on aura aléatoirement :<br />
- Destruction de ressources du royaume.<br />
- Perte de PV d'un bâtiment de la ville au hasard (qui a des PV), si un bâtiment n'a plus de PV il perd un niveau. Si plus aucun bâtiment ne peut perdre de niveau, alors on inflige des dégâts à la ville en elle même.<br />
Si la ville est détruite cela entraîne pendant 1 mois :</p>


	<ul>
	<li>Impossibilité de l'attaquer.</li>
		<li>Les habitants peuvent toujours rez dessus mais a 5% HP / MP</li>
		<li>On ne peut pas upgrader de bâtiments.</li>
		<li>Les joueurs ne peuvent plus acheter sur la capitale.</li>
	</ul>


	<p>Nouveau bâtiment en ville, les murs :</p>


	<ul>
	<li>Palissade => dégâts reçu lors d'un siège réduit de 10%</li>
		<li>Mur => 20%</li>
		<li>Grand mur => 30%</li>
		<li>Muraille => 40%</li>
	</ul>


	<h3 id="Points-de-victoires">Points de victoires<a href="#Points-de-victoires" class="wiki-anchor">&para;</a></h3>


	<p>Chaque royaume aura un nombre de point de victoire en fonction de certaines actions, et augmenteront 2 compteurs, le premier "nombre de point de victoire total" qui servira juste comme référentiel pour savoir qui a la plus grosse, l'autre compteur "point de victoire" que l'on pourra dépenser pour faire certaines choses.<br />
Comment gagner des points de victoires :<br />
Soit en détruisant des bâtiments ennemis :</p>


	<ul>
	<li>Poste avancé 1 pts</li>
		<li>Fortin 2 pts</li>
		<li>Fort 4 pts</li>
		<li>Forteresse 8 pts</li>
		<li>Petit bourg 1 pts</li>
		<li>Bourgade 2 pts</li>
		<li>Bourg 4 pts</li>
		<li>Bâtiment en ville de niveau 1 => 2 pts</li>
		<li>Niveau 2 => 4 pts</li>
		<li>Niveau 3 => 6 pts</li>
		<li>Niveau 4 => 8 pts</li>
		<li>Niveau 5 => 10 pts</li>
		<li>Niveau 6 => 12 pts</li>
		<li>Destruction de la capitale => 100 pts</li>
	</ul>


	<p>Soit en contrôlant un point exceptionnel sur la carte.<br />
Il y a 5 points exceptionnels, et si on contrôle la case de ce point, il rapporte 1 pts de victoire par jour.<br />
Que faire de ses points de victoires ?<br />
Quelques idées d'actions (sur tous les joueurs de son royaume, les buffs ne seront pas supprimable et dureront 1 mois) :</p>


	<ul>
	<li>Supprimer la famine => 10 pts</li>
	</ul>


	<h2 id="Royaumes">Royaumes<a href="#Royaumes" class="wiki-anchor">&para;</a></h2>


	<h3 id="Interface-des-rois">Interface des rois<a href="#Interface-des-rois" class="wiki-anchor">&para;</a></h3>


	<p>L'ergonomie a été totalement refaite.<br />
La gestion des groupes est maintenant accessible.</p>


	<h3 id="Bourgs-et-mines">Bourgs et mines<a href="#Bourgs-et-mines" class="wiki-anchor">&para;</a></h3>


	<p>Les péages ne sont plus achetables.<br />
Pour améliorer une bourgade en petit bourg, il faut attendre 1 mois, et pour un petit bourg en bourg 3 mois.<br />
Il en est de même pour passer des mines 1 à 2, et des mines 2 à 3.</p>


	<h2 id="Joueurs">Joueurs<a href="#Joueurs" class="wiki-anchor">&para;</a></h2>


	<h3 id="Réputation">Réputation<a href="#Réputation" class="wiki-anchor">&para;</a></h3>


	<p>La réputation est un limitant à la perte d'honneur.<br />
Lorsque vous gagnez de l'honneur en PvP, vous gagnerez en plus 10% de cette honneur en réputation.<br />
Lors de la perte d'honneur journalier, si vous avez moins d'honneur que de réputation la baisse d'honneur est de 2%, sinon de 3%.</p>


	<h3 id="XP-contre-les-monstres">XP contre les monstres<a href="#XP-contre-les-monstres" class="wiki-anchor">&para;</a></h3>


	<p>Lorsque l'on attaque un monstre, on gagne de l'xp dorénavant.<br />
Le nombre d'xp gagné est égal a 50% de l'xp gagné en le tuant avant * (nombre de PV supprimé au monstre / nombre de PV maximum du monstre).<br />
Lorsque l'on tue un monstre on gagne 50% de ce que l'on gagnait avant.</p>


	<h3 id="Chance-de-monter-les-compétences">Chance de monter les compétences<a href="#Chance-de-monter-les-compétences" class="wiki-anchor">&para;</a></h3>


	<p>Globalement les chances de monter les compétences a été réduits.<br />
De plus, les chances de monter les compétences pour les sorts a été modifiés.</p>


	<h2 id="Autres">Autres<a href="#Autres" class="wiki-anchor">&para;</a></h2>


	<p>Lors de la connexion, on est redirigé directement dans le jeu et pas sur la page d'index.</p>


	<p>Tout le jeu a été modifié pour passer en "objet", c'est en théorie invisible pour vous (sauf bug) mais c'est un passage important pour nous qui nous a pris énormément de temps et qui nous a fait refaire l'intégralité des fichiers du jeu.</p>
</body>
</html>