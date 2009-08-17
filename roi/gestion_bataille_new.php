<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
require('haut_roi.php');
include_once(root.'fonction/messagerie.inc.php');


function affiche_map($bataille)
{
	global $db;
	$map = new map($x, $y, 5, '../', false, 'low');
	print_r($bataille->reperes);
	if(array_key_exists('action', $bataille->reperes)) $map->set_repere($bataille->reperes['action']);
	if(array_key_exists('batiment', $bataille->reperes)) $map->set_batiment_ennemi($bataille->reperes['batiment']);
	$map->set_onclick("affichePopUp('gestion_bataille.php?id_bataille=".$bataille->id."&amp;case=%%ID%%&amp;info_case');");
	$map->quadrillage = true;
	$map->affiche();
}

if($joueur->get_rang_royaume() != 6)
	echo '<p>Cheater</p>';
//Nouvelle bataille
elseif(array_key_exists('move_map', $_GET))
{
	if(array_key_exists('x', $_GET)) $x = $_GET['x'];
	else $x = $Trace[$royaume->get_race()]['spawn_x'];
	if(array_key_exists('y', $_GET)) $y = $_GET['y'];
	else $y = $Trace[$royaume->get_race()]['spawn_y'];
	$map = new map($x, $y, 8, '../', false, 'low');
	$map->set_onclick("envoiInfo('gestion_bataille.php?valide_choix_bataille&amp;case=%%ID%%', 'valide_choix_bataille');");
	$map->quadrillage = true;
	?>
		<div id="move_map_menu">
		<a href="gestion_bataille.php?move_map&x=<?php echo $x; ?>&y=<?php echo ($y - 10); ?>" onclick="return envoiInfo(this.href, 'choix_bataille');">Haut</a>
		<a href="gestion_bataille.php?move_map&x=<?php echo $x; ?>&y=<?php echo ($y + 10); ?>" onclick="return envoiInfo(this.href, 'choix_bataille');">Bas</a>
		<a href="gestion_bataille.php?move_map&x=<?php echo ($x - 10); ?>&y=<?php echo $y; ?>" onclick="return envoiInfo(this.href, 'choix_bataille');">Gauche</a>
		<a href="gestion_bataille.php?move_map&x=<?php echo ($x + 10); ?>&y=<?php echo $y; ?>" onclick="return envoiInfo(this.href, 'choix_bataille');">Droite</a><br />
		X : <input type="text" id="go_x" style="width : 30px;" /> / Y : <input type="text" id="go_y" style="width : 30px;" /> <input type="button" onclick="envoiInfo('gestion_bataille.php?move_map&x=' + $('go_x').value + '&y=' + $('go_y').value, 'choix_bataille');" value="Go !" /><br />
		<div id="valide_choix_bataille"></div>
		</div>
	
	<?php
	$map->affiche();
}
elseif(array_key_exists('valide_choix_bataille', $_GET))
{
	$coord = convert_in_coord($_GET['case']);
	?>
	Vous avez séléctionné X : <?php echo $coord['x']; ?> / Y : <?php echo $coord['y']; ?> comme centre de la bataille.
	<input type="hidden" name="x" id="x" value="<?php echo $coord['x']; ?>" />
	<input type="hidden" name="y" id="y" value="<?php echo $coord['y']; ?>" />
	<?php
}
//Nouvelle bataille etape 2 => Création
elseif(array_key_exists('new2', $_GET))
{
	include_once(root.'roi/gestion_bataille_menu.php');
	$bataille = new bataille();
	$bataille->nom = $_GET['nom'];
	$bataille->description = $_GET['description'];
	$bataille->id_royaume = $royaume->get_id();
	$bataille->etat = 0;
	$bataille->x = $_GET['x'];
	$bataille->y = $_GET['y'];
	$bataille->sauver();
	?>
	Bataille créée avec succès<br />
	<?php
}
//Refresh de la carte de la bataille
elseif(array_key_exists('refresh_bataille', $_GET))
{
	$bataille = new bataille($_GET['refresh_bataille']);
	affiche_map($bataille);
}
//Information et modification sur une bataille
else
{
	echo "
	<div style='float:left;width:500px;'>
	<fieldset>
	<legend>Information</legend>
	Nom : <input type='text' name='nom' id='nom' /><br />
	Description :<br />
	<textarea name='description' id='description'></textarea><br />
	</fieldset>";

$requete = "SELECT groupe.id as groupeid, groupe.nom as groupenom, groupe_joueur.id_joueur, perso.nom, perso.race FROM groupe LEFT JOIN groupe_joueur ON groupe.id = groupe_joueur.id_groupe LEFT JOIN perso ON groupe_joueur.id_joueur = perso.ID WHERE groupe_joueur.leader = 'y' AND perso.race = '".$joueur->get_race()."'";
$req = $db->query($requete);
echo "<fieldset>
<legend>Groupe Disponible</legend>
<ul>";
while($row = $db->read_assoc($req))
{
	if($row['groupenom'] == '') $row['groupenom'] = '-----';
	echo "<li id='groupe_".$row['groupeid']."' onclick='refresh('infos_groupe.php?id_groupe=".$row['groupeid']."', 'infos_groupe');'>".$row['groupeid']." - ".$row['groupenom']."</li>";
}

echo "</ul>";
echo "</fieldset>
</div>";
	$x = $Trace[$royaume->get_race()]['spawn_x'];
	$y = $Trace[$royaume->get_race()]['spawn_y'];
	$map = new map($x, $y, 8, '../', false, 'low');
	$map->set_onclick("envoiInfo('gestion_bataille.php?valide_choix_bataille&amp;case=%%ID%%', 'valide_choix_bataille');");
	$map->quadrillage = true;
	echo "<div id='choix_bataille' style='float : left;'>";
	echo "
		<div id='move_map_menu'>
		<a href='gestion_bataille_new.php?move_map&x=$x&y=".($y - 10)."' onclick=\"return envoiInfo(this.href, 'choix_bataille');\">Haut</a>
		<a href='gestion_bataille_new.php?move_map&x=$x&y=".($y + 10)."' onclick=\"return envoiInfo(this.href, 'choix_bataille');\">Bas</a>
		<a href='gestion_bataille_new.php?move_map&x=".($x - 10)."&y=$y' onclick=\"return envoiInfo(this.href, 'choix_bataille');\">Gauche</a>
		<a href='gestion_bataille_new.php?move_map&x=".($x + 10)."&y=$y' onclick=\"return envoiInfo(this.href, 'choix_bataille');\">Droite</a><br />
		X : <input type='text' id='go_x' style='width : 30px;' /> / Y : <input type='text' id='go_y' style='width : 30px;' /> <input type='button' onclick=\"envoiInfo('gestion_bataille_new.php?move_map&x=' + $('go_x').value + '&y=' + $('go_y').value, 'choix_bataille');\" value='Go !' /><br />
		<div id='valide_choix_bataille'></div>
		</div>";
	$map->affiche();
	echo "	</div>
	</div>
	<div style='clear : both;'></div>
	<input type='button' onclick=\"description = $('description').value.replace(new RegExp('\n', 'gi'), '[br]'); envoiInfoPost('gestion_bataille_new.php?nom=' + $('nom').value + '&amp;description=' + description + '&amp;x=' + $('x').value + '&amp;y=' + $('y').value + '&amp;new2', 'conteneur');\" value='Créer cette bataille' />
	</div>";

}
?>
