<?php

//Inclusion du haut du document html
include('haut_ajax.php');

$joueur = recupperso($_SESSION['ID']);

check_perso($joueur);

$position = convert_in_pos($joueur['x'], $joueur['y']);

//Vérifie si le perso est mort
verif_mort($joueur, 1);
$verif_ville = verif_ville($joueur['x'], $joueur['y']);
$W_requete = 'SELECT * FROM map WHERE ID =\''.sSQL($position).'\'';
$W_req = $db->query($W_requete);
$W_row = $db->read_array($W_req);
$R = get_royaume_info($joueur['race'], $W_row['royaume']);
$_SESSION['position'] = $position;
?>
	<div id="carte">
	<?php	
	if($verif_ville AND $R['diplo'] == 127)
	{
		echo 'toto';
	}
	?>
	</div>