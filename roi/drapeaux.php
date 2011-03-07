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
if (array_key_exists('poseall', $_REQUEST)) {
	$nb = pose_drapeau_roi_all();
	if ($nb !== false) {
		echo "<p>$nb drapeaux posés</p>";
	}
}

function print_map($mag_factor, $r_c) {
	global $db;
	echo "<map name=\"mapimmap\">\n";
	while ($r = $db->read_object($r_c)) {
		$r->x1 = ($r->x - 1) * 4 * $mag_factor;
		$r->y1 = ($r->y - 1) * 4 * $mag_factor;
		$r->x2 = $r->x1 + (4 * $mag_factor);
		$r->y2 = $r->y1 + (4 * $mag_factor);
		echo "<area shape=\"rect\" coords=\"$r->x1,$r->y1,$r->x2,$r->y2\" alt=\"$r->x,$r->y\" href=\"javascript:pose_drapeau($r->x, $r->y)\" />\n";
	}
	echo "</map>\n";
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


make_tmp_adj_tables($roy_id);
$req = "select * from tmp_adj_lib";
$r_c = $db->query($req);
$nb_cases_ok = $db->num_rows($r_c);

$mleft = 0;
$mtop = 0;
$mag_factor = 1;
if (array_key_exists('mag_factor', $_GET)) {
	$mag_factor = $_GET['mag_factor'];
}
if (array_key_exists('mtop', $_GET)) {
	$mtop = $_GET['mtop'];
}
if (array_key_exists('mleft', $_GET)) {
	$mleft = $_GET['mleft'];
}

$rand = rand();
$_SESSION['map_drap_key'] = $rand;

$map_size = 760 * $mag_factor;

?>
<div id="info">
Drapeaux disponibles : <?php echo $nb_drapeaux_dispo; ?><br/>
Drapeaux posés : <?php echo $nb_drapeaux_poses; ?><br/>
Cases de pose autorisées : <?php echo $nb_cases_ok; ?><br/>
</div>
<div id="ctrls" style="float: right; background-color: #FFFFCC; padding: 3px">
<input type="button" onclick="movel()" value="←" />
<input type="button" onclick="mover()" value="→" />
<input type="button" onclick="moveu()" value="↑" />
<input type="button" onclick="moveb()" value="↓" /><br />
<input type="button" onclick="zoomm()" value="+" />
<input type="button" onclick="zooml()" value="−" /><br />
<input type="button" onclick="toutposer()" value="Poser un maximum" />
</div>
<div id="map" style="width: 760px; height: 760px; overflow: hidden; position: relative">
<img style="position: absolute; left: <?php echo $mleft; ?>px; top: <?php echo $mtop; ?>px" width="<?php echo $map_size; ?>" height="<?php echo $map_size; ?>" id="mapim" usemap="#mapimmap" alt="Carte des poses de drapeaux" src="drapeaux_map.php?img=<?php echo $rand; ?>" />
<div id="mapinmapd">
<?php print_map($mag_factor, $r_c); ?>
</div>
</div>

<script type="text/javascript">
var mtop = <?php echo $mtop; ?>;
var mleft = <?php echo $mleft; ?>;

function pose_drapeau(x, y)
{
  // TODO: poser une question ?
	//alert("pose_drapeau: " + x + "/" + y);
	var url = 'drapeaux.php?posex=' + x + '&posey=' + y + '&mag_factor=' + mag;
	if (mtop != 0) { url = url + '&mtop=' + mtop; }
	if (mleft != 0) { url = url + '&mleft=' + mleft; }
	affiche_page(url);
}

function mover()
{
	//var x = $("#mapim").css("left").substring(0, -2);
	var l = $("#mapim").css("left").split('p');
	var x = new Number(l[0]);
	mleft = x - 50;
	$("#mapim").css("left", mleft + 'px');
}

function movel()
{
	var l = $("#mapim").css("left").split('p');
	var x = new Number(l[0]);
	mleft = x + 50;
	$("#mapim").css("left", mleft + 'px');
}

function moveu()
{
	var l = $("#mapim").css("top").split('p');
	var x = new Number(l[0]);
	mtop = x + 50;
	$("#mapim").css("top", mtop + 'px');
}

function moveb()
{
	var l = $("#mapim").css("top").split('p');
	var x = new Number(l[0]);
	mtop = x - 50;
	$("#mapim").css("top", mtop + 'px');
}

var mag = <?php echo $mag_factor; ?>;

function zoomm() {	mag++; affimg(); }
function zooml() { if (mag > 1) { mag--; affimg(); } }

function affimg()
{
	var url = 'drapeaux.php?mag_factor=' + mag;
	if (mtop != 0) { url = url + '&mtop=' + mtop; }
	if (mleft != 0) { url = url + '&mleft=' + mleft; }
	affiche_page(url);
}

function toutposer()
{
	var url = 'drapeaux.php?poseall';
	if (mag > 1) { url = url + '&mag_factor=' + mag; }
	if (mtop != 0) { url = url + '&mtop=' + mtop; }
	if (mleft != 0) { url = url + '&mleft=' + mleft; }
	affiche_page(url);
}

</script>