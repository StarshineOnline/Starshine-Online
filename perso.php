<?php

include('fonction\base.inc.php');

$joueur = recupperso($_SESION['ID']);

$vie = $joueur['vie'];
$energie = $joueur['energie'];
$dexterite = $joueur['dexterite'];
$force = $joueur['force'];
$volonte = $joueur['volonte'];
$puissance = $joueur['puissance'];

echo '
	<table>
	<tr>
		<td>Nom</td>
		<td>'.$joueur['nom'].'</td>
	</tr>
	<tr>
		<td>Race</td>
		<td>'.$joueur['race'].'</td>
	</tr>
	<tr>
		<td>Classe</td>
		<td>'.$joueur['classe'].'</td>
	</tr>
	<tr>
		<td>Exp�rience</td>
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
		<td>Dext�rit�</td>
		<td>'.$carac['dexterite'].'</td>
		<td>'.$dexterite.'</td>
	</tr>
	<tr>
		<td>Puissance</td>
		<td>'.$carac['puissance'].'</td>
		<td>'.$puissance.'</td>
	</tr>
	<tr>
		<td>Volont�</td>
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
		<td>Points de caract�ristique</td>
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