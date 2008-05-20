<?php
$textures = false;
include('haut.php');
setlocale(LC_ALL, 'fr_FR', 'FRA');
include('haut_site.php');
if ($G_maintenance)
{
	echo 'Starshine-online est actuellement en cours de mis à jour.<br />
	le forum est toujours disponible <a href="punbb/">ici - Forum</a>';
}
else
{
	include('menu.php');
	//Si le joueur est connecté on affiche le menu de droite
?>
	<div id="contenu">
		<div id="centre2">
			<h2>Changelog 0.5</h2>
			<?php
			require_once('menu_0_5.php');
			?>
			<h2>Gameplay</h2>
			<h3>Les niveaux</h3>
			
			<p>Les anciens bonus de niveau ont &eacute;t&eacute; totallement supprim&eacute;s.</p>
			<p>A la place, pour chaque niveau gagn&eacute;, vous avez 1 point Shine.</p>
			<p>Les points Shine peuvent &ecirc;tre utiliser pour am&eacute;liorer votre personnage en achetant des bonus Shine, am&eacute;liorer non pas dans le sens augmenter, ses caract&eacute;ristiques ses d&eacute;gats ou ce genre de chose, mais pouvoir &eacute;changer des choses, masquer des informations aux autres joueurs, choisir son sexe, etc.</p>
			
			<p>Le placement de ces points ce fait dans 3 &quot;arbres&quot; (pour le moment, peut-&ecirc;tre plus dans le futur), le premier est appell&eacute; &quot;&eacute;change&quot; et est bas&eacute; sur la fabrication d'objets, le deuxi&egrave;me est appell&eacute; &quot;mim&eacute;tisme&quot; et est bas&eacute; sur le niveau, le dernier est appell&eacute; &quot;personnalisation&quot; et est bas&eacute; sur l'honneur.</p>
			
			<p>Nous ne d&eacute;voileront pas plus pour garder la surprise au moment de mettre vos points et de d&eacute;couvrir ce que d&eacute;bloque chaque chose.</p>
			<h3>Survie</h3>
			<p>Fini les temps heureux ou vous saviez exactement le nombre de point de vie d'un monstre ou d'un adversaire, vous n'aurez plus qu'une estimation en pourcentage en fonction du niveau de l'adversaire et de votre comp&eacute;tence survie. A chaque attaque vous avez une chance de monter cette comp&eacute;tence.</p>
			<p>Certaines classes ont droit a de nouvelles comp&eacute;tences li&eacute; a cela, les archers ont connaissance des b&ecirc;tes qui leur permet d'&ecirc;tre plus pr&eacute;cis sur l'estimation de point de vie des b&ecirc;tes. Les sorciers et n&eacute;cromanciens ont connaissance des cr&eacute;atures magiques, et les voleurs connaissance des humano&iuml;des (monstre humano&iuml;des et joueurs).</p>
			
			<h3>Le nombre de rounds</h3>
			<p>Le nombre de round a &eacute;t&eacute; multipli&eacute; par 2. Et par cons&eacute;quent le nombre de point de vie et de RM de votre personnage aussi.</p>
			<p>Cela va permettre plusieurs choses importantes :</p>
			<p>- L'utilisation de comp&eacute;tences qui pouvaient paraitre inutile sur 5 rounds car utilisant 20% du potentiel de d&eacute;gats (berzeker, b&eacute;n&eacute;diction, ...).</p>
			
			<p>- R&eacute;duction de la g&egrave;ne que pouvait avoir certain sorts (paralysie, silence, ...)</p>
			<p>- R&eacute;duction du fameux bonus des orcs qui reste toutefois tr&egrave;s bon.</p>
			<p>- Pouvoir d&eacute;multiplier la diversit&eacute; des sorts et comp&eacute;tences.</p>
			
			<h3>L'Anticipation</h3>
			<p>Nouveau concept qui va boulverser vos scripts de combat je vous assure. L'anticipation est le fait qu'&agrave; force d'utiliser la m&ecirc;me comp&eacute;tence contre un adversaire, celui ci l'anticipe et &eacute;vite totallement cette action.</p>
			<p>Concr&eacute;tement, si vous lancer 10 fois boule de feu, la premi&egrave;re fois tout se passera bien, la deuxi&egrave;me votre adversaire aura une chance infime d'anticiper votre boule de feu et donc de l'annuler, ..., la 8&egrave;me fois il y aura une chance sur 2 que votre sort soit anticiper, et la 10&egrave;me fois 4 chances sur 5.</p>
			
			<p>Cela va permettre de varier les script a base de 10 traits de feu, 10 dissimulation, etc.</p>
			<p>Attention toutefois, boule de feu 4 et boule de feu 3 (par exemple) ne sont pas consid&eacute;r&eacute; comme la m&ecirc;me action, et l'attaque de base peut aussi &ecirc;tre anticip&eacute;e !</p>
			<p>Autres &eacute;l&eacute;ments, les joueurs et monstres de niveau 1 &agrave; 4 ont beaucoup moins de chance d'anticiper les actions. Et le &quot;compteur&quot; est bien sur r&eacute;initialiser a chaque combat sinon cela aurait &eacute;t&eacute; ing&eacute;rable.</p>
			
			<h3>Mont&eacute;e de comp&eacute;tences</h3>
			<p>La mont&eacute;e en comp&eacute;tence via les sorts hors combat ne se fait plus de facon lin&eacute;aire avec le cout en PA / MP mais comme la fonction racine carr&eacute;e.</p>
			<p>La comp&eacute;tence m&eacute;l&eacute;e est un peu plus difficile &agrave; mont&eacute;e, les comp&eacute;tences de maitrise sont plus dur, par contre maitrise du critique et art du critique sont plus facile.</p>
			
			<br />
			<h2>Contenu</h2>
			<h3>Nouveaux monstres</h3>
			<p>39 nouveaux monstres de haut niveau (du niveau 11 au 26) ont &eacute;t&eacute; ajout&eacute;</p>
			<p>Des quètes associées à ces monstres devraient voir le jour bientôt.</p>
			
			<h3>Nouveaux sorts et comp&eacute;tences</h3>
			<p>Une vingtaine de nouveaux sorts et une vingtaine de nouvelles comp&eacute;tences ont &eacute;t&eacute; cr&eacute;&eacute;s.</p>
			<p>Avec nottament un nouveau concept, le sort de zone, qui permet de lancer un sort sur tous les joueurs (except&eacute; vous) qui se trouve sur votre case. Ce sont des sorts de d&eacute;buffs qui affecte aussi vos alli&eacute;s ou personnages de la m&ecirc;me race.</p>
			
			<h2>Am&eacute;liorations g&eacute;n&eacute;rales</h2>
			<h3>Esth&eacute;tique</h3>
			<p>L'interface du jeu a &eacute;t&eacute; totallement refaite pour &ecirc;tre beaucoup plus belle et ergonomique. (trop de modification a list&eacute; vu que tout a &eacute;t&eacute; refait, le mieux est de se connecter pour voir toutes les modifications)</p>
			
			<p>Les villes des elfes des bois et des orcs ont &eacute;t&eacute; refaites.</p>
			<h3>Page des statistiques</h3>
			<p>La page des statistiques a &eacute;t&eacute; refaite, vous pouvez maintenant voir les statistiques g&eacute;n&eacute;rales ou les statistiques de royaumes. De plus, en cliquant sur une des images vous allez acc&eacute;der &agrave; l'historique de cette statistique.</p>
			<h3>Page de r&eacute;surrection</h3>
			<p>La page de r&eacute;surrection a &eacute;t&eacute; refaite, plus claire et plus esth&eacute;tique, elle permet maintenant de savoir la position de notre mort ainsi que les derni&egrave;res action de votre personnage.</p>
			
			<h3>Batiments</h3>
			<p>Les batiments externe ont enfin leur ic&ocirc;ne associ&eacute; ^^<br />
			Attaquer un batiment coute maintenant 10 PA</p>
			<h3>Script d'action</h3>
			<p>Il est dor&eacute;navant possible de mettre plusieurs condition (ET) pour le d&eacute;roulement d'une action.<br />
			Et ajout d'une condition &quot;si nombre d'utilisation de la comp&eacute;tence ...&quot;<br />
			Le script de combat sera refait plus tard, il n'est pas ergonomique pour le moment mais fonctionnel tout de même.</p>
			
			<h2>Corrections</h2>
			<p>- Les bugs 1, 7, 13, 18, 20, 21, 22, 34, 36, 49, 50 (malheureusement pas l'admin) sont corrig&eacute;s</p>
			<p>- Les scavengers ont bien 45% en bonus de craft.</p>
			<p>- Les comp&eacute;tences de groupe ont une port&eacute; de 7 cases (infini avant)</p>
			<p>- les rois peuvent mettre un message du roi avec des di&egrave;ses</p>
			
			<p>- Divers autres bugs corrig&eacute;s.</p>
			<p>&nbsp;</p>
			<p>PS : noubliez pas de faire Ctrl + F5 pour vider votre cache et profiter des nouvelles images de tout le site ^^</p>
		</div>
<?php
	include('menu_d.php');
?>
</div>
<?php
	include('bas.php');
}
?>