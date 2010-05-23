<?php
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'./inc/fp.php');
$race = $_GET['race'];
$classe = $_GET['classe'];

if ($classe == 'guerrier')
{
	$Trace[$race]['vie']++;
	$Trace[$race]['force']++;
	$Trace[$race]['dexterite']++;
}
else
{
	$Trace[$race]['puissance']++;
	$Trace[$race]['volonte']++;	
	$Trace[$race]['energie']++;
}

$races = array('barbare', 'elfebois', 'elfehaut', 'humain', 'humainnoir', 'mortvivant', 'nain', 'orc', 'scavenger', 'troll', 'vampire');


$requete = "SELECT star_nouveau_joueur FROM royaume WHERE ID = ".$Trace[$race]['numrace'];
$req = $db->query($requete);
$row = $db->read_row($req);
$stars[$race] = $row[0];
$requete = "SELECT propagande FROM motk WHERE id_royaume = ".$Trace[$race]['numrace'];
$req = $db->query($requete);
$row = $db->read_row($req);
$propa = $row[0];
$propa = htmlspecialchars(stripslashes($propa));
$propa = str_replace('[br]', '<br />', $propa);
$propagande[$race] = $propa;


echo "<h3>".$Gtrad[$race]." - ".$classe."</h3>
		<div id='personnage_box' class='personnage_box'>
			<strong>Stars au début du jeu :</strong> ".$stars[$race]."<br />
			<br />
			<strong>Passif :</strong><br />
			".$Trace[$race]['passif']."<br />
			<ul class='carac'>
				<li><strong>Caractéristiques :</strong></li>
				<li title='Caractérise vos points de vie.'>
					<span>Vie</span>
					<span>".$Trace[$race]['vie']."</span>
				</li>
				<li title='Augmente vos dégâts physiques, permet de porter de plus grosses armes ou armures.'>
					<span>Force</span>
					<span>".$Trace[$race]['force']."</span>
				</li>
				<li title='Augmente vos chances de toucher, d esquiver et de porter des coups critiques'>
					<span>Dextérité</span>
					<span>".$Trace[$race]['dexterite']."</span>
				</li>
				<li title='Augmente vos dégâts magiques.'>
					<span>Puissance</span>
					<span>".$Trace[$race]['puissance']."</span>
				</li>	
				<li title='Augmente vos chances de lancer un sort, d esquiver un sort, ou de toucher une cible avec un sort.'>
					<span>Volonté</span>
					<span>".$Trace[$race]['volonte']."</span>
				</li>
				<li title='Caractérise vos points de mana'>
					<span>Energie</span>
					<span>".$Trace[$race]['energie']."</span>
				</li>
			</ul>
			<ul class='carac'>			
			<li><strong>Affinités magiques :</strong></li>
			<li>Magie de la Vie : ".$Gtrad['affinite'.$Trace[$race]['affinite_sort_vie']]."</li>
			<li>Magie de la Mort : ".$Gtrad['affinite'.$Trace[$race]['affinite_sort_mort']]."</li>
			<li>Magie Elementaire : ".$Gtrad['affinite'.$Trace[$race]['affinite_sort_element']]."</li>
			</ul>
			</div>
			<div class='personnage_roi'>
			<p>
			<strong>Propagande Royale :</strong><br />
			".$propagande[$race]."
			</p>
			</div>";
	?>




