<?php // -*- tab-width:2; mode: php -*- 
if (file_exists('../root.php')) {
  include_once('../root.php');
}

require('haut_roi.php');

if ($joueur->get_rang_royaume() != 6) {
	echo '<p>Cette page vous est interdite</p>';
  exit(0);
}
$case = new map_case();
$case->check_case('all');

if (array_key_exists('posex', $_REQUEST) && 
    array_key_exists('posey', $_REQUEST)) {
  if (pose_drapeau_roi($_REQUEST['posex'], $_REQUEST['posey']))
    echo "<p>Drapeau posé en $_REQUEST[posex], $_REQUEST[posey]</p>";
}

$roy_id = $royaume->get_id();

$req = "SELECT count(1) from depot_royaume d, objet_royaume o, batiment b where o.id = d.id_objet and o.id_batiment = b.id and o.type = 'drapeau' and b.hp = 1 and d.id_royaume = $roy_id";
$r_nbd = $db->query($req);
$nbd = $db->read_array($r_nbd);
$nb_drapeaux_dispo = $nbd[0];

$req = "SELECT count(1) from placement where type = 'drapeau' and hp = 1 and royaume = $roy_id";
$r_nbp = $db->query($req);
$nbp = $db->read_array($r_nbp);
$nb_drapeaux_poses = $nbp[0];

// On va utiliser des tables temporaires car la requete kifaitout prends ~30 s à s'effectuer
$req1 = "create temporary table tmp_royaume as select x,y from map where royaume = $roy_id";
$db->query($req1); // on prends le royaume
$req2 = "create temporary table tmp_adj as select distinct m.x, m.y from map m, tmp_royaume t where m.royaume = 0 and m.info != 5 and ((m.x = t.x + 1 and m.y = t.y) or (m.x = t.x - 1 and m.y = t.y) or (m.x = t.x and m.y = t.y + 1) or (m.x = t.x and m.y = t.y - 1))";
$db->query($req2); // on prends les cases neutres autour du royaume qui ne sont pas de l'eau
$req3 = "create temporary table tmp_adj_lib as select * from tmp_adj m where not exists (select x, y from placement p where p.x = m.x and p.y = m.y) and not exists (select x, y from construction c where c.x = m.x and c.y = m.y)";
$db->query($req3); // on enleve les cases occupées par un placement ou un batiment
$req = "select * from tmp_adj_lib";
$r_c = $db->query($req);
$nb_cases_ok = $db->num_rows($r_c);

$rand = rand();
$_SESSION['map_drap_key'] = $rand;

?>
<div id="info">
Drapeaux disponibles : <?php echo $nb_drapeaux_dispo; ?><br/>
Drapeaux posés : <?php echo $nb_drapeaux_poses; ?><br/>
Cases de pose autorisées : <?php echo $nb_cases_ok; ?><br/>
</div>
<div id="map">
<img id="mapim" usemap="#mapimmap" alt="Carte des poses de drapeaux" src="drapeaux_map.php?img=<?php echo $rand; ?>" />
<map name="mapimmap">
<?php
	while ($r = $db->read_object($r_c)) {
		$r->x1 = ($r->x - 1) * 4;
		$r->y1 = ($r->y - 1) * 4;
		$r->x2 = $r->x1 + 4;
		$r->y2 = $r->y1 + 4;
		echo "<area shape=\"rect\" coords=\"$r->x1,$r->y1,$r->x2,$r->y2\" alt=\"$r->x,$r->y\" href=\"javascript:pose_drapeau($r->x, $r->y)\" />\n";
	}
?>
</map>
</div>

<script type="text/javascript">
function pose_drapeau(x, y)
{
  // TODO: poser une question ?
	//alert("pose_drapeau: " + x + "/" + y);
	affiche_page('drapeaux.php?posex=' + x + '&posey=' + y);
}
</script>