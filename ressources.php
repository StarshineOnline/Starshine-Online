<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
include_once(root.'class/db.class.php');
include_once(root.'fonction/time.inc.php');
include_once(root.'fonction/action.inc.php');
//Récupère le timestamp en milliseconde de début de création de la page
$debut = getmicrotime();

//Récupération des variables de connexion à la base et connexion à cette base
include_once(root.'connect.php');

//Inclusion du fichier contenant toutes les variables indispensablent
include_once(root.'inc/variable.inc.php');

//Inclusion du fichier contenant toutes les informations sur les races
include_once(root.'inc/race.inc.php');

//Inclusion du fichier contenant toutes les informations sur les classes
include_once(root.'inc/classe.inc.php');

//Inclusion du fichier contenant les traductions
include_once(root.'inc/traduction.inc.php');

//Inclusion du fichier contenant toutes les variables du terrain (nom, pa)
include_once(root.'inc/type_terrain.inc.php');

//Inclusion du fichier contenant toutes les fonctions de base
include_once(root.'fonction/base.inc.php');

//Inclusion du fichier contenant toutes les fonctions concernant les groupes
include_once(root.'fonction/groupe.inc.php');

//Inclusion du fichier contenant les fonctions permettant de gérer les quètes
include_once(root.'fonction/quete.inc.php');

//Inclusion du fichier contenant les fonctions permettant de gérer l'équipement
include_once(root.'fonction/equipement.inc.php');

//Inclusion du fichier contenant la classe inventaire
include_once(root.'class/inventaire.class.php');

?>
<h1>Présentation</h1>

<p>Il y aura 8 types de ressources :<br />Pierre<br />Bois<br />Eau<br />Sable<br />Charbon<br />Essence Magique<br />Nourriture<br />Star
</p>
<p class='vspace'>Chaque terrain produit un certain nombre de ces ressources par 10 cases contrôlées de ce type :
</p>
<div class='vspace'></div><h3>Plaine</h3>
<p>Pierre = 4<br />Bois = 4<br />Eau = 5<br />Sable = 2<br />Charbon = 0<br />Essence Magique = 0<br />Nourriture = 8<br />Star = 0

</p>
<div class='vspace'></div><h3>Forêt</h3>
<p>Pierre = 3<br />Bois = 8<br />Eau = 4<br />Sable = 0<br />Charbon = 0<br />Essence Magique = 3<br />Nourriture = 5<br />Star = 0<br />
</p><h3>Désert</h3>
<p>Pierre = 6<br />Bois = 0<br />Eau = 0<br />Sable = 8<br />Charbon = 2<br />Essence Magique = 4<br />Nourriture = 2<br />Star = 0<br />

</p><h3>Montagne</h3>
<p>Pierre = 8<br />Bois = 4<br />Eau = 3<br />Sable = 5<br />Charbon = 0<br />Essence Magique = 1<br />Nourriture = 2<br />Star = 0<br />
</p><h3>Marais</h3>
<p>Pierre = 0<br />Bois = 1<br />Eau = 1<br />Sable = 3<br />Charbon = 4<br />Essence Magique = 8<br />Nourriture = 2<br />Star = 0<br />

</p><h3>Terre Maudite</h3>
<p>Pierre = 2<br />Bois = 2<br />Eau = 0<br />Sable = 1<br />Charbon = 8<br />Essence Magique = 5<br />Nourriture = 1<br />Star = 0<br />
</p><h3>Glace</h3>
<p>Pierre = 1<br />Bois = 0<br />Eau = 8<br />Sable = 0<br />Charbon = 2<br />Essence Magique = 3<br />Nourriture = 2<br />Star = 0<br />

</p><h3>Route</h3>
<p>Pierre = 0<br />Bois = 0<br />Eau = 0<br />Sable = 0<br />Charbon = 0<br />Essence Magique = 0<br />Nourriture = 0<br />Star = 30
</p>
<div class='vspace'></div><h1>Augmenter le revenu</h1>
<h2>Les extracteurs</h2>
<h3>Extracteur de ressource I</h3>

<p>Augmente la production de ressource de 5 * les ressources de base de la case.
</p><h3>Extracteur de ressource II</h3>
<p>Augmente la production de ressource de 10 * les ressources de base de la case.
</p><h3>Extracteur de ressource III</h3>
<p>Augmente la production de ressource de 20 * les ressources de base de la case.
</p>
<div class='vspace'></div><h2>Les mines</h2>
<p>Il existe un type de mine par ressource (excepté pour les stars).
</p><h3>Mine de ressource xxx I</h3>
<p>Augmente la production de xxx de 25 * xxx de base de la case.
</p><h3>Mine de ressource xxx II</h3>
<p>Augmente la production de xxx de 50 * xxx de base de la case.

</p><h3>Mine de ressource xxx III</h3>
<p>Augmente la production de xxx de 100 * xxx de base de la case.
</p>
<div class='vspace'></div><h2>Limitation du nombre de mines et extracteurs</h2>
<p>On pourra poser un certain nombre de mine / extracteur par bourg dans un rayon de 5 cases.
Exemple un petit bourg pourra avoir 2 mines / extracteurs dans son rayon de 5 cases.
</p>
<p class='vspace'>Bourgade =&gt; 1 mine
Petit bourg =&gt; 2 mines
Bourg =&gt; 4 mines
Village =&gt; 8 mines

<h2>La nourriture</h2>
<h3>Cout par joueur actif de la race</h3>

Pour d�finir le cout par joueur actif de la race, il faut prendre le total de la nourriture produite dans le monde la veille et diviser par le nombre de joueurs actifs de la veille.<br />
Cela donne le cout en nourriture par joueur actif du jour.<br />
<h3>Paiement et effets</h3>
<br />
Tous les jours chaque race devra payer (joueur actif * cout en nourriture par joueur actif) points de nourriture.<br />
Si le royaume n'a pas assez de nourriture, alors chaque joueur de la race recevra un debuff calcul� ainsi :<br />
arrondi inf�rieur(Somme a payer / nourriture restante) = % d'HP et MP en moins.<br />
Avec un maximum de 5% par jour, et un cumul maximum de 50%.<br />
<br />
Par contre si le royaume a assez de nourriture alors personne n'a de debuff, et si des joueurs en avaient d�j� un, l'effet est r�duit de 5%. <br />
</p>
<div class='vspace'></div><h1>Stats</h1>
<p>plaine 8119 = 811
Pierre = 3244
Bois = 3244
Eau = 4055
Sable = 1622
Charbon = 0
Essence Magique = 0
Nourriture = 6488
Star = 0
</p>
<p class='vspace'>montagne 3342 = 334
Pierre = 2672
Bois = 1336
Eau = 1002
Sable = 1670
Charbon = 0
Essence Magique = 334
Nourriture = 668
Star = 0
</p>
<p class='vspace'>forêt 2499 = 249
Pierre = 747
Bois = 1992
Eau = 996
Sable = 0
Charbon = 0
Essence Magique = 747
Nourriture = 1245
Star = 0
</p>
<p class='vspace'>terre maudite 1701 = 170
Pierre = 340
Bois = 340
Eau = 0
Sable = 170
Charbon = 1360
Essence Magique = 850
Nourriture = 170
Star = 0
</p>
<p class='vspace'>glace 1486 = 148
Pierre = 148
Bois = 0
Eau = 1184
Sable = 0
Charbon = 296
Essence Magique = 444
Nourriture = 296
Star = 0
</p>
<p class='vspace'>route 1363 = 136
Pierre = 0
Bois = 0
Eau = 0
Sable = 0
Charbon = 0
Essence Magique = 0
Nourriture = 0
Star = 4080
</p>
<p class='vspace'>désert 1230 = 123
Pierre = 738
Bois = 0
Eau = 0
Sable = 984
Charbon = 246
Essence Magique = 492
Nourriture = 246
Star = 0
</p>

<p class='vspace'>marais 1118 = 111
Pierre = 0
Bois = 111
Eau = 111
Sable = 333
Charbon = 444
Essence Magique = 888
Nourriture = 222
Star = 0
</p>
<p class='vspace'>Total
Pierre = 7889
Bois = 7023
Eau = 7348
Sable = 4779
Charbon = 2346
Essence Magique = 3755
Nourriture = 9335
Star = 4080

<h1>Gains journaliers actuels</h1>
<?php
$ressources = array();

$requete = "SELECT royaume.race as race, info, FLOOR(COUNT(*) / 10) as tot, COUNT(*) as tot_terrain FROM `map` LEFT JOIN royaume ON map.royaume = royaume.id WHERE royaume <> 0 GROUP BY info, royaume";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	if($row['tot'] > 0)
	{
		$typeterrain = type_terrain($row['info']);
		$ressources[$row['race']][$typeterrain[1]] = $row['tot'];
		$terrain[$row['race']][$typeterrain[1]] = $row['tot_terrain'];
	}
}

$ress = array();
$ress['Plaine']['Pierre'] = 4;
$ress['Plaine']['Bois'] = 4;
$ress['Plaine']['Eau'] = 5;
$ress['Plaine']['Sable'] = 2;
$ress['Plaine']['Nourriture'] = 8;
$ress['Plaine']['Star'] = 0;
$ress['Plaine']['Charbon'] = 0;
$ress['Plaine']['Essence Magique'] = 0;

$ress['Forêt']['Pierre'] = 3;
$ress['Forêt']['Bois'] = 8;
$ress['Forêt']['Eau'] = 4;
$ress['Forêt']['Sable'] = 0;
$ress['Forêt']['Nourriture'] = 5;
$ress['Forêt']['Star'] = 0;
$ress['Forêt']['Charbon'] = 0;
$ress['Forêt']['Essence Magique'] = 3;

$ress['Désert']['Pierre'] = 6;
$ress['Désert']['Bois'] = 0;
$ress['Désert']['Eau'] = 0;
$ress['Désert']['Sable'] = 8;
$ress['Désert']['Nourriture'] = 2;
$ress['Désert']['Star'] = 0;
$ress['Désert']['Charbon'] = 2;
$ress['Désert']['Essence Magique'] = 4;

$ress['Montagne']['Pierre'] = 8;
$ress['Montagne']['Bois'] = 4;
$ress['Montagne']['Eau'] = 3;
$ress['Montagne']['Sable'] = 5;
$ress['Montagne']['Nourriture'] = 2;
$ress['Montagne']['Star'] = 0;
$ress['Montagne']['Charbon'] = 0;
$ress['Montagne']['Essence Magique'] = 1;

$ress['Marais']['Pierre'] = 0;
$ress['Marais']['Bois'] = 1;
$ress['Marais']['Eau'] = 1;
$ress['Marais']['Sable'] = 3;
$ress['Marais']['Nourriture'] = 2;
$ress['Marais']['Star'] = 0;
$ress['Marais']['Charbon'] = 4;
$ress['Marais']['Essence Magique'] = 8;

$ress['Terre Maudite']['Pierre'] = 2;
$ress['Terre Maudite']['Bois'] = 2;
$ress['Terre Maudite']['Eau'] = 0;
$ress['Terre Maudite']['Sable'] = 1;
$ress['Terre Maudite']['Nourriture'] = 1;
$ress['Terre Maudite']['Star'] = 0;
$ress['Terre Maudite']['Charbon'] = 8;
$ress['Terre Maudite']['Essence Magique'] = 5;

$ress['Glace']['Pierre'] = 1;
$ress['Glace']['Bois'] = 0;
$ress['Glace']['Eau'] = 8;
$ress['Glace']['Sable'] = 0;
$ress['Glace']['Nourriture'] = 2;
$ress['Glace']['Star'] = 0;
$ress['Glace']['Charbon'] = 2;
$ress['Glace']['Essence Magique'] = 3;

$ress['Route']['Pierre'] = 0;
$ress['Route']['Bois'] = 0;
$ress['Route']['Eau'] = 0;
$ress['Route']['Sable'] = 0;
$ress['Route']['Nourriture'] = 0;
$ress['Route']['Star'] = 30;
$ress['Route']['Charbon'] = 0;
$ress['Route']['Essence Magique'] = 0;

$i = 0;
$key = array_keys($ressources);
foreach($ressources as $res)
{
	$j = 0;
	$keys = array_keys($res);
	while($j < count($res))
	{
		$k = 0;
		$kei = array_keys($ress[$keys[$j]]);
		foreach($ress[$keys[$j]] as $rr)
		{
			$ressource_final[$key[$i]][$kei[$k]] += $rr * $ressources[$key[$i]][$keys[$j]];
			$k++;
		}
		$j++;
	}
	$i++;
}

echo '<pre>';
print_r($ressource_final);
print_r($terrain);

?>
<h1>Nouveaux prix des batiments</h1>
<?php
$types = array();
$types['drapeau']['pierre'] = 0.333;
$types['drapeau']['bois'] = 1;
$types['drapeau']['eau'] = 1;
$types['drapeau']['sable'] = 0.666;
$types['drapeau']['charbon'] = 1.5;
$types['drapeau']['essence'] = 2;

$types['fort']['pierre'] = 1.5;
$types['fort']['bois'] = 0.333;
$types['fort']['eau'] = 0.666;
$types['fort']['sable'] = 2;
$types['fort']['charbon'] = 1;
$types['fort']['essence'] = 1;

$types['tour']['pierre'] = 1;
$types['tour']['bois'] = 0.666;
$types['tour']['eau'] = 2;
$types['tour']['sable'] = 1;
$types['tour']['charbon'] = 0.333;
$types['tour']['essence'] = 1.5;

$types['bourg']['pierre'] = 0.666;
$types['bourg']['bois'] = 1.5;
$types['bourg']['eau'] = 1;
$types['bourg']['sable'] = 1;
$types['bourg']['charbon'] = 2;
$types['bourg']['essence'] = 0.333;

$types['mur']['pierre'] = 2;
$types['mur']['bois'] = 1;
$types['mur']['eau'] = 0.333;
$types['mur']['sable'] = 1.5;
$types['mur']['charbon'] = 0.666;
$types['mur']['essence'] = 1;

$types['arme_de_siege']['pierre'] = 1;
$types['arme_de_siege']['bois'] = 2;
$types['arme_de_siege']['eau'] = 1.5;
$types['arme_de_siege']['sable'] = 0.333;
$types['arme_de_siege']['charbon'] = 1;
$types['arme_de_siege']['essence'] = 0.666;

$requete = "SELECT * FROM objet_royaume";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	echo '<strong>'.$row['nom'].'</strong><br />';
	$i = 0;
	$keys = array_keys($types[$row['type']]);
	echo 'Stars = '.$row['prix'].'<br />';
	foreach($types[$row['type']] as $multiple)
	{
		echo $keys[$i].' = '.ceil($row['prix'] * $multiple).'<br />';
		$i++;
	}
}
?>