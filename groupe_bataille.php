<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php

include_once(root.'inc/fp.php');
include_once(root.'fonction/messagerie.inc.php');
$joueur = new perso($_SESSION['ID']);
$R = new royaume($Trace[$joueur->get_race()]['numrace']);
function affiche_bataille_groupe($bataille, $leader = false)
{
	global $joueur;
	?>
		<div class="information_case">
			<h4><a href="groupe_bataille.php?affiche_bataille=<?php echo $bataille->id; ?>" onclick="affichePopUp(this.href); return false;"><?php echo $bataille->nom; ?></a></h4>
			<div>
				<?php echo transform_texte($bataille->description); ?>
			</div>
	<?php
	if($bataille->is_groupe_in($joueur->get_groupe()))
	{
		$bataille->get_reperes();
		echo 'Vous participez à cette bataille<br /><br />';
		echo 'Liste des missions :<br />';
		foreach($bataille->reperes as $repere)
		{
			if($repere_groupe = $repere->get_groupe($joueur->get_groupe()))
			{
				$repere->get_type();
				if($repere_groupe->accepter == 0)
				{
					$accepter = ' - <a href="groupe_bataille.php?id_bataille_repere='.$repere_groupe->id.'&amp;accepter" onclick="return envoiInfo(this.href, \'bgr'.$repere_groupe->id.'\');" id="bgr'.$repere_groupe->id.'">V</a>';
				}
				else
				{
					$accepter = '';
				}
				if(($repere_groupe->accepter == 0 AND $leader) OR $repere_groupe->accepter == 1) echo $repere->repere_type->nom.' en '.$repere->x.' / '.$repere->y.$accepter.'<br />';
				else echo $repere->repere_type->nom.' en '.$repere->x.' / '.$repere->y.'<br />';
			}
		}
	}
	else
	{
		?>
		<a href="groupe_bataille.php?id_bataille=<?php echo $bataille->id; ?>&amp;participe" onclick="return envoiInfo(this.href, 'bataille_<?php echo $bataille->id; ?>');">Participer à cette bataille</a>
		<?php
	}
	?>
	</div>
	<?php
}

$groupe = recupgroupe($joueur->get_groupe(), '');

if(array_key_exists('affiche_bataille', $_GET))
{
	$bataille = new bataille($_GET['affiche_bataille']);
	if ($bataille->id_royaume == $R->get_id())
	{
		$bataille->get_reperes('tri_type');
		$batiments = array();
		$dimensions = dimension_map($bataille->x, $bataille->y, 11);
		$requete = "SELECT x, y, hp, nom, type, image FROM construction WHERE royaume = ".$R->get_id()." AND x >= ".$dimensions['xmin']." AND x <= ".$dimensions['xmax']." AND y >= ".$dimensions['ymin']." AND y <= ".$dimensions['ymax'];
		$req = $db->query($requete);
		while($row = $db->read_assoc($req))
		{
			$batiments[convert_in_pos($row['x'], $row['y'])] = $row;
		}
		$x = $bataille->x;
		$y = $bataille->y;
		
		$map = new map($x, $y, 10, '', false, 'low');
		$map->set_batiment($batiments);
		$map->get_joueur($R->get_race(), false, true);
		if(array_key_exists('action', $bataille->reperes)) $map->set_repere($bataille->reperes['action']);
		if(array_key_exists('batiment', $bataille->reperes)) $map->set_batiment_ennemi($bataille->reperes['batiment']);
		$map->set_onclick("return false;");
		$map->affiche();
	}
}
else
{
	/*//Si c'est le chef de groupe
	if($groupe['id_leader'] == $joueur->get_id())
	{*/
		$bataille_royaume = new bataille_royaume($Trace[$joueur->get_race()]['numrace']);
		$bataille_royaume->get_batailles();
		
		if(array_key_exists('participe', $_GET))
		{
			$bataille = new bataille($_GET['id_bataille']);
			$bataille_groupe = new bataille_groupe();
			$bataille_groupe->id_bataille = $_GET['id_bataille'];
			$bataille_groupe->id_groupe = $joueur->get_groupe();
			$bataille_groupe->sauver();
			//Si c'est le chef de groupe
			if($groupe['id_leader'] == $joueur->get_id())
				affiche_bataille_groupe($bataille, true);
			else
				affiche_bataille_groupe($bataille);
		}
		elseif(array_key_exists('accepter', $_GET))
		{
			$bgr = new bataille_groupe_repere($_GET['id_bataille_repere']);
			$bgr->accepte();
		}
		else
		{
			foreach($bataille_royaume->batailles as $bataille)
			{
				//il faut que ça soit des batailles "en cours"
				if($bataille->etat == 1 AND $bataille->is_groupe_in($joueur->get_groupe()))
				{
					?>
					<div id="bataille_<?php echo $bataille->id; ?>">
					<?php
						if($groupe['id_leader'] == $joueur->get_id())
							affiche_bataille_groupe($bataille, true);
						else
							affiche_bataille_groupe($bataille);
					?>
					</div>
					<?php
				}
			}
		}
	/*}
	//On affiche uniquement les bataille auquel le groupe participe
	else
	{
		$requete = "SELECT bataille_repere.id_bataille FROM bataille_groupe_repere LEFT JOIN bataille_repere ON bataille_repere.id = bataille_groupe_repere.id_repere LEFT JOIN bataille_groupe ON bataille_groupe.id = bataille_groupe_repere.id_groupe WHERE bataille_groupe.id_groupe = ".$joueur->get_groupe()." AND accepter = 1";
		$req = $db->query($requete);
		while($row = $db->read_assoc($req))
		{
			$bataille = new bataille($row['id_bataille']);
			affiche_bataille_groupe($bataille);
		}
	}*/
}
?>
