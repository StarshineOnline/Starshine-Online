<?php
include('inc/fp.php');

$joueur = recupperso($_SESSION['ID']);
?>
<h2>Liste de vos �changes</h2>
<?php
if(array_key_exists('annule', $_GET))
{
	//On passe l'�change en mode annul�
	$requete = "UPDATE echange SET statut = 'annule' WHERE id_echange = ".sSQL($_GET['id_echange']);
	if($db->query($requete))
	{
		?>
		<h5>L'�change a �t� supprim�.</h5>
		<?php
	}
}
$echanges = recup_tout_echange_perso_ranger($joueur['ID']);
$i = 0;
$count = count($echanges['creation']);
echo '<h3>Cr�ation</h3><ul class="information_case">';
if ($count == 0) 
{
	echo '<p>Aucune cr�ation d\'�change pour le moment</p>';
}
else
{
	while($i < $count)
	{
		if($joueur['ID'] == $echanges['creation'][$i]['id_j1']) $j = recupperso_essentiel($echanges['creation'][$i]['id_j2']);
		else $j = recupperso_essentiel($echanges['creation'][$i]['id_j1']);
		if ($echanges['creation'][$i]['statut']=='creation')
		{
			?>
			<li><a href="javascript:onclick=envoiInfo('echange.php?id_echange=<?php echo $echanges['creation'][$i]['id_echange']; ?>', 'information');">N�<?php echo $echanges['creation'][$i]['id_echange']; ?> : <?php echo $j['nom']; ?></a></li>
			<?php	
		}
		$i++;
	}
}
echo '</ul><h3>Proposition</h3><ul class="information_case">';
$echanges = recup_tout_echange_perso_ranger($joueur['ID']);
$i = 0;
$count = count($echanges['proposition']);
if ($count == 0) 
{
	echo '<p>Aucune proposition d\'�change pour le moment</p>';
}
else
{
	while($i < $count)
	{
		if($joueur['ID'] == $echanges['proposition'][$i]['id_j1']) $j = recupperso_essentiel($echanges['proposition'][$i]['id_j2']);
		else $j = recupperso_essentiel($echanges['proposition'][$i]['id_j1']);
		
		if ($echanges['proposition'][$i]['statut']=='proposition')
		{
			?>
			<li><a href="javascript:onclick=envoiInfo('echange.php?id_echange=<?php echo $echanges['proposition'][$i]['id_echange']; ?>', 'information');">N�<?php echo $echanges['proposition'][$i]['id_echange']; ?> : <?php echo $j['nom']; ?></a></li>
			<?php
		}
		$i++;
	}
}
echo '</ul><h3>Finalisation</h3><ul class="information_case">';
$echanges = recup_tout_echange_perso_ranger($joueur['ID']);
$i = 0;
$count = count($echanges['finalisation']);
if ($count == 0) 
{
	echo '<p>Aucune finalisation d\'�change pour le moment</p>';
}
else
{
	while($i < $count)
	{
		if($joueur['ID'] == $echanges['finalisation'][$i]['id_j1']) $j = recupperso_essentiel($echanges['finalisation'][$i]['id_j2']);
		else $j = recupperso_essentiel($echanges['finalisation'][$i]['id_j1']);
	
		if ($echanges['finalisation'][$i]['statut']=='finalisation')
			{
				?>
		
				<li><a href="javascript:onclick=envoiInfo('echange.php?id_echange=<?php echo $echanges['finalisation'][$i]['id_echange']; ?>', 'information');">N�<?php echo $echanges['finalisation'][$i]['id_echange']; ?> : <?php echo $j['nom']; ?></a></li>
		
				<?php
			}
		$i++;
	}
}
echo '</ul>';

?>