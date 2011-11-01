<?php
if (file_exists('../root.php'))
  include_once('../root.php');
$admin = true;

include_once(root.'inc/fp.php');

$textures = false;
include_once(root.'admin/admin_haut.php');
setlocale(LC_ALL, 'fr_FR');
include_once(root.'haut_site.php');
include_once(root.'admin/menu_admin.php');
if(!array_key_exists('titre', $_GET))
{
	$x=0;
?>
	<form method="get" action="titre_honorifique.php">
		Titre à attribuer : <input type="text" name="titre" /><br />
		Perso : <input type="text" name="id<?php echo $x;?>" onkeydown = "affiche_test(<?php echo $x;?>)" /><br />
		<div id="test0"></div>
		<?php 
		$x=1;
		while ($x < 4)
		{
			echo '<div id="test'.$x.'"></div>';
			$x++;
			}		
		 ?>
		<input type="submit" value="Valider" />
	</form>
<?php
}
else
{
	$x = 0;
	$verif = true;
	$ids = array();
	while ($x <5 && $_GET['id'.$x] != '')
	{
		$req_id = "SELECT * FROM perso WHERE nom = '".$_GET['id'.$x]."'" ;
		$req = $db->query($req_id);
		$row = $db->read_array($req);
		if (!$row)
		{
			echo 'Le pseudo '.$_GET['id'.$x].' est erroné <br/>';
			$verif = false;
		}
		else
		{
			$ids[] = $row['ID'];
		}
		$x++;
	}

	
	
	if ($verif)
	{
		foreach ($ids as $id)
		{
			$requete = "INSERT INTO titre_honorifique (id, id_perso, titre) VALUE ('', ".$id.", '".$_GET['titre']."')";
			$db->query($requete);
			Echo "attribution effectuée </br>";
		}
	}
	
}

