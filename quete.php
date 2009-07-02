<?php
//Affiche et gère l'inventaire du personnage

//Inclusion des fichiers indispensables
include('inc/fp.php');
$joueur = new perso($_SESSION['ID']);
if(array_key_exists('action', $_GET))
{
	switch($_GET['action'])
	{
		case 'delete' :
			$joueur = supprime_quete($joueur, $_GET['quete_joueur']);
		break;
	}
}
?>
<fieldset>
<legend>Quetes</legend>
<div id="liste_quete">
	<table width="99%">
		<tr>
			<td colspan="4" style="font-size : 0.8em; text-align : center;"><div style="float : left;"><a href="" onclick="if(document.getElementById('liste_quete').style.height == '100px') document.getElementById('liste_quete').style.height = '300px'; else document.getElementById('liste_quete').style.height = '100px'; return false;"><img src="image/expand.png" alt="expand" /></a></div>
<label onclick="envoiInfo('quete.php', 'information'); montre('');" style="cursor : pointer;">Toutes</label>
<?php
$ts = array(1 => 'Plaine',2 => 'Forêt',3 => 'Désert',4 => 'Neige',6 => 'Montagne',7 => 'Marais',8 => 'Route');
foreach ($ts as $tt => $tn)
{
	echo '| <label onClick="javascript:envoiInfo(\'quete.php?filter='.$tt.
		'\', \'information\');montre(\'\');" style="cursor : pointer;">'.$tn."</label>\n";
}
?>
			</td>
		</tr>
	<?php
	if($joueur->get_quete() != '')
	{
		$i = 0;
		$quete_id = array();
		foreach($joueur['quete'] as $quete)
		{
			$quete_id[] = $quete['id_quete'];
			$quest[$quete['id_quete']] = $i;
			$i++;
		}
		if (array_key_exists('filter', $_GET))
		{ // On récupère les ID des monstres possibles suivant ce filtre
		  $requete = 'SELECT id FROM monstre WHERE terrain REGEXP \''.
		  sSQL('^([0-9]+;)*'.$_GET['filter'].'(;[0-9]+)*$').'\';';
		  $reqf = $db->query($requete);
		  $qfilter = array();
		  while($row = $db->read_array($reqf))
		  {
		    $qfilter[] = $row['id'];
		  }
		  //var_dump($qfilter);
		}
		$i = 0;
		$ids = implode(',', $quete_id);
		$requete = 'SELECT * FROM quete WHERE id IN ('.$ids.') ORDER BY lvl_joueur DESC';
		if(count($quete_id) > 0)
		{
			$req = $db->query($requete);
			while($row = $db->read_array($req))
			{
				if($row['repete'] == 'y') $repetable = ' - R'; else $repetable = '';
				if($row['mode'] == 's') $mode = 'S'; else $mode = 'G';
				$objectif = unserialize($row['objectif']);
				if (array_key_exists('filter', $_GET))
				{
				  $found = false;
				  foreach ($qfilter as $mfilter)
				  {
				    //if (strstr($objectif[0]->cible,
				    //       "M$mfilter"))
				    if ($objectif[0]->cible == "M$mfilter")
				    {
				      $found = true;
				      break;
				    }
				  }
				  if ($found == false)
				    continue; // On affiche pas ça
				}
				$normal = 'BEC8CE';
				$highlight = 'blue';
				$i = 0;
				$total = 0;
				$total_fait = 0;
				foreach($joueur['quete'][$quest[$row['id']]]['objectif'] as $objectif_fait)
				{
					$total_fait += $objectif_fait->nombre;
					$total += $objectif[$i]->nombre;
					$i++;
				}
				echo '<tr>
				<td width="40%" onclick="$(\'liste_quete\').style.height = \'100px\'; envoiInfo(\'desc_quete.php?id_quete='.$row['id'].'&amp;quete_joueur='.$quest[$row['id']].'\', \'desc_quete\');">
					<span class="small" style="cursor : pointer;">'.$row['nom'].'
				</td>
				<td width="10%">
					<span class="small" style="cursor : pointer;">Niv.'.$row['lvl_joueur'].'</span>
				</td>
				<td width="10%">
					<span class="small" style="cursor : pointer;">'.$mode.$repetable.'</span>
				</td>
				<td width="20%">
					<span class="small" style="cursor : pointer;">'.$total_fait.' / '.$total.'</span>
				</td>
				</tr>';
				?>
				<?php
				$i++;
			}
		}
	}
	?>
	</table>
</div>
<div id="desc_quete" class="quete_description">
	Pour avoir une description plus précise d'une quète, il vous faut simplement cliquer sur son titre dans la partie du haut.
</div>
</fieldset>