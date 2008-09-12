<?php // -*- tab-width:	 2 -*-
	if (isset($_GET['javascript']))
	{
		include('inc/fp.php');
		$joueur = recupperso($_SESSION['ID']);
		$W_case = 1000 * $joueur['y'] + $joueur['x'];
		$W_requete = 'SELECT * FROM map WHERE ID ='.sSQL($W_case);
		$W_req = $db->query($W_requete);
		$W_row = $db->read_array($W_req);
		$R = get_royaume_info($joueur['race'], $W_row['royaume']);
	}
//Filtre
if(array_key_exists('filtre', $_GET)) $filtre_url = '&amp;filtre='.$_GET['filtre'];
else $filtre_url = '';
?>
<table class="information_case" style="width:95%;">
<tr>
	<td>
		Nom
	</td>
	<td>
		Action
	</td>
</tr>
<?php
$i = 0;
if($joueur['inventaire_slot'] != '')
{
	foreach($joueur['inventaire_slot'] as $invent)
	{
		if($invent !== 0 AND $invent != '')
		{
			$objet_d = decompose_objet($invent);
			//echo '<!-- '; var_dump($objet_d); echo '-->';
			if($objet_d['identifier'])
			{
				switch ($objet_d['categorie'])
				{
					//Si c'est une arme
					case 'a' :
						$requete = "SELECT * FROM arme WHERE ID = ".$objet_d['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$mains = explode(';', $row['mains']);
						$partie = $mains[0];
					break;
					//Si c'est une protection
					case 'p' :
						$requete = "SELECT * FROM armure WHERE ID = ".$objet_d['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$partie = $row['type'];
					break;
					case 'o' :
						$requete = "SELECT * FROM objet WHERE ID = ".$objet_d['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$partie = $row['type'];
					break;
					case 'g' :
						$requete = "SELECT * FROM gemme WHERE ID = ".$objet_d['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$partie = $row['type'];
						$row['prix'] = pow(10, $row['niveau']) * 10;
					break;
					case 'r' :
						$requete = "SELECT * FROM objet_royaume WHERE ID = ".$objet_d['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$partie = $row['type'];
						$row['utilisable'] = 'y';
					break;
					case 'm' :
						$requete = "SELECT * FROM accessoire WHERE ID = ".$objet_d['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$partie = 'accessoire';
						$row['utilisable'] = 'n';
					break;
					case 'l' :
						$requete = "SELECT * FROM grimoire WHERE ID = ".$objet_d['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$partie = $row['type'];
						$row['utilisable'] = 'y';
					break;
				}
			}
			else
			{
				$row['nom'] = 'Objet non-identifiée';
			}
			//Filtrage
			if(array_key_exists('filtre', $_GET)) $filtre = $_GET['filtre']; else $filtre = 'utile';
			$check = false;
			$liste_categorie = array('o', 'a', 'p');
			if((($objet_d['categorie'] == 'o' AND $filtre == 'utile')
					OR ($objet_d['categorie'] == 'l' AND $filtre == 'utile')
					OR ($objet_d['categorie'] == 'a' AND $filtre == 'arme')
					OR ($objet_d['categorie'] == 'p' AND $filtre == 'armure'))
				 AND $objet_d['identifier'])
			{
				$check = true;
			}
			elseif(!in_array($objet_d['categorie'], $liste_categorie) AND $filtre == 'autre') $check = true;
			if($check)
			{
				$echo = description_objet($invent);
			?>
			<tr>
			<td onmouseover="return <?php echo make_overlib($echo); ?>" onmouseout="return nd();">
			<?php
			$pages = array();
			$pages_nom = array();
			if($objet_d['identifier'])
			{
				if($objet_d['categorie'] == 'g')
				{
					$pages[] = 'inventaire.php?action=enchasse&amp;key_slot='.$i.$filtre_url;
					$pages_nom[] = 'Enchasser (20 PA)';
				}
				elseif($objet_d['categorie'] == 'a' OR $objet_d['categorie'] == 'p' OR $objet_d['categorie'] == 'm')
				{
					$pages[] = 'inventaire.php?action=equip&amp;id_objet='.$objet_d['id_objet'].'&amp;partie='.$partie.'&amp;key_slot='.$i.'&amp;categorie='.$objet_d['categorie'].$filtre_url;
					$pages_nom[] = 'Equiper';
				}
				elseif($objet_d['categorie'] == 'o' OR $objet_d['categorie'] == 'r')
				{
					if($row['utilisable'] == 'y')
					{
						$pages[] = 'inventaire.php?action=utilise&amp;id_objet='.$objet_d['id_objet'].'&amp;type='.$row['type'].'&amp;key_slot='.$i.$filtre_url;
						$pages_nom[] = 'Utiliser';
					}
					if($W_row['type'] == 1)
					{
						$pages[] = 'inventaire.php?action=depot&amp;id_objet='.$objet_d['id_objet'].'&amp;type='.$row['type'].'&amp;key_slot='.$i.$filtre_url;
						$pages_nom[] = 'Déposer au dépot';
					}
				}
				elseif($objet_d['categorie'] == 'l')
				{
					$pages[] = 'inventaire.php?action=utilise&amp;id_objet='.$objet_d['id_objet'].'&amp;type='.$row['type'].'&amp;key_slot='.$i.$filtre_url;
					$pages_nom[] = 'Lire';
				}
				if ($W_row['type'] == 1 AND $objet_d['categorie'] != 'r' AND $objet_d['categorie'] != 'h')
				{
					$prix = floor($row['prix'] * $modif_prix / $G_taux_vente);
					$pages[] = 'inventaire.php?action=vente&amp;id_objet='.$objet_d['id'].'&amp;key_slot='.$i.$filtre_url;
					$pages_nom[] = 'Vendre '.$prix.' Stars';
					$pages[] = 'inventaire.php?action=ventehotel&amp;id_objet='.$objet_d['categorie'].$objet_d['id_objet'].'&amp;key_slot='.$i.$filtre_url;
					$pages_nom[] = 'Hotel des ventes';
				}
				if(($objet_d['categorie'] == 'a' OR $objet_d['categorie'] == 'p' OR $objet_d['categorie'] == 'm') AND $objet_d['slot'] == '' AND $objet_d['enchantement'] == '')
				{
					$pages[] = 'inventaire.php?action=slot&amp;key_slot='.$i.$filtre_url;
					$pages_nom[] = 'Mettre un slot à cet objet (10 PA)';
				}
			}

			$onclick = 'onglet = \'information\';';
			$ij = 0;
			foreach($pages as $page)
			{
				$onclick .= 'page'.$ij.' = \''.$page.'\';';
				$ij++;
			}
			$text = '';
			$ij = 0;
			foreach($pages_nom as $page_nom)
			{
				$text .= addslashes('<a onclick=\'envoiInfo(page'.$ij.', onglet); nd()\'>'.$page_nom.'</a><br />');
				$ij++;
			}
			?>
			<span onclick="<?php echo $onclick; ?> return overlib('<?php echo $text; ?>', STICKY, CAPTION, '<?php echo addslashes($row['nom']); ?>', OFFSETX, -50, OFFSETY, -30, CLOSECLICK);"><?php echo $row['nom']; ?></span>
			<?php
			$modif_prix = 1;
			if($objet_d['stack'] > 1) echo ' X '.$objet_d['stack'];
			if($objet_d['slot'] > 0)
			{
				echo '<br /><span class="xsmall">Slot niveau '.$objet_d['slot'].'</span>';
				$modif_prix = 1 + ($objet_d['slot'] / 5);
			}
			if($objet_d['slot'] == '0')
			{
				echo '<br /><span class="xsmall">Slot impossible</span>';
				$modif_prix = 0.9;
			}
			if($objet_d['enchantement'] > '0')
			{
				$requete = "SELECT * FROM gemme WHERE id = ".$objet_d['enchantement'];
				$req = $db->query($requete);
				$row_e = $db->read_assoc($req);
				$modif_prix = 1 + ($row_e['niveau'] / 2);
				echo '<br /><span class="xsmall">Enchantement de '.$row_e['enchantement_nom'].'</span>';
			}
			//else echo ' X 1';
			//print_r($objet_d);
			if($objet_d['identifier'])
			{
				if($objet_d['categorie'] == 'g')
				{
					echo ' <a href="javascript:envoiInfo(\'inventaire.php?action=enchasse&amp;key_slot='.$i.$filtre_url.'\', \'information\');">Enchasser</a> <span class="xsmall">(20 PA)</span> / ';
				}
				elseif($objet_d['categorie'] == 'a' OR $objet_d['categorie'] == 'p' OR $objet_d['categorie'] == 'm')
				{
					echo ' <a href="javascript:envoiInfo(\'inventaire.php?action=equip&amp;id_objet='.$objet_d['id_objet'].'&amp;partie='.$partie.'&amp;key_slot='.$i.'&amp;categorie='.$objet_d['categorie'].$filtre_url.'\', \'information\');">Equiper</a> / ';
				}
				elseif($objet_d['categorie'] == 'o' OR $objet_d['categorie'] == 'r')
				{
					if($row['utilisable'] == 'y') echo ' <a href="javascript:envoiInfo(\'inventaire.php?action=utilise&amp;id_objet='.$objet_d['id_objet'].'&amp;type='.$row['type'].'&amp;key_slot='.$i.$filtre_url.'\', \'information\');">Utiliser</a> / ';
					if($W_row['type'] == 1 AND $objet_d['categorie'] == 'o') echo '<a href="javascript:envoiInfo(\'inventaire.php?action=depot&amp;id_objet='.$objet_d['id_objet'].'&amp;type='.$row['type'].'&amp;key_slot='.$i.$filtre_url.'\', \'information\');">Déposer au dépot</a>';
				}
				elseif($objet_d['categorie'] == 'l')
				{
					echo ' <a href="javascript:envoiInfo(\'inventaire.php?action=utilise&amp;id_objet='.$objet_d['id_objet'].'&amp;type=grimoire&amp;key_slot='.$i.$filtre_url.'\', \'information\');">Lire</a> / ';
				}
				if ($W_row['type'] == 1 AND $objet_d['categorie'] != 'r' AND $objet_d['categorie'] != 'h')
				{
					$prix = floor($row['prix'] * $modif_prix / $G_taux_vente);
					echo ' <a href="javascript:if(confirm(\'Voulez vous vendre cet objet ?\')) envoiInfo(\'inventaire.php?action=vente&amp;id_objet='.$objet_d['id'].'&amp;key_slot='.$i.$filtre_url.'\', \'information\');">Vendre '.$prix.' Stars</a> / <a href="javascript:envoiInfo(\'inventaire.php?action=ventehotel&amp;id_objet='.$objet_d['categorie'].$objet_d['id_objet'].'&amp;key_slot='.$i.$filtre_url.'\', \'information\');">Hotel des ventes</a>';
				}
				if(($objet_d['categorie'] == 'a' OR $objet_d['categorie'] == 'p' OR $objet_d['categorie'] == 'm') AND $objet_d['slot'] == '' AND $objet_d['enchantement'] == '')
				{
					echo '<br /><a href="javascript:envoiInfo(\'inventaire.php?action=slot&amp;key_slot='.$i.$filtre_url.'\', \'information\');">Mettre un slot à cet objet</a> <span class="xsmall">(10 PA)</span>';
				}
			}
			echo '
			</td>
		</tr>';
			}
			$i++;
		}
	}
}
?>
</table>