<?php // -*- tab-width:2; mode: php -*- 
if (file_exists('../root.php'))
  include_once('../root.php');

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'inc/fp.php');


$perso = joueur::get_perso();
$royaume = new royaume($Trace[$perso->get_race()]['numrace']);
$lieu = verif_ville($perso->get_x(), $perso->get_y(), $royaume->get_id());
if( !$lieu && $batiment = verif_batiment($perso->get_x(), $perso->get_y(), $royaume->get_id()) )
{
	if($batiment['type'] == 'fort' OR $batiment['type'] == 'bourg')
	{
		$bourg = new batiment($batiment['id_batiment']);
		$lieu = $bourg->has_bonus('royaume');
	}
}
if( ($perso->get_rang() != 6 && $perso->get_rang() != 1) || !$lieu || $perso->get_hp() <= 0 )
{
	/// @todo logguer triche
	exit;
}

$onglet = array_key_exists('onglet', $_GET) ? $_GET['onglet'] : 'batiments';
$action = array_key_exists('action', $_GET) ? $_GET['action'] : null;
$ajax = array_key_exists('ajax', $_GET) ? $_GET['ajax'] : 0;

$cadre = $G_interf->creer_royaume();

switch($action)
{
case 'renommer':
	$constr = new construction($_GET['id']);
  include_once(root.'interface/interf_bat_drap.class.php');
	$cadre->set_dialogue( new interf_batiment_nom($constr) );
	if( $ajax )
		exit;
	break;
case 'modif_nom':
	$constr = new construction($_GET['id']);
	$constr->set_nom( sSQL($_GET['nom'], SSQL_STRING) );
	$constr->sauver();
	break;
case 'suppr':
	$constr = new construction($_GET['id']);
	if( !$constr->get_buff('assiege') )
	{
		$constr->supprimer();
		journal_royaume::ecrire_perso('suppr_batiment', $constr->get_def(), $constr->get_nom(), $constr->get_id(), $constr->get_x(), $constr->get_y());
	}
	break;
}

if( $ajax == 2 )
{
		switch($onglet)
		{			
		case 'invasions':
			$cadre->add( $G_interf->creer_bd_invasions($royaume) );
			break;
		case 'constructions':
			$cadre->add( $G_interf->creer_bd_constructions($royaume) );
			break;
		case 'ads':
			$cadre->add( $G_interf->creer_bd_ads($royaume) );
			break;
		case 'drapeaux':
			$cadre->add( $G_interf->creer_bd_drapeaux($royaume) );
			break;
		case 'batiments':
			$cadre->add( $G_interf->creer_bd_batiments($royaume) );
			break;
		case 'depot':
			$cadre->add( $G_interf->creer_bd_depot($royaume) );
			break;
		}
}
else if( $ajax == 1 && array_key_exists('x', $_GET) && array_key_exists('y', $_GET) )
{
	$cadre->add_section('minicarte_'.$onglet, new interf_carte($_GET['x'], $_GET['y'], interf_carte::aff_gestion, 5, 'carte_'.$onglet));
}
else
{
	$cont = $cadre->set_gestion( new interf_bal_cont('div') );
	interf_alerte::aff_enregistres($cont);
	$cont->add( $G_interf->creer_bat_drap($royaume, $onglet, $_GET['x'], $_GET['y']) );
	$cadre->maj_tooltips();
}












exit;

if(true)
{
	$requete = $db->query("SELECT *, id FROM construction WHERE royaume = ".$royaume->get_id()." AND x <= 190 AND y <= 190 ORDER BY type, date_construction ASC");
	if ($db->num_rows($requete)>0)
	{
		echo "<fieldset>";	
		echo "<legend>Liste de vos bâtiments</legend>";	
		echo "<ul>";
		$boutique_class = 't1';		
		while($row = $db->read_assoc($requete))
		{
			$construction = new construction($row['id']);
			$batiment = $construction->get_batiment();

			$tmp = "HP - ".$construction->get_hp();
			echo "
			<li class='$boutique_class'  onclick=\"minimap(".$construction->get_x().",".$construction->get_y().")\">
				<span style='display:block;width:320px;float:left;'>
					<img src='../image/batiment_low/".$batiment->get_image()."_04.png' style='vertical-align : top;' title='".$construction->get_nom()."' /> ".$construction->get_nom();
			
			//On peut l'upragder si il y a un suivant
			if($batiment->get_suivant() && !$joueur->is_buff('debuff_rvr'))
			{
				$batiment_suivant = new batiment($batiment->get_suivant());
				
				if ($batiment_suivant->get_cond1() < (time() - $construction->get_date_construction()))
				{
					echo ' - <a href="construction.php?direction=up_construction&amp;id='.$row['id'].'" onclick="if(confirm(\'Voulez-vous upgrader ce '.$construction->get_nom().' ?\')) return envoiInfo(this.href, \'message_confirm\'); else return false;">Upgrader - '.$batiment_suivant->get_cout().' stars</a>';
				}
				else
				{
					$tmp = transform_sec_temp($batiment_suivant->get_cond1() - (time() - $construction->get_date_construction()));
					echo "<span style='font-style: italic ;font-size:8pt;'> - update possible dans $tmp</span>";
				}
			}
			echo "</span>";
				
			//my_dump($batiment);
			//my_dump($construction);
			echo "<span style='display:block;width:100px;float:left;'> X : ".$construction->get_x()." - Y : ".$construction->get_y()." </span>";
			$longueur = round(100 * ($construction->get_hp() / $batiment->get_hp()), 2);
			echo "<img style='display:block;width:100px;float:left;height:6px;padding-top:5px;' src='genere_barre_hp.php?longueur=".$longueur."' alt='".$construction->get_hp()." / ".$batiment->get_hp()."' title='".$construction->get_hp()." / ".$batiment->get_hp()."'>";

			// Possibilité ou non de supprimer un batiment attribuant des points des victoire lors de sa destruction		
			if($construction->get_point_victoire() > 0 && $construction->get_hp() >= $batiment->get_hp() * $G_prct_vie_suppression_pv )
			{
  				echo "<span style='display:block;width:30px;float:left;cursor:pointer;padding-left:4px;'>
  					<a onclick=\"if(confirm('Voulez-vous supprimer ce ".$construction->get_nom()." ?')) {return envoiInfo('construction.php?direction=suppr_construction&amp;id=".$construction->get_id()."', 'message_confirm');} else {return false;};\"><img src='../image/interface/croix_quitte.png' alt='suppression' title='Supprimer.'/></a>
  				</span>";
      		}
      		elseif($construction->get_point_victoire() > 0 && $construction->get_hp() < $batiment->get_hp() * $G_prct_vie_suppression_pv )
			{
  				echo "<span style='display:block;width:30px;float:left;cursor:pointer;padding-left:4px;'>
  					<img src='../image/interface/croix_quitte_gris.png'/ alt='suppression impossibe' title='Vous ne pouvez pas supprimer ce bâtiment si il a moins de ".floor($G_prct_vie_suppression_pv*100)."% de ses HP.'></span>";
      		}
      		
      		// Possibilité ou non de supprimer un batiment n'attribuant pas des points des victoire lors de sa destruction
      		elseif($construction->get_point_victoire() == 0 && $construction->get_hp() >= $batiment->get_hp() * $G_prct_vie_suppression_nopv )
      		{
      			echo "<span style='display:block;width:30px;float:left;cursor:pointer;padding-left:4px;'>
  					<a onclick=\"if(confirm('Voulez-vous supprimer ce ".$construction->get_nom()." ?')) {return envoiInfo('construction.php?direction=suppr_construction&amp;id=".$construction->get_id()."', 'message_confirm');} else {return false;};\"><img src='../image/interface/croix_quitte.png' alt='suppression' title='Supprimer.'/></a>
  				</span>";
      		}
      		else
      		{
      			echo "<span style='display:block;width:30px;float:left;cursor:pointer;padding-left:4px;'>
  					<img src='../image/interface/croix_quitte_gris.png'/ alt='suppression impossibe' title='Vous ne pouvez pas supprimer ce bâtiment si il a moins de ".floor($G_prct_vie_suppression_nopv*100)."% de ses HP.'></span>";
      		}
			echo "</li>";
			if ($boutique_class == 't1'){$boutique_class = 't2';}else{$boutique_class = 't1';}									
		}
		echo "</ul>";
		echo "</fieldset>";		
	}


}
elseif ($RAZ_ROYAUME)
{
	echo "<h5>Gestion impossible quand la capitale est mise à sac.</h5>";
	exit(0);
}
elseif($joueur->is_buff('debuff_rvr'))
{
	echo "<h5>RvR impossible pendant la trêve.</h5>";
}
elseif($_GET['direction'] == 'suppr_construction')
{
	$construction = new construction($_GET['id']);
	//On vérifie que c'est le bon royaume
	if($construction->get_royaume() == $royaume->get_id())
	{
		$batiment = new batiment($construction->get_id_batiment());
		//On vérifie que la construction a plus de 50% (ou 90%) de ses PV max
		if(
		( $construction->get_point_victoire() > 0 && $construction->get_hp() >= ($batiment->get_hp() * $G_prct_vie_suppression_pv) )
		or
		( $construction->get_point_victoire() == 0 && $construction->get_hp() >= ($batiment->get_hp() * $G_prct_vie_suppression_nopv) )
		)
		{
			$requete = "DELETE FROM construction WHERE id = ".sSQL($_GET['id']);
			if($db->query($requete))
			{
				echo '<h6>La construction a été correctement supprimée.</h6>';


				echo "<script type='text/javascript'>
					// <![CDATA[\n

					envoiInfo('construction.php','contenu_jeu');
						// ]]>
				  </script>";


				//On supprime un bourg au compteur
				if($row[0] == 'bourg')
				{
					supprime_bourg($row[1]);
				}
			}
			else echo "<h5>Erreur dans la requête.</h5>";
		}
		else echo "<h5>Impossible de détruire cette construction sans risquer la vie des ouvriers. Il faut d'abord la réparer.</h5>";
	}
	else echo "<h5>Cette construction ne vous appartient pas.</h5>";
}
elseif($_GET['direction'] == 'up_construction')
{
	$construction = new construction(sSQL($_GET['id']));
	$ancien_batiment = new batiment($construction->get_id_batiment());
	$batiment = new batiment($ancien_batiment->get_suivant());
	if($ancien_batiment->get_suivant() && $royaume->get_star() >= $batiment->get_cout() &&
		 $batiment->get_cond1() < (time() - $construction->get_date_construction()))
	{
		// On modifie la contruction
		$construction->set_id_batiment($batiment->get_id());
		$construction->set_nom($batiment->get_nom());
		$construction->set_date_construction(time());
		$construction->set_hp($construction->get_hp() + $batiment->get_hp() - $ancien_batiment->get_hp());
		$construction->set_point_victoire($batiment->get_point_victoire());
		$construction->sauver();
		
		$royaume->set_star($royaume->get_star() - $batiment->get_cout());
		$royaume->sauver();
		echo '<h6>La construction a été correctement upgradée</h6>';
		/*
		//On migre les anciens extracteurs vers le nouveau bourg
		$requete = "UPDATE construction SET rechargement = ".$construction_bourg->get_id()." WHERE type = 'mine' AND rechargement = ".sSQL($_GET['id']);
		$db->query($requete);
		$requete = "UPDATE placement SET rez = ".$construction_bourg->get_id()." WHERE type = 'mine' AND rez = ".sSQL($_GET['id']);
		$db->query($requete);
			*/
	}
	else
	{
		echo "<h5>Construction impossible à upgrader.</h5>";
	}


}
echo "</div>";
?>
