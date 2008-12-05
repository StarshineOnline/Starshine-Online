<?php

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include('haut.php');

$joueur = recupperso($_SESSION['ID']);

//Si c'est pour entrer dans un donjon
if(array_key_exists('donjon_id', $_GET))
{
	$id = $_GET['donjon_id'];
	
	$requete = "SELECT x, y, x_donjon, y_donjon FROM donjon WHERE id = ".$id;
	$req = $db->query($requete);
	
	$row = $db->read_assoc($req);
	//sortie
	if(array_key_exists('type', $_GET))
	{
		if($joueur['x'] == $row['x_donjon'] AND $joueur['y'] == $row['y_donjon'])
		{
			$requete = "UPDATE perso SET x = ".$row['x'].", y = ".$row['y']." WHERE ID = ".$_SESSION['ID'];
			$db->query($requete);
		}
	}
	//Entrée
	else
	{
		if($joueur['x'] == $row['x'] AND $joueur['y'] == $row['y'])
		{
			$requete = "UPDATE perso SET x = ".$row['x_donjon'].", y = ".$row['y_donjon']." WHERE ID = ".$_SESSION['ID'];
			$db->query($requete);
		}
	}
}

//Vérifie si le perso est mort
verif_mort($joueur, 1);

check_perso($joueur);

$_SESSION['position'] = convert_in_pos($joueur['x'], $joueur['y']);
?>

<div id="conteneur">
	<div id="loading" style="display : none;"> </div>
	<div id="perso">
		<?php
		require_once('infoperso.php');
		?>
	</div>
	<div id="centre">
	<div id="loading" style='display:none'></div>

<?php
//Génération de la carte apparaissant au centre.
//Si coordonées supérieur à 100 alors c'est un donjon
if(is_donjon($joueur['x'], $joueur['y']))
{
	include('donjon.php');
}
else include('map2.php');

?>




</div>
<?php include('menu_carte.php');?>
	<div id="information">

			<h2>Information</h2>
		
<?php

$case = convert_in_pos($joueur['x'], $joueur['y']);
if(array_key_exists('page_info', $_GET)) $page_info = $_GET['page_info']; else $page_info = 'informationcase.php';
?>
<img src="image/pixel.gif" onLoad="envoiInfo('<?php echo $page_info; ?>?case=<?php echo $case; ?>', 'information');" />

	</div>
</div>
<?php
//Inclusion du bas de la page
include('bas.php');
?>