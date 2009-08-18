<?php
if (file_exists('root.php'))
  include_once('root.php');

{//-- Initialisation
	require_once('inc/fp.php');
	if(!isset($joueur)) { $joueur = new perso($_SESSION["ID"]); }; 		//-- Récupération du tableau contenant toutes les informations relatives au joueur
	$joueur = check_perso($joueur);
	echo '<div id="perso_contenu">';
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
	include_once(root."deplacementjeu.php");

	echo " <div id='joueur_PA' style='background:transparent url(".genere_image_pa($joueur).") center;' title='PA'>".$joueur->get_pa()." / $G_PA_max</div>";
	echo " <div id='joueur_HP' style='background:transparent url(".genere_image_hp($joueur).") center;' title='HP'>".$joueur->get_hp()." / ".$joueur->get_hp_max()."</div>";
	echo " <div id='joueur_MP' style='background:transparent url(".genere_image_mp($joueur).") center;' title='MP'>".$joueur->get_mp()." / ".$joueur->get_mp_max()."</div>";
	echo " <div id='joueur_XP' style='background:transparent url(".genere_image_exp($joueur->get_exp(), prochain_level($joueur->get_level()), progression_level(level_courant($joueur->get_exp()))).") center;' title='".progression_level(level_courant($joueur->get_exp()))." % (".number_format($joueur->get_exp(), 0, ",", ".")." / ".number_format(prochain_level($joueur->get_level()), 0, ",", ".").")'></div>";
	echo " <div id='joueur_PO' title='Vos stars'>".$joueur->get_star()."</div>";
	echo ' <div id="joueur_PH" title="Votre honneur : '.$joueur->get_honneur().' / Votre réputation : '.$joueur->get_reputation().'">'.$joueur->get_honneur().'</div>';
	echo " <div id='joueur_Psso' onclick=\"envoiInfo('point_sso.php', 'information');\" title=\"Vous avez ".$joueur->get_point_sso()." point(s) shine en r&eacute;serve.\"></div>";
	$script_attaque = recupaction_all($joueur->get_action_a());
	//-- Index, Forums, Exit, Options


	echo "</div>";
}
{//-- Buffs, Grade, Pseudo
	echo "<div id='joueur_buffs_nom' style=\"background:transparent url('./image/interface/fond_info_perso_".$joueur->get_race().".png') top left no-repeat;\">";
	echo " <div id='joueur_nom' onclick=\"envoiInfo('personnage.php', 'information');\" title=\"Accès à la fiche de votre personnage\">".ucwords($joueur->get_grade()->get_nom())." ".ucwords($joueur->get_nom())." - niv.".$joueur->get_level()."<br />".ucwords($Gtrad[$joueur->get_race()])." ".ucwords($joueur->get_classe())." </div>
	";
	echo " <div id='buff_list'>
			<ul>";
		//print_r($joueur->get_buff());
		if(is_array($joueur->get_buff()))
		{
			foreach($joueur->get_buff() as $buff)
			{//-- Listing des buffs
				$overlib = str_replace("'", "\'", trim("<ul><li class='overlib_titres'>".$buff->get_nom()."</li><li>".description($buff->get_description(), $buff)."</li><li>Durée ".transform_sec_temp($buff->get_fin() - time())."</li><li class='overlib_infos'>(double-cliquer pour annuler ce buff)</li></ul>"));
				echo "<li class='buff'>
					   <img src='image/buff/".$buff->get_type()."_p.png' 
							alt='".$buff->get_type()."'
							ondblclick=\"cancelBuff('".$buff->get_id()."', '".$buff->get_nom()."');\"
							onmouseover=\"return overlib('$overlib', BGCLASS, 'overlib', BGCOLOR, '', FGCOLOR, '');\"
							onmouseout=\"return nd();\"  />
					   ".genere_image_buff_duree($buff)."
					  </li>";
			}
		}
		if(count($joueur->get_buff()) < ($joueur->get_grade()->get_rang() + 2) )
		{
			$case_buff_dispo = ($joueur->get_grade()->get_rang() + 2) - count($joueur->get_buff());
			for($b = 0; $b < $case_buff_dispo; $b++)
			{
				echo "<li class='buff_dispo' title='vous pouvez encore recevoir $case_buff_dispo buffs'>&nbsp;</li>";
			}
		}
		if(($joueur->get_grade()->get_rang() + 2) < 10)
		{
			$RqNextGrade = $db->query("SELECT * FROM grade WHERE rang > ".$joueur->get_grade()->get_rang()." ORDER BY rang ASC;");
			while($objNextGrade = $db->read_object($RqNextGrade))
			{
				$tmp = "il faut être ".strtolower($objNextGrade->nom)." pour avoir cette case";
				if($objNextGrade->honneur > 0) { $tmp .= " (encore ".number_format(($objNextGrade->honneur - $joueur->get_honneur()), 0, ".", ".")."pt d&apos;honneur)"; }
				$title_grade[$objNextGrade->rang + 2] = $tmp.".";
			}
			for($b = ($joueur->get_grade()->get_rang() + 2 + 1); $b <= 10; $b++)
			{
				echo "<li class='buff_nondispo' title='".$title_grade[$b]."'>&nbsp;</li>";
			}
		}
		echo " </ul>
		</div>
		<br />
		<div id='debuff_list'>";
		if(is_array($joueur->get_debuff()))
		{
			echo "<ul>";
			foreach($joueur->get_debuff() as $buff)
			{//-- Listing des buffs
				$overlib = str_replace("'", "\'", trim("<ul><li class='overlib_titres'>".$buff->get_nom()."</li><li>".description($buff->get_description(), $buff)."</li><li>Durée ".transform_sec_temp($buff->get_fin() - time())."</li></ul>"));
				echo "<li class='buff'>
					   <img src='image/buff/".$buff->get_type()."_p.png' 
							alt='".$buff->get_type()."'
							onmouseover=\"return overlib('$overlib', BGCLASS, 'overlib', BGCOLOR, '', FGCOLOR, '');\"
							onmouseout=\"return nd();\"  />
					   ".genere_image_buff_duree($buff)."
					  </li>";
			}
		echo " </ul>";
		}

		  echo "</div>";
	echo "</div>";
}
if($joueur->get_groupe() != 0)
{//-- Affichage du groupe si le joueur est groupé
	if(!isset($groupe)) $groupe = new groupe($joueur->get_groupe());

	echo "<div id='joueur_groupe'>
			<div id='joueur_groupe_bouton'>
		   <div id='mail_groupe' title=\"Envoyer un message à l'ensemble du groupe.\" onclick=\"return envoiInfo('envoimessage.php?id_type=g".$groupe->get_id()."', 'information');\"></div>
		   <div id='info_groupe' title='Voir les informations de mon groupe.' onclick=\"return envoiInfo('infogroupe.php?id=".$groupe->get_id()."', 'information');\"></div>
		   </div>";
	echo " <ul>";
	foreach($groupe->get_membre_joueur() as $membre)
	{//-- Récupération des infos sur le membre du groupe
		if($joueur->get_id() != $membre->get_id())
		{
			$membre->poscase = calcul_distance(convert_in_pos($membre->get_x(), $membre->get_y()), convert_in_pos($joueur->get_x(), $joueur->get_y()));
			$membre->pospita = calcul_distance_pytagore(convert_in_pos($membre->get_x(),$membre->get_y()), convert_in_pos($joueur->get_x(), $joueur->get_y()));
			$overlib = "<ul><li class='overlib_titres'>".ucwords($membre->get_grade()->get_nom())." ".ucwords($membre->get_nom())."</li><li>".ucwords($membre->get_race())." - ".ucwords($membre->get_classe())." (Niv.".$membre->get_level().")</li><li>HP : ".$membre->get_hp()." / ".$membre->get_hp_max()."</li><li>MP : ".$membre->get_mp()." / ".$membre->get_mp_max()."</li><li>Posisiton : x:".$membre->get_x().", y:".$membre->get_y()."</li><li>Distance : ".$membre->poscase." - Pytagorienne : ".$membre->pospita."</li>";
			{//-- Récupération des buffs
				$overlib .= "<li>";
				foreach($membre->get_buff() as $buff)
				{
					$overlib .= "<img src='image/buff/".$buff->get_type()."_p.png' style='margin:0px 2px;' alt='".$buff->get_type()."' />";
				}
				foreach($membre->get_debuff() as $debuff)
				{
					$overlib .= "<img src='image/buff/".$debuff->get_type()."_p.png' style='margin:0px 2px;' alt='".$debuff->get_type()."' />";
				}
				$overlib .= "</li>";
			}
			
			$laptime_last_connexion = time() - $membre->get_dernieraction();
			if($laptime_last_connexion > (21 * 86400))														{ $activite_perso = "noir"; 	$libelle_activite = "ce joueur est inactif ou banni"; }	
			elseif( ($laptime_last_connexion <= (21 * 86400)) && ($laptime_last_connexion > (1 * 86400)) )	{ $activite_perso = "rouge"; 	$libelle_activite = "s'est connecté il y a plus d'1 jour."; }	
			elseif( ($laptime_last_connexion <= (1 * 86400)) && ($laptime_last_connexion > (10 * 60)) )		{ $activite_perso = "bleu"; 	$libelle_activite = "s'est connecté il y a moins d'1 jour."; }	
			elseif($laptime_last_connexion <= (10 * 60))													{ $activite_perso = "vert"; 	$libelle_activite = "s'est connecté il y a moins de 10 min."; }	
			else	
																									{ $activite_perso = "rouge"; 	$libelle_activite = "impossible de deacute;finir l&apos;activit&eacute; de ce joueur."; }
			if ($membre->get_hp() <= 0) { $joueur_mort = "Le personnage est mort"; } else { $joueur_mort = ""; };
			$overlib .= "<li>$joueur_mort<br/>$libelle_activite</li><li class='overlib_infos'>(Cliquer pour plus d'information)</li>";
			$overlib = str_replace("'", "\'", trim($overlib));
			
			echo "<li onmouseover=\"return overlib('$overlib', BGCLASS, 'overlib', BGCOLOR, '', FGCOLOR, '');\"
					  onmouseout=\"return nd();\" 
					  onclick=\"envoiInfo('infojoueur.php?ID=".$membre->get_id()."&amp;poscase=".$membre->poscase."', 'information');\">
				   <span class='joueur_groupe_activite$activite_perso'></span>
				   <span class='joueur_groupe_pseudo'>".ucwords($membre->get_nom())." : </span>
				   <span class='joueur_groupe_barre_hp'>".genere_image_hp_groupe($membre)."</span>
				   <span class='joueur_groupe_barre_mp'>".genere_image_mp_groupe($membre)."</span>";
			if ($membre->get_hp() <= 0) { echo "<span class='joueur_groupe_mort'></span>"; } 
			
			echo " <div class='spacer'></div>
				  </li>";
		}
	}
	echo " </ul>
		  </div>";
}
else
{
$invitation = invitation::create('receveur', $_SESSION['ID']);

//Si il y a une invitation pour le joueur
if (count($invitation) > 0)
{
	$perso = new perso($invitation[0]->get_inviteur());
	echo '
	<div id="joueur_groupe">
	Vous avez reçu une invitation pour grouper de la part de '.$perso->get_nom().'<br />
	<a href="reponseinvitation.php?id='.$invitation[0]->get_id().'&groupe='.$invitation[0]->get_groupe().'&reponse=oui" onclick="return envoiInfo(this.href, \'information\');">Accepter</a> / <a href="reponseinvitation.php?ID='.$invitation[0]->get_id().'&reponse=non" onclick="return envoiInfo(this.href, \'information\');">Refuser</a>
	</div>';
}
}
echo "</div>
		<div id='perso_menu'>
			<ul>
				<li id='lejeu' class='menu' onclick=\"menu_change('lejeu');\">Le jeu</li>
				<li id='starshine' class='menu' onclick=\"menu_change('starshine');\">Starshine</li>
				<li id='communaute' class='menu' onclick=\"menu_change('communaute');\">Communauté</li>
			</ul>
			
		</div>";

?>
