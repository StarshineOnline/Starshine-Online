<?php
if(array_key_exists('javascript', $_GET)) include('inc/fp.php');
if (!isset($_GET['id_message']) AND !array_key_exists('action', $_GET))
{
	$_GET['action'] = 'reception';
}
if(!array_key_exists('action', $_GET))
{
	if (isset($_GET['id_message']))
	{
		if(array_key_exists('mode', $_GET)) $id_dest = '1'; else $id_dest = "id_dest = '".$_SESSION['ID']."'";
		$requete = "SELECT * FROM message WHERE ".$id_dest." AND id = ".sSQL($_GET['id_message']);
		if($req = $db->query($requete))
		{
			$time2 =  time()-604800;
			$row = $db->read_assoc($req);
			$date = strftime("%d/%m/%Y %H:%M", $row['date']);
			$message = htmlspecialchars(stripslashes($row['message']));
			//bbcode de merde
			$message = str_replace('[br]', '<br />', $message);
			$message = eregi_replace("\[b\]([^[]*)\[/b\]", '<strong>\\1</strong>', $message );
			$message = eregi_replace("\[i\]([^[]*)\[/i\]", '<i>\\1</i>', $message );
			$message = eregi_replace("\[url\]([^[]*)\[/url\]", '<a href="\\1">\\1</a>', $message );
			$message = str_replace("[/color]", "</span>", $message);
			//Lien vers échange
			$message = eregi_replace("\[echange:([^[]*)\]", "<a href=\"javascript:envoiInfo('echange.php?id_echange=\\1', 'information')\">Echange ID : \\1</a>", $message);
			echo '<h3><strong>'.htmlspecialchars(stripslashes($row['titre'])).'</strong> par '.$row['nom_envoi'].' le '.$date.'</h3>
			<p class="information_case">'.$message.'</p>';
			
			if($row['id_envoi'] != 0 AND $row['date'] > $time2 ) echo '<a href="javascript:envoiInfo(\'envoimessage.php?id_message='.$row['id'].'&amp;ID='.$row['id_envoi'].'\', \'information\')">Répondre</a> / ';
			
			
			if(!array_key_exists('mode', $_GET)) echo '<a href="javascript:envoiInfo(\'messagerie.php?ID='.$_GET['id_message'].'&amp;action=del\', \'information\')">Supprimer</a>';
			
			
			if($row['groupe'] != 0 AND $joueur['groupe'] == $row['groupe'] AND $row['date'] > $time2 ) echo '<br /><a href="javascript:envoiInfo(\'envoimessage.php?id_message='.$row['id'].'&amp;type=groupe&amp;id_groupe='.$row['groupe'].'\', \'information\')">Répondre au groupe</a>';
			
			
			
			if($row['type'] != 'lu') 
			{
				echo '<br /><br />';
				$requete2 = "SELECT * FROM message WHERE ".$id_dest." AND id > ".sSQL($_GET['id_message'])." LIMIT 0, 1";
				if($req2 = $db->query($requete2))
				{
					$row = $db->read_assoc($req2);
					$message_suivant = $row['id'];
				}
				$requete3 = "SELECT * FROM message WHERE ".$id_dest." AND id < ".sSQL($_GET['id_message'])." ORDER by id DESC LIMIT 0, 1";
				if($req3 = $db->query($requete3))
				{
					$row = $db->read_assoc($req3);
					$message_precedent = $row['id'];
				}
				
				if ($message_precedent != '' AND $row['type'] != 'lu')
				{
				?>
					<a href="javascript:envoiInfo('messagerie.php?ID=<?php echo $_SESSION['ID']; ?>&amp;id_message=<?php echo $message_precedent; ?>', 'information');" style="<?php echo $style; ?>">Précédent</a>
				<?php
				}
				if ($message_suivant != '')
				{
				?>	
					<a href="javascript:envoiInfo('messagerie.php?ID=<?php echo $_SESSION['ID']; ?>&amp;id_message=<?php echo $message_suivant; ?>', 'information');" style="<?php echo $style; ?>">Suivant</a>
				<?php
				}
			}
			//mis a jour message lu
			if(!array_key_exists('mode', $_GET))
			{
				$requete = "UPDATE message SET type = 'lu' WHERE id = ".sSQL($_GET['id_message']);
				$req2 = $db->query($requete);
			}

			//<img src="image/pixel.gif" onLoad="envoiInfo('menu_carte.php?javascript=oui', 'deplacement');" style="float : left;" />

		}
	}
}
else
{
	$id_mess = $_GET['ID'];
	switch($_GET['action'])
	{
		//Confirmation de suppression d'un message
		case 'del' :
			echo 'Voulez vous vraiment effacer ce message ?<br />
			<a href="javascript:envoiInfo(\'messagerie.php?ID='.$id_mess.'&amp;action=delc\', \'information\')">Oui</a> / <a href="javascript:envoiInfo(\'messagerie.php\', \'information\')">Non</a>';
		break;
		//Suppression d'un message
		case 'delc' :
			$ids = explode('|', $_GET['ID']);
			foreach($ids as $id)
			{
				$requete = "DELETE FROM message WHERE ID = ".$id;
				$db->query($requete);
			}
		break;
	}
	//Affichage de la liste des messages envoyés
	if($_GET['action'] == 'envoi')
	{
		$champ = 'id_envoi';
		$champ2 = 'nom_dest';
	}
	else
	{
		$champ = 'id_dest';
		$champ2 = 'nom_envoi';
	}

	$requete = "SELECT * FROM message WHERE ".$champ." = ".$_SESSION['ID']." ORDER BY date DESC";
	//echo $requete;
	//Affichage des messages
	?>
	<table width="95%" class="information_case">
	<tr>
		<td>
		</td>
		<td>
			Titre
		</td>
		<td>
			<?php
			if($champ == 'id_dest')
			{
			?>
			Par
			<?php
			}
			else
			{
			?>
			Pour
			<?php
			}
			?>
		</td>
		<td>
			Date
		</td>
	</tr>
	<?php
	if($req = $db->query($requete))
	{
		$i = 0;
		while($row = $db->read_array($req))
		{
			$date = strftime("%d/%m/%Y %H:%M", $row['date']);
			$style = '';
			if($champ == 'id_envoi') $mode = '&amp;mode=envoi'; else $mode = '';
			if($row['type'] == '') $style = 'font-weight : bold;';
			?>
	<tr>
		<td>
			<?php
			if($champ == 'id_dest')
			{
			?>
			<input type="checkbox" id="mess<?php echo $i; ?>" value="<?php echo $row['id']; ?>" />
			<?php
			}
			?>
		</td>
		<td>
			<?php
			//Si le titre est trop long je le coupe pour que ça casse pas ma mise en page qui déchire ta soeur en deux
			$titre = htmlspecialchars(stripslashes($row['titre']));
			if(strlen($titre)>=20) 
			{
				$titre=substr($titre,0,20) . "...";
			}
			?>
			<a href="javascript:envoiInfo('messagerie.php?ID=<?php echo $_SESSION['ID']; ?>&amp;id_message=<?php echo $row['id'].$mode; ?>', 'information');" style="<?php echo $style; ?>">

			<?php echo $titre; ?></a>
		</td>
		<td>
			<?php
			if($champ == 'id_envoi' AND $row['groupe'] != 0) echo 'Groupe - '.$row[$champ2];
			else echo $row[$champ2];
			?>
		</td>
		<td style="font-size : 0.9em;">
			<?php echo $date; ?>
		</td>
	</tr>
			<?php
			$i++;
		}
	}
	?>
	</table>
	<?php
}

?>
