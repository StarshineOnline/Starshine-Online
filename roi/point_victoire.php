<?php
if (file_exists('../root.php'))
  include_once('../root.php');

require('haut_roi.php');
include_once(root.'fonction/messagerie.inc.php');

if($joueur->get_rang_royaume() != 6)
	echo '<p>Cheater</p>';
elseif(array_key_exists('action', $_GET))
{
	switch($_GET['action'])
	{
		case 'famine' :
			$ids = array();
			$requete = "SELECT buff.id FROM perso LEFT JOIN buff ON buff.id_perso = perso.id WHERE buff.type = 'famine' AND race = '".$royaume->get_race()."'";
			$req = $db->query($requete);
			while($row = $db->read_row($req))
			{
				$ids[] = $row[0];
			}
			$ids_implode = implode(', ', $ids);
			$requete = "DELETE FROM buff WHERE id IN (".$ids_implode.")";
			$db->query($requete);
		break;
	}
}
else
{
?>
<div id='point_victoire'>
	Point de victoire : <?php echo $royaume->get_point_victoire(); ?>
</div>
<?php
}
?>
