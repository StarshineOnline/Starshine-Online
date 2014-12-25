<?php
if (file_exists('../root.php'))
  include_once('../root.php');

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'inc/fp.php');


$perso = joueur::get_perso();
$royaume = new royaume($Trace[$perso->get_race()]['numrace']);
if( ($perso->get_rang() != 6 && $royaume->get_ministre_economie() != $perso->get_id()) || $royaume->is_raz() )
{
	/// @todo logguer triche
	exit;
}

$lieu = verif_ville($perso->get_x(), $perso->get_y(), $royaume->get_id());
if( !$lieu && $batiment = verif_batiment($perso->get_x(), $perso->get_y(), $royaume->get_id()) )
{
	if($batiment['type'] == 'fort' OR $batiment['type'] == 'bourg')
	{
		$bourg = new batiment($batiment['id_batiment']);
		$lieu = $bourg->has_bonus('royaume');
	}
}
$action = array_key_exists('action', $_GET) ? $_GET['action'] : null;

$cadre = $G_interf->creer_royaume();

if( $action && $lieu && $perso->get_hp()>0 )
{
	switch($action)
	{
	case 'creer':
		include_once(root.'interface/interf_echange_roy.class.php');
		/// @todo passer à l'objet
		$requete = "INSERT INTO echange_royaume(id_r1, id_r2, statut, date_fin) VALUES(".$Trace[$perso->get_race()]['numrace'].", ".sSQL($_GET['id'], SSQL_INTEGER).", 'creation', ".time().")";
		$db->query($requete);
		$echange = recup_echange($db->last_insert_id(), true);
		$cadre->set_dialogue( new interf_echg_roy_dlg($echange) );
		if( array_key_exists('ajax', $_GET) )
		 exit;
		break;
	case 'echanger':
		include_once(root.'interface/interf_echange_roy.class.php');
		$echange = recup_echange(sSQL($_GET['id'], SSQL_INTEGER), true);
		$cadre->set_dialogue( new interf_echg_roy_dlg($echange) );
		if( array_key_exists('ajax', $_GET) )
		 exit;
		break;
	case 'valider':
		$ide = sSQL($_GET['id'], SSQL_INTEGER);
		$echange = recup_echange($ide, true);
		$idr = $Trace[$perso->get_race()]['numrace'];
		//$echange = recup_echange($ide, true);
		switch($echange['statut'])
		{
		case 'creation':
			if($echange['id_r1'] != $idr)
				break;
			$ok = echange_royaume_ajout(sSQL($_GET['star'], SSQL_INTEGER), 'star', $ide, $idr);
			$ok &= echange_royaume_ajout(sSQL($_GET['food'], SSQL_INTEGER), 'food', $ide, $idr);
			$ok &= echange_royaume_ajout(sSQL($_GET['bois'], SSQL_INTEGER), 'bois', $ide, $idr);
			$ok &= echange_royaume_ajout(sSQL($_GET['eau'], SSQL_INTEGER), 'eau', $ide, $idr);
			$ok &= echange_royaume_ajout(sSQL($_GET['pierre'], SSQL_INTEGER), 'pierre', $ide, $idr);
			$ok &= echange_royaume_ajout(sSQL($_GET['sable'], SSQL_INTEGER), 'sable', $ide, $idr);
			$ok &= echange_royaume_ajout(sSQL($_GET['essence'], SSQL_INTEGER), 'essence', $ide, $idr);
			$ok &= echange_royaume_ajout(sSQL($_GET['charbon'], SSQL_INTEGER), 'charbon', $ide, $idr);
			if( $ok )
			{
				//On passe l'échange en mode proposition
				/// @todo passer à l'objet
				$requete = "UPDATE echange_royaume SET statut = 'proposition' WHERE id_echange = '".$ide."'";
				$db->query($requete);
				interf_alerte::enregistre(interf_alerte::msg_succes, 'Votre proposition a bien été envoyée.');
			}
			else
				interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez de ressources !');
			break;
		case 'proposition':
			$ide = sSQL($_GET['id'], SSQL_INTEGER);
			$echange = recup_echange($ide, true);
			$idr = $Trace[$perso->get_race()]['numrace'];
			if($echange['id_r2'] != $idr)
				break;
			$ok = echange_royaume_ajout(sSQL($_GET['star'], SSQL_INTEGER), 'star', $ide, $idr);
			$ok &= echange_royaume_ajout(sSQL($_GET['food'], SSQL_INTEGER), 'food', $ide, $idr);
			$ok &= echange_royaume_ajout(sSQL($_GET['bois'], SSQL_INTEGER), 'bois', $ide, $idr);
			$ok &= echange_royaume_ajout(sSQL($_GET['eau'], SSQL_INTEGER), 'eau', $ide, $idr);
			$ok &= echange_royaume_ajout(sSQL($_GET['pierre'], SSQL_INTEGER), 'pierre', $ide, $idr);
			$ok &= echange_royaume_ajout(sSQL($_GET['sable'], SSQL_INTEGER), 'sable', $ide, $idr);
			$ok &= echange_royaume_ajout(sSQL($_GET['essence'], SSQL_INTEGER), 'essence', $ide, $idr);
			$ok &= echange_royaume_ajout(sSQL($_GET['charbon'], SSQL_INTEGER), 'charbon', $ide, $idr);
			if( $ok )
			{
				//On passe l'échange en mode proposition
				/// @todo passer à l'objet
				$requete = "UPDATE echange_royaume SET statut = 'finalisation' WHERE id_echange = '".$ide."'";
				$db->query($requete);
				interf_alerte::enregistre(interf_alerte::msg_succes, 'Votre proposition a bien été envoyée.');
			}
			else
				interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez de ressources !');
			break;
		case 'finalisation':
			if($echange['id_r1'] != $idr)
				break;
			//Finalisation de l'échange donc vérifications
			$req_tmp = $db->query("SELECT date_fin FROM echange_royaume WHERE statut = 'fini' AND ((id_r2 = '".$Trace[$row['race']]['numrace']."' AND id_r1 = '".$Trace[$perso->get_race()]['numrace']."') OR (id_r1 = '".$Trace[$row['race']]['numrace']."' AND id_r2 = '".$Trace[$perso->get_race()]['numrace']."')) ORDER BY date_fin DESC LIMIT 0,1");
			$row_tmp = $db->read_assoc($req_tmp);
			$temps = $row_tmp['date_fin'] + (60*60*24*7) - time();
			$r1 = new royaume($echange['id_r1']);
			$r2 = new royaume($echange['id_r2']);
			if(verif_echange_both_royaume(sSQL($_GET['id'], SSQL_INTEGER), $r1->get_id(), $r2->get_id()) AND $temps < 0)
			{
				//On échange les ressources
				$r1->set_star($r1->get_star() + intval($echange['ressource']['star'][$r2->get_id()]['nombre']) - intval($echange['ressource']['star'][$r1->get_id()]['nombre']));
				$r2->set_star($r2->get_star() + intval($echange['ressource']['star'][$r1->get_id()]['nombre']) - intval($echange['ressource']['star'][$r2->get_id()]['nombre']));
				
				$r1->set_pierre($r1->get_pierre() + intval($echange['ressource']['pierre'][$r2->get_id()]['nombre']) - intval($echange['ressource']['pierre'][$r1->get_id()]['nombre']));
				$r2->set_pierre($r2->get_pierre() + intval($echange['ressource']['pierre'][$r1->get_id()]['nombre']) - intval($echange['ressource']['pierre'][$r2->get_id()]['nombre']));
				
				$r1->set_bois($r1->get_bois() + intval($echange['ressource']['bois'][$r2->get_id()]['nombre']) - intval($echange['ressource']['bois'][$r1->get_id()]['nombre']));
				$r2->set_bois($r2->get_bois() + intval($echange['ressource']['bois'][$r1->get_id()]['nombre']) - intval($echange['ressource']['bois'][$r2->get_id()]['nombre']));
				
				$r1->set_eau($r1->get_eau() + intval($echange['ressource']['eau'][$r2->get_id()]['nombre']) - intval($echange['ressource']['eau'][$r1->get_id()]['nombre']));
				$r2->set_eau($r2->get_eau() + intval($echange['ressource']['eau'][$r1->get_id()]['nombre']) - intval($echange['ressource']['eau'][$r2->get_id()]['nombre']));
				
				$r1->set_sable($r1->get_sable() + intval($echange['ressource']['sable'][$r2->get_id()]['nombre']) - intval($echange['ressource']['sable'][$r1->get_id()]['nombre']));
				$r2->set_sable($r2->get_sable() + intval($echange['ressource']['sable'][$r1->get_id()]['nombre']) - intval($echange['ressource']['sable'][$r2->get_id()]['nombre']));
				
				$r1->set_charbon($r1->get_charbon() + intval($echange['ressource']['charbon'][$r2->get_id()]['nombre']) - intval($echange['ressource']['charbon'][$r1->get_id()]['nombre']));
				$r2->set_charbon($r2->get_charbon() + intval($echange['ressource']['charbon'][$r1->get_id()]['nombre']) - intval($echange['ressource']['charbon'][$r2->get_id()]['nombre']));
				
				$r1->set_essence($r1->get_essence() + intval($echange['ressource']['essence'][$r2->get_id()]['nombre']) - intval($echange['ressource']['essence'][$r1->get_id()]['nombre']));
				$r2->set_essence($r2->get_essence() + intval($echange['ressource']['essence'][$r1->get_id()]['nombre']) - intval($echange['ressource']['essence'][$r2->get_id()]['nombre']));
				
				$r1->set_food($r1->get_food() + intval($echange['ressource']['food'][$r2->get_id()]['nombre']) - intval($echange['ressource']['food'][$r1->get_id()]['nombre']));
				$r2->set_food($r2->get_food() + intval($echange['ressource']['food'][$r1->get_id()]['nombre']) - intval($echange['ressource']['food'][$r2->get_id()]['nombre']));
				
				$r1->sauver();
				$r2->sauver();
				
				$donne_r1 = '';
				$donne_r2 = '';
				//Ce qu'a donné r1
				if(intval($echange['ressource']['star'][$r1->get_id()]['nombre']) > 0) $donne_r1 .= ', '.intval($echange['ressource']['star'][$r1->get_id()]['nombre']).' stars';
				if(intval($echange['ressource']['pierre'][$r1->get_id()]['nombre']) > 0) $donne_r1 .= ', '.intval($echange['ressource']['pierre'][$r1->get_id()]['nombre']).' pierre';
				if(intval($echange['ressource']['bois'][$r1->get_id()]['nombre']) > 0) $donne_r1 .= ', '.intval($echange['ressource']['bois'][$r1->get_id()]['nombre']).' bois';
				if(intval($echange['ressource']['eau'][$r1->get_id()]['nombre']) > 0) $donne_r1 .= ', '.intval($echange['ressource']['eau'][$r1->get_id()]['nombre']).' eau';
				if(intval($echange['ressource']['sable'][$r1->get_id()]['nombre']) > 0) $donne_r1 .= ', '.intval($echange['ressource']['sable'][$r1->get_id()]['nombre']).' sable';
				if(intval($echange['ressource']['charbon'][$r1->get_id()]['nombre']) > 0) $donne_r1 .= ', '.intval($echange['ressource']['charbon'][$r1->get_id()]['nombre']).' charbon';
				if(intval($echange['ressource']['essence'][$r1->get_id()]['nombre']) > 0) $donne_r1 .= ', '.intval($echange['ressource']['essence'][$r1->get_id()]['nombre']).' essence';
				if(intval($echange['ressource']['food'][$r1->get_id()]['nombre']) > 0) $donne_r1 .= ', '.intval($echange['ressource']['food'][$r1->get_id()]['nombre']).' food';
				//Ce qu'a donné r2
				if(intval($echange['ressource']['star'][$r2->get_id()]['nombre']) > 0) $donne_r2 .= ', '.intval($echange['ressource']['star'][$r2->get_id()]['nombre']).' stars';
				if(intval($echange['ressource']['pierre'][$r2->get_id()]['nombre']) > 0) $donne_r2 .= ', '.intval($echange['ressource']['pierre'][$r2->get_id()]['nombre']).' pierre';
				if(intval($echange['ressource']['bois'][$r2->get_id()]['nombre']) > 0) $donne_r2 .= ', '.intval($echange['ressource']['bois'][$r2->get_id()]['nombre']).' bois';
				if(intval($echange['ressource']['eau'][$r2->get_id()]['nombre']) > 0) $donne_r2 .= ', '.intval($echange['ressource']['eau'][$r2->get_id()]['nombre']).' eau';
				if(intval($echange['ressource']['sable'][$r2->get_id()]['nombre']) > 0) $donne_r2 .= ', '.intval($echange['ressource']['sable'][$r2->get_id()]['nombre']).' sable';
				if(intval($echange['ressource']['charbon'][$r2->get_id()]['nombre']) > 0) $donne_r2 .= ', '.intval($echange['ressource']['charbon'][$r2->get_id()]['nombre']).' charbon';
				if(intval($echange['ressource']['essence'][$r2->get_id()]['nombre']) > 0) $donne_r2 .= ', '.intval($echange['ressource']['essence'][$r2->get_id()]['nombre']).' essence';
				if(intval($echange['ressource']['food'][$r2->get_id()]['nombre']) > 0) $donne_r2 .= ', '.intval($echange['ressource']['food'][$r2->get_id()]['nombre']).' food';
				
				//On met le log dans la base
				$message_mail = $r1->get_race()." échange à ".$r2->get_race()."".$donne_r1." contre".$donne_r2;
				$log_admin = new log_admin();
				$log_admin->send($perso->get_id(), 'Echange royaume', $message_mail);
				
				//On met a jour le statut de l'échange
				//On passe l'échange en mode fini
				$requete = "UPDATE echange_royaume SET statut = 'fini', date_fin = '".time()."' WHERE id_echange = '".sSQL($_GET['id'], SSQL_INTEGER)."'";
				if($db->query($requete))
				{
					//C'est ok
					interf_alerte::enregistre(interf_alerte::msg_succes, 'L\'échange s\'est déroulé avec succès.');
					unset($echange);
				}
		}
		break;
	}
	break;
	case 'suppr':
		$ide = sSQL($_GET['id'], SSQL_INTEGER);
		$echange = recup_echange($ide, true);
		$idr = $Trace[$perso->get_race()]['numrace'];
		if( $echange['id_r2'] != $idr && $echange['id_r1'] != $idr )
			break;
		$requete = "UPDATE echange_royaume SET statut = 'annule', date_fin = '".time()."' WHERE id_echange = '".$ide."'";
		$db->query($requete);
	}
}

$cont = $cadre->set_gestion( new interf_bal_cont('div') );
interf_alerte::aff_enregistres($cont);
$cont->add( $G_interf->creer_echange_roy($lieu && $perso->get_hp()>0) );
$cadre->maj_tooltips();

?>