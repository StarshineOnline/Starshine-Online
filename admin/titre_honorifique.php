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
	$pseudo = array();
	$x = 0;
	$verif = false;
	while ($x <5 && $_GET['id'.$x] != '')
	{
		$id = $_GET['id'.$x];
		$req_id = "SELECT id FROM perso WHERE nom = '".$id."'" ;
		$req = $db->query($req_id);
		$row = $db->read_row($req);	
		if ($row)
		{
			$pseudo[] = $row[0];
			echo "Attribution effectuée";
			$verif = true;
		}
		else
		{
			$verif = false;
			echo 'Le pseudo '.$id.' est erroné <br/>';
		}
		$x++;
	}
	echo '<br/>';echo '<br/>';echo '<br/>';
	
	$liste_id = implode('-', $pseudo);
	if ($verif)
	{
		$requete = "INSERT INTO titre_honorifique (id_perso, titre) VALUE ('".$liste_id."', '".$_GET['titre']."')";
		$db->query($requete);
	}
	
}

