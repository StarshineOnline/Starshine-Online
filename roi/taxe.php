<?php
require('haut_roi.php');

$duree = (60 * 60 * 24) * 7;
if($joueur->get_rang_royaume() != 6)
	echo '<p>Cheater</p>';
else if((time() - $duree) < $R['taxe_time'])
{
	echo 'Vous avez déjà modifié le taux de taxe récemment.<br />
	Vous pourrez le modifier dans '.transform_sec_temp(($R['taxe_time'] + $duree) - time());
}
else
{
	if($_GET['action'] == 'valid')
	{
		$requete = "UPDATE royaume SET taxe = ".sSQL($_GET['taux']).", taxe_time = ".time()." WHERE id = ".$R['ID'];
		if($db->query($requete))
		{
			echo 'Taux de taxe modifié !';
		}
	}
	else
	{
	?>
	<form action="gestion_royaume.php">
		Modifier le taux de taxe pour : <select name="taux" id="taux">
		<?php
			$debut = $R['taxe_base'] - 3;
			$fin = $R['taxe_base'] + 3;
			for($i = $debut; $i < $fin; $i++)
			{
				echo '
				<option value="'.$i.'">'.$i.' %</option>';
			}
		?>
		</select>
		<input type="button" onclick="envoiInfo('taxe.php?direction=taxe&amp;action=valid&amp;taux=' + $('taux').value, 'conteneur')" value="Ok" />
	</form>
	<?php
	}
}

?>
