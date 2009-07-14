<?php
include('inc/fp.php');

$joueur = new perso($_SESSION['ID']);
?>
<fieldset>
<legend>Liste de vos échanges</legend>
<?php
if(array_key_exists('annule', $_GET))
{
	//On passe l'échange en mode annulé
	$requete = "UPDATE echange SET statut = 'annule' WHERE id_echange = ".sSQL($_GET['id_echange']);
	if($db->query($requete))
	{
		?>
		<h5>L'échange a été supprimé.</h5>
		<?php
	}
}
$echanges = recup_tout_echange_perso_ranger($joueur->get_id());
$i = 0;
$count = count($echanges['creation']);
echo '<h3>Création</h3><ul class="information_case">';
if ($count == 0) 
{
	echo '<p>Aucune création d\'échange pour le moment</p>';
}
else
{
	while($i < $count)
	{
		if($joueur->get_id() == $echanges['creation'][$i]['id_j1']) $j = recupperso_essentiel($echanges['creation'][$i]['id_j2']);
		else $j = recupperso_essentiel($echanges['creation'][$i]['id_j1']);
		if ($echanges['creation'][$i]['statut']=='creation')
		{
			?>
			<li><a href="echange.php?id_echange=<?php echo $echanges['creation'][$i]['id_echange']; ?>" onclick="return envoiInfo(this.href, 'information');">N°<?php echo $echanges['creation'][$i]['id_echange']; ?> : <?php echo $j['nom']; ?></a></li>
			<?php	
		}
		$i++;
	}
}
echo '</ul><h3>Proposition</h3><ul class="information_case">';
$echanges = recup_tout_echange_perso_ranger($joueur->get_id());
$i = 0;
$count = count($echanges['proposition']);
if ($count == 0) 
{
	echo '<p>Aucune proposition d\'échange pour le moment</p>';
}
else
{
	while($i < $count)
	{
		if($joueur->get_id() == $echanges['proposition'][$i]['id_j1']) $j = recupperso_essentiel($echanges['proposition'][$i]['id_j2']);
		else $j = recupperso_essentiel($echanges['proposition'][$i]['id_j1']);
		
		if ($echanges['proposition'][$i]['statut']=='proposition')
		{
			?>
			<li><a href="echange.php?id_echange=<?php echo $echanges['proposition'][$i]['id_echange']; ?>" onclick="return envoiInfo(this.href, 'information');">N°<?php echo $echanges['proposition'][$i]['id_echange']; ?> : <?php echo $j['nom']; ?></a></li>
			<?php
		}
		$i++;
	}
}
echo '</ul><h3>Finalisation</h3><ul class="information_case">';
$echanges = recup_tout_echange_perso_ranger($joueur->get_id());
$i = 0;
$count = count($echanges['finalisation']);
if ($count == 0) 
{
	echo '<p>Aucune finalisation d\'échange pour le moment</p>';
}
else
{
	while($i < $count)
	{
		if($joueur->get_id() == $echanges['finalisation'][$i]['id_j1']) $j = recupperso_essentiel($echanges['finalisation'][$i]['id_j2']);
		else $j = recupperso_essentiel($echanges['finalisation'][$i]['id_j1']);
	
		if ($echanges['finalisation'][$i]['statut']=='finalisation')
			{
				?>
		
				<li><a href="echange.php?id_echange=<?php echo $echanges['finalisation'][$i]['id_echange']; ?>" onclick="return envoiInfo(this.href, 'information');">N°<?php echo $echanges['finalisation'][$i]['id_echange']; ?> : <?php echo $j['nom']; ?></a></li>
		
				<?php
			}
		$i++;
	}
}
echo '</ul>';

?>
</fieldset>
