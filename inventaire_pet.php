<?php //	 -*- tab-width:	 2 -*-

// ALTER TABLE `perso` ADD `inventaire_pet` TEXT NOT NULL AFTER `inventaire` 



if (file_exists('root.php'))
  include_once('root.php');

//Affiche et gère l'inventaire du personnage

//Inclusion des fichiers indispensables
include_once(root.'inc/fp.php');

// Inclusion du gestionnaire de compétences
include_once(root.'fonction/competence.inc.php');

//Visu par un autre joueur
if(array_key_exists('id_perso', $_GET))
{
	$visu = true;
	$bonus = recup_bonus($_GET['id_perso']);
	if(array_key_exists(20, $bonus) AND check_affiche_bonus($bonus[20], $joueur, $perso))
	{
		$joueur_id = $_GET['id_perso'];
	}
	else exit();
}
else
{
	$visu = false;
	$joueur_id = $_SESSION['ID'];
}
$joueur = new perso($joueur_id);
//Filtre
if(array_key_exists('filtre', $_GET)) $filtre_url = '&amp;filtre='.$_GET['filtre'];
else $filtre_url = '';
$W_requete = 'SELECT royaume, type FROM map WHERE x ='.$joueur->get_x()
		 .' and y = '.$joueur->get_y();
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);
?>
<fieldset>
<legend>Inventaire de votre créature</legend>
<ul id="messagerie_onglet">
	<li><a href="inventaire.php" onclick="return envoiInfo(this.href, 'information');">Personnage</a></li>
	<li><a href="inventaire_pet.php" onclick="return envoiInfo(this.href, 'information');">Créature</a></li>
</ul>
	<div class="spacer"></div>
<?php
//Switch des actions
if(!$visu AND isset($_GET['action']))
{
	switch($_GET['action'])
	{
		case 'desequip' :
			if($joueur->desequip($_GET['partie'], true))
			{
			}
			else
			{
				echo '<h5>'.$G_erreur.'</h5>';
			}
		break;
		case 'equip' :
			if($joueur->equip_objet($joueur->get_inventaire_slot_partie($_GET['key_slot']), true))
			{
				//On supprime l'objet de l'inventaire
				$joueur->supprime_objet($joueur->get_inventaire_slot_partie($_GET['key_slot'], true), 1);
				$joueur->sauver();
			}
			else
			{
				echo '<h5>'.$G_erreur.'</h5>';
			}
		break;
		case 'slot' :
			$craft = $joueur->get_forge();
			if($joueur->get_race() == 'scavenger') $craft = round($craft * 1.45);
			if($joueur->get_accessoire() !== false)
			{
				$accessoire = $joueur->get_accessoire();
				if($accessoire->type == 'fabrication')
					$craft = round($craft * (1 + ($accessoire->effet / 100)));
			}

			// Gemme de fabrique : augmente de effet % le craft
			if ($joueur->get_enchantement()!== false &&
					$joueur->is_enchantement('forge')) {
				$craft += round($craft * ($joueur->get_enchantement('forge','effet') / 100));
			}

			$chance_reussite1 = pourcent_reussite($craft, 10);
			$chance_reussite2 = pourcent_reussite($craft, 30);
			$chance_reussite3 = pourcent_reussite($craft, 100);
			echo 'Quel niveau d\'enchâssement voulez vous ?
			<ul>
				<li><a href="inventaire_pet.php?action=slot2&amp;key_slot='.$_GET['key_slot'].'&amp;niveau=1'.$filtre_url.'" onclick="return envoiInfo(this.href, \'information\');">Niveau 1</a> <span class="small">('.$chance_reussite1.'% de chances de réussite)</span></li>
				<li><a href="inventaire_pet.php?action=slot2&amp;key_slot='.$_GET['key_slot'].'&amp;niveau=2'.$filtre_url.'" onclick="return envoiInfo(this.href, \'information\');">Niveau 2</a> <span class="small">('.$chance_reussite2.'% de chances de réussite)</span></li>
				<li><a href="inventaire_pet.php?action=slot2&amp;key_slot='.$_GET['key_slot'].'&amp;niveau=3'.$filtre_url.'" onclick="return envoiInfo(this.href, \'information\');">Niveau 3</a> <span class="small">('.$chance_reussite3.'% de chances de réussite)</span></li>
			</ul>';
		break;
		case 'slot2' :
			if($joueur->get_pa() >= 10)
			{
				$objet = decompose_objet($joueur->get_inventaire_slot_partie($_GET['key_slot']));
				if(empty($objet['slot']))
				{
					switch($_GET['niveau'])
					{
						case '1' :
							$difficulte = 10;
						break;
						case '2' :
							$difficulte = 30;
						break;
						case '3' :
							$difficulte = 100;
						break;
					}
					$craft = $joueur->get_forge();
					if($joueur->get_race() == 'scavenger') $craft = round($craft * 1.45);
					if($joueur->get_accessoire() !== false)
					{
						$accessoire = $joueur->get_accessoire();
						if($accessoire->type == 'fabrication')
							$craft = round($craft * (1 + ($accessoire->effet / 100)));
					}

					// Gemme de fabrique : augmente de effet % le craft
					if ($joueur->get_enchantement()!== false &&
							$joueur->is_enchantement('forge')) {
						$craft += round($craft * ($joueur->get_enchantement('forge','effet') / 100));
					}

					$craftd = rand(0, $craft);
					$diff = rand(0, $difficulte);
					echo 'dé du joueur : '.$craft.' / dé difficulté : '.$difficulte.'<br />
					Résultat joueur : '.$craftd.' / Résultat difficulte : '.$diff.'<br />';
					if($craftd >= $diff)
					{
						//Craft réussi
						echo 'Réussite !<br />';
						$objet['slot'] = $_GET['niveau'];
					}
					else
					{
						//Craft échec
						echo 'Echec... L\'objet ne pourra plus être enchâssable<br />';
						$objet['slot'] = 0;
					}
					$augmentation = augmentation_competence('forge', $joueur, 2);
					if ($augmentation[1] == 1)
					{
						$joueur->set_forge($augmentation[0]);
						$joueur->sauver();
					}
					$objet_r = recompose_objet($objet);
					$joueur->set_inventaire_slot_partie($objet_r, $_GET['key_slot']);
					$joueur->set_inventaire_slot(serialize($joueur->get_inventaire_slot_partie(false, true)));
					$joueur->set_pa($joueur->get_pa() - 10);
					$joueur->sauver();
				}
				else
					echo 'Cet objet &agrave; d&eacute;j&agrave; un slot!';
			}
			else
			{
				echo 'Vous n\'avez pas assez de PA.';
			}
		break;
		case 'enchasse' :
			$gemme = decompose_objet($joueur->get_inventaire_slot_partie($_GET['key_slot']));
			$requete = "SELECT * FROM gemme WHERE id = ".$gemme['id_objet'];
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			switch($row['type'])
			{
				case 'arme' :
					$type = 'a';
				break;
				case 'armure' :
					$type = 'p';
				break;
				case 'accessoire' :
					$type = 'm';
				break;
			}
			switch($row['niveau'])
			{
				case 1 :
					$difficulte = 10;
				break;
				case 2 :
					$difficulte = 30;
				break;
				case 3 :
					$difficulte = 100;
				break;
			}
			$craft = $joueur->get_forge();
			if($joueur->get_race() == 'scavenger') $craft = round($craft * 1.45);
			if($joueur->get_accessoire() !== false)
			{
				$accessoire = $joueur->get_accessoire();
				if($accessoire->type == 'fabrication')
					$craft = round($craft * (1 + ($accessoire->effet / 100)));
			}

			// Gemme de fabrique : augmente de effet % le craft
			if ($joueur->get_enchantement()!== false &&
					$joueur->is_enchantement('forge')) {
				$craft += round($craft * ($joueur->get_enchantement('forge','effet') / 100));
			}

			echo 'Dans quel objet voulez vous enchâsser cette gemme de niveau '.$row['niveau'].' ?
			<ul>';
			//Recherche des objets pour enchassement possible
			$i = 0;
			while($i <= $G_place_inventaire)
			{
				if($joueur->get_inventaire_slot_partie($i) != '')
				{
					$objet_i = decompose_objet($joueur->get_inventaire_slot_partie($i));
					//echo '<br />'.$joueur->get_inventaire_slot()[$i].'<br />';
					if($objet_i['identifier'] AND $objet_i['categorie'] != 'r')
					{
						if($objet_i['categorie'] == 'a') $table = 'arme';
						elseif($objet_i['categorie'] == 'p') $table = 'armure';
						elseif($objet_i['categorie'] == 'm') $table = 'accessoire';
						elseif($objet_i['categorie'] == 'o') $table = 'objet';
						elseif($objet_i['categorie'] == 'g') $table = 'gemme';
						else {
							print_debug("table introuvable pour $objet_i[categorie]");
							$i++;
							continue;
						}
						$requete = "SELECT type FROM ".$table." WHERE id = ".$objet_i['id_objet'];
						$req_i = $db->query($requete);
						$row_i = $db->read_row($req_i);
						$check = true;
						$j = 0;
						$parties = explode(';', $row['partie']);
						$count = count($parties);
						if (strlen($row['partie']) > 0) $check = false;
						while(!$check AND $j < $count)
						{
							if($parties[$j] == $row_i[0]) $check = true;
							//echo $parties[$j].' '.$row_i[0].'<br />';
							$j++;
						}
						if($check AND ($objet_i['categorie'] == $type) AND ($objet_i['slot'] >= $row['niveau']))
						{
							$nom = nom_objet($joueur->get_inventaire_slot_partie($i));
							$chance_reussite = pourcent_reussite($craft, $difficulte);
							//On peut mettre la gemme
							echo '<li><a href="inventaire_pet.php?action=enchasse2&amp;key_slot='.$_GET['key_slot'].'&amp;key_slot2='.$i.'&amp;niveau='.$row['niveau'].$filtre_url.'" onclick="return envoiInfo(this.href, \'information\');">'.$nom.' / slot niveau '.$objet_i['slot'].'</a> <span class="xsmall">'.$chance_reussite.'% de chance de réussite</span></li>';
						}
					}
				}
				$i++;
			}
			echo '
			</ul>';
		break;
		case 'enchasse2' :
			if($joueur->get_pa() >= 20)
			{
				$craft = $joueur->get_forge();
				if($joueur->get_race() == 'scavenger') $craft = round($craft * 1.45);
				if($joueur->get_accessoire() !== false)
				{
					$accessoire = $joueur->get_accessoire();
					if($accessoire->type == 'fabrication')
						$craft = round($craft * (1 + ($accessoire->effet / 100)));
				}				

				$gemme = decompose_objet($joueur->get_inventaire_slot_partie($_GET['key_slot']));
				$objet = decompose_objet($joueur->get_inventaire_slot_partie($_GET['key_slot2']));
				switch($_GET['niveau'])
				{
					case '1' :
						$difficulte = 10;
					break;
					case '2' :
						$difficulte = 30;
					break;
					case '3' :
						$difficulte = 100;
					break;
				}

				// Gemme de fabrique : augmente de effet % le craft
				if ($joueur->get_enchantement()!== false &&
						$joueur->is_enchantement('forge')) {
					$craft += round($craft * ($joueur->get_enchantement('forge','effet') / 100));
				}

				$craftd = rand(0, $craft);
				$diff = rand(0, $difficulte);
				echo 'dé du joueur : '.$craft.' / dé difficulté : '.$difficulte.'<br />
				Résultat joueur : '.$craftd.' / Résultat difficulte : '.$diff.'<br />';
				$gemme_casse = false;
				if($craftd >= $diff)
				{
					//Craft réussi
					echo 'Réussite !<br />';
					$objet['enchantement'] = $gemme['id_objet'];
					$objet['slot'] = 0;
					$gemme_casse = true;
				}
				else
				{
					//Craft échec
					//66% chance objet plus enchassable, 34% gemme disparait
					$rand = rand(1, 100);
					if($rand <= 34)
					{
						echo 'Echec... la gemme a cassé...<br />';
						$gemme_casse = true;
					}
					else
					{
						echo 'Echec... L\'objet ne pourra plus être enchassable...<br />';
						$objet['slot'] = 0;
					}
				}
				$augmentation = augmentation_competence('forge', $joueur, 1);
				if ($augmentation[1] == 1)
				{
					$joueur->set_forge($augmentation[0]);
					$joueur->sauver();
				}
				$objet_r = recompose_objet($objet);
				$joueur->set_inventaire_slot_partie($objet_r, $_GET['key_slot2']);
				$joueur->set_inventaire_slot(serialize($joueur->get_inventaire_slot_partie(false, true)));
				$joueur->set_pa($joueur->get_pa() - 20);
				$joueur->sauver();
				if($gemme_casse) $joueur->supprime_objet($joueur->get_inventaire_slot_partie($_GET['key_slot']), 1);
			}
			else
			{
				echo 'Vous n\'avez pas assez de PA.';
			}
		break;
	}
	refresh_perso();
}
$joueur = new perso($joueur_id);
$tab_loc = array();

$tab_loc[0]['loc'] = 'cou';
$tab_loc[0]['type'] = 'collier';
$tab_loc[1]['loc'] = 'selle';
$tab_loc[1]['type'] = 'selle';
$tab_loc[2]['loc'] = 'dos';
$tab_loc[2]['type'] = 'dos';

$tab_loc[3]['loc'] = 'arme';
$tab_loc[3]['type'] = 'arme';
$tab_loc[4]['loc'] = 'torse';
$tab_loc[4]['type'] = 'armure';
$tab_loc[5]['loc'] = ' ';
$tab_loc[5]['type'] = 'vide';

?>

<table cellspacing="3" width="100%" style="background: url('image/creature.png') center no-repeat;">

<?php
$color = 2;
$compteur=0;
foreach($tab_loc as $loc)
{
	if (($compteur % 3) == 0)
		{
			echo '<tr style="height : 55px;">';
		}
		if ($loc['type']=='vide')
		{
			echo '<td>';
		}
		else
		{
			echo '<td class="inventaire2">';
		}
		
		if($joueur->inventaire_pet()->$loc['loc'] != '')
		{
			$objet = decompose_objet($joueur->get_inventaire_partie($loc['loc'], true));
			//On peut désequiper
			if(!$visu AND $joueur->get_inventaire_partie($loc['loc'], true) != '' AND $joueur->get_inventaire_partie($loc['loc'], true) != 'lock') $desequip = true; else $desequip = false;
			switch($loc['type'])
			{
				case 'arme' :
					if($joueur->get_inventaire_partie($loc['loc'], true) != 'lock')
					{
						$requete = "SELECT * FROM `objet_pet` WHERE id = ".$objet['id_objet'];
						$sqlQuery = $db->query($requete);
						$row = $db->read_array($sqlQuery);
						$image = 'image/objet_pet/arme/arme'.$row['id'].'.png'; 
						$nom = $row['nom'];
					}
					else
					{
						$nom = 'Lock';
						$image = '';
					}
				break;
				case 'armure' :
				case 'selle':
				case 'collier':
				case 'carapacon':
					$requete = "SELECT * FROM `objet_pet` WHERE id = ".$objet['id_objet'];
					$sqlQuery = $db->query($requete);
					$row = @$db->read_array($sqlQuery);
					$image = 'image/objet_pet/'.$loc['loc'].'/'.$loc['loc'].$row['id'].'.png'; 
					$nom = $row['nom'];
				break;
				case 'accessoire' :
					$requete = "SELECT * FROM `accessoire` WHERE id = ".$objet['id_objet'];
					$sqlQuery = $db->query($requete);
					$row = @$db->read_array($sqlQuery);
					$image = 'image/accessoire/accessoire'.$row['id'].'.png'; 
					$nom = $row['nom'];
				break;
			}
			if($desequip)
			{
				echo '<a href="inventaire_pet.php?action=desequip&amp;partie='.$loc['loc'].$filtre_url.'" onclick="return envoiInfo(this.href, \'information\');">
				<img src="'.$image.'" style="float : left;" title="Déséquiper" alt="Déséquiper" />
				</a>';
			}
			echo '<strong>'.$nom.'</strong>';
			if($objet['slot'] > 0)
			{
				echo '<br /><span class="xsmall">Slot niveau '.$objet['slot'].'</span>';
			}
			if($objet['slot'] == '0')
			{
				echo '<br /><span class="xsmall">Slot impossible</span>';
			}
			if($objet['enchantement'] > '0')
			{
				$requete = "SELECT * FROM gemme WHERE id = ".$objet['enchantement'];
				$req = $db->query($requete);
				$row_e = $db->read_assoc($req);
				echo '<br /><span class="xsmall">Enchantement de '.$row_e['enchantement_nom'].'</span>';
			}
		}
		else
		{
			echo $Gtrad[$loc['loc']];
		}
		
		
		if($joueur->get_inventaire_partie($loc['loc'], true) != '' AND $joueur->get_inventaire_partie($loc['loc'], true) != 'lock')
		{
			switch($loc['type'])
			{
				case 'arme' :
					echo '<br />Dégâts : '.$joueur->get_arme_degat('pet');
				break;
				case 'armure' :
				case 'selle' :
				case 'collier' :
				case 'carapacon' :
					echo '<br />PP : '.$row['PP'].' / PM : '.$row['PM'];
				break;
			}
		}
		
	echo '</td>';
	if ((($compteur + 1) % 3) == 0)
	{ 
		echo '</tr>';
	}
$compteur++;
}
?>
</table>
<?php
if(!$visu)
{
	 if(array_key_exists('filtre', $_GET)) $filtre = $_GET['filtre'];
	 else $filtre = 'utile';
?>
<p>Place restante dans l'inventaire : <?php echo ($G_place_inventaire - count($joueur->get_inventaire_slot_partie())) ?> / <?php echo $G_place_inventaire;?></p>
<div id='messagerie_menu'>
<span class="<?php if($filtre == 'utile'){echo 'seleted';}?>" onclick="envoiInfo('inventaire_slot.php?javascript=ok&amp;filtre=utile', 'inventaire_slot')">Utile</span>
<span class="<?php if($filtre == 'arme'){ echo 'seleted';} ?>" onclick="envoiInfo('inventaire_slot.php?javascript=ok&amp;filtre=arme', 'inventaire_slot')">Arme</span>
<span class="<?php if($filtre == 'armure'){echo 'seleted';}?>" onclick="envoiInfo('inventaire_slot.php?javascript=ok&amp;filtre=armure', 'inventaire_slot')">Armure</span>
<span class="<?php if($filtre == 'autre'){echo 'seleted';}?>" onclick="envoiInfo('inventaire_slot.php?javascript=ok&amp;filtre=autre', 'inventaire_slot')">Autre</span>
</div>
<div id="inventaire_slot">
	<?php
	require_once('inventaire_slot.php');
	?>
</fieldset>
<?php
}
?>
