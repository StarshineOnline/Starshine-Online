<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php

//Inclusion du haut du document html
include_once(root.'haut_ajax.php');

$joueur = recupperso($_SESSION['ID']);
check_perso($joueur);

//Vérifie si le perso est mort
verif_mort($joueur, 1);

$W_case = $_GET['poscase'];
$W_requete = 'SELECT * FROM map WHERE ID =\''.sSQL($W_case).'\'';
$W_req = $db->query($W_requete);
$W_row = $db->read_array($W_req);
$R = get_royaume_info($joueur['race'], $W_row['royaume']);

if(!isset($_GET['type'])) $_GET['type'] = 'arme';

$_SESSION['position'] = convert_in_pos($joueur['x'], $joueur['y']);
$W_distance = detection_distance($W_case,$_SESSION["position"]);
$W_coord = convert_in_coord($W_case);
if($W_distance == 0)
{
	if(isset($_GET['action']))
	{
		switch ($_GET['action'])
		{
			//Achat
			case 'achat' :
				switch ($_GET['type'])
				{
					case 'arme' :
						$requete = "SELECT id, prix FROM objet WHERE id = ".sSQL($_GET['id']);
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$taxe = ceil($row['prix'] * $R['taxe'] / 100);
						$cout = $row['prix'] + $taxe;
						if ($joueur['star'] >= $cout)
						{
							$i = 0;
							//Recherche un emplacement libre
							while(($i <= $G_place_inventaire) AND !$trouver)
							{
								if($joueur['inventaire_slot'][$i] === 0 OR $joueur['inventaire_slot'][$i] == '')
								{
									$trouver = true;
								}
								else $i++;
							}
							//Inventaire plein
							if(!$trouver)
							{
								echo 'Vous n\'avez plus de place dans votre inventaire<br />';
							}
							else
							{
								$joueur['inventaire_slot'][$i] = 'o'.$_GET['id'];
								$inventaire_slot = serialize($joueur['inventaire_slot']);
								$req = $db->query($requete);
								$joueur['star'] = $joueur['star'] - $cout;
								$requete = "UPDATE perso SET inventaire_slot = '".$inventaire_slot."', star = ".$joueur['star']." WHERE ID = ".$joueur['ID'];
								$req = $db->query($requete);
								//Récupération de la taxe
								if($taxe > 0)
								{
									$requete = 'UPDATE royaume SET star = star + '.$taxe.' WHERE ID = '.$R['ID'];
									$db->query($requete);
								}
								echo 'Objet acheté !<br />';
							}
						}
						else
						{
							echo 'Vous n\'avez pas assez de Stars<br />';
						}
					break;
				}
			break;
		}
	}
	
	if(!isset($_GET['order']) OR ($_GET['order'] == '')) $_GET['order'] = 'type,prix';
	$order = explode(',', $_GET['order']);
	$i = 0;
	$ordre = '';
	while($i < count($order))
	{
		if($i != 0) $ordre .= ',';
		$ordre .= ' '.$order[$i].' ASC';
		$i++;
	}
	//Affichage du menu de séléction et de tri
	$url = 'bazar.php?type='.$_GET['type'].'&amp;poscase='.$W_case.'&amp;order=';
	?>
	
	Trier par :	<a href="<?php echo $url; ?>prix" onclick="return envoiInfo(this.href, 'carte')">Prix</a> :: <a href="<?php echo $url; ?>type" onclick="return envoiInfo(this.href, 'carte')">Type</a> :: <a href="<?php echo $url; ?>" onclick="return envoiInfo(this.href, 'carte')">Effets</a> :: <a href="<?php echo $url; ?>forcex" onclick="return envoiInfo(this.href, 'carte')">Force</a><br />
	<br />
	
	<?php
	$url2 = 'bazar.php?type=arme&amp;poscase='.$W_case.'&amp;order='.$_GET['order'];
	?>
		[<a href="<?php echo $url2; ?>&amp;part=dague" onclick="return envoiInfo(this.href, 'carte')">Dague</a> - <a href="<?php echo $url2; ?>&amp;part=epee" onclick="return envoiInfo(this.href, 'carte')">Epée</a> - <a href="<?php echo $url2; ?>&amp;part=hache" onclick="return envoiInfo(this.href, 'carte')">Hache</a>]<br />
		<table class="marchand" cellspacing="0px">
		<tr class="header trcolor2">
			<td>
				Nom
			</td>
			<td>
				Type
			</td>
			<td>
				Dégats
			</td>
			<td>
				Force
			</td>
			<td>
				Mélée
			</td>
			<td>
				Stars
			</td>
			<td>
				Achat
			</td>
		</tr>
		
		<?php
		
		$color = 1;
		$where = "1";
		if(array_key_exists('part', $_GET))
		{
			$where .= " AND type = '".$_GET['part']."'";
		}
		$requete = "SELECT * FROM objet WHERE ".$where." ORDER BY".$ordre;
		$req = $db->query($requete);
		while($row = $db->read_array($req))
		{
			$taxe = ceil($row['prix'] * $R['taxe'] / 100);
			$cout = $row['prix'] + $taxe;
			$couleur = $color;
			if($cout > $joueur['star']) $couleur = 3;
		?>
		<tr class="element trcolor<?php echo $couleur; ?>">
			<td>
				<?php echo $row['nom']; ?>
			</td>
			<td>
				<?php echo $row['type']; ?>
			</td>
			<td>
				<?php echo $row['degat']; ?>
			</td>
			<td>
				<?php echo $row['forcex']; ?>
			</td>
			<td>
				<?php echo $row['melee']; ?>
			</td>
			<td>
				<?php echo $cout; ?>
			</td>
			<td>
				<a href="bazar.php?action=achat&amp;id=<?php echo $row['id']; ?>&amp;poscase=<?php echo $_GET['poscase']; ?>" onclick="return envoiInfo(this.href, 'carte')">Acheter</a>
			</td>
		</tr>
		<?php
			if($color == 1) $color = 2; else $color = 1;
		}
		
		?>
		
		</table>
<?php
}
?>