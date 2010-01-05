<?php
if (file_exists('../root.php'))
  include_once('../root.php');

$textures = false;
$admin = true;

include_once(root.'haut.php');
setlocale(LC_ALL, 'fr_FR');
include_once(root.'haut_site.php');
if ($G_maintenance)
{
	echo 'Starshine-online est actuellement en cours de mis à jour.<br />
	le forum est toujours disponible <a href="punbb/">ici - Forum</a>';
  exit(0);
}
include_once(root.'admin/menu_admin.php');

/*
 CREATE TABLE `arenes_joueurs` (
`x` INT NOT NULL ,
`y` INT NOT NULL ,
`id` INT NOT NULL ,
PRIMARY KEY ( `x` , `y` , `id` )
) ENGINE = MYISAM COMMENT = 'Position des joueurs avant TP arène' 
 */

$joueur = new perso($_SESSION['ID']);

if (isset($_REQUEST['teleport_in'])) {
  $arene = $_REQUEST['teleport_in'];
  $player = $_REQUEST['player'];

  $requete_perso = "select x, y, ID, nom from perso where nom = '".sSQL($player)."'";
  $req = $db->query($requete_perso);
  if ($db->num_rows > 0)
    $R_perso = $db->read_assoc($req);
  else
    die('perso inconnu');
  $x = $R_perso['x'];
  $y = $R_perso['y'];
  $id = $R_perso['ID'];
  $requete_arene = "select * from arenes where nom = '$arene'";
  $req = $db->query($requete_arene);
  if ($db->num_rows > 0)
    $R_arene = $db->read_assoc($req);
  else
    die('arene inconnue');
  $nx = $R_arene['xmin'] + round(($R_arene['xmax'] - $R_arene['xmin']) / 2);
  $ny = $R_arene['ymin'] + round(($R_arene['ymax'] - $R_arene['ymin']) / 2);
  $requete_arenes_perso = "insert into arenes_joueurs value($x, $y, $id)";
  $req = $db->query($requete_arenes_perso);
  $requete_perso = "update perso set x=$nx, y=$ny where id = $id";
  $req = $db->query($requete_perso);
  $requete_journal = "INSERT INTO journal VALUES('', $id, 'teleport', '".$joueur->get_nom()."', '".$R_perso['nom']."', NOW(), '$arene', 0, ".$joueur->get_x().", ".$joueur->get_y().")";
  $req = $db->query($requete_journal);
}

if (isset($_REQUEST['remove']))
{
  $requete_arenes_perso = "select * from arenes_joueurs where id = '".sSQL($_REQUEST['remove'])."'";
  $req = $db->query($requete_arenes_perso);
  if ($db->num_rows > 0)
    $R_arene = $db->read_assoc($req);
  else
    die('perso pas dans l\'arène');
  $nx = $R_arene['x'];
  $ny = $R_arene['y'];
  $id = $R_arene['id'];
  $requete_perso = "update perso set x=$nx, y=$ny where id = $id";
  $req = $db->query($requete_perso);
  $requete_arenes_perso = "delete from arenes_joueurs where id = '$id'";
  $db->query($requete_arenes_perso);
  $requete_journal = "INSERT INTO journal VALUES('', $id, 'teleport', '".$joueur->get_nom()."', '".$R_perso['nom']."', NOW(), 'jeu', 0, ".$joueur->get_x().", ".$joueur->get_y().")";
  $req = $db->query($requete_journal);
}

?>

<form action="arenes.php" method="get">
<select name="teleport_in">
<?php
$requete_arene = "select * from arenes where open = 1";
$req = $db->query($requete_arene);
if ($db->num_rows > 0) {
  while ($R_arene = $db->read_assoc($req)) {
    echo '<option value="'.$R_arene['nom'].'">'.$R_arene['nom']."</option>\n";
  }
}
?>
</select>
<input name="player" type="text" />
<input type="submit" />
</form>
<p>Retirer un joueur d&rsquo;une ar&egrave;ne : <br />
<?php
$requete_arene = "select * from arenes_joueurs";
$req = $db->query($requete_arene);
if ($db->num_rows > 0)
{
	while ($R_arene = $db->read_assoc($req))
	{
		$p = recupperso_essentiel($R_arene['id'], 'ID, nom');
		echo '<a href="arenes.php?remove='.$R_arene['id'].'">'.$p['nom']."</a><br />\n";
	}
}
?>
</p>
