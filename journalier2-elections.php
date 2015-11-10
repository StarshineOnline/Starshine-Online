<?php
if (file_exists('root.php'))
  include_once('root.php');
if (isset($_SERVER['REMOTE_ADDR'])) die('Forbidden connection from '.$_SERVER['REMOTE_ADDR']);

include_once('journalier2-head.php');

//Groupes du forum
$groupe = array();
$groupe['barbare'][0] = 5;
$groupe['barbare'][1] = 16;
$groupe['barbare'][2] = 27;
$groupe['elfebois'][0] = 6;
$groupe['elfebois'][1] = 17;
$groupe['elfebois'][2] = 29;
$groupe['elfehaut'][0] = 7;
$groupe['elfehaut'][1] = 18;
$groupe['elfehaut'][2] = 30;
$groupe['humain'][0] = 8;
$groupe['humain'][1] = 19;
$groupe['humain'][2] = 31;
$groupe['humainnoir'][0] = 9;
$groupe['humainnoir'][1] = 20;
$groupe['humainnoir'][2] = 28;
$groupe['mortvivant'][0] = 10;
$groupe['mortvivant'][1] = 21;
$groupe['mortvivant'][2] = 32;
$groupe['nain'][0] = 11;
$groupe['nain'][1] = 22;
$groupe['nain'][2] = 33;
$groupe['orc'][0] = 12;
$groupe['orc'][1] = 23;
$groupe['orc'][2] = 34;
$groupe['scavenger'][0] = 13;
$groupe['scavenger'][1] = 24;
$groupe['scavenger'][2] = 35;
$groupe['troll'][0] = 14;
$groupe['troll'][1] = 25;
$groupe['troll'][2] = 36;
$groupe['vampire'][0] = 15;
$groupe['vampire'][1] = 26;
$groupe['vampire'][2] = 37;
//On regarde si une élection a lieu
$requete = "SELECT id, id_royaume, type FROM elections WHERE date = '".date("Y-m-d", time())."'";
$req = $db->query($requete);
$elections = Array();
//S'il y a une élection de prévue
if($db->num_rows > 0)
{
	require_once(root.'fonction/forum.inc.php');
	while($row = $db->read_assoc($req))
	{
		$requete = "SELECT race FROM royaume WHERE id = ".$row['id_royaume'];
		$req_n = $db->query($requete);
		$row_n = $db->read_assoc($req_n);
		$race = $row_n['race'];
		$royaumes[ $row['id_royaume'] ]["race"] = $race;
		if( $row["type"] == "nomination" )
		{
		  $requete = "SELECT id FROM perso WHERE rang_royaume = 6 AND race = '$race'";
		  $req_r = $db->query($requete);
		  $row_r = $db->read_assoc($req_r);
		  $id_roi = $row_r["id"];
		}
		$data = array();
		$legend = array();
		$label = array();
		if( $row["type"] == "nomination" )
		  $requete = "SELECT * FROM vote WHERE id_election = ".$row['id']." AND id_perso = $id_roi";
		else
		  $requete = "SELECT vote.id_candidat, COUNT(*) as count, perso.honneur FROM vote, perso WHERE id_election = ".$row['id']." AND perso.id = vote.id_candidat GROUP BY id_candidat ORDER BY count DESC, honneur DESC";
		$req_v = $db->query($requete);
		$i = 0;
		if($db->num_rows > 0)
		{
  		//Suppression de l'ancien roi
  		$requete = "UPDATE punbbusers SET group_id = ".$groupe[$race][2]." WHERE group_id = ".$groupe[$race][1];
  		$db_forum->query($requete);
		  //Groupe forum
  		$requete = "UPDATE perso SET rang_royaume = 7 WHERE (rang_royaume = 6 OR rang_royaume = 1) AND race = '$race'";
  		$db->query($requete);
  		// Résultat des votes
			while($row_v = $db->read_assoc($req_v))
			{
				$requete = "SELECT * FROM candidat WHERE id_perso = ".$row_v['id_candidat']." AND id_election = ".$row['id'];
				
				$req_c = $db->query($requete);
				$row_c = $db->read_assoc($req_c);
				//C'est le roi on l'active, et on met en place la prochaine élection
				if($i == 0)
				{
					//Ministres
					$requete = "UPDATE perso SET rang_royaume = 1 WHERE id = ".$row_c['id_ministre_economie']." OR id = ".$row_c['id_ministre_militaire'];
					$db->query($requete);	
					
					$req_m = "SELECT nom FROM perso WHERE id = ".$row_c['id_ministre_economie']." OR id = ".$row_c['id_ministre_militaire'];
					$req_m = $db->query($req_m);
					while($row_m = $db->read_assoc($req_m))
					{
						$requete = "UPDATE punbbusers SET group_id = ".$groupe[$race][2]." WHERE username = '".$row_m['nom']."'";
						$db_forum->query($requete);
					}
					
					//roi
					$requete = "UPDATE perso SET rang_royaume = 6 WHERE id = ".$row_v['id_candidat'];
					$db->query($requete);
					$requete = "UPDATE punbbusers SET group_id = ".$groupe[$race][1]." WHERE username = '".$row_c['nom']."'";
					$db_forum->query($requete);

					//Prochaine élection
					if($row_c['duree'] == 1 && date('d') > 12) $date_e = mktime(0, 0, 0, date("m") + 2, 1, date("Y"));
					else $date_e = mktime(0, 0, 0, date("m") + $row_c['duree'], 1, date("Y"));
					$election = new elections();
					$election->set_id_royaume($row['id_royaume']);
					$election->set_date( date("Y-m-d", $date_e) );
					$election->set_type($row_c['type']);
					$election->sauver();
					// Ministres
					$royaume = new royaume( $row['id_royaume'] );
					$royaume->set_ministre_economie( $row_c["id_ministre_economie"] );
					$royaume->set_ministre_militaire( $row_c["id_ministre_militaire"] );
					$royaume->sauver();
					
					// Message du forum
					$elections[ $row['id_royaume'] ]["prochain"] = "Prochaine ".($row_c['type']=="universel" ? "élection" : "nomination")
            ." le ".date("d / m / Y", $date_e).".";
				}
				$data[] = $row_v['count'];
				$legend[] = $row_c['nom'].'('.$row_v['count'].')';
				$label[] = $row_c['nom']."(".$row_v['count'].")\n%.1f%%";
				$i++;
			}

      // Création du graphe si c'est une élection
		  if( $row["type"] == "universel" )
		  {
  			$DataSet = new pData;
  			$DataSet->AddPoint($data,"Serie1");
  			$DataSet->AddPoint($legend,"Serie2");
  			$DataSet->AddAllSeries();
  			$DataSet->SetAbsciseLabelSerie("Serie2");
  
  			// Initialise the graph
  			$graph = new pChart(700, 400);
  			$graph->drawFilledRoundedRectangle(7,7,693,393,5,240,240,240);
  			$graph->drawRoundedRectangle(5,5,695,395,5,230,230,230);
  
  			// Draw the pie chart
  			$graph->setFontProperties("pChart/fonts/tahoma.ttf",8);
  			$graph->drawPieGraph($DataSet->GetData(),$DataSet->GetDataDescription(),315,210,200,PIE_LABELS,TRUE,50,20,5);
  			//$graph->drawPieLegend(590,15,$DataSet->GetData(),$DataSet->GetDataDescription(),250,250,250);
  			$graph->setFontProperties("pChart/fonts/tahoma.ttf",12);
  			$graph->drawTitle(50,22,'Elections du roi '.$Gtrad[$race].' du '.$date ,50,50,50,585);
  
  			$graph->Render('image/election_'.$race.'.png');
  			
				// Message du forum
				$elections[ $row['id_royaume'] ]["resultat"] = "[img]".BASE."image/election_$race.png[/img]";
      }
      else
      {
				// Message du forum
				$elections[ $row['id_royaume'] ]["resultat"] = "Nomination ".creer_cdn($row_c['nom']).".\n";
      }
		}
  	else // pas de votant
  	{
      // On garde le roi et les ministres et on crée une nouvelle élection universelle pour le mois prochain
			$date_e = mktime(0, 0, 0, date("m") + 1, 1, date("Y"));
			$election = new elections();
			$election->set_id_royaume($row['id_royaume']);
			$election->set_date( date("Y-m-d", $date_e) );
			$election->set_type("universel");
			$election->sauver();
			
			// Récupération du nom du roi
      $requete = "SELECT nom FROM perso WHERE rang_royaume = 6 AND race = '$race'";
		  $req_r = $db->query($requete);
		  $row_r = $db->read_assoc($req_r);
		  $nom_roi = $row_r["nom"];
			// Message du forum
			$elections[ $row['id_royaume'] ]["resultat"] = "$nom_roi reconduit pour un mois, suffrage universel.";
			$elections[ $row['id_royaume'] ]["prochain"] = "Prochaine élection le ".date("d / m / Y", $date_e).".";
    }
	}
}

//On regarde si une révolution a lieu
$requete = "SELECT id, id_royaume FROM revolution WHERE date = '".date("Y-m-d", time())."'";
$req = $db->query($requete);
//S'il y a une révolution de prévue
if($db->num_rows > 0)
{
	require_once(root.'fonction/forum.inc.php');
	while($row = $db->read_assoc($req))
	{
		$requete = "SELECT race FROM royaume WHERE id = ".$row['id_royaume'];
		$req_n = $db->query($requete);
		$row_n = $db->read_assoc($req_n);
		$race = $row_n['race'];
		$data = array();
		$legend = array();
		$label = array();
		$requete = "SELECT *, SUM(poid_vote) as count FROM vote_revolution WHERE id_revolution = ".$row['id']." GROUP BY pour ORDER BY count DESC";
		$req_v = $db->query($requete);
		$i = 0;
		if($db->num_rows > 0)
		{
		  $pour = $contre = 0;
			while($row_v = $db->read_assoc($req_v))
			{
				if($row_v['pour'] == 1)
				{
					$pour = $row_v['count'];
					$data[] = $row_v['count'];
					$legend[] = 'Pour ('.$row_v['count'].')';
					$label[] = 'Pour ('.$row_v['count'].")\n%.1f%%";
				}
				else
				{
					$contre = $row_v['count'];
					$data[] = $row_v['count'];
					$legend[] = 'Contre ('.$row_v['count'].')';
					$label[] = 'Contre ('.$row_v['count'].")\n%.1f%%";
				}
			}

			$DataSet = new pData;
			$DataSet->AddPoint($data,"Serie1");
			$DataSet->AddPoint($legend,"Serie2");
			$DataSet->AddAllSeries();
			$DataSet->SetAbsciseLabelSerie("Serie2");

			// Initialise the graph
			$graph = new pChart(700, 400);
			$graph->drawFilledRoundedRectangle(7,7,693,393,5,240,240,240);
			$graph->drawRoundedRectangle(5,5,695,395,5,230,230,230);

			// Draw the pie chart
			$graph->setFontProperties("pChart/fonts/tahoma.ttf",8);
			$graph->drawPieGraph($DataSet->GetData(),$DataSet->GetDataDescription(),315,210,200,PIE_LABELS,TRUE,50,20,5);
			//$graph->drawPieLegend(590,15,$DataSet->GetData(),$DataSet->GetDataDescription(),250,250,250);
			$graph->setFontProperties("pChart/fonts/tahoma.ttf",12);
			$graph->drawTitle(50,22,'Révolution du peuple '.$Gtrad[$race].' du '.$date ,50,50,50,585);

			$graph->Render('image/revolution_'.$race.'_'.date("Y-m-d").'.png');
			
			// Message du forum
			$elections[ $row['id_royaume'] ]["resultat"] .= "[img]".BASE."image/revolution_$race"."_".date("Y-m-d").".png[/img]\n";

			//On met en route la révolution si pour > contre
			if($pour > $contre)
			{
				//Suppression de l'ancien roi
				$requete = "UPDATE punbbusers SET group_id = ".$groupe[$race][2]." WHERE group_id = ".$groupe[$race][1];
				$db_forum->query($requete);
				$requete = "UPDATE perso SET rang_royaume = 7 WHERE (rang_royaume = 6 OR rang_royaume = 1) AND race = '".$race."'";
				$db->query($requete);
				// Supression des ministres
				$royaume = new royaume( $row['id_royaume'] );
				$royaume->set_ministre_economie( 0 );
				$royaume->set_ministre_militaire( 0 );
				$royaume->sauver();
				//On supprime la prochaine election
				$prochaine = elections::get_prochain_election($row["id_royaume"], true);
				$prochaine[0]->supprimer();
				//Mis en route de nouvelles élections pour le mois suivant
				if(date('d') > 12) $date_e = mktime(0, 0, 0, date("m") + 2, 1, date("Y"));
				else $date_e = mktime(0, 0, 0, date("m") + 1, 1, date("Y"));
				$election = new elections();
				$election->set_id_royaume($row['id_royaume']);
				$election->set_date( date("Y-m-d", $date_e) );
				$election->set_type('universel');
				$election->sauver();
  			// Message du forum
  			$elections[ $row['id_royaume'] ]["resultat"] .= "Le roi et ses ministres ont été destitués";
			  $elections[ $row['id_royaume'] ]["prochain"] = "Prochaine élection le ".date("d / m / Y", $date_e).".";
			}
			else
			{
    		// Récupération du nom du roi
        $requete = "SELECT nom FROM perso WHERE rang_royaume = 6 AND race = '$race'";
    	  $req_r = $db->query($requete);
    	  $row_r = $db->read_assoc($req_r);
    	  $nom_roi = $row_r["nom"];
  			$elections[ $row['id_royaume'] ]["resultat"] .= "$nom_roi reste roi.";
  			// Récupération de la date de la prochaine élection
        $prochaine = elections::get_prochain_election($row["id_royaume"], true);
        $date_e = explode('-', $prochaine[0]->get_date());
        $elections[ $row['id_royaume'] ]["prochain"] = "Prohaine ".($prochaine[0]->get_type()=="universel" ? "élection" : "nomination").
          " le ".$date_e[2]." / ".$date_e[1]." / ".$date_e[0].".";
      }
		}
	}
}

// Annonce sur le forum s'il y a eu des élections
if( count($elections) )
{
	// Début du message
	$msg_elec = "[i]Voici le résultat des élections ".creer_cdn(nom_mois_prec())." pour chaque royaume :[/i]\n\n";
	$msg_elec .= "(CTRL + F5 pour ceux qui ne voient pas les bonnes images)";
	// On parcours les royaume et donne l'évolution pour chacun
	$requete = "SELECT id,race FROM royaume WHERE race NOT LIKE ''";
	$req = $db->query($requete);
	while( $row = $db->read_assoc($req) )
	{
    $msg_elec .= "\n\n[b]".$Gtrad[ $row["race"] ]."[/b]\n";
    // Est-ce qu'il y a eut une élection ?
    if( array_key_exists($row["id"], $elections) )
    {
      $msg_elec .= $elections[$row["id"]]["resultat"]."\n".$elections[$row["id"]]["prochain"];
    }
    else // Pas de changement => on rappelle le roi et la date de la prochaine élection
    {
			// Récupération du nom du roi
      $requete = "SELECT nom FROM perso WHERE rang_royaume = 6 AND race = '".$row["race"]."'";
		  $req_r = $db->query($requete);
		  $row_r = $db->read_assoc($req_r);
      $msg_elec .= "Mandat de ".$row_r["nom"]." non terminé.\n";
      // Récupération de la prochaine élection
      $prochaine = elections::get_prochain_election($row["id"], true);
      $date_e = explode('-', $prochaine[0]->get_date());
      $msg_elec .= "Prohaine ".($prochaine[0]->get_type()=="universel" ? "élection" : "nomination").
        " le ".$date_e[2]." / ".$date_e[1]." / ".$date_e[0].".";
    }
  }
  // Création de l'annonce
  $id_sujet = creer_annonce("Élections pour le mois ".creer_cdn(nom_mois()), $msg_elec);
  annonce::envoyer('Élections pour le mois '.creer_cdn(nom_mois()).' '.date('Y').' : http://forum.starshine-online.com/viewtopic.php?pid='.$id_sujet, false);
}

?>