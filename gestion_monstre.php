<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
/**
*
* Permet l'affichage des informations d'une case en fonction du joueur.
*
*/
include_once(root.'inc/fp.php');
//Récupération des informations du personnage
$joueur = new perso($_SESSION['ID']);

if(array_key_exists('principale', $_GET))
{
	$joueur->set_pet_principale($_GET['principale']);
}
if(array_key_exists('supprimer', $_GET))
{
	$pet = new pet($_GET['supprimer']);
	$pet->supprimer();
}
if(array_key_exists('stop', $_GET))
{
	$buff = $joueur->get_buff('dressage');
	$buff->supprimer();
	refresh_perso();
	$joueur->get_buff();
}
?>
<fieldset>
	<legend>Mes créatures <?php echo $joueur->nb_pet().' / '.$joueur->get_max_pet(); ?></legend>
<?php
$pets = $joueur->get_pets();
if(count($pets) > 0)
{
	foreach($pets as $pet)
	{
		$pet->get_monstre();
		?>
			<h3><?php if($pet->get_principale() == 1) echo '<img src="image/icone/couronne.png">'; ?> <?php echo $pet->get_nom(); ?></h3>
			<img src="image/monstre/<?php echo $pet->monstre->get_lib(); ?>.png" style="float : left; margin : 0 10px 0 0;">
			Type : <?php echo $pet->monstre->get_nom(); ?><br />
			HP : <?php echo $pet->get_hp(); ?> / <?php echo $pet->monstre->get_hp(); ?><br />
			MP : <?php echo $pet->get_mp(); ?> / <?php echo $pet->get_mp_max(); ?><br />
			<a href="gestion_monstre.php?supprimer=<?php echo $pet->get_id(); ?>" onclick="if(confirm('Voulez vous vraiment supprimer cette créature ~<?php echo $pet->get_nom(); ?>~ ?')) return envoiInfo(this.href, 'information'); else return false;" title="Supprimer"><span class="del" style="float : right;"></span></a>
			<?php
			if($pet->monstre->get_sort_dressage() != '')
			{
				$sort = $pet->monstre->get_infos_sort_dressage();
				echo'<a href="gestion_monstre.php?sort='.$pet->get_id().'" onclick="return envoiInfo(this.href, \'information\');">'.$sort->get_nom().'</a><br />';
			}
			if($pet->get_principale() == 0) echo'<a href="gestion_monstre.php?principale='.$pet->get_id().'" onclick="return envoiInfo(this.href, \'information\');">Définir comme créature principale</a>';
	}
}
else
{
	echo '<h5>Vous n\'avez pas de monstre</h5>';
}

if($joueur->is_buff('dressage'))
{
	?>
	<h3>Vous dressez une créature</h3>
	<?php
	$monstre = new map_monstre($joueur->get_buff('dressage', 'effet2'));
	?>
	<a href="dressage.php?id=<?php echo $monstre->get_id(); ?>" onclick="return envoiInfo(this.href, 'information')">Continuez le dressage de "<?php echo $monstre->get_nom(); ?>" <span class="small">(10 PA)</span>.</a><br />
	<a href="gestion_monstre.php?stop" onclick="if(confirm('Voulez vous arrêtez le dressage ?')) return envoiInfo(this.href, 'information'); else return false;">Arretez le dressage de "<?php echo $monstre->get_nom(); ?>".</a><br />
	<?php
}
else
{
	?>
	<h3>Créatures que vous pouvez dresser sur la case <span class="xsmall">(niveau <= <?php echo $joueur->max_dresse(); ?>)</span></h3>
	<?php
	$W_requete = 'SELECT id, nom, type, hp, level FROM map_monstre WHERE (x = '.$joueur->get_x().') AND (y = '.$joueur->get_y().') AND level <= '.$joueur->max_dresse().' ORDER BY level ASC, nom ASC, id ASC';
	$W_query = $db->query($W_requete);
	//Affichage des infos des monstres
	if($db->num_rows > 0)
	{
		echo '<ul>';
		while($W_row = $db->read_array($W_query))
		{
			$W_nom = $W_row['nom'];
			$W_type = $W_row['type'];
			$W_ID = $W_row['id'];
			// on envois dans infojoueur.php -> ID du joueur et La position de la case ou il se trouve
			$image = $W2_row['lib'];
			if (file_exists('image/monstre/'.$image.'.png')) $image .= '.png';
			else $image .= '.gif';
			echo '
			<li style="clear:both;"><img src="image/monstre/'.$image.'" alt="'.$W2_row['nom'].'" style="vertical-align : middle;height:21px;float:left;width:21px;" /><span style="font-weight : '.$strong.';float:left;width:300px;margin-left:15px;">'.$W_nom.'</span>

				<span style="float:left;">';
				echo ' <a href="dressage.php?id='.$W_ID.'" onclick="return envoiInfo(this.href, \'information\')"><img src="image/icone/miniconedressage.png" alt="Dressage" title="Dresser cette créature (10 PA)" style="vertical-align : middle;" /></a>';
			echo '</span>
			</li>';
		}
		echo '</ul>';
	}
}
?>
</fieldset>