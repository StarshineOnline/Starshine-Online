<?php
if (file_exists('root.php'))
  include_once('root.php');


include_once(root.'inc/fp.php');
$joueur = new perso($_SESSION['ID']);

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
$groupe = new groupe($_GET['id']);
$num_joueur = $groupe->trouve_position_joueur($joueur->get_id());
if($num_joueur !== false)
{
?>
<fieldset>
	<legend>Informations sur votre groupe</legend>
	<ul>
		<li><a href="groupe_info.php?javascript" onclick="return envoiInfo(this.href, 'div_groupe');">Info groupe</a></li>
		<li><a href="groupe_bataille.php" onclick="return envoiInfo(this.href, 'div_groupe');">Batailles</a></li>
	</ul>
	<div id="div_groupe">
		<?php include_once(root.'groupe_info.php'); ?>
	<div>

<?php
}
else
{
?>
<fieldset>
	<legend class=".message_rouge">Vous n'appartenez pas à ce groupe!<?php echo $num_joueur;?></legend>
<?php
}
if($groupe->get_id_leader() ==  $joueur->get_id())
{
	$invitations = invitation::create('groupe', $groupe->get_id());
	echo '<h3>Invitations envoyées</h3>';
	echo '<ul>';
	foreach($invitations as $invit)
	{
		$perso = new perso($invit->get_receveur());
		echo '<li>'.$perso->get_nom().' - '.$Gtrad[$perso->get_race()].' '.$perso->get_classe().' - Niv.'.$perso->get_level().' <a href="infogroupe.php?id='.$groupe->get_id().'&amp;suppinvit='.$invit->get_id().'" onclick="return envoiInfo(this.href, \'information\');">X</a></li>';
	}
	?>
	</ul>
	<?php

}
?>
</fieldset>
