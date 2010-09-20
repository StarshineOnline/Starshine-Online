<?php
if (file_exists('../root.php'))
  include_once('../root.php');

require('haut_roi.php');
include_once(root.'fonction/messagerie.inc.php');


if($joueur->get_rang_royaume() != 6 AND $joueur->get_id() != $royaume->get_ministre_militaire())
	echo '<p>Cheater</p>';
//Nouvelle bataille etape 2 => Création
elseif(array_key_exists('new2', $_GET))
{
	include_once(root.'roi/gestion_bataille_menu.php');
	
	$id_royaume = $royaume->get_id();
	$x = sSQL($_GET['x']);
	$y = sSQL($_GET['y']);
	$nom = sSQL($_GET['nom']);
	$description = sSQL($_GET['description']);
	
	//Si c'est une modification
	if(array_key_exists('id_bataille', $_GET))
	{
		$id_bataille = (int) $_GET['id_bataille'];
		$old_bataille = new bataille($id_bataille);
		$bataille = new bataille($id_bataille, $id_royaume, $x, $y, $nom, $description);
		$bataille->etat = $old_bataille->etat;
		$message = 'La bataille "'.$bataille->nom.'" a été modifiée avec succès';
	}
	else
	{
		$bataille = new bataille("", $id_royaume, $x, $y, $nom, $description);
		$message = 'La bataille "'.$bataille->nom.'" a été créée avec succès';
	}
	$bataille->sauver();
	
	// On récupère les groupes participants
	foreach($_GET as $key => $value)
	{
		$id_groupe = str_replace("groupe", "", $key); //On recupere que l'id
		if($value == 1 AND $key != "x" AND $key != "y" AND $key != "id_bataille") // Si on ajoute un groupe
		{
			$groupe = new bataille_groupe(0,0,$id_groupe);
			// Si le groupe ne participe pas deja a la bataille
			if($groupe->id_bataille != $id_bataille)
			{
				$bataille_groupe = new bataille_groupe(0, $bataille->id, $id_groupe);
				$bataille_groupe->sauver();
				unset($bataille_groupe);
			}
		}
		 // Si on a supprimé un groupe de la bataille
		elseif($value == 0 AND $key != "x" AND $key != "y")
		{
			$groupe = new bataille_groupe(0,0,$id_groupe);
			if($groupe->id_bataille == $id_bataille)
			{
				// On supprime les reperes associés au groupe, puis le groupe
				$groupe->get_reperes();
				foreach ($groupe->reperes as $repere)
					$repere->supprimer();
			
				$groupe->supprimer();
			}
		}
	}

	echo '<br /><br /><h6>'.$message.'</h6>';

}
//Information et modification sur une bataille
else
{
	if(array_key_exists('modif', $_GET))
	{
		$id_bataille = (int) $_GET['id_bataille'];
		$bataille = new bataille($id_bataille);
		//On verifie que la bataille est bien une de ce royaume
		if($bataille->id_royaume == $royaume->get_id())
		{
			$modif = true;
			$description = $bataille->description;
			$nom = $bataille->nom;
			$x = $bataille->x;
			$y = $bataille->y;
			$case = convert_in_pos($x, $y);
			$lien_modif = "&id_bataille=".$bataille->id;
			?>
			<script type="text/javascript">
			<!--
			repere_bataille('<?php echo $case; ?>')
			-->
			</script>
			<?php
		}
		else echo "<p>cheater</p>";
	}
	else
	{
		$modif = false;
		$description = "";
		$nom = "";
		$x = $Trace[$royaume->get_race()]['spawn_x'];
		$y = $Trace[$royaume->get_race()]['spawn_y'];
		$case = "";
		$lien_modif = "";
	}
	
	
	echo "<div id='bataille_new'>
	<fieldset>
	<legend>Information</legend>
	<label>Nom : </label><span><input type='text' name='nom' id='nom' value='".$nom."'/></span>
	<label>Description : </label><span><textarea name='description' id='description'>".$description."</textarea></span>
	</fieldset>
	<input type='hidden' name='case_old' id='case_old' value='".$case."' />";
	
	$lien_groupe = "";
	$requete = "SELECT groupe.id as groupeid, groupe.nom as groupenom, groupe_joueur.id_joueur, perso.nom, perso.race FROM groupe LEFT JOIN groupe_joueur ON groupe.id = groupe_joueur.id_groupe LEFT JOIN perso ON groupe_joueur.id_joueur = perso.ID WHERE groupe_joueur.leader = 'y' AND perso.race = '".$joueur->get_race()."'";
	$req = $db->query($requete);
	echo "<fieldset>
	<legend>Groupe Disponible</legend>
	<ul>";
	$class_groupe = 't1';
	while($row = $db->read_assoc($req))
	{
		// On verifie que le groupe n'est pas deja dans une bataille, sauf si c'est celle qu'on modif
		$groupe = new bataille_groupe(0,0,$row['groupeid']);
		if(!$groupe->is_bataille() OR $groupe->id_bataille == $bataille->id)
		{
			if($groupe->id_bataille == $bataille->id AND $groupe->is_bataille())
			{
				if($row['groupenom'] == '') $row['groupenom'] = '-----';
				echo "<li id='ligroupe_".$row['groupeid']."' class='$class_groupe select' onclick=\"select_groupe('".$row['groupeid']."')\">".$row['groupenom']."</li>";
				echo "<input type='hidden' id='groupe_".$row['groupeid']."' name='groupe_".$row['groupeid']."' value='1' />";
				if ($class_groupe == 't1'){$class_groupe = 't2';}else{$class_groupe = 't1';}	    
			}
			else
			{
				if($row['groupenom'] == '') $row['groupenom'] = '-----';
				echo "<li id='ligroupe_".$row['groupeid']."' class='$class_groupe' onclick=\"select_groupe('".$row['groupeid']."')\">".$row['groupenom']."</li>";
				echo "<input type='hidden' id='groupe_".$row['groupeid']."' name='groupe_".$row['groupeid']."' value='0' />";
				if ($class_groupe == 't1'){$class_groupe = 't2';}else{$class_groupe = 't1';}	    
			}
			$lien_groupe .= "+'&groupe".$row['groupeid']."='+encodeURIComponent($('#groupe_".$row['groupeid']."').val())";
		}
	}
	echo "</ul>";
	echo "</fieldset>
	</div>";
	
	$map = new map($x, $y, 8, '../', false, 'low');
	$map->onclick_status = true;
	$map->set_onclick("envoiInfo('gestion_bataille.php?valide_choix_bataille&amp;case=%%id%%', 'valide_choix_bataille');repere_bataille('%%id%%');");
	$map->quadrillage = true;
	echo "<div id='choix_bataille'>";
	echo "<div style='float:left;'>";
	$map->affiche();
	echo "</div>";
	echo "<div id='rose'>
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
		<input type="button" onclick="envoiInfo('gestion_bataille.php?move_map&x=' + $('go_x').value + '&y=' + $('go_y').value, 'choix_bataille');" value="Go !" style="width : 30px;" /><br />

		<div id="valide_choix_bataille" style='clear:both;'>
<?php
		if($modif)
		{
			echo "Vous avez séléctionné X :".$x."/ Y :".$y." comme centre de la bataille.
			<input type='hidden' name='x' id='x' value='".$x."' />
			<input type='hidden' name='y' id='y' value='".$y."' />
			<input type='hidden' name='case' id='case' value='".$case."' />";
		}
?>
		</div>
		</div>
	</div>
	
	<div style='clear : both;'></div>
	<input type='button' onclick="javascript: if($('#x').val()) { affiche_page('gestion_bataille_new.php?new2<?php echo $lien_modif; ?>&x='+encodeURIComponent($('#x').val())+'&y='+encodeURIComponent($('#y').val())+'&nom='+encodeURIComponent($('#nom').val())+'&description='+encodeURIComponent($('#description').val())<?php echo $lien_groupe; ?>);} else { alert('Veuillez choisir un centre de bataille');}" value="Créer cette bataille" />
</div>
<?php
}
?>