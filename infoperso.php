<?php
{//-- Initialisation
	require_once('inc/fp.php');
	if(!isset($joueur)) { $joueur = recupperso($_SESSION["ID"]); }; 		//-- Récupération du tableau contenant toutes les informations relatives au joueur
	
	require_once("levelup.php"); 				//-- Dans le cas ou le joueur a pris un level on traite son level up.
}
{//-- Javascript
	echo "<script type='text/javascript'>
			// <![CDATA[\n";
	{//-- cancelBuff(buff_id)
		echo "	function cancelBuff(buff_id, buff_nom)
				{
					if(confirm('Voulez vous supprimer '+ buff_nom +' ?')) 
					{
						envoiInfo('suppbuff.php?id='+ buff_id, 'perso');
					}
				}";
	}
	echo "	// ]]>
		  </script>";
}


{//-- PA, HP, MP, XP, ...
	echo "<div id='infos_perso'>"; 
	//--  inclusion de la rosace des vents.
	include_once("deplacementjeu.php");

	echo " <div id='joueur_PA' style='background:transparent url(".genere_image_pa($joueur).") center;'>".$joueur["pa"]." / $G_PA_max</div>";
	echo " <div id='joueur_HP' style='background:transparent url(".genere_image_hp($joueur).") center;'>".$joueur["hp"]." / ".$joueur["hp_max"]."</div>";
	echo " <div id='joueur_MP' style='background:transparent url(".genere_image_mp($joueur).") center;'>".$joueur["mp"]." / ".$joueur["mp_max"]."</div>";
	echo " <div id='joueur_XP' style='background:transparent url(".genere_image_exp($joueur["exp"], prochain_level($joueur["level"]), progression_level(level_courant($joueur["exp"]))).") center;' title='".progression_level(level_courant($joueur["exp"]))." % (".number_format($joueur["exp"], 0, ",", ".")." / ".number_format(prochain_level($joueur["level"]), 0, ",", ".").")'></div>";
	echo " <div id='joueur_PO'>".$joueur["star"]."</div>";
	echo " <div id='joueur_PH'>".$joueur["honneur"]."</div>";
	echo " <div id='joueur_Psso' onclick=\"envoiInfo('point_sso.php', 'information');\" title=\"Vous avez ".$joueur["point_sso"]." point(s) shine en r&eacute;serve.\"></div>";
	
	//-- Index, Forums, Exit, Options
	echo " <div id='joueur_onglets'>
			<div id='joueur_onglets_index' title=\"Retour &agrave; l&apos;index\" onclick=\"document.location.href='site_index.php';\">index</div>
	 		<div id='joueur_onglets_forums' title='Acc&eacute;der aux forums' onclick=\"document.location.href='http://forum.starshine-online.com/';\">forums</div>
	 		<div id='joueur_onglets_options' title='Modifier vos options' onclick=\"document.location.href='option.php';\">options</div>
	 	    <div id='joueur_onglets_exit' title='Se déconnecter' onclick=\"if(confirm('Voulez vous déconnecter ?')) { document.location.href='index.php?deco=ok'; };\">exit</div>
	 	   </div>";

	echo "</div>";
}
{//-- Buffs, Grade, Pseudo
	echo "<div id='joueur_buffs_nom'>";
	echo " <div id='joueur_nom' onclick=\"envoiInfo('personnage.php', 'information');\" title=\"Acc&eacute; &agrave la fiche de votre personnage\">".ucwords($joueur["grade"])." ".ucwords($joueur["nom"])." ".ucwords($Gtrad[$joueur["race"]])."<br />(".ucwords($joueur["classe"]).") - niv.".$joueur["level"]."</div>
	<br />";
	echo " <div id='buff_list'>
			<ul>";
		//print_r($joueur["buff"]);
		if(count($joueur["buff"]) > 0)
		{
			foreach($joueur["buff"] as $buff)
			{//-- Listing des buffs
				$overlib = str_replace("'", "\'", trim("<ul><li class='overlib_titres'>".$buff["nom"]."</li><li>".description($buff["description"], $buff)."</li><li>Durée ".transform_sec_temp($buff["fin"] - time())."</li><li class='overlib_infos'>(double-cliquer pour annuler ce buff)</li></ul>"));
				echo "<li class='buff'>
					   <img src='image/buff/".$buff["type"]."_p.png' 
							alt='".$buff["type"]."'
							ondblclick=\"cancelBuff('".$buff["id"]."', '".$buff["nom"]."');\"
							onmouseover=\"return overlib('$overlib', BGCLASS, 'overlib', BGCOLOR, '', FGCOLOR, '');\"
							onmouseout=\"return nd();\"  />
					   ".genere_image_buff_duree($buff)."
					  </li>";
			}
		}
		if(count($joueur["buff"]) < ($joueur["rang_grade"] + 2) )
		{
			$case_buff_dispo = ($joueur["rang_grade"] + 2) - count($joueur["buff"]);
			for($b = 0; $b < $case_buff_dispo; $b++)
			{
				echo "<li class='buff_dispo' title='vous pouvez encore recevoir $case_buff_dispo buffs'></li>";
			}
		}
		if(($joueur["rang_grade"] + 2) < 10)
		{
			$RqNextGrade = $db->query("SELECT * FROM grade WHERE rang > ".$joueur["rang_grade"]." ORDER BY rang ASC;");
			while($objNextGrade = $db->read_object($RqNextGrade))
			{
				$tmp = "il faut être ".strtolower($objNextGrade->nom)." pour avoir cette case";
				if($objNextGrade->honneur > 0) { $tmp .= " (encore ".number_format(($objNextGrade->honneur - $joueur["honneur"]), 0, ".", ".")."pt d&apos;honneur)"; }
				$title_grade[$objNextGrade->rang + 2] = $tmp.".";
			}
			for($b = ($joueur["rang_grade"] + 2 + 1); $b <= 10; $b++)
			{
				echo "<li class='buff_nondispo' title='".$title_grade[$b]."'> </li>";
			}
		}
		echo " </ul>
		</div>
		<br />
		<div id='debuff_list'>
			<ul>";
		if(count($joueur["debuff"]) > 0)
		{
			foreach($joueur["debuff"] as $buff)
			{//-- Listing des buffs
				$overlib = str_replace("'", "\'", trim("<ul><li class='overlib_titres'>".$buff["nom"]."</li><li>".description($buff["description"], $buff)."</li><li>Durée ".transform_sec_temp($buff["fin"] - time())."</li></ul>"));
				echo "<li class='buff'>
					   <img src='image/buff/".$buff["type"]."_p.png' 
							alt='".$buff["type"]."'
							onmouseover=\"return overlib('$overlib', BGCLASS, 'overlib', BGCOLOR, '', FGCOLOR, '');\"
							onmouseout=\"return nd();\"  />
					   ".genere_image_buff_duree($buff)."
					  </li>";
			}
		}
	echo " </ul>
		  </div>";
	echo "</div>";
}
if(!empty($joueur["groupe"]))
{//-- Affichage du groupe si le joueur est groupé
	if(!isset($groupe)) { $groupe = recupgroupe($joueur["groupe"], ""); };

	echo "<div id='joueur_groupe'>
		   <div id='mail_groupe' title='Envoyer un message &agrave; l&apos;ensemble du groupe.' onclick=\"return envoiInfo('envoimessage.php?id_type=g".$groupe["id"]."', 'information');\"></div>
		   <div id='info_groupe' title='Voir les informations de mon groupe.' onclick=\"return envoiInfo('infogroupe.php?id=".$groupe["id"]."', 'information');\"></div>";
	echo " <ul>";
	for($m = 0; $m < count($groupe["membre"]); $m++)
	{//-- Récupération des infos sur le membre du groupe
		if($joueur["ID"] != $groupe["membre"][$m]["id_joueur"])
		{
			unset($RqMembre);
			unset($objMembre);
			$RqMembre = $db->query("SELECT hp, hp_max, mp, mp_max, x, y, nom, classe, statut, rang_royaume, level, race, dernieraction
									FROM perso 
									WHERE ID=".$groupe["membre"][$m]["id_joueur"].";");
			$objMembre = $db->read_assoc($RqMembre);
			$groupe["membre"][$m] = array_merge($objMembre, $groupe["membre"][$m]);
			$groupe["membre"][$m]["hp_max"] = floor($groupe["membre"][$m]["hp_max"]);
			$groupe["membre"][$m]["mp_max"] = floor($groupe["membre"][$m]["mp_max"]);
			$groupe["membre"][$m]["poscase"] = calcul_distance(convert_in_pos($objMembre["x"], $objMembre["y"]), convert_in_pos($joueur["x"], $joueur["y"]));
			$groupe["membre"][$m]["pospita"] = calcul_distance_pytagore(convert_in_pos($objMembre["x"], $objMembre["y"]), convert_in_pos($joueur["x"], $joueur["y"]));
			if(!empty($objMembre["rang_royaume"]))
			{//-- Récupération du grade
				$RqGrade = $db->query("SELECT nom, rang FROM grade WHERE id=".$objMembre["rang_royaume"].";");
				$objGrade = $db->read_assoc($RqGrade);
				$groupe["membre"][$m]["grade"] = $objGrade["nom"];
			}
			$overlib = "<ul><li class='overlib_titres'>".ucwords($groupe["membre"][$m]["grade"])." ".ucwords($groupe["membre"][$m]["nom"])."</li><li>".ucwords($groupe["membre"][$m]["race"])." - ".ucwords($groupe["membre"][$m]["classe"])." (Niv.".$groupe["membre"][$m]["level"].")</li><li>HP : ".$groupe["membre"][$m]["hp"]." / ".$groupe["membre"][$m]["hp_max"]."</li><li>MP : ".$groupe["membre"][$m]["mp"]." / ".$groupe["membre"][$m]["mp_max"]."</li><li>Posisiton : x:".$objMembre["x"].", y:".$objMembre["y"]."</li><li>Distance : ".$groupe["membre"][$m]["poscase"]." - Pitagorienne : ".$groupe["membre"][$m]["pospita"]."</li>";
			{//-- Récupération des buffs
				$groupe["membre"][$m]["buff"] = array();
				$groupe["membre"][$m]["debuff"] = array();
				
				$RqBuffMembre = $db->query("SELECT * FROM buff WHERE id_perso = ".$groupe["membre"][$m]["id_joueur"]." ORDER BY debuff ASC;");
				if(mysql_num_rows($RqBuffMembre) > 0)
				{
					$overlib .= "<li>";
					while($objBuffMembre = $db->read_assoc($RqBuffMembre))
					{
						if($objBuffMembre["debuff"] == 1) { $col = "debuff"; } else { $col = "buff"; };
						$groupe["membre"][$m][$col][$objBuffMembre["type"]] = $row;
						
						$overlib .= "<img src='image/buff/".$objBuffMembre["type"]."_p.png' style='margin:0px 2px;' alt='".$objBuffMembre["type"]."' />";
					}
					$overlib .= "</li>";
				}
			}
			
			$laptime_last_connexion = time() - $groupe["membre"][$m]["dernieraction"];
			if($laptime_last_connexion > (21 * 86400)) 														{ $activite_perso = "noir"; 	$libelle_activite = "ce joueur est inactif ou banni"; }	
			elseif( ($laptime_last_connexion <= (21 * 86400)) && ($laptime_last_connexion > (1 * 86400)) )	{ $activite_perso = "rouge"; 	$libelle_activite = "s'est connecté il y a plus d'1 jour."; }	
			elseif( ($laptime_last_connexion <= (1 * 86400)) && ($laptime_last_connexion > (10 * 60)) )		{ $activite_perso = "bleu"; 	$libelle_activite = "s'est connecté il y a moins d'1 jour."; }	
			elseif($laptime_last_connexion <= (10 * 60))													{ $activite_perso = "vert"; 	$libelle_activite = "s'est connecté il y a moins de 10 min."; }	
			else	
																									{ $activite_perso = "rouge"; 	$libelle_activite = "impossible de deacute;finir l&apos;activit&eacute; de ce joueur."; }
			if ($groupe["membre"][$m]["hp"] <= 0) { $joueur_mort = "Le personnage est mort"; } else { $joueur_mort = ""; };
			$overlib .= "<li>$joueur_mort<br/>$libelle_activite</li><li class='overlib_infos'>(Cliquer pour plus d'information)</li>";
			$overlib = str_replace("'", "\'", trim($overlib));
			
			echo "<li onmouseover=\"return overlib('$overlib', BGCLASS, 'overlib', BGCOLOR, '', FGCOLOR, '');\"
					  onmouseout=\"return nd();\" 
					  onclick=\"envoiInfo('infojoueur.php?ID=".$groupe["membre"][$m]["id_joueur"]."&amp;poscase=".$groupe["membre"][$m]["poscase"]."', 'information');\">
				   <span class='joueur_groupe_activite$activite_perso'></span>
				   <span class='joueur_groupe_pseudo'>".ucwords($groupe["membre"][$m]["nom"])." : </span>
				   <span class='joueur_groupe_barre_hp'>".genere_image_hp_groupe($groupe["membre"][$m])."</span>
				   <span class='joueur_groupe_barre_mp'>".genere_image_mp_groupe($groupe["membre"][$m])."</span>";
			if ($groupe["membre"][$m]["hp"] <= 0) { echo "<span class='joueur_groupe_mort'></span>"; } 
			echo " <div class='spacer'></div>
				  </li>";
		}
	}
	echo " </ul>
		  </div>";
}
else
{
$W_requete = 'SELECT * FROM invitation WHERE receveur = '.$_SESSION['ID'];
$W_req = $db->query($W_requete);
$W_row = $db->read_array($W_req);
$ID_invitation = $W_row['ID'];
$ID_groupe = $W_row['groupe'];
//Si il y a une invitation pour le joueur
if ($db->num_rows > 0)
{
	$W_requete = "SELECT nom FROM perso WHERE ID = ".$W_row['inviteur'];
	$W_req = $db->query($W_requete);
	$W_row2 = $db->read_array($W_req);
	
	echo '
	<div class="infoperso">
	Vous avez reçu une invitation pour grouper de la part de '.$W_row2['nom'].'<br />
	<a href="reponseinvitation.php?ID='.$ID_invitation.'&groupe='.$ID_groupe.'&reponse=oui" onclick="return envoiInfo(this.href, \'information\');">Accepter</a> / <a href="reponseinvitation.php?ID='.$ID_invitation.'&reponse=non" onclick="return envoiInfo(this.href, \'information\');">Refuser</a>
	</div>';
}
}
?>
