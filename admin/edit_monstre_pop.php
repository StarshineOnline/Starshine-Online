<?php
if (file_exists('../root.php'))
  include_once('../root.php');

$admin = true;
$textures = false;
include_once(root.'admin/admin_haut.php');
setlocale(LC_ALL, 'fr_FR');
include_once(root.'haut_site.php');
if ($G_maintenance)
{
	echo 'Starshine-online est actuellement en cours de mis à jour.<br />
	le forum est toujours disponible <a href="punbb/">ici - Forum</a>';
}
else
{
	include_once(root.'admin/menu_admin.php');
	if(array_key_exists('id_monstre', $_GET)) $id_monstre = $_GET['id_monstre'];
	else $id_monstre = $_POST['id_monstre'];
	$monstre = recupmonstre($id_monstre, false);
	$requete = "SELECT * FROM monstre WHERE id = ".$id_monstre;
	$req = $db->query($requete);
	$monstre = $db->read_assoc($req);
	$pops = explode(';', $monstre['spawn_loc']);
	while (count($pops) > 0 && $pops[0] == '') array_shift($pops);
	if(array_key_exists('x', $_POST))
	{
		$pops[] = 'p'.$_POST['x'].'-'.$_POST['y'];
		$pops_i = implode(';', $pops);
		$requete = "UPDATE monstre SET spawn_loc = '".$pops_i."' WHERE id = ".$id_monstre;
		$db->query($requete);
	}
  if (array_key_exists('delx', $_GET)) {
    foreach ($pops as $k => $p) {
      if ($p == ($_GET['delx'].'-'.$_GET['dely'])) {
        unset($pops[$k]);
      }
      if ($p == ('p'.$_GET['delx'].'-'.$_GET['dely'])) {
        unset($pops[$k]);
      }
    }
    $pops = array_values($pops);
		$pops_i = implode(';', $pops);
		$requete = "UPDATE monstre SET spawn_loc = '".$pops_i."' WHERE id = ".$id_monstre;
		$db->query($requete);
  }

  $monstre['reserve'] = ceil(2.1 * ($monstre['energie'] + floor(($monstre['energie'] - 8) / 2)))
	?>
	<div id="contenu">
		<div id="centre3">
			<div class="titre">
				Edition du pop de <?php echo $monstre['nom']; ?> - <?php echo $monstre['reserve']; ?> RM - Arme : <?php echo $monstre['arme']; ?>
			</div>
			pops actuels :
			<ul>
			<?php
				foreach($pops as $pop)
				{
					if($pop != '')
					{
						$explode = explode('-', $pop);
						echo '<li>X : '.$explode[0].' - Y : '.$explode[1].' '.
              '<a href="edit_monstre_pop.php?delx='.$explode[0].'&amp;dely='.
              $explode[1].'&amp;id_monstre='.$id_monstre.
              '">[Supprimer]</a></li>';
					}
				}
			?>
			</ul>
			<form action="edit_monstre_pop.php" method="post">
			Ajouter un pop :<br />
			X : <input type="text" id="x" name="x" /><br />
			Y : <input type="text" id="y" name="y" />
			<input type="hidden" name="id_monstre" value="<?php echo $id_monstre; ?>" />
			<input type="submit" value="Valider" />
			</form>
			<?php
}
			?>
		</div>
	</div>