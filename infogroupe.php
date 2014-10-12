<?php
/**
* @file infogroupe.php
* Informations du groupe et accès au bataille pour le groupe
*/
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');

$interf_princ = $G_interf->creer_jeu();
//Vérifie si le perso est mort
$perso = joueur::get_perso();


// Cadre de la partie droite
$cadre = $interf_princ->set_droite( $G_interf->creer_droite('Groupe') );

$onglets = $cadre->add( new interf_onglets('onglets_groupe', 'groupe') );

$action = array_key_exists('action', $_GET) ? $_GET['action'] : 'infos';

$onglets->add_onglet('Infos groupe', 'infogroupe.php?action=infos&ajax=2', 'ongl_infos', false, $action=='infos');
//$onglets->add_onglet('Batailles', 'infogroupe.php?action=batailles&ajax=2', 'ongl_batailles', $action=='batailles');

$groupe = new groupe( $perso->get_groupe() );
switch($action)
{
case 'infos':
	$onglets->get_onglet('ongl_infos')->add( $G_interf->creer_groupe('infos_groupe', $groupe) );
}


exit;


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
	<ul id="messagerie_onglet">
		<li><a href="groupe_info.php?javascript" onclick="return envoiInfo(this.href, 'div_groupe');">Info groupe</a></li>
		<li><a href="groupe_bataille.php" onclick="return envoiInfo(this.href, 'div_groupe');">Batailles</a></li>
	</ul>
	<div class="spacer"></div>
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
