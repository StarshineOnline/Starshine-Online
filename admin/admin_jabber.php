<?php
if (file_exists('../root.php'))
  include_once('../root.php');

$textures = false;
$admin = true;

include_once(root.'admin/admin_haut.php');
setlocale(LC_ALL, 'fr_FR');
// include_once(root.'haut_site.php');

if ($G_maintenance)
{
	echo 'Starshine-online est actuellement en cours de mise à jour.<br />
	le forum est toujours disponible <a href="punbb/">ici - Forum</a>';
}
else
{
	include_once(root.'admin/menu_admin.php');

  if ($_REQUEST['do'] == 'del') {
    $requete = "delete from jabber_admin where nom = '$_REQUEST[who]'";
    $req = $db->query($requete);
  }

  if ($_REQUEST['do'] == 'new') {
    if ($_REQUEST['hash'] != '') {
      $do = true;
      $pw = "'$_REQUEST[hash]'";
    }
    elseif ($_REQUEST['pw'] == $_REQUEST['pw2']) {
      $do = true;
      $pw = "MD5('$_REQUEST[pw]')";
    }
    else {
      echo 'Passwords doesn\'t match !';
      $do = false;
    }
    if ($do) {
      $requete = "insert into jabber_admin values ('$_REQUEST[who]', $pw, '$_REQUEST[statut]')";
      $req = $db->query($requete);
    }
  }
  
  echo '<h3>Comptes hors-jeu</h3>';
  echo '<table><tr><th>Nom</th><th>Type</th><th>Action</th></tr>';
  $requete = "select * from jabber_admin order by statut";
  $req = $db->query($requete);
  while ($row = $db->read_assoc($req)) {
    $act = '<a href="?do=del&amp;who='.$row['nom'].'">Suppr</a>';
    echo "<tr><td>$row[nom]</td><td>$row[statut]</td><td>$act</td></tr>\n";
  }
  echo '</table>';

?>
<br />
<h4>Ajouter un compte hors-jeu</h4>
<form method="post" action="?do=new">
<table>
<tr><td>Nom</td><td><input type="text" name="who" /></td></tr>
<tr><td>Password</td><td><input type="password" name="pw" /></td></tr>
<tr><td>Password (confirmation)</td><td><input type="password" name="pw2" /></td></tr>
<tr><td>OU hash</td><td><input type="text" name="hash" maxlength="32" /></td></tr>
<tr><td>Type</td><td><select name="statut">
<option value="admin">Admin</option>
<option value="modo" selected="">Modo</option></select>
</td></tr>
<tr><td /><td><input type="submit" value="OK" /></td></tr>
</table>
</form>

<?php

  echo '<hl/><h3>Salons</h3>';
  
  echo '<table><tr><th>Nom</th><th>Topic</th><th>Users</th></tr>';
  $requete = "select * from jabber.rooms";
  $req = $db->query($requete);
  while ($row = $db->read_assoc($req)) {
    echo "<tr><td>$row[name]</td><td>$row[topic]</td><td>$row[users]</td></tr>\n";
  }
  echo '</table>';

}
?>