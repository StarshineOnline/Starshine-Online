<?php

include('fonction\base.inc.php');

$joueur = new perso($_SESION['ID']);

$vie = $joueur->get_vie();
$energie = $joueur->get_energie();
$dexterite = $joueur['dexterite'];
$force = $joueur['force'];
$volonte = $joueur['volonte'];
$puissance = $joueur['puissance'];

echo '
	<table>
	<tr>
		<td>Nom</td>
		<td>'.$joueur->get_nom().'</td>
	</tr>
	<tr>
		<td>Race</td>
		<td>'.$joueur->get_race().'</td>
	</tr>
	<tr>
		<td>Classe</td>
		<td>'.$joueur->get_classe().'</td>
	</tr>
	<tr>
		<td>Expérience</td>
		<td>'.$joueur['exp'].'</td>
	</tr>
	<tr>
		<td>Niveau</td>
		<td>'.$joueur['level'].'</td>
	</tr>
	<tr>
		<td>Vie</td>
		<td>'.$joueur['vie'].'</td>
		<td>'.$vie.'</td>
	</tr>
	<tr>
		<td>Force</td>
		<td>'.$carac['force'].'</td>
		<td>'.$force.'</td>
	</tr>
	<tr>
		<td>Dextérité</td>
		<td>'.$carac['dexterite'].'</td>
		<td>'.$dexterite.'</td>
	</tr>
	<tr>
		<td>Puissance</td>
		<td>'.$carac['puissance'].'</td>
		<td>'.$puissance.'</td>
	</tr>
	<tr>
		<td>Volonté</td>
		<td>'.$carac['volonte'].'</td>
		<td>'.$volonte.'</td>
	</tr>
	<tr>
		<td>Energie</td>
		<td>'.$carac['energie'].'</td>
		<td>'.$energie.'</td>
	</tr>
	<tr>
		<td>Points de sort</td>
		<td>'.$psort.'</td>
	</tr>
	<tr>
		<td>Points de caractéristique</td>
		<td>'.$pcarac.'</td>
	</tr>
	<tr>
		<td>Or</td>
		<td>'.$or.'</td>
	</tr>
	</table>
	<br />
	';
	
?>
