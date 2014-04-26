<?php // -*- php -*-
if (file_exists('root.php'))
  include_once('root.php');

/**
*
* Permet l'affichage des informations d'une case en fonction du joueur.
*
*/
include_once(root.'inc/fp.php');
//Récupération des informations du personnage
$joueur = new perso($_SESSION['ID']);
verif_mort($joueur, 1);
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
	$debugs = 0;
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
					$msg = '';
					foreach($des as $de)
					{
						$rnd = rand(1, $de);
						$heal += $rnd;
						if ($msg == '') $msg = 'Soin: jet de ';
						else $msg .= ' + ';
						$msg .= "d${de}($rnd)";
					}
					print_debug($msg.'<br/>');
					//Heal MP
					$heal_mp = floor($heal / 3);
					if($heal > ($pet->monstre->get_hp() - $pet->get_hp())) $heal = $pet->monstre->get_hp() - $pet->get_hp();
					if($heal_mp > ($pet->get_mp_max() - $pet->get_mp())) $heal_mp = $pet->get_mp_max() - $pet->get_mp();
					$pet->set_hp($pet->get_hp() + $heal);
					$pet->set_mp($pet->get_mp() + $heal_mp);
					$augmentation = augmentation_competence('dressage', $joueur, 5);
					if ($augmentation[1] == 1) $joueur->set_dressage($augmentation[0]);
					$joueur->sauver();
					$pet->sauver();
					refresh_perso();
					echo '<h6>Vous soignez '.$pet->get_nom().' de '.$heal.' HP et '.$heal_mp.' MP <a onclick="for (i=0; i<'.$debugs.'; i++) {if(document.getElementById(\'debug\' + i).style.display == \'inline\') document.getElementById(\'debug\' + i).style.display = \'none\'; else document.getElementById(\'debug\' + i).style.display = \'inline\';}"><img src="image/interface/debug.png" alt="Debug" Title="Débug pour voir en détail le lancement du sort" style="vertical-align : middle;cursor:pointer;" /></a></h6>';
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
	$newPetName = $_GET['name'];
	if($newPetName != '')
	{
		$pet = new pet($_GET['id']);
		$pet->set_nom($newPetName);
		$pet->sauver();
		echo '<h6>Nom de la créature modifié avec succès.</h6>';
	}
	else
	{
		echo '<h5>Nom de la créature incorrect.</h5>';
	}
}
else
{
	$pets = $joueur->get_pets(true);
?>
	<fieldset>
		<legend>Mes créatures <?php echo $joueur->nb_pet(); ?> / <?php echo $joueur->get_max_pet(); ?></legend>
		<?php if(count($pets) > 0): ?>
			<?php foreach($pets as $pet): ?>
				<?php $pet->get_monstre(); ?>
				<div class="monstre">
					<h3>
						<?php if($pet->get_principale() == 1): ?>
							<img class="left" src="image/icone/couronne.png" />
							<form action="" onsubmit="$('#loading_information').load('gestion_monstre.php?id=<?php echo $pet->get_id(); ?>&name=' + $('#monstre_name_<?php echo $pet->get_id(); ?>').val(), function(){$(this).show().delay(1500).fadeOut(1000); $('#monstre_name_<?php echo $pet->get_id(); ?>').blur();}); return false;">
								<input type="text" class="monstre_name not_focused" id="monstre_name_<?php echo $pet->get_id(); ?>" value="<?php echo htmlspecialchars($pet->get_nom()); ?>" onfocus="$(this).removeClass('not_focused');" onblur="$(this).addClass('not_focused');" />
								<button class="no_style" type="button" onclick="$('#monstre_name_<?php echo $pet->get_id(); ?>').focus();" title="Modifier le nom de votre créature"><img src="image/edit.png" alt="edit" /></button>
								<button class="no_style" type="submit" title="Valider la modification"><img style="width:16px;" src="image/valid.png" alt="submit" /></button>
							</form>
						<?php endif ?>
					</h3>
					<img class="left" src="image/monstre/<?php echo $pet->monstre->get_lib(); ?>.png" />
					<div class="monstre_infos">
						<a href="gestion_monstre.php?soin=<?php echo $pet->get_id(); ?>" onclick="return envoiInfo(this.href, 'information');" title="Soigner, puissance : <?php echo $joueur->soin_pet(); ?>"><span style="float : right;">Soin <span class="xsmall">(vous coûte <?php echo ceil($joueur->get_hp_max() / 10); ?> HP / 1 PA)</span></span></a>
						Type : <?php echo htmlspecialchars($pet->monstre->get_nom()); ?><br />
						HP : <?php echo $pet->get_hp(); ?> / <?php echo $pet->monstre->get_hp(); ?><br />
						MP : <?php echo $pet->get_mp(); ?> / <?php echo $pet->get_mp_max(); ?><br />
						<a href="gestion_monstre.php?supprimer=<?php echo $pet->get_id(); ?>" onclick="if(confirm(<?php echo htmlspecialchars(json_encode('Voulez-vous vraiment supprimer cette créature ~'.$pet->get_nom().'~ ?')); ?>)) return envoiInfo(this.href, 'information'); else return false;" title="Supprimer"><span class="del" style="float : right;"></span></a>
						<?php
						if($pet->monstre->get_sort_dressage() != '')
						{
							$sort = $pet->monstre->get_infos_sort_dressage();
							$sortmp = $sort->get_mp();
							if($joueur->is_buff('buff_concentration', true))
								$sortmp = ceil($sortmp * (1 - ($joueur->get_buff('buff_concentration','effet') / 100)));
							echo'<a href="sort.php?ID='.$sort->get_id().'&lanceur=monstre&id_pet='.$pet->get_id().'" onclick="return envoiInfo(this.href, \'information\');" onmouseover="'.make_overlib(description($sort->get_description(), $sort).'<br />MP créature : '.$sortmp.'<br />PA personnage : '.$sort->get_pa()).'" onmouseout="return nd();">Utiliser le sort : '.$sort->get_nom().'</a><br />';
							echo'<a href="sort.php?ID='.$sort->get_id().'&lanceur=monstre&id_pet='.$pet->get_id().'&groupe=yes" onclick="return envoiInfo(this.href, \'information\');" onmouseover="'.make_overlib(description($sort->get_description(), $sort).'<br />MP créature : '.ceil($sortmp * 1.5).'<br />PA personnage : '.$sort->get_pa()).'" onmouseout="return nd();">Utiliser le sort : '.$sort->get_nom().' (en groupe)</a><br />';
						}
						if($pet->get_principale() == 0)
							echo'<a href="gestion_monstre.php?principale='.$pet->get_id().'" onclick="return envoiInfo(this.href, \'information\');">Définir comme créature principale</a>';
						?>
					</div>
				</div>
			<?php endforeach ?>
		<?php else: ?>
			<h5>Vous n'avez pas de monstre.</h5>
		<?php endif ?>
		
		<?php
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
			$W_requete = '
				SELECT mm.id, m.nom, mm.hp, m.level, m.lib
				FROM map_monstre mm INNER JOIN monstre m ON mm.type = m.id
				WHERE
					mm.x = '.$joueur->get_x().' AND mm.y = '.$joueur->get_y().'
					AND m.affiche != \'h\' AND m.level <= '.$joueur->max_dresse().'
				ORDER BY level ASC, nom ASC, id ASC
			';
			$W_query = $db->query($W_requete);
			//Affichage des infos des monstres
			if($db->num_rows > 0)
			{
				echo '<ul>';
				while($W_row = $db->read_array($W_query))
				{
					$W_ID = $W_row['id'];
					$W_nom = $W_row['nom'];
					// on envois dans infojoueur.php -> ID du joueur et La position de la case ou il se trouve
					$image = $W_row['lib'];
					if (file_exists('image/monstre/'.$image.'.png')) $image .= '.png';
					else $image .= '.gif';
					echo '
					<li style="clear:both;"><img src="image/monstre/'.$image.'" alt="'.$W_nom.'" style="vertical-align : middle;height:21px;float:left;width:21px;" /><span style="float:left;width:300px;margin-left:15px;">'.$W_nom.'</span>

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
