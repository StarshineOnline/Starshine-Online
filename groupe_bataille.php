<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php

include_once(root.'inc/fp.php');
include_once(root.'fonction/messagerie.inc.php');
$joueur = new perso($_SESSION['ID']);
$groupe_joueur = new groupe($joueur->get_groupe());
$leader = new perso($groupe_joueur->get_id_leader());
$R = new royaume($Trace[$leader->get_race()]['numrace']);
function affiche_bataille_groupe($bataille, $leader = false)
{
	global $joueur;
	?>
		<div class="information_case">
			<h4><a href="groupe_bataille.php?affiche_bataille=<?php echo $bataille->get_id(); ?>" onclick="affichePopUp(this.href); return false;"><?php echo $bataille->get_nom(); ?></a></h4>
			<div>
				<?php echo transform_texte($bataille->get_description()); ?>
			</div>
	<?php
	if($bataille->is_groupe_in($joueur->get_groupe()))
	{
		$reperes = $bataille->get_reperes();
		echo 'Vous participez à cette bataille<br /><br />';
		echo 'Liste des missions :<br />';
		foreach($reperes as $repere)
		{
			if($repere_groupe = $repere->get_groupe($joueur->get_groupe()))
			{
				$repere->get_type();
				if($repere_groupe->accepter == 0)
				{
					$accepter = ' - <a href="groupe_bataille.php?id_bataille_repere='.$repere_groupe->get_id().'&amp;accepter" onclick="return envoiInfo(this.href, \'bgr'.$repere_groupe->get_id().'\');" id="bgr'.$repere_groupe->get_id().'">V</a>';
				}
				else
				{
					$accepter = '';
				}
				if(($repere_groupe->accepter == 0 AND $leader) OR $repere_groupe->accepter == 1) echo $repere->get_repere_type()->get_nom().' en '.$repere->get_x().' / '.$repere->get_y().$accepter.'<br />';
				else echo $repere->get_repere_type()->get_nom().' en '.$repere->get_x().' / '.$repere->get_y().'<br />';
			}
		}
	}
	else
	{
		?>
		<a href="groupe_bataille.php?id_bataille=<?php echo $bataille->get_id(); ?>&amp;participe" onclick="return envoiInfo(this.href, 'bataille_<?php echo $bataille->get_id(); ?>');">Participer à cette bataille</a>
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
	if ($bataille->get_id_royaume() == $R->get_id())
	{
		$reperes = $bataille->get_reperes('tri_type');
		$batiments = array();
		$dimensions = dimension_map($bataille->get_x(), $bataille->get_y(), 11);
		$requete = "
			SELECT c.x, c.y, c.hp, c.nom, c.type, b.image
			FROM construction c INNER JOIN batiment b ON c.id_batiment = b.id
			WHERE c.royaume = ".$R->get_id()." AND c.x >= ".$dimensions['xmin']." AND c.x <= ".$dimensions['xmax']." AND c.y >= ".$dimensions['ymin']." AND c.y <= ".$dimensions['ymax']."
		";
		$req = $db->query($requete);
		while($row = $db->read_assoc($req))
		{
			$batiments[convert_in_pos($row['x'], $row['y'])] = $row;
		}
		$x = $bataille->get_x();
		$y = $bataille->get_y();
		
		$map = new map($x, $y, 10, '', false, 'low');
		$map->set_batiment($batiments);
		$map->get_joueur($R->get_race(), false, true);
		if(array_key_exists('action', $reperes)) $map->set_repere($reperes['action']);
		if(array_key_exists('batiment', $reperes)) $map->set_batiment_ennemi($reperes['batiment']);
		$map->set_onclick("return false;");
		$map->affiche();
	}
}
else
{
	/*//Si c'est le chef de groupe
	if($groupe['id_leader'] == $joueur->get_id())
	{*/
		$bataille_royaume = new bataille_royaume($Trace[$leader->get_race()]['numrace']);
		$bataille_royaume->get_batailles();
		
		if(array_key_exists('participe', $_GET))
		{
			$bataille = new bataille($_GET['id_bataille']);
			$bataille_groupe = new bataille_groupe();
			$bataille_groupe->set_id_bataille($_GET['id_bataille']);
			$bataille_groupe->set_id_groupe($joueur->get_groupe());
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
			
			// Augmentation du compteur de l'achievement
			foreach($groupe_joueur->get_membre_joueur() as $membre)
			{
				$achiev = $membre->get_compteur('bataille');
				$achiev->set_compteur($achiev->get_compteur() + 1);
				$achiev->sauver();
			}
		}
		else
		{
			foreach($bataille_royaume->get_batailles() as $bataille)
			{
				//il faut que ça soit des batailles "en cours"
				if($bataille->get_etat() == 1 AND $bataille->is_groupe_in($joueur->get_groupe()))
				{
					?>
					<div id="bataille_<?php echo $bataille->get_id(); ?>">
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
}
?>
