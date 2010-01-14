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

$admin_nom = $_SESSION['admin_nom'];
if ($admin_nom == '') {
	$admin_nom = 'admin';
}

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
  $requete_arene = "select x as xmin, y as ymin, x + size as xmax, y + size as ymax from arenes where nom = '$arene'";
  $req = $db->query($requete_arene);
  if ($db->num_rows > 0)
    $R_arene = $db->read_assoc($req);
  else
    die('arene inconnue');
  $nx = $R_arene['xmin'] + $_REQUEST['p_x'];
  $ny = $R_arene['ymin'] + $_REQUEST['p_y'];
  $requete_arenes_perso = "insert into arenes_joueurs value($x, $y, $id)";
  $req = $db->query($requete_arenes_perso);
  if (array_key_exists('full', $_REQUEST))
    $fullish = ", hp=hp_max, mp=mp_max, pa=$G_PA_max";
  $requete_perso = "update perso set x=$nx, y=$ny $fullish where id = $id";
  $req = $db->query($requete_perso);
  $requete_journal = "INSERT INTO journal VALUES('', $id, 'teleport', '".$admin_nom."', '".$R_perso['nom']."', NOW(), '$arene', 0, 0, 0)";
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
  $requete_perso = "update perso set hp=1 where id = $id and hp < 1";
  $req = $db->query($requete_perso);
  $requete_arenes_perso = "delete from arenes_joueurs where id = '$id'";
  $db->query($requete_arenes_perso);
  $requete_journal = "INSERT INTO journal VALUES('', $id, 'teleport', '".$admin_nom."', '".$R_perso['nom']."', NOW(), 'jeu', 0, 0, 0)";
  $req = $db->query($requete_journal);
}

if (isset($_REQUEST['close']))
{
  $requete_arenes_action = "update arenes set open = 0 where nom = '".
		sSQL($_REQUEST['close'])."'";
	$req = $db->query($requete_arenes_action);
}

if (isset($_REQUEST['open']))
{
  $requete_arenes_action = "update arenes set open = 1 where nom = '".
		sSQL($_REQUEST['open'])."'";
	$req = $db->query($requete_arenes_action);
}

?>

<h3>Ajouter un joueur dans une ar&egrave;ne :</h3>
<form action="arenes.php" method="get"><p>
<select name="teleport_in">
<?php
$requete_arene = "select * from arenes where open = 1";
$req = $db->query($requete_arene);
if ($db->num_rows > 0) {
  while ($R_arene = $db->read_assoc($req)) {
		$size_a = ceil($R_arene['size'] / 2);
		if (!isset($size_a1)) $size_a1 = $size_a;
    echo '<option value="'.$R_arene['nom'].'">'.$R_arene['nom']."</option>\n";
  }
}
?>
</select>
<input name="player" type="text" />
<label>Full HP/MP/PA <input name="full" type="checkbox" /></label>
<label>Pos X <input name="p_x" type="text" size="2"
 value="<?php echo $size_a1; ?>" /></label>
<label>Pos Y <input name="p_y" type="text" size="2"
 value="<?php echo $size_a1; ?>" /></label>
<input type="submit" />
</p>
</form>
<h3>Joueurs en ar&egrave;ne :</h3>
<table><tr><th>nom</th><th>arene</th><th>action</th></tr>
<?php
$requete_arene = "select * from arenes_joueurs";
$req = $db->query($requete_arene);
if ($db->num_rows > 0)
{
	while ($R_arene = $db->read_assoc($req))
	{
		//$p = recupperso_essentiel($R_arene['id'], 'ID, nom');
    $p = new perso($R_arene['id']);
		$a = $p->in_arene();
    echo '<tr><td>'.$p->get_nom().'</td><td>'.$a->nom.'</td><td>'.
      '<a href="arenes.php?remove='.$R_arene['id'].'">Retirer</td></tr>';
	}
}
?>
</table>
<h3>Arènes :</h3>
<table><tr><th>nom</th><th>ouverture</th><th>action</th></tr>
<?php
$requete_arene = "select * from arenes";
$req = $db->query($requete_arene);
if ($db->num_rows > 0) {
  while ($R_arene = $db->read_assoc($req)) {
		if ($R_arene['open'] == 1) {
			$open = 'oui';
			$act = '<a href="?close='.$R_arene['nom'].'">fermer</a>';
		}
		else {
			$open = 'non';
			$act = '<a href="?open='.$R_arene['nom'].'">ouvrir</a>';
		}
    echo "<tr><td>$R_arene[nom]</td><td>$open</td><td>$act</td></tr>\n";
  }
}
?>

