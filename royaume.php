<?php
include('haut.php');
include('haut_site.php');
include('menu.php');
?>
<div id="contenu">
	<div class="centre2">
		<div class="titre">
			Carte du monde 3D
		</div>
		<img src="image/carte3d.png" /><br />
		<a href="image/cartebeta.jpg">Carte 2D</a>
	<div class="titre">
		Carte des royaumes
	</div>
		<table style="border : 0px; background-color : #E4EAF2;">
		<tr>
			<td>
				<img src="image/carte_royaume.png" />
			</td>
			<td style="vertical-align : top;">
		<?php
		$requete = "SELECT * FROM royaume";
		$req = $db->query($requete);
		while($row = $db->read_assoc($req))
		{
			if($row['nom'] == 'Neutre') $Trace[$row['race']]['couleur'] = "#aaaaaa";
			echo $row['nom'].' - '.$Gtrad[$row['race']].'<div style="width : 15px; height : 15px; border : 1px solid black; background-color : '.$Trace[$row['race']]['couleur'].';"></div>';
		}
		?>
			</td>
		</tr>
		</table>
	<div class="titre">
		Densité des monstres
	</div>
		<table style="border : 0px; background-color : #E4EAF2;">
		<tr>
			<td>
				<img src="image/carte_densite_mob.png" />
			</td>
			<td style="vertical-align : top;">
				Vide <div style="width : 15px; height : 15px; border : 1px solid black; background-color : #ffffff"></div>
				Très faible <div style="width : 15px; height : 15px; border : 1px solid black; background-color : #66ff33"></div>
				Faible <div style="width : 15px; height : 15px; border : 1px solid black; background-color : #e6fa37"></div>
				Normal <div style="width : 15px; height : 15px; border : 1px solid black; background-color : #e6ba04"></div>
				Forte <div style="width : 15px; height : 15px; border : 1px solid black; background-color : #e67604"></div>
				Très forte <div style="width : 15px; height : 15px; border : 1px solid black; background-color : #e63b07"></div>
				Maximum <div style="width : 15px; height : 15px; border : 1px solid black; background-color : #ff0000"></div>
			</td>
		</tr>
		</table>
	</div>
</div>