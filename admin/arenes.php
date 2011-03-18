<?php
if (file_exists('../root.php'))
  include_once('../root.php');

$textures = false;
$admin = true;

ob_start();

include_once(root.'admin/admin_haut.php');
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

require_once(root.'arenes/gen_arenes.php');

if (isset($_REQUEST['teleport_in'])) {
  $perso = perso::create('nom', $_REQUEST['player']);
  if( !$perso )
    die('<h5>Perso inconnu !</h5>');
  $perso = $perso[0];
  $arene = new arene($_REQUEST['teleport_in']);
  $groupe = groupe::create('nom', 'DTE '.$perso->get_race());
  if(array_key_exists('pa', $_REQUEST) && $_REQUEST['pa'] != '')
  {
    $perso->set_pa($_REQUEST[pa] * $G_PA_max);
    $perso->set_dernieraction( time() );
  }
  arenes_joueur::tp_arene($perso, $arene, $_REQUEST['p_x'], $_REQUEST['p_y'], $groupe[0], array_key_exists('full', $_REQUEST), 0, 0, $admin_nom);
}

if (isset($_REQUEST['remove']))
{
  $ar_perso = new arenes_joueur(0, $_REQUEST['remove']);
  $ar_perso->teleporte($admin_nom);
}

if (isset($_REQUEST['decal']))
{
  $t = $_REQUEST['t'];
  $requete_arenes_action = "update arenes set decal = $t where nom = '".
		sSQL($_REQUEST['decal'])."'";
	$req = $db->query($requete_arenes_action);
  ob_end_clean();
  $ar = mysql_affected_rows();
  echo "Recorded lines: $ar \n<br />";
  echo "Heure courante: ".date_sso(time() + $t);
  if ($ar < 1) echo $requete_arenes_action;
  exit (0);
}

if (isset($_REQUEST['calc']))
{
  ob_end_clean();
  $t = 0;
  $p = 0;
  if (isset($_REQUEST['perc'])
      && $_REQUEST['perc'] >= 0 && $_REQUEST['perc'] < 100)
    $p = $_REQUEST['perc'];
  if (isset($_REQUEST['heure'])) {
    if (is_numeric($_REQUEST['heure']))
      $t = $_REQUEST['heure'];
    else {
      $t = strtotime($_REQUEST['heure']);
      if ($t === false) {
        echo "Mauvaise heure";
        $t = 0;
      } else echo "Heure parsée : ".date('r', $t)."<br/>\n";
    }
  }
  echo "Décalage: ".calcul_decal($_REQUEST['moment'], $t, $p);
  exit (0);
}

if (isset($_REQUEST['close']))
{
  $requete_arenes_action = "update arenes set open = 0 where nom = '".
		sSQL($_REQUEST['close'])."'";
	$req = $db->query($requete_arenes_action);
  $requete_arenes_info = "select file from arenes where nom = '".
		sSQL($_REQUEST['close'])."'";
	$req = $db->query($requete_arenes_info);
  $R_arene = $db->read_assoc($req);
  unlink(root.'/arenes/'.$R_arene['file']);
  unlink(root.'/arenes/admin/'.$R_arene['file']);
}

if (isset($_REQUEST['open']))
{
  $requete_arenes_action = "update arenes set open = 1 where nom = '".
		sSQL($_REQUEST['open'])."'";
	$req = $db->query($requete_arenes_action);
  gen_all();
}

?>
<table><tr><td>
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
    echo '<option value="'.$R_arene['id'].'">'.$R_arene['nom']."</option>\n";
  }
}
?>
</select>
<input name="player" type="text" />
<label>Groupe racial <input name="group" type="checkbox" /></label>
<label>Full HP/MP <input name="full" type="checkbox" /></label>
<label>PA <select name="pa">
<option selected="selected"></option>
<option value="0">0%</option>
<option value="0.2">20%</option>
<option value="0.4">40%</option>
<option value="0.5">50%</option>
<option value="0.6">60%</option>
<option value="0.7">80%</option>
<option value="1">100%</option>
</select></label>
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
$requete_arene = "select * from arenes_joueurs where statut < 30";
$req = $db->query($requete_arene);
if ($db->num_rows > 0)
{
	while ($R_arene = $db->read_assoc($req))
	{
		//$p = recupperso_essentiel($R_arene['id'], 'ID, nom');
    $p = new perso($R_arene['id_perso']);
		$a = $p->in_arene();
    echo '<tr><td>'.$p->get_nom().'</td><td>'.$a->nom.'</td><td>'.
      '<a href="arenes.php?remove='.$R_arene['id'].'">Retirer</td></tr>';
	}
}
?>
</table>
<h3>Arènes :</h3>
<script type="text/javascript">
function chDecal(num, nom) {
  var dec = $('#d' + num).val();
  
  //alert(nom + ': ' + dec);

  $.ajax({
    type: "POST",
    url: "arenes.php",
    data: 'decal=' + nom + '&t=' + dec, 
    success:function(res){
      $('#ajax_res').html(res);
    }
  });
  // data: ['decal' : nom, 't': dec],

}

function calcDecal() {
  var moment = $('#moment_vise').val();
  var heure = $('#heure_ref').val();
  var perc = $('#perc').val();
  
  //alert(nom + ': ' + dec);

  $.ajax({
    type: "POST",
    url: "arenes.php",
    data: 'calc=1&moment=' + moment + '&heure=' + heure + '&perc=' + perc, 
    success:function(res){
      $('#ajax_res').html(res);
    }
  });
  // data: ['decal' : nom, 't': dec],

}
</script>
<table><tr><th>nom</th><th>ouverture</th><th>décalage</th><th>action</th></tr>
<?php
$requete_arene = "select * from arenes";
$req = $db->query($requete_arene);
$a = 0;
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
    $decal = '<input type="text" value="'.$R_arene['decal'].'" id="d'.$a
      .'" style="font-size: 0.8em; width: 60px;" />'
      .'<a href="javascript:chDecal('.$a.',\''.$R_arene['nom']
      .'\');">Décaler</a>';
    $a++;
		if ($_SESSION['admin_nom'] == 'admin' OR
				$_SESSION['admin_db_auth'] == 'admin') {
			$act .= ' <small><a href="?dump='.$R_arene['nom'].'">dumper</a></small>';
		}
    echo "<tr><td>$R_arene[nom]</td><td>$open</td><td>$decal</td><td>$act</td></tr>\n";
  }
}
echo "</table><div id=\"ajax_res\"></div>\n";

if (isset($_REQUEST['dump'])) {
	
	$requete_arene = "select * from arenes where nom = '$_REQUEST[dump]'";
	$req = $db->query($requete_arene);
  if ($row = $db->read_assoc($req)) {
		$arenes = array();
		for ($x = $row['x'] - 1; $x <= $row['x'] + $row['size'] + 1; $x++) {
			for ($y = $row['y'] - 1; $y <= $row['y'] + $row['size'] + 1; $y++) {
				$arenes[] = convert_in_pos($x, $y);
			}
		}
		$req = $db->query('select * from map where id in ('.
											implode(',', $arenes).')');
		$map = array();
		$upd = '<hr><pre>';
		while ($m = $db->read_array($req)) {
			$map[] = "($m[x], $m[y], $m[info], $m[decor], $m[royaume], $m[type])";
			$upd .= "update map set decor=$m[decor], type=$m[type] where x=$m[x], y=$m[y] ;\n";
		}
		echo "<pre>insert into map(x,y,info,decor,royaume,type) values \n".
			implode(",\n", $map).';</pre>';
		echo $upd;
	}
}


?>
<h3>Calcul de décalage</h3>
<label for="moment_vise">Moment visé : 
<select id="moment_vise" style="font-size: 0.8em; width: 60px;">
<option>Matin</option>
<option>Journee</option>
<option>Soir</option>
<option>Nuit</option>
</select></label>
<label for="heure_ref">Heure de référence : 
<input id="heure_ref" type="text" value="0" style="font-size: 0.8em; width: 60px;" /></label>
<label for="perc">Pourcent écoulé : 
<input id="perc" type="text" value="0" style="font-size: 0.8em; width: 60px;" /></label>
<a href="javascript:calcDecal()">Calculer</a>
</td></tr></table>
</body></html>
