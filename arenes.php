<?php
//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include('haut.php');

$joueur = recupperso($_SESSION['ID']);

$arene_masters = array('Irulan', 'Azeroth');
if (!in_array($joueur['nom'], $arene_masters))
     die("Vous n'êtes pas arène master");

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
  $requete_journal = "INSERT INTO journal VALUES('', $id, 'teleport', '".$joueur['nom']."', '".$R_perso['nom']."', NOW(), '$arene', 0, ".$joueur['x'].", ".$joueur['y'].")";
  $req = $db->query($requete_journal);
}

if (isset($_REQUEST['remove'])) {
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
  $requete_journal = "INSERT INTO journal VALUES('', $id, 'teleport', '".$joueur['nom']."', '".$R_perso['nom']."', NOW(), 'jeu', 0, ".$joueur['x'].", ".$joueur['y'].")";
  $req = $db->query($requete_journal);
}

?>

<form action="arenes.php" method="get">
<select name="teleport_in">
<?php
$requete_arene = "select * from arenes";
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
if ($db->num_rows > 0) {
  while ($R_arene = $db->read_assoc($req)) {
    $p = recupperso_essentiel($R_arene['id'], 'ID, nom');
    echo '<a href="arenes.php?remove='.$R_arene['id'].'">'.$p['nom']."</a><br />\n";
  }
}
?>
</p>
