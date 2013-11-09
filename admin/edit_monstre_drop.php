<?php // -*- mode: php; tab-width:2 -*-
if (file_exists('../root.php'))
  include_once('../root.php');

$admin = true;
$textures = false;
include_once(root.'admin/admin_haut.php');
setlocale(LC_ALL, 'fr_FR');
include_once(root.'haut_site.php');
include_once(root.'admin/menu_admin.php');

if ($G_maintenance)
{
	echo 'Starshine-online est actuellement en cours de mis à jour.<br />
	le forum est toujours disponible <a href="punbb/">ici - Forum</a>';
}
else
{
  $uloot = array();

	if(array_key_exists('id_monstre', $_GET))
    $id_monstre = $_GET['id_monstre'];
	else
    $id_monstre = $_POST['id_monstre'];
  $lastloot = '';
	$monstre = recupmonstre($id_monstre, false);
	$requete = "SELECT id, nom, drops FROM monstre WHERE id = ".$id_monstre;
	$req = $db->query($requete);
	$monstre = $db->read_assoc($req);
	$drops = explode(';', $monstre['drops']);
  if (count($drops) == 1 && $drops[0] == '') $drops = array();
	while (count($drops) > 0 && $drops[0] == '') array_shift($drops);
	if (array_key_exists('delete_unique', $_GET))
  {
    $db->query("delete from boss_loot where item = '".
               sSQL($_GET['delete_unique'])."' and id_monstre = $id_monstre");
  }
	elseif (array_key_exists('loot_unique', $_POST))
  {
    $item = sSQL($_POST['loot_unique']);
    $lastloot = $item;
    $chance = intval($_POST['chance']);
    $level = intval($_POST['level']);
    if (preg_match('/^([oamlp]|hg)[0-9]+$/', $item)
      && $chance > 0 && $chance < 100000
        && ($level >= 0 && $level <= 2))
    {
      $db->query("insert into boss_loot(id_monstre, item, chance, level) ".
        "values ($id_monstre, '$item', $chance, $level)");
    }
    else
      echo "<h5>Mauvaise entrée: $item / $chance / $level </h5>";
  }
  elseif(array_key_exists('chance_drop', $_POST))
	{
		if($_POST['objet'] != '') $o_drop = $_POST['objet'];
		elseif($_POST['arme'] != '') $o_drop = $_POST['arme'];
		elseif($_POST['armure'] != '') $o_drop = $_POST['armure'];
		elseif($_POST['accessoire'] != '') $o_drop = $_POST['accessoire'];
		elseif($_POST['grimoire'] != '') $o_drop = $_POST['grimoire'];
		echo $o_drop;
		$drops[] = $o_drop.'-'.$_POST['chance_drop'];
		$drops_i = implode(';', $drops);
		$requete = "UPDATE monstre SET drops = '".$drops_i."' WHERE id = ".$id_monstre;
		$db->query($requete);
	}
	if(array_key_exists('key', $_GET))
	{
		unset($drops[$_GET['key']]);
		$drop = implode(';', $drops);
		$requete = "UPDATE monstre SET drops = '".$drop."' WHERE id = ".$id_monstre;
		$db->query($requete);
	}
	?>
	<div id="contenu">
		<div id="centre3">
			<div class="titre">
				Edition des drops de <?php echo $monstre['nom']; ?> - <?php echo $monstre['reserve']; ?> RM - Arme : <?php echo $monstre['arme_type']; ?>
			</div>
			Drops actuels :
			<ul>
			<?php
				foreach($drops as $key => $drop)
				{
					if($drop != '')
					{
						$explode = explode('-', $drop);
						if($explode[0][0] == 'r')
						{
							$nom_objet = 'recette';
							$description_objet = '';
						}
						else
						{ 
							$nom_objet = nom_objet($explode[0]);
							$description_objet = nom_objet($explode[0]);
						}
						echo '<li>'.$nom_objet.' - 1 chance sur '.$explode[1].' <span class="xsmall">'.$description_objet.'</span> <a href="edit_monstre_drop.php?id_monstre='.$id_monstre.'&key='.$key.'">X</a></li>';
					}
				}
			?>
			</ul>
			<form action="edit_monstre_drop.php" method="post">
			Objet :
			<select name="objet">
					<option></option>
			<?php
        $tmp = array();

				$requete = "SELECT * FROM objet ORDER BY nom";
				$req_r = $db->query($requete);
				while($row_r = $db->read_assoc($req_r))
				{
          $tmp[$row_r['id']] = $row_r['nom'];
					echo '<option value="o'.$row_r['id'].'">'.$row_r['nom'].'</option>';
				}

        $uloot['o'] = $tmp;
				?>
			</select>
			<br />
			Arme :
			<select name="arme">
					<option></option>
				<?php
        $tmp = array();
				$requete = "SELECT * FROM arme ORDER BY nom";
				$req_r = $db->query($requete);
				while($row_r = $db->read_assoc($req_r))
				{
					echo '<option value="a'.$row_r['id'].'">'.$row_r['nom'].'</option>';
          $tmp[$row_r['id']] = $row_r['nom'];
				}
        $uloot['a'] = $tmp;
				?>
			</select>
			<br />
			Armure :
			<select name="armure">
					<option></option>
				<?php
        $tmp = array();
				$requete = "SELECT * FROM armure ORDER BY nom";
				$req_r = $db->query($requete);
				while($row_r = $db->read_assoc($req_r))
				{
					echo '<option value="p'.$row_r['id'].'">'.$row_r['nom'].'</option>';
          $tmp[$row_r['id']] = $row_r['nom'];
				}
        $uloot['p'] = $tmp;
				?>
			</select>
			<br />
			Accessoire :
			<select name="accessoire">
					<option></option>
				<?php
        $tmp = array();
				$requete = "SELECT * FROM accessoire ORDER BY nom";
				$req_r = $db->query($requete);
				while($row_r = $db->read_assoc($req_r))
				{
					echo '<option value="m'.$row_r['id'].'">'.$row_r['nom'].'</option>';
          $tmp[$row_r['id']] = $row_r['nom'];
				}
        $uloot['m'] = $tmp;
				?>
			</select>
			<br />
			Grimoire :
			<select name="grimoire">
					<option></option>
				<?php
        $tmp = array();
				$requete = "SELECT * FROM grimoire ORDER BY nom";
				$req_r = $db->query($requete);
				while($row_r = $db->read_assoc($req_r))
				{
					echo '<option value="l'.$row_r['id'].'">'.$row_r['nom'].'</option>';
          $tmp[$row_r['id']] = $row_r['nom'];
				}
        $uloot['g'] = $tmp;


        $tmp = array();
				$requete = "SELECT * FROM gemme ORDER BY nom";
				$req_r = $db->query($requete);
				while($row_r = $db->read_assoc($req_r))
          $tmp[$row_r['id']] = $row_r['nom'];
        $uloot['hg'] = $tmp;

			?>
			</select>
			Chance de drop, 1 sur <input type="text" name="chance_drop" />
			<input type="hidden" name="id_monstre" value="<?php echo $id_monstre; ?>" />
			<input type="submit" value="Valider" />
			</form>

<hr/>
<h1>Loots uniques</h1>
<form method="post" action="?">	
<!-- helper -->
  <select id="type-opt">
    <option value="">--</option>
    <option value="o">Objet</option>
    <option value="a">Arme</option>
    <option value="p">Armure</option>
    <option value="hg">Gemme</option>
    <option value="m">Accessoire</option>
    <option value="g">Grimoire</option>
  </select>
  <select id="loot-opt"></select>

<br/>

  <label for="item-txt">Item</label>
  <input id="item-txt" type="text" disabled="disabled" size="4" />
  <label for="chance-txt">Probabilité relative</label>
  <input id="chance-txt" type="text" name="chance" size="4" />
  <label for="level-sel">Type</label>
  <select id="level-sel"  name="level">
    <option value="0">moyen</option>
    <option value="1">moyen unique</option>
    <option value="2">gros bill</option>
  </select><br/>
  <input type="hidden" name="id_monstre" value="<?php echo $id_monstre; ?>" />
  <input type="hidden" id="hd-loot-id" name="loot_unique" value="<?php echo $lastloot ?>" />
	<input type="submit" value="Valider" />
</form>

<?php

$uloot_json = json_encode($uloot);

print_js("var uLoot = $uloot_json;");
print_js_onload("

$('#type-opt').click(function() { setLootSelector($(this).val()); });
$('#loot-opt').click(function() { setUniqueLoot($(this).val()); });

");

$req = $db->query("select * from boss_loot where id_monstre = $id_monstre");
$total_m = $total_gb = 0;
$drops = array();
while ($row = $db->read_object($req)) {
  if( $row->level == 2 )
    $total_gb += $row->chance;
  else
    $total_m += $row->chance;
  $drops[] = $row;
}

echo '<table><thead><th>Nom</th><th>type</th><th>Chances</th></thead><tbody>';
foreach ($drops as $d) {
  $percent = floor(10000 * $d->chance / ($d->level==2?$total_gb:$total_m)) / 100;
  $type = $d->item[0];
  if ($type == 'h') {
    $type = 'hg';
    $id = substr($d->item, 2);
  } 
  else
    $id = substr($d->item, 1);
  $nom = $uloot[$type][$id];
  echo '<tr><td>'.$nom.'</td><td>';
  switch($d->level)
  {
  case 0:
    echo 'moyen';
    break;
  case 1:
    echo 'moyen unique';
    break;
  case 2:
    echo 'gros bill';
    break;
  default:
    echo "inconnu ($d->level)";
  }
  echo '</td><td>'.$d->chance.' ('.$percent.'%)</td><td>'.
    '<a href="?delete_unique='.$d->item.'&amp;id_monstre='.
    $id_monstre.'">Supprimer</a></td></tr>';
}
echo '</table>';

}
?>
		</div>
	</div>