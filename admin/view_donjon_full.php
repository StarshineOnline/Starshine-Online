<?php
if (file_exists('../root.php'))
  include_once('../root.php');

$admin = true;
$textures = false;
include_once(root.'inc/fp.php');

add_js_to_head('../javascript/jquery/jquery.cluetip.min.js');

include_once(root.'admin/admin_haut.php');
setlocale(LC_ALL, 'fr_FR');
include_once(root.'haut_site.php');

include_once(root.'admin/menu_admin.php');
echo '<div style="margin-left: 200px">';

$req = $db->query("select max(x) xmax, min(x) xmin, min(y) ymin, max(y) ymax from map where y > 190");
$range = $db->read_object($req);
$db->free($req);

$range_str = "(x >= $range->xmin and x <= $range->xmax and y >= $range->ymin and y <= $range->ymax)";

$map = array();

class view_map
{
  var $map;
  var $perso = array();
  var $monstre = array();
	var $pnj = array();

  function __construct($map) { $this->map = $map; }
  function add_monstre($monstre) { $this->monstre[] = $monstre; }
  function add_perso($perso) { $this->perso[] = $perso; }
  function add_pnj($pnj) { $this->pnj[] = $pnj; }
  function get_id() { return 'TT_'.$this->map->x.'_'.$this->map->y; }
  function prnt() {
    global $Tclasse;
    $add = '';
		echo '<td style="border: 0px; width: 60px; height: 60px;"';
		if ($this->map->decor != 0) {
			echo 'class="decor tex'.$this->map->decor.'"';
		} else {
			echo 'class="decor texblack"';
		}
		echo '>';
    if (count($this->perso)) {
      echo '<div rel="#'.$this->get_id().'" class="map_contenu" style="background-image: url(../image/personnage/'.$this->perso[0]->race.'/'.$this->perso[0]->race.'_'.$Tclasse[$this->perso[0]->classe]['type'].'.png)">&nbsp;</div>';
    }
    elseif (count($this->pnj)) {
      echo '<div rel="#'.$this->get_id().'" class="map_contenu" style="background-image: url(../image/pnj/'.$this->pnj[0]->image.'.png); width: 54px; height: 54px"></div>';
    }
    elseif (count($this->monstre)) {
      echo '<div rel="#'.$this->get_id().'" class="map_contenu" style="background-image: url(../image/monstre/'.$this->monstre[0]->image.'.png); width: 54px; height: 54px"></div>';
    }
    if (count($this->perso) || count($this->monstre) || count($this->pnj)) {
      $add = '<div id="'.$this->get_id().'" class="map_contenu_div">';
      foreach ($this->perso as $perso) {
        $image = '../image/personnage_low/'.$perso->race.'/'.$perso->race.'_'.
          $Tclasse[$perso->classe]['type'].'.png';
        $add .= '<li class="overlib_perso"><img src="'.$image.'" /> '
          .$perso->nom.' - '.$perso->race.' - '.$perso->classe.' - '
          .$perso->hp.' / '.$perso->hp_max.' HP - '.$perso->mp.' / '
          .$perso->mp_max.' MP - '.$perso->pa.' PA</li>';
      }
      foreach ($this->pnj as $pnj) {
        $add .= '<li class="overlib_perso"><img src="../image/pnj/'
          .$pnj->image.'.png" />'.$pnj->nom.'</li>';
      }
      foreach ($this->monstre as $monstre) {
        $add .= '<li class="overlib_perso"><img src="../image/monstre/'
          .$monstre->image.'.png" />'.$monstre->nom.' - '.$monstre->hp
          .' / '.$monstre->hp_max.' HP</li>';
      }
      $add .= "</div>\n";
    }
		echo '</td>';
    return $add;
  }
}

function prnt_blank() {
  echo '<td style="border: 0px; width: 60px; height: 60px;" class="decor texblack"></td>';
}

$req = $db->query("select * from map where $range_str");
while ($row = $db->read_object($req)) {
  $map[$row->x][$row->y] = new view_map($row);
}
$db->free($req);

$req = $db->query("select mm.*, m.lib as image, m.nom, m.hp as hp_max from map_monstre mm, monstre m where mm.type = m.id and $range_str order by m.level asc");
while ($row = $db->read_object($req)) {
  if (array_key_exists($row->x, $map) &&
      array_key_exists($row->y, $map[$row->x])) 
    $map[$row->x][$row->y]->add_monstre($row);
}
$db->free($req);

$req = $db->query("select * from pnj where $range_str");
while ($row = $db->read_object($req)) {
	if (array_key_exists($row->x, $map) &&
			array_key_exists($row->y, $map[$row->x]))
		$map[$row->x][$row->y]->add_pnj($row);
}
$db->free($req);

$req = $db->query("select * from perso where statut = 'actif' and $range_str");
while ($row = $db->read_object($req)) {
  if (array_key_exists($row->x, $map) &&
      array_key_exists($row->y, $map[$row->x]))
  $map[$row->x][$row->y]->add_perso($row);
}
$db->free($req);

//my_dump($map);
$add = '';
$size = 'min-width: '.(($range->xmax - $range->xmin) * 60 + 45).
'px; min-height: '.(($range->ymax - $range->ymin) * 60 + 20).'px;';
echo '<table cellpadding="0" cellspacing="0" style="'.$size.'"><tr><th></th>';
for ($x = $range->xmin; $x <= $range->xmax; $x++) {
	echo "<th>$x</th>";
}
echo '</tr>';
for ($y = $range->ymin; $y <= $range->ymax; $y++) {
	echo "\n<tr><th>$y</th>";
  for ($x = $range->xmin; $x <= $range->xmax; $x++) {
    if (array_key_exists($x, $map) && array_key_exists($y, $map[$x])) 
      $add .= $map[$x][$y]->prnt();
    else
      prnt_blank();
  }
  echo '</tr>';
}
echo "</table>\n";

print_js_onload("$('.map_contenu').each(function(index) { $(this).cluetip({local:true, showTitle: false, leftOffset: -5, dropShadow: false, waitImage: false }); }); ");

echo $add;
echo '</div>';
include_once(root.'admin/admin_bas.php');

?>