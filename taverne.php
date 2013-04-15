<?php // -*- mode: php; tab-width:2 -*-
if (file_exists('root.php'))
  include_once('root.php');

//Inclusion du haut du document html
include_once(root.'haut_ajax.php');

$joueur = new perso($_SESSION['ID']);
$joueur->check_perso();

//Vérifie si le perso est mort
verif_mort($joueur, 1);

$W_requete = 'SELECT royaume, type FROM map WHERE x = '.$joueur->get_x().' and y = '.$joueur->get_y();
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);

$case = new map_case(array('x' => $joueur->get_x(), 'y' => $joueur->get_y()));
if(!$case->is_ville(true, 'taverne')) exit();


$R = new royaume($W_row['royaume']);

if ($R->is_raz() && $W_row['type'] == 1 && $joueur->get_x() <= 190 && $joueur->get_y() <= 190)
{
	echo "<h5>Impossible de commercer dans une ville mise à sac</h5>";
	exit (0);
}

$R->get_diplo($joueur->get_race());
?>
<fieldset><legend><?php if(verif_ville($joueur->get_x(), $joueur->get_y())) return_ville( '<a href="ville.php" onclick="return envoiInfo(this.href, \'centre\')">'.$R->get_nom().'</a> > ', $joueur->get_pos()); ?> <?php echo '<a href="taverne.php?poscase='.$W_case.'" onclick="return envoiInfo(this.href,\'carte\')">';?> Taverne </a></legend>
		<?php include_once(root.'ville_bas.php');?>	
		<div class="ville_test">
		<span class="texte_normal">
		Bien le bonjour ami voyageur !<br />
		<?php
		//Affichage des quêtes
		if($R->get_nom() != 'Neutre') $return = affiche_quetes('taverne', $joueur);
		if($return[1] > 0 AND !array_key_exists('fort', $_GET))
		{
			echo 'Voici quelques petits services que j\'ai à vous proposer :';
			echo $return[0];
		}
		?></span></div><br /><?php
if ($joueur->get_race() == $R->get_race() ||
		$R->get_diplo($joueur->get_race()) <= 6)
{
	if(isset($_GET['action']))
	{
		switch ($_GET['action'])
		{
			//Achat
			case 'achat' :
				$requete = "SELECT * FROM taverne WHERE id = ".sSQL($_GET['id'], SSQL_INTEGER);
				$req_taverne = $db->query($requete);
				$row_taverne = $db->read_array($req_taverne);
				$taxe = ceil($row_taverne['star'] * $R->get_taxe_diplo($joueur->get_race()) / 100);
				$cout = $row_taverne['star'] + $taxe;
				if ($joueur->get_star() >= $cout)
				{
					if($joueur->get_pa() >= $row_taverne['pa'])
					{
						$valid = true;
						$bloque_regen = false;
						if($row_taverne['pute'] == 1)
						{
							$debuff = false;
							$buff = false;
							$honneur_need = $row_taverne['honneur'] + (($row_taverne['honneur_pc'] * $joueur->get_honneur()) / 100);
							if($joueur->get_honneur() >= $honneur_need)
							{
								$joueur->set_honneur($joueur->get_honneur() - $honneur_need);
							}
							else $joueur->set_honneur(0);
						
              $texte .= pute_effets($joueur, $honneur_need);
	
            }
						if($valid)
						{
							$joueur->set_star($joueur->get_star() - $cout);
							$joueur->set_pa($joueur->get_pa() - $row_taverne['pa']);
							if(!$bloque_regen)
							{
								$joueur->set_hp($joueur->get_hp() + $row_taverne['hp'] + floor($row_taverne['hp_pc'] * $joueur->get_hp_maximum() / 100));
								if ($joueur->get_hp() > $joueur->get_hp_maximum()) $joueur->set_hp(floor($joueur->get_hp_maximum()));
								$joueur->set_mp($joueur->get_mp() + $row_taverne['mp'] + floor($row_taverne['mp_pc'] * $joueur->get_mp_maximum() / 100));
								if ($joueur->get_mp() > $joueur->get_mp_maximum()) $joueur->set_mp(floor($joueur->get_mp_maximum()));
							}
							$joueur->sauver();
							//Récupération de la taxe
							if($taxe > 0)
							{
								$R->set_star($R->get_star() + $taxe);
								$R->sauver();
								$requete = "UPDATE argent_royaume SET taverne = taverne + ".$taxe." WHERE race = '".$R->get_race()."'";
								$db->query($requete);
							}
							echo '<h6>La taverne vous remercie de votre achat !<br />'.$texte.'</h6>';
							
							if($row_taverne['pa'] == 12 AND $row_taverne['pute'] == 0) // Equivaut à "c'est un repas"
							{
								// Augmentation du compteur de l'achievement
								$achiev = $joueur->get_compteur('stars_en_repas');
								$achiev->set_compteur($achiev->get_compteur() + $cout);
								$achiev->sauver();
								
								// Augmentation du compteur de l'achievement
								$achiev = $joueur->get_compteur('nbr_repas');
								$achiev->set_compteur($achiev->get_compteur() + 1);
								$achiev->sauver();
							}
						}
					}
					else
					{
						echo '<h5>Vous n\'avez pas assez de PA</h5>';
					}
				}
				else
				{
					echo '<h5>Vous n\'avez pas assez de Stars</h5>';
				}
			break;
		}
	}
	
	//Affichage de la taverne

	?>

	<div class="ville_test">
	<table class="marchand" cellspacing="0px">
	<tr class="header trcolor2">
		<td>
			Nom
		</td>
		<td>
			Stars
		</td>
		<td>
			Cout en PA
		</td>
		<td>
			Cout en Honneur
		</td>
		<td>
			HP gagné
		</td>
		<td>
			MP gagné
		</td>
		<td>
			Achat
		</td>
	</tr>
		
		<?php
		
		$color = 1;
		$requete = "SELECT * FROM taverne";
		$req = $db->query($requete);
		$champ = 'nom';
		if($joueur->get_bonus_shine(12) !== false)
		{
			if($joueur->get_bonus_shine(12)->get_valeur() == 2)
				$champ = 'nom_f';
		}
		while($row = $db->read_array($req))
		{
			if ($row['requis'] != '')
			{ // Vérifier les conditions
				$cond = explode(';', $row['requis']);
				foreach ($cond as $tcond)
				{
					$ctype = substr($tcond, 0, 1);
					$cval = substr($tcond, 1);
					$cok = true;
					switch ($ctype)
					{
					case 'q': // quete
						$q = explode(';', $joueur->get_quete_fini());
						$cok = in_array($cval, $q);
						break;
					default:
						$cok = false;
						break;
					}
					if (!$cok)
						break; // un requis pas matché : on s'arrête
				}
				if (!$cok) // un requis pas matché : on ignore la ligne
					continue;
			}

			$taxe = ceil($row['star'] * $R->get_taxe_diplo($joueur->get_race()) / 100);
			$cout = $row['star'] + $taxe;
			if(array_key_exists('fort', $_GET)) $fort = '&amp;fort=ok'; else $fort = '';
		?>
		<tr class="element trcolor<?php echo $color; ?>">
			<td>
				<?php echo $row[$champ]; ?>
			</td>
			<td>
				<?php echo $cout; ?>
			</td>
			<td>
				<?php echo $row['pa']; ?>
			</td>
			<td onmouseover="<?php echo make_overlib('Vous perdrez '.$row['honneur'].' + '.$row['honneur_pc'].'% points d\'honneur'); ?>" onmouseout="nd();">
				<?php echo ($row['honneur'] + ceil($joueur->get_honneur() * $row['honneur_pc'] / 100)); ?>
			</td>
			<td onmouseover="<?php echo make_overlib('Vous regagnerez '.$row['hp'].' + '.$row['hp_pc'].'% HP'); ?>" onmouseout="nd();">
				<?php echo ($row['hp'] + ceil($joueur->get_hp_maximum() * $row['hp_pc'] / 100)); ?>
			</td>
			<td onmouseover="<?php echo make_overlib('Vous regagnerez '.$row['mp'].' + '.$row['mp_pc'].'% MP'); ?>" onmouseout="nd();">
				<?php echo ($row['mp'] + ceil($joueur->get_mp_maximum() * $row['mp_pc'] / 100)); ?>
			</td>
			<td>
				<a href="taverne.php?action=achat&amp;id=<?php echo $row['ID'].$fort; ?>" onclick="return envoiInfo(this.href, 'carte')"><span class="achat">Achat</span></a>
			</td>
		</tr>
		<?php
			if($color == 1) $color = 2; else $color = 1;
		}
		
		?>
		
		</table>
		</div>
</fieldset>

<?php
}
refresh_perso();
?>