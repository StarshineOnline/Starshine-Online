<?php

include('inc/fp.php');
$joueur = recupperso($_SESSION['ID']);

$partages = array(array('r', 'Aléatoire'), array('t', 'Par tour'), array('l', 'Leader'), array('k', 'Trouve = Garde'));
if(array_key_exists('partage', $_GET))
{
	$requete = "UPDATE groupe SET partage = '".sSQL($_GET['partage'])."' WHERE id = ".sSQL($_GET['id']);
	$db->query($requete);
	$requete = "UPDATE groupe SET nom = '".sSQL($_GET['nom'])."' WHERE id = ".sSQL($_GET['id']);
	$db->query($requete);
	$requete = "UPDATE groupe_joueur SET leader = 'n' WHERE id_groupe = ".sSQL($_GET['id']);
	$db->query($requete);
	$requete = "UPDATE groupe_joueur SET leader = 'y' WHERE id_joueur = ".sSQL($_GET['leader']);
	$db->query($requete);
	?>
	<?php
}
if(array_key_exists('suppinvit', $_GET))
{
	$requete = "DELETE FROM invitation WHERE ID = ".sSQL($_GET['suppinvit']);
	$db->query($requete);
}
$groupe = recupgroupe($_GET['id'], $joueur['x'].'-'.$joueur['y']);
$level_groupe = level_groupe($groupe);
$num_joueur = groupe_trouve_joueur($joueur['ID'], $groupe);
$share_xp = ($groupe['membre'][$num_joueur]['share_xp'] / $groupe['share_xp']);
if($num_joueur)
{
?>
<fieldset>
	<legend>Informations sur votre groupe</legend>
	<ul>
		<li><a href="groupe_info.php?javascript" onclick="return envoiInfo(this.href, 'div_groupe');">Info groupe</a></li>
		<li><a href="groupe_bataille.php" onclick="return envoiInfo(this.href, 'div_groupe');">Batailles</a></li>
	</ul>
	<div id="div_groupe">
		<?php include('groupe_info.php'); ?>
	<div>

<?php
}
else
{
?>
<fieldset>
	<legend class=".message_rouge">Vous n'appartenez pas à ce groupe!</legend>
<?php
}
if($groupe['id_leader'] ==  $_SESSION['ID'])
{
	$requete = "SELECT * FROM invitation WHERE groupe = ".$groupe['id'];
	$req = $db->query($requete);

	echo '<h3>Invitations envoyées</h3>';
	echo '<ul>';
	while($row = $db->read_assoc($req))
	{
		$perso = recupperso($row['receveur']);
		echo '<li>'.$perso['nom'].' - '.$Gtrad[$perso['race']].' '.$perso['classe'].' - Niv.'.$perso['level'].' <a href="infogroupe.php?id='.$groupe['id'].'&amp;suppinvit='.$row['ID'].'" onclick="return envoiInfo(this.href, \'information\');">X</a></li>';
	}
	?>
	</ul>
	<?php

}
?>
</fieldset>