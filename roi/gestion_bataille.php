<?php
if (file_exists('../root.php'))
  include_once('../root.php');

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'inc/fp.php');


$perso = joueur::get_perso();
$royaume = new royaume($Trace[$perso->get_race()]['numrace']);
if( $perso->get_rang() != 6 && $royaume->get_ministre_militaire() != $perso->get_id() )
{
	/// @todo logguer triche
	exit;
}

$action = array_key_exists('action', $_GET) ? $_GET['action'] : null;
$cadre = $G_interf->creer_royaume();

switch($action)
{
case 'carte':
	$cadre->add_section('gest_bat_carte', new interf_carte($_GET['x'], $_GET['y'], interf_carte::aff_gest_batailles, 8, 'carte'));
	exit;
case 'suppr':  // Suppression d'une bataille
	$bataille = new bataille(sSQL($_GET['id']));
	$bataille->supprimer(true);
	break;
case 'debut':  // Début d'une bataille
	$bataille = new bataille(sSQL($_GET['id']));
	$bataille = new bataille($_GET['id_bataille']);
	$bataille->set_etat(1);
	$bataille->sauver();
	// Envoie des messages aux groupes participants
	/// @todo à modifier quand on aura le nouveau type de messagerie
	$groupes = $bataille->get_groupes();
	foreach($groupes as $groupe)
	{
		$titre = 'Mission pour la bataille : '.$bataille->get_nom();
		$message = 'Votre groupe a été affecté à une mission concernant la bataille : '.$bataille->get_nom().'[br]
		[bataille:'.$bataille->get_nom().']';
		//Si le groupe n'a pas deja son thread pour cette bataille
		if($groupe->get_id_thread() == 0)
		{
			$thread = new messagerie_thread(0, $groupe->get_id_groupe(), 0, $joueur->get_id(), 1, null, $titre);
			$thread->sauver();
			$messagerie = new messagerie($joueur->get_id(), $joueur->get_groupe());
			$messagerie->envoi_message($thread->id_thread, 0, $titre, $message, $groupe->get_id_groupe(), 1);
			$groupe->set_id_thread($thread->id_thread);
			$groupe->sauver();
		}
		else
		{
			$messagerie = new messagerie($joueur->get_id(), $joueur->get_groupe());
			$messagerie->envoi_message($groupe->get_id_thread(), 0, $titre, $message, $groupe->get_id_groupe(), 1);
		}
	}
	break;
case 'fermer':  // Fin d'une bataille
	$bataille = new bataille(sSQL($_GET['id']));
	$bataille->set_etat(2);
	$bataille->sauver();
	break;
case 'modifier': // Modifier une bataille
	$bataille = new bataille(sSQL($_GET['id']));
	$cadre->set_gestion( $G_interf->creer_modif_bataille($bataille) );
	$cadre->maj_tooltips();
	exit;
case 'nouveau': // Nouvelle bataille
	$bataille = new bataille();
	$cadre->set_gestion( $G_interf->creer_modif_bataille($bataille) );
	$cadre->maj_tooltips();
	exit;
case 'creer': // validation de la création ou modification
	$bataille = array_key_exists('id', $_GET) ? new bataille(sSQL($_GET['id'])) : new bataille();
	$bataille->set_nom( $_POST['nom'] );
	$bataille->set_description( $_POST['texte'] );
	$bataille->set_x( $_POST['x'] );
	$bataille->set_y( $_POST['y'] );
	$bataille->sauver();
	// Groupes
	/// @todo passer à l'objet
	$requete = "SELECT groupe.id as groupeid, groupe.nom as groupenom, groupe_joueur.id_joueur, perso.nom, perso.race FROM groupe LEFT JOIN groupe_joueur ON groupe.id = groupe_joueur.id_groupe LEFT JOIN perso ON groupe_joueur.id_joueur = perso.ID WHERE groupe_joueur.leader = 'y' AND perso.race = '".joueur::get_perso()->get_race()."'";
	$req = $db->query($requete);
	// On regarde tous les groupes possibles
	while($row = $db->read_assoc($req))
	{
		$bat_groupe = new bataille_groupe(0,0,$row['groupeid']);
		if( $bat_groupe->is_bataille() )
		{
			// on regarde si le groupe a été retiré de la bataille
			if( $bat_groupe->get_id_bataille() == $bataille->get_id() && !in_array($row['groupeid'], $_POST['groupes']) )
			{
				// On supprime les reperes associés au groupe, puis le groupe
				$bat_groupe->get_reperes();
				foreach ($bat_groupe->reperes as $repere)
					$repere->supprimer();
				$bat_groupe->supprimer();
			}
		}
		else if( in_array($row['groupeid'], $_POST['groupes']) )
		{
			$bataille_groupe = new bataille_groupe(0, $bataille->get_id(), $row['groupeid']);
			$bataille_groupe->sauver();
			//unset($bataille_groupe);  // <- utile ?
		}
	}
case 'gerer': // Gérer une bataille
	$bataille = new bataille(sSQL($_GET['id']));
	$cadre->set_gestion( $G_interf->creer_gerer_bataille($bataille) );
	$cadre->maj_tooltips();
	exit;
case 'suppr_repere':  // Suppression d'un repère
	$repere = new bataille_repere($_GET['id_repere']);
	$repere->supprimer(true);
	interf_alerte::enregistre(interf_alerte::msg_succes, 'Repère supprimé avec succès');
}

$cadre->set_gestion( $G_interf->creer_gest_batailles($royaume) );
$cadre->maj_tooltips();






exit;




function affiche_bataille($bataille)
{
	?>
	<div style="clear : both;"></div>
	<div id="bataille_<?php echo $bataille->get_id(); ?>" style="margin : 10px;">
		<fieldset style="float : left; height : 75px; padding : 5px;">
			<legend><?php echo ucwords($bataille->etat_texte()); ?></legend>
			<a href="#" onclick="affiche_bataille('gestion_bataille.php','id_bataille=<?php echo $bataille->get_id(); ?>&amp;info_bataille');">Gérer</a><br />
		<?php
		if($bataille->get_etat() == 0)
		{
			?>
			<a href="#" onclick="affiche_page('gestion_bataille_new.php?modif&id_bataille=<?php echo $bataille->get_id(); ?>');">Modifier</a><br />
			<a href="gestion_bataille.php?id_bataille=<?php echo $bataille->get_id(); ?>&amp;debut_bataille" onclick="return envoiInfo(this.href, 'bataille_<?php echo $bataille->get_id(); ?>');">Debuter</a><br />
			<?php
		}
		elseif($bataille->get_etat() == 1)
		{
			?>
			<a href="#" onclick="affiche_page('gestion_bataille_new.php?modif&id_bataille=<?php echo $bataille->get_id(); ?>');">Modifier</a><br />
			<a href="gestion_bataille.php?id_bataille=<?php echo $bataille->get_id(); ?>&amp;fin_bataille" onclick="return envoiInfo(this.href, 'bataille_<?php echo $bataille->get_id(); ?>');">Fermer</a><br />
			<?php
		}
		?>
		<a href="#" onclick="javascript: if(confirm('Voulez-vous vraiment supprimer cette bataille ?')) { return envoiInfo('gestion_bataille.php?id_bataille=<?php echo $bataille->get_id(); ?>&amp;suppr_bataille', 'bataille_<?php echo $bataille->get_id(); ?>');} else{return false;}" >Supprimer</a><br />
		</fieldset>
		<fieldset style="padding : 5px; width : 500px; float : left; min-height : 50px;">
			<legend><?php echo $bataille->get_nom(); ?></legend>
			<?php echo transform_texte($bataille->get_description()); ?><br />
		</fieldset>
	</div>
	<?php
}

function affiche_map($bataille)
{
	global $db, $royaume;
	$reperes = $bataille->get_reperes('tri_type');
	$map = new map($bataille->get_x(), $bataille->get_y(), 10, '../', false, 'low');
	if(array_key_exists('action', $reperes)) $map->set_repere($reperes['action']);
	if(array_key_exists('batiment', $reperes)) $map->set_batiment_ennemi($reperes['batiment']);
	$map->set_onclick("affichePopUp('gestion_bataille.php?id_bataille=".$bataille->get_id()."&amp;case=%%pos%%&amp;info_case=true');");
	$map->quadrillage = true;
	$map->onclick_status = true;
	$map->get_joueur($royaume->get_race(), false, true);
	$map->affiche();
}

if($joueur->get_rang_royaume() != 6 AND $joueur->get_id() != $royaume->get_ministre_militaire())
	echo '<p>Cette page vous est interdite</p>';
//Nouvelle bataille
elseif(array_key_exists('move_map', $_GET))
{
	echo "<div style='float:left;'>";
	if(array_key_exists('x', $_GET)) $x = $_GET['x'];
	else $x = $Trace[$royaume->get_race()]['spawn_x'];
	if(array_key_exists('y', $_GET)) $y = $_GET['y'];
	else $y = $Trace[$royaume->get_race()]['spawn_y'];
	$map = new map($x, $y, 8, '../', false, 'low');
	$map->set_onclick("envoiInfo('gestion_bataille.php?valide_choix_bataille&amp;case=%%pos%%', 'valide_choix_bataille');repere_bataille('%%id%%');");
	$map->onclick_status = true;	
	
	$map->affiche();
	echo "</div><div id='rose'>
	   <a id='rose_div_hg' href='gestion_bataille.php?move_map&x=".($x - 10)."&y=".($y - 10)."' onclick=\"return envoiInfo(this.href, 'choix_bataille');\"></a>
	   <a id='rose_div_h' href='gestion_bataille.php?move_map&x=".$x."&y=".($y - 10)."' onclick=\"return envoiInfo(this.href, 'choix_bataille');\"></a>
	   <a id='rose_div_hd' href='gestion_bataille.php?move_map&x=".($x + 10)."&y=".($y - 10)."' onclick=\"return envoiInfo(this.href, 'choix_bataille');\"></a>
	   <a id='rose_div_cg' href='gestion_bataille.php?move_map&x=".($x - 10)."&y=".$y."' onclick=\"return envoiInfo(this.href, 'choix_bataille');\"></a>
	   <a id='rose_div_c'  href='gestion_bataille.php?move_map&x=".$x."&y=".$y."' onclick=\"return envoiInfo(this.href, 'choix_bataille');\"></a>
	   <a id='rose_div_cd' href='gestion_bataille.php?move_map&x=".($x + 10)."&y=".$y."' onclick=\"return envoiInfo(this.href, 'choix_bataille');\"></a>
	   <a id='rose_div_bg' href='gestion_bataille.php?move_map&x=".($x - 10)."&y=".($y + 10)."' onclick=\"return envoiInfo(this.href, 'choix_bataille');\"></a>
	   <a id='rose_div_b' href='gestion_bataille.php?move_map&x=".$x."&y=".($y + 10)."' onclick=\"return envoiInfo(this.href, 'choix_bataille');\"></a>
	   <a id='rose_div_bd' href='gestion_bataille.php?move_map&x=".($x + 10)."&y=".($y + 10)."' onclick=\"return envoiInfo(this.href, 'choix_bataille');\"></a>
	</div>";
?>
		<div id="move_map_menu" style='margin-top:8px;'>

		<span style='float:left;margin-left:5px;width : 20px;'>X :</span><input type="text" id="go_x" style="width : 50px;" />
		<span style='float:left;margin-left:5px;width : 20px;'>Y :</span><input type="text" id="go_y" style="width : 50px;" />
		<input type="button" onclick="envoiInfo('gestion_bataille.php?move_map&x=' + $('#go_x').val() + '&y=' + $('#go_y').val(), 'choix_bataille');" value="Go !" style="width : 30px;" /><br />

		<div id="valide_choix_bataille" style='clear:both;'></div>
		</div>
		<?php

}
elseif(array_key_exists('valide_choix_bataille', $_GET))
{
	$coord = convert_in_coord($_GET['case']);
	
	echo "Vous avez séléctionné X :".$coord['x']."/ Y :".$coord['y']." comme centre de la bataille.
	<input type='hidden' name='x' id='x' value='".$coord['x']."' />
	<input type='hidden' name='y' id='y' value='".$coord['y']."' />
	<input type='hidden' name='case' id='case' value='".$_GET['case']."' />";
}
//Refresh de la carte de la bataille
elseif(array_key_exists('refresh_bataille', $_GET))
{
	$bataille = new bataille($_GET['refresh_bataille']);
	affiche_map($bataille);
}
//Information et modification sur une bataille
elseif(array_key_exists('info_bataille', $_GET))
{
		/*<div id="information_modif"></div>
		<div id="menu_bataille">
			<ul>
				<li><a href="gestion_bataille_groupe.php?id_bataille=<?php echo $bataille->get_id(); " onclick="return envoiInfo(this.href, 'information_onglet_bataille');">Groupes</a></li>
			</ul>
		</div>
		<div id="information_onglet_bataille">
		</div>
	</div>*/
}
//Information sur une case d'une bataille
elseif(array_key_exists('info_case', $_GET) OR array_key_exists('type', $_GET))
{
	$bataille = new bataille($_GET['id_bataille']);
	$case = $_GET['case'];
	$coord = convert_in_coord($case);
	if(array_key_exists('type', $_GET))
	{
		$type  = $_GET['id_type'][0];
		$repere = new bataille_repere();
		switch($type)
		{
			case 'a' :
				$repere->set_type('action');
			break;
			case 'b' :
				$repere->set_type('batiment');
			break;
		}
		$repere->set_id_bataille($_GET['id_bataille']);
		$repere->set_id_type(substr($_GET['id_type'], 1, strlen($_GET['id_type'])));
		$repere->set_x($coord['x']);
		$repere->set_y($coord['y']);
		$repere->sauver();
	}
	$reperes = $bataille->get_repere_by_coord($coord['x'], $coord['y']);
	?>
	<h4>Case <?php echo $coord['x']; ?> / <?php echo $coord['y']; ?></h4>
	<?php
	$batiment = false;
	$requete = "SELECT hp, nom FROM construction WHERE royaume = ".$royaume->get_id()." AND x = ".$coord['x']." AND y = ".$coord['y'];
	$req = $db->query($requete);
	if($db->num_rows($req) > 0)
	{
		$batiment = $db->read_assoc($req);
		echo '<div>'.$batiment['nom'].' - '.$batiment['hp'].' HP</div>';
	}
	$type_reperes = array();
	foreach($reperes as $repere)
	{
		$type_reperes[] = $repere->get_type();
		switch($repere->get_type())
		{
			case 'action' :
				$type = 'Mission';
			break;
			case 'batiment' :
				$type = 'Batiment Ennemi';
			break;
		}
		echo '
		<div id="repere'.$repere->get_id().'">'.$type.' : '.$repere->get_repere_type()->get_nom().' 
		<a href="gestion_bataille.php?id_repere='.$repere->get_id().'&del_repere" onclick="javascript: if(confirm(\'Voulez-vous vraiment supprimer ce repère ?\')) { $(\'#repere'.$repere->get_id().'\').remove(); return envoiInfo(this.href, \'information_modif\'); } else return false;">X</a><br />';
		if($repere->get_type() == 'action') echo '&nbsp;&nbsp;&nbsp;<i>'.count($repere->get_groupes()).' groupe(s)</i>';
		echo '
		</div>';
	}
	//Si ya moins de 2 repères
	if(count($reperes) < 10)
	{
		?>
		<hr /><br />Ajouter un nouveau repère ?<br />
		<select name="type" id="type">
<?php
			$bataille_royaume = new bataille_royaume($royaume->get_id());
			$types = $bataille_royaume->get_all_repere_type();
			print_r($types);
			foreach($types as $type)
			{
				?>
				<option value="a<?php echo $type->get_id(); ?>">Mission : <?php echo $type->get_nom(); ?> (<?php echo $type->get_description(); ?>)</option>
				<?php
			}

		if(!in_array('batiment', $type_reperes))
		{
			if(!$batiment)
			{
				$requete = "SELECT id, nom FROM batiment";
				$req = $db->query($requete);
				while($row = $db->read_assoc($req))
				{
					?>
					<option value="b<?php echo $row['id']; ?>">Batiment ennemi : <?php echo $row['nom']; ?></option>
					<?php
				}
			}
		}
		?>
		</select><input type="button" value="Ok" onclick="envoiInfo('gestion_bataille.php?id_type=' + $('#type').val() + '&amp;id_bataille=<?php echo $bataille->get_id(); ?>&amp;case=<?php echo $case; ?>&amp;type', 'popup_content');"/>
		<?php
	}
}
?>
