<?php // -*- tab-width:2; mode: php -*- 
if (file_exists('../root.php')) {
  include_once('../root.php');
}

require('haut_roi.php');

if ($joueur->get_rang_royaume() != 6) {
	echo '<p>Cette page vous est interdite</p>';
  exit(0);
}

die('Work in progress');

if (array_key_exists('posex', $_REQUEST) && 
    array_key_exists('posey', $_REQUEST)) {
  if (pose_drapeau_roi($_REQUEST['posex'], $_REQUEST['posey']))
    echo "<p>Drapeau posé en $_REQUEST[posex], $_REQUEST[posey]</p>";
}

$roy_id = $royaume->get_id();

$nb_drapeaux_dispo = 0;
$nb_drapeaux_poses = 0;

$req = "select x,y from map m where royaume = 0 and exists (select royaume from map m2 where ((m.x = m2.x + 1 and m.y = m2.y) or (m.x = m2.x - 1 and m.y = m2.y) or (m.x = m2.x and m.y = m2.y + 1) or (m.x = m2.x and m.y = m2.y + 1)) and m2.royaume = $roy_id)";
$r = $db->query($req);
$nb_cases_ok = $db->num_rows($r);



?>
<div id="info">
Drapeaux disponibles : <?php echo $nb_drapeaux_dispo; ?><br/>
Drapeaux posés : <?php echo $nb_drapeaux_poses; ?><br/>
Cases de pose autorisées : <?php echo $nb_cases_ok; ?><br/>
</div>
<div id="map">
<img id="mapim" alt="Carte des poses de drapeaux" src="drapeaux_map.php?img" />
</div>

<script type="text/javascript">
function pose_drapeau(x, y)
{
  // TODO
}
</script>