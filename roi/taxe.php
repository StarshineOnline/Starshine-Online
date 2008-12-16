<?php
require('haut_roi.php');

$duree = (60 * 60 * 24) * 7;
if((time() - $duree) < $R['taxe_time'])
{
	echo 'Vous avez déjà modifié le taux de taxe récemment.<br />
	Vous pourrais le modifier dans '.transform_sec_temp(($R['taxe_time'] + $duree) - time());
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
		<input type="button" onclick="envoiInfo('gestion_royaume.php?poscase=<?php echo $W_case; ?>&amp;direction=taxe&amp;action=valid&amp;taux=' + document.getElementById('taux').value, 'carte')" value="Ok" />
	</form>
	<?php
	}
}

?>