<?php // -*- tab-width: 2 -*-
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

$tab_loc[3]['loc'] = 'arme_pet';
$tab_loc[3]['type'] = 'arme_pet';
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
				case 'arme_pet' :
					if($joueur->get_inventaire_partie($loc['loc'], true) != 'lock')
					{
						$requete = "SELECT * FROM `objet_pet` WHERE id = ".$objet['id_objet'];
						$sqlQuery = $db->query($requete);
						$row = $db->read_array($sqlQuery);
						$image = 'image/objet_pet/arme_pet/arme'.$row['id'].'.png'; 
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
				case 'arme_pet' :
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
