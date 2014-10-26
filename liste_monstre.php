<?php
if (file_exists('root.php'))
  include_once('root.php');
  
include_once(root.'inc/fp.php');

$interf_princ = $G_interf->creer_jeu();

$terrain =  array_key_exists('terrain', $_GET) ? $_GET['terrain'] : 'plaine';

if( $_GET['ajax'] == 2 )
{
	$interf_princ->add( $G_interf->creer_liste_monstres($terrain) );
}
else
{
	$dlg = $interf_princ->set_dialogue( new interf_dialogBS('Bestiaire', true, 'dlg_bestiaire') );
	//$dlg->add( new interf_bal_smpl('div', 'truc') );
	$dlg->add( $G_interf->creer_bestiaire($terrain) );
}





exit;

$tab = array('', 'Plaine', 'Forêt', 'Désert', 'Glace', 'Eau', 'Montagne', 'Marais', 'Route', '', '', 'Terre maudite');
if(array_key_exists('terrain', $_GET)) $terrain = $_GET['terrain'];
else $terrain = 1;
?>
<ul>
<?php
foreach($tab as $key => $t)
{
	if($t != '') echo '<li style="list-style-type : none; float : left; margin : 0 3px 0 3px;"><a href="liste_monstre.php?terrain='.$key.'" onclick="return envoiInfo(this.href, \'popup_content\');">'.$t.'</a></li>';
}
?>
</ul>
	<?php
	$i = 0;
	$requete = "SELECT lib, terrain, nom, level FROM monstre WHERE affiche = 'y' AND (terrain = '".$terrain."' OR terrain LIKE '".$terrain.";%' OR terrain LIKE '%;".$terrain."' OR terrain LIKE '%;".$terrain.";%') ORDER BY level ASC, xp ASC";
	$req = $db->query($requete);
	echo "<div id='bestiaire'>";
	echo "<ul>";
	while($row = $db->read_array($req))
	{
		$image = $row['lib'];
		$terrain = explode(';', $row['terrain']);
		$type_terrain = array();
		foreach($terrain as $t)
		{
			$type_terrain[] = $tab[$t];
		}
		$type_terrain = implode(', ', $type_terrain);
		if (file_exists('image/monstre/'.$image.'.png')) $image .= '.png';
		else $image .= '.gif';
		echo '
	<li>
		<span style="width:50px;">
			<img src="image/monstre/'.$image.'" />
		</span>
		<span style="width:170px;">
			'.$row['nom'].'
		</span>
		<span style="width:30px;">
			'.$row['level'].'
		</span>
		<span style="width:425px;">
			'.$type_terrain.'
		</span>
	</li>';
		$i++;
	}
	echo "</ul></div>";

	?>
