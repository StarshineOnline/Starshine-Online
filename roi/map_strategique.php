<?php
include('../class/map.class.php');
$x = $bataille->x;
$y = $bataille->y;

$map = new map($x, $y, 11, '../', false, 'low');
$map->set_batiment($batiments);
$map->set_repere($bataille->reperes);
$map->set_onclick("envoiInfo('gestion_bataille.php?id_bataille=".$bataille->id."&amp;case=%%ID%%&amp;info_case', 'information');");

?>
<div style="float : left;">
<?php
$map->affiche();
?>
</div>
<div id="information" style="float : right;">
	INFOS
</div>