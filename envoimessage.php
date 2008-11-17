<?php
include('inc/fp.php');

$W_ID = $_GET['ID'];
$id_mess = $_GET['id_message'];
$joueur = recupperso($_SESSION['ID']);
if($joueur['groupe'] != 0) $groupe_joueur = recupgroupe($joueur['groupe'], ''); else $groupe_joueur = false;

normalize_entry_charset(array('titre', 'message'));

//Envoi du message
if(isset($_GET['titre']))
{
	$ids = array();
	//Si c'est un message à tout un groupe
	if(array_key_exists('type', $_GET) AND $groupe_joueur)
	{
		foreach($groupe_joueur['membre'] as $membre)
		{
			if($membre['id_joueur'] != $joueur['ID']) $ids[] = $membre['id_joueur'];
		}
		$groupe_id = $joueur['groupe'];
	}
	else
	{
		$ids[] = $_GET['ID'];
		$groupe_id = 0;
	}
	foreach($ids as $W_ID)
	{
		$titre = addslashes($_GET['titre']);
		if (empty($titre)){$titre = 'Sans titre';}
		if($titre != '')
		{
			$message = addslashes($_GET['message']);
			if ($message != '')
			{
				$recep = recupperso($W_ID);
				$date = time();
				$requete = "INSERT INTO message VALUES('','".$W_ID."','".$_SESSION['ID']."','".$joueur['nom']."','".$recep['nom']."','".$titre."','".$message."','','".$date."', ".$groupe_id.")";
				if($req = $db->query($requete)) 
				{
					echo '<h6>Message bien envoyé à '.$recep['nom'].' !</h6>';
				}
				else echo('<h5>Erreur lors de l\'envoi du message</h5>');
			}
			else
			{
				echo '<h5>Vous n\'avez pas saisi de message</h5>';
			}
		}
		else
		{
			echo '<h5>Vous n\'avez pas saisi de titre</h5>';
		}
	}
	echo '<a href="'.$_SERVER["HTTP_REFERER"].'">Retour au jeu</a><br />';
}
else
{
	$type = '';
	if(array_key_exists('type', $_GET))
	{
		$W_ID = $_GET['id_groupe'];
		$type = '&amp;type=groupe';
		$mess = 'au groupe';
	}
	else
	{
		$perso = recupperso($W_ID);
		$mess = 'à '.$perso['nom'];
	}

?>
<h2>Envoi d'un message <?php echo $mess; ?></h2>
<a href="javascript:envoiInfo('messagerie.php', 'information');">Réception</a> - <a href="javascript:envoiInfo('messagerie.php?action=envoi', 'information');">Envoi</a><br />
<div class="information_case">
<form method="post" action="javascript:message = document.getElementById('message').value.replace(new RegExp('\n', 'gi'), '[br]'); envoiInfoPost('envoimessage.php?ID=<?php echo $W_ID.$type; ?>&amp;titre=' + document.getElementById('titre').value + '&amp;message=' + message, 'information');">
	<?php
	if ($id_mess != '')
	{
		$requete = "SELECT * FROM message WHERE id = ".$id_mess."";
		if($req = $db->query($requete))
		{
			$row = $db->read_assoc($req);	
			$titre_message = stripslashes($row['titre']);
			$re_mess = mb_substr($titre_message, 0, 4);
			if ($re_mess != 'Re :')
			{
				$titre_message = 'Re : '.$titre_message;	
			}
		?>
	Titre du message :<br />
	<input type="text" name="titre" id="titre" size="30" value="<?php echo $titre_message;?>"/><br />
	<?// print_messbar() ?>
		<?php
		}
	}
	else
	{
	?>
	Titre du message :<br />	
	<input type="text" name="titre" id="titre" size="30" value=""/><br />
	<?// print_messbar() ?>
	Message :<br />
	<?php 
	}
	?>
	<textarea name="message" id="message" cols="45" rows="12"></textarea><br />
	<br />
	<input type="submit" name="btnSubmit" value="Envoyer" />
</form>
</div>
<?php

}
?>
