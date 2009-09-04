<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
require('haut_roi.php');
include_once(root.'fonction/messagerie.inc.php');


if($joueur->get_rang_royaume() != 6)
	echo '<p>Cheater</p>';
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
//Information et modification sur une bataille
else
{
	echo "
	<div id='bataille_new'>
	<fieldset>
	<legend>Information</legend>
	<label>Nom : </label><span><input type='text' name='nom' id='nom' /></span>
	<label>Description : </label><span><textarea name='description' id='description'></textarea></span>
	<label>Action</label><span>";
	
$requete = "SELECT  id,	nom, description FROM bataille_repere_type";
$req = $db->query($requete);
echo "<select>";
while($row = $db->read_assoc($req))
{
	echo "<option value='".$row['id']."'>".$row['nom']."</option>";
}
echo "</select></span>
	
	</fieldset>";
	
$requete = "SELECT groupe.id as groupeid, groupe.nom as groupenom, groupe_joueur.id_joueur, perso.nom, perso.race FROM groupe LEFT JOIN groupe_joueur ON groupe.id = groupe_joueur.id_groupe LEFT JOIN perso ON groupe_joueur.id_joueur = perso.ID WHERE groupe_joueur.leader = 'y' AND perso.race = '".$joueur->get_race()."'";
$req = $db->query($requete);
echo "<fieldset>
<legend>Groupe Disponible</legend>
<ul>";
$class_groupe = 't1';
while($row = $db->read_assoc($req))
{
	if($row['groupenom'] == '') $row['groupenom'] = '-----';
	echo "<li id='ligroupe_".$row['groupeid']."' class='$class_groupe' onclick=\"select_groupe('".$row['groupeid']."')\">".$row['groupenom']."</li>";
	echo "<input type='hidden' id='groupe_".$row['groupeid']."' name='groupe_".$row['groupeid']."' value='0' />";
	if ($class_groupe == 't1'){$class_groupe = 't2';}else{$class_groupe = 't1';}	    

}
echo "</ul>";
echo "</fieldset>
</div>";
	$x = $Trace[$royaume->get_race()]['spawn_x'];
	$y = $Trace[$royaume->get_race()]['spawn_y'];
	$map = new map($x, $y, 8, '../', false, 'low');
	$map->onclick_status = true;
	
	$map->set_onclick("envoiInfo('gestion_bataille.php?valide_choix_bataille&amp;case=%%id%%', 'valide_choix_bataille');");
	$map->quadrillage = true;
	echo "<div id='choix_bataille'>";
	echo "<div style='float:left;'>";
	$map->affiche();
	echo "</div>";
	echo "
	
	<div id='rose'>
	   <a id='rose_div_hg'></a>
	   <a id='rose_div_h' href='gestion_bataille.php?move_map&x=$x&y=".($y - 10)."' onclick=\"return envoiInfo(this.href, 'choix_bataille');\"></a>
	   <a id='rose_div_hd'></a>
	   <a id='rose_div_cg'  href='gestion_bataille.php?move_map&x=".($x - 10)."&y=$y' onclick=\"return envoiInfo(this.href, 'choix_bataille');\"></a>
	   <a id='rose_div_c'></a>
	   <a id='rose_div_cd' href='gestion_bataille.php?move_map&x=".($x + 10)."&y=$y' onclick=\"return envoiInfo(this.href, 'choix_bataille');\"></a>
	   <a id='rose_div_bg'></a>
	   <a id='rose_div_b' href='gestion_bataille.php?move_map&x=".$x."&y=".($y + 10)."' onclick=\"return envoiInfo(this.href, 'choix_bataille');\"></a>
	   <a id='rose_div_bd'></a>
	</div>	";
	?>
		<div id="move_map_menu" style='margin-top:8px;'>


		<span style='float:left;margin-left:5px;width : 20px;'>X :</span><input type="text" id="go_x" style="width : 50px;" />
		<span style='float:left;margin-left:5px;width : 20px;'>Y :</span><input type="text" id="go_y" style="width : 50px;" />
		<input type="button" onclick="envoiInfo('gestion_bataille.php?move_map&x=' + $('go_x').value + '&y=' + $('go_y').value, 'choix_bataille');" value="Go !" style="width : 30px;" /><br />

		<div id="valide_choix_bataille" style='clear:both;'></div>
		</div>
	</div>
	<?php
	echo "
	<div style='clear : both;'></div>
	<input type='button' onclick=\"validation_bataille();\" value='Créer cette bataille' />
	</div>";

}
?>
