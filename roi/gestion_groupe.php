<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
require('haut_roi.php');
echo "<ul style='float :left;'>";

$requete = "SELECT groupe.id as groupeid, groupe.nom as groupenom, groupe_joueur.id_joueur, perso.nom, perso.race FROM groupe LEFT JOIN groupe_joueur ON groupe.id = groupe_joueur.id_groupe LEFT JOIN perso ON groupe_joueur.id_joueur = perso.ID WHERE groupe_joueur.leader = 'y' AND perso.race = '".$joueur->get_race()."'";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	if($row['groupenom'] == '') $row['groupenom'] = '-----';
	?>
	<li id="groupe_<?php echo $row['groupeid']; ?>" onclick="refresh('infos_groupe.php?id_groupe=<?php echo $row['groupeid']; ?>', 'infos_groupe');"><?php echo $row['groupeid'].' - '.$row['groupenom']; ?></li>
	<?php
}
?>
</ul>
<div id="infos_groupe" style="float : right;">
	Cliquez sur un groupe pour obtenir des informations
</div>