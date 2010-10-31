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
$joueur->check_perso();

if(array_key_exists('principale', $_GET))
{
	$joueur->set_pet_principale($_GET['principale']);
}
if(array_key_exists('supprimer', $_GET))
{
	$pet = new pet($_GET['supprimer']);
	//Si c'est la créature principale que l'on supprime
	if($pet->get_principale() == 1)
		$new_principale = true;
	$pet->supprimer();
	if($new_principale)
	{
		$pets = $joueur->get_pets();
		$joueur->set_pet_principale($pets[0]->get_id());
		unset($pets);
	}
}
if(array_key_exists('stop', $_GET))
{
	$buff = $joueur->get_buff('dressage');
	$buff->supprimer();
	refresh_perso();
	$joueur->get_buff();
}
if(array_key_exists('soin', $_GET))
{
	$pet = new pet($_GET['soin']);
	$pet->get_monstre();
	//Si on a assez de PV
	if($joueur->get_hp() > ceil($joueur->get_hp_max() / 10))
	{
		if($joueur->get_pa() >= 1)
		{
			if($pet->get_hp() < $pet->monstre->get_hp())
			{
				if($pet->get_hp() > 0)
				{
					$joueur->set_hp($joueur->get_hp() - ceil($joueur->get_hp_max() / 10));
					$joueur->set_pa($joueur->get_pa() - 1);
					$joueur->sauver();
					$des = de_soin(0, $joueur->soin_pet());
					foreach($des as $de)
					{
						$heal += rand(1, $de);
					}
					//Heal MP
					$heal_mp = floor($heal / 3);
					if($heal > ($pet->monstre->get_hp() - $pet->get_hp())) $heal = $pet->monstre->get_hp() - $pet->get_hp();
					if($heal_mp > ($pet->get_mp_max() - $pet->get_mp())) $heal_mp = $pet->get_mp_max() - $pet->get_mp();
					$pet->set_hp($pet->get_hp() + $heal);
					$pet->set_mp($pet->get_mp() + $heal_mp);
					echo '<h6>Vous soignez '.$pet->get_nom().' de '.$heal.' HP et '.$heal_mp.' MP</h6>';
					$pet->sauver();
					refresh_perso();
				}
				else echo '<h5>Votre créature est morte.</h5>';
			}
			else echo '<h5>Votre créature a toute sa vie.</h5>';
		}
		else echo '<h5>Vous n\'avez pas assez de PA.</h5>';
	}
	else echo '<h5>Vous n\'avez pas assez de HP.</h5>';
}
if(array_key_exists('name', $_GET))
{
	$pet = new pet($_GET['id']);
	$pet->set_nom($_GET['name']);
	$pet->sauver();
	echo '<h6>Nom de la créature modifié avec succès</h6>';
}
else
{
?>
<fieldset>
	<div id="monstre_message"></div>
	<legend>Mes créatures <?php echo $joueur->nb_pet().' / '.$joueur->get_max_pet(); ?></legend>
<?php
$pets = $joueur->get_pets(true);
if(count($pets) > 0)
{
	foreach($pets as $pet)
	{
		$pet->get_monstre();
		?>
	<div class="monstre">
		<h3><?php if($pet->get_principale() == 1) echo '<img src="image/icone/couronne.png">'; ?> <form action="" onsubmit="$('#monstre_message').load('gestion_monstre.php?id=<?php echo $pet->get_id(); ?>&name=' + $('#monstre_name_<?php echo $pet->get_id(); ?>').val()); return false;"><input type="text" class="monstre_name" id="monstre_name_<?php echo $pet->get_id(); ?>" value="<?php echo $pet->get_nom(); ?>" /></form></h3>
			<img src="image/monstre/<?php echo $pet->monstre->get_lib(); ?>.png">
			<div class="monstre_infos">
				<a href="gestion_monstre.php?soin=<?php echo $pet->get_id(); ?>" onclick="return envoiInfo(this.href, 'information');" title="Soigner, puissance : <?php echo $joueur->soin_pet(); ?>"><span style="float : right;">Soin <span class="xsmall">(vous coûte <?php echo ceil($joueur->get_hp_max() / 10); ?> HP / 1 PA)</span></span></a>
				Type : <?php echo $pet->monstre->get_nom(); ?><br />
				HP : <?php echo $pet->get_hp(); ?> / <?php echo $pet->monstre->get_hp(); ?><br />
				MP : <?php echo $pet->get_mp(); ?> / <?php echo $pet->get_mp_max(); ?><br />
				<a href="gestion_monstre.php?supprimer=<?php echo $pet->get_id(); ?>" onclick="if(confirm('Voulez vous vraiment supprimer cette créature ~<?php echo $pet->get_nom(); ?>~ ?')) return envoiInfo(this.href, 'information'); else return false;" title="Supprimer"><span class="del" style="float : right;"></span></a>
				<?php
				if($pet->monstre->get_sort_dressage() != '')
				{
					$sort = $pet->monstre->get_infos_sort_dressage();
					echo'<a href="sort.php?ID='.$sort->get_id().'&lanceur=monstre&id_pet='.$pet->get_id().'" onclick="return envoiInfo(this.href, \'information\');" onmouseover="'.make_overlib(description($sort->get_description(), $sort).'<br />MP créature : '.$sort->get_mp().'<br />PA personnage : '.$sort->get_pa()).'" onmouseout="return nd();">Utiliser le sort : '.$sort->get_nom().'</a><br />';
				}
				if($pet->get_principale() == 0) echo'<a href="gestion_monstre.php?principale='.$pet->get_id().'" onclick="return envoiInfo(this.href, \'information\');">Définir comme créature principale</a>';
		?>
			</div>
	</div>
		<?php
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
	<a href="dressage.php?id=<?php echo $monstre->get_id(); ?>" onclick="return envoiInfo(this.href, 'information')">Continuez le dressage de "<?php echo $monstre->get_nom(); ?>" <span class="small">(10 PA)</span>.</a>
	<a href="gestion_monstre.php?stop" style="float : right;" onclick="if(confirm('Voulez vous arrêtez le dressage ?')) return envoiInfo(this.href, 'information'); else return false;" title="Arrêter le dressage">x</a><br />
	<?php
}
else
{
	?>
	<h3>Créatures que vous pouvez dresser sur la case <span class="xsmall">(niveau <= <?php echo $joueur->max_dresse(); ?>)</span></h3>
	<?php
	$W_requete = 'SELECT mm.id, m.nom, mm.type, mm.hp, m.level FROM map_monstre mm, monstre m WHERE mm.type = m.id AND (mm.x = '.$joueur->get_x().') AND (mm.y = '.$joueur->get_y().') AND m.level <= '.$joueur->max_dresse().' ORDER BY level ASC, nom ASC, id ASC';
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
<?php
}
?>