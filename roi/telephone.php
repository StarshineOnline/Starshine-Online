<?php
require('haut_roi.php');
if($joueur['rang_royaume'] != 6)
	echo '<p>Cheater</p>';
else if(array_key_exists('message', $_POST))
{
	$erreur = false;
	$titre = addslashes($_POST['titre']);
	$message = addslashes($_POST['message']);
	if (empty($titre)){$titre = 'Sans titre';}
	if($titre != '')
	{
		if ($message != '')
		{
			$id_groupe = 0;
			$id_dest = 0;
			$id_thread = 0;
			$id_dest = $_POST['id_dest'];
			$messagerie = new messagerie($joueur['ID']);
			$messagerie->envoi_message($id_thread, $id_dest, $titre, $message, $id_groupe);
			echo '<h6>Message transmis avec succés</h6>';
		}
		else
		{
			echo '<h5>Vous n\'avez pas saisi de message</h5>';
			$erreur = true;
		}
	}
	else
	{
		echo '<h5>Vous n\'avez pas saisi de titre</h5>';
		$erreur = true;
	}
}
elseif(array_key_exists('id_dest', $_GET))
{
?>
<fieldset>
	<legend>Envoi d'un message</legend>
	<div class="information_case">
	<form method="post" action="telephone.php?id_type=<?php echo $id_type; ?>" id="formMessage">
		Titre du message :<br />
		<input type="text" name="titre" id="titre" size="30" value=""/><br />
		Message :<br />
		<textarea name="message" id="message" cols="45" rows="12"></textarea><br />
		<br />
		<input type="hidden" name="id_dest" id="id_dest" value="<?php echo $_GET['id_dest']; ?>" />
		<input type="button" onclick="envoiFormulaire('formMessage', 'conteneur');" name="btnSubmit" value="Envoyer" />
	</form>
</fieldset>
<?php
}
else
{
?>
<table class="ville">
<?php
$requete = "SELECT * FROM perso WHERE rang_royaume = 6 AND ID <> ".$joueur['ID'];
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	echo '
	<tr>
		<td>
			<a href="telephone.php?id_dest='.$row['ID'].'" onclick="return envoiInfo(this.href, \'conteneur\')">'.$row['nom'].'</a>
		</td>
		<td>
			 - Roi des '.$Gtrad[$row['race']].'
		</td
	</td>';
}
?>
</table>
<?php
}
?>