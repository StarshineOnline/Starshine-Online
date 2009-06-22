<?php
require('haut_roi.php');
	if($_GET['direction'] == 'diplomatie')
	{
		?>
		<h3>Diplomatie</h3>
		<?php
		$requete = "SELECT * FROM diplomatie WHERE race = '".$joueur['race']."'";
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		if($_GET['action'] == 'valid')
		{
			if($R['diplo_time'][$_GET['race']] > time())
			{
			    echo 'Vous ne pouvez pas changer votre diplomatie avec ce royaume avant : <br />'.transform_sec_temp($R['diplo_time'][$_GET['race']] - time()).'<br />';
			}
			else
			{
			    //Si modification moins, on envoi la demande à l'autre royaume
			    if($_GET['diplo'] == 'm')
			    {
				$requete = 'SELECT * FROM diplomatie_demande WHERE royaume_demande = \''.$joueur['race'].'\' AND royaume_recois = \''.$_GET['race'].'\'';
				$db->query($requete);
				if(empty($db->num_rows))
				{
					$diplo = $row[$_GET['race']] - 1;
					$star = $_GET['star'];
					if($star > $R['star']) $star = $R['star'];
					//Suppression des stars
					$requete = "UPDATE royaume SET star = star - ".$star." WHERE ID = ".$R['ID'];
					$db->query($requete);
					//Envoi de la demande
					$requete = "INSERT INTO diplomatie_demande VALUES(NULL, ".$diplo.", '".$joueur['race']."', '".$_GET['race']."',  ".$star.")";
					$db->query($requete);
					echo 'Une demande au royaume '.$Gtrad[$_GET['race']].' pour passer en diplomatie : '.$Gtrad['diplo'.$diplo].' en échange de '.$star.' stars a été envoyée.<br /><br />';
				}
				else
					echo 'Une demande au royaume '.$Gtrad[$_GET['race']].' pour passer en diplomatie : '.$Gtrad['diplo'.$diplo].' est déjà en cours.<br /><br />';
			    }
			    //Sinon, on change la diplomatie.
			    else
			    {
			        $diplo = $row[$_GET['race']] + 1;
			        $duree = (pow(2, abs(5 - $diplo)) * 60 * 60 * 24);
			        $prochain_changement = time() + $duree;
			        //Requète de changement pour ce royaume
			        $requete = "UPDATE diplomatie SET ".sSQL($_GET['race'])." = ".$diplo." WHERE race = '".$joueur['race']."'";
			        $db->query($requete);
			        //Requète de changement pour l'autre royaume
			        $requete = "UPDATE diplomatie SET ".$joueur['race']." = ".$diplo." WHERE race = '".sSQL($_GET['race'])."'";
			        $db->query($requete);
			        $requete = "SELECT diplo_time FROM royaume WHERE race = '".sSQL($_GET['race'])."'";
			        $req = $db->query($requete);
			        $row2 = $db->read_assoc($req);
			        $row2['diplo_time'] = unserialize($row2['diplo_time']);
			        $row2['diplo_time'][$joueur['race']] = $prochain_changement;
			        $row2['diplo_time'] = serialize($row2['diplo_time']);
			        $R['diplo_time'][$_GET['race']] = $prochain_changement;
			        $R['diplo_time'] = serialize($R['diplo_time']);
			        $requete = "UPDATE royaume SET diplo_time = '".$row2['diplo_time']."' WHERE race = '".sSQL($_GET['race'])."'";
			        $db->query($requete);
			        $requete = "UPDATE royaume SET diplo_time = '".$R['diplo_time']."' WHERE ID = ".$R['ID'];
			        $db->query($requete);
			        echo 'Vous êtes maintenant en '.$Gtrad['diplo'.$diplo].' avec les '.$Gtrad[$_GET['race']].'<br /><br />';
			        //Recherche du roi
			        $requete = "SELECT ID, nom FROM perso WHERE race = '".sSQL($_GET['race'])."' AND rang_royaume = 6";
			        $req = $db->query($requete);
			        $row_roi = $db->read_assoc($req);
			        //Envoi d'un message au roi
			        $message = 'Le roi des '.$Gtrad[$joueur['race']].' a changé son attitude diplomatique envers votre royaume en : '.$Gtrad['diplo'.$diplo];
			        $requete = "INSERT INTO message VALUES('', ".$row_roi['ID'].", 0, 'Mess. Auto', '".$row_roi['nom']."', 'Modification de diplomatie', '".$message."', '', '".time()."', 0)";
			        $db->query($requete);
			    }
			    $requete = "SELECT * FROM diplomatie WHERE race = '".$joueur['race']."'";
			    $req = $db->query($requete);
			    $row = $db->read_assoc($req);
			}
		}
		$i = 0;
		$keys = array_keys($row);
		$count = count($keys);
		echo '
		<table>';
		while($i < $count)
		{
			if($keys[$i] != 'race' AND $row[$keys[$i]] != 127)
			{
				$temps = $R['diplo_time'][$keys[$i]] - time();
				if($temps > 0) $show = transform_sec_temp($temps).' avant changement possible';
				else $show = 'Modif. Possible';
				if($row[$keys[$i]] > 6)
				{
					$image_diplo = '../image/interface/attaquer.png';
				}
				elseif($row[$keys[$i]] < 4)
				{
					$image_diplo = '../image/interface/paix.png';
				}
				else
				{
					$image_diplo = '../image/interface/neutre.gif';
				}
				echo '
		<tr style="vertical-align : middle;">
			<td>
				<img src="../image/g_etendard/g_etendard_'.$Trace[$keys[$i]]['numrace'].'.png" style="vertical-align : middle;">'.$Gtrad[$keys[$i]].'
			</td>
			<td style="font-weight : normal;">';
				echo ' <img src="'.$image_diplo.'" style="vertical-align : middle;"> '.$Gtrad['diplo'.$row[$keys[$i]]].' 
			</td>
			<td>
				<a style="font-size : 0.8em;" href="gestion_royaume.php?poscase='.$W_case.'&amp;direction=diplomatie&amp;action=modif&amp;race='.$keys[$i].'" onclick="refresh(this.href, \'conteneur\'); return false;"><span class="xsmall">'.$show.'</span></a>
			</td>
		</td>';
			}
			$i++;
		}
		?>
		</table>
		<?php
		if($_GET['action'] == 'modif')
		{
			?>
			<h3>Modification de la diplomatie avec <?php echo $Gtrad[$_GET['race']]; ?></h3>
			Changer votre diplomatie pour :<br />
			<select name="diplo" id="diplo">
			<?php
			$diplo = $row[$_GET['race']];
			if($diplo > 0) $diplom = $diplo - 1;
			if($diplo < 10) $diplop = $diplo + 1;
			if(isset($diplom)) echo '<option value="m">'.$Gtrad['diplo'.$diplom].' - Exige l\'accord de l\'autre roi</option>';
			if(isset($diplop)) echo '<option value="p">'.$Gtrad['diplo'.$diplop].'</option>';
			?>
			</select><br />
			<?php
			//Si monter de diplo, on peut donner des stars
			if(isset($diplom))
			{
			    ?>
			<span>Vous pouvez donner des stars au royaume destinataire de la demande en échange de son acceptation.<br />
			Ces stars seront prise dès l'envoi de la demande.</span>
			<input type="text" value="0" name="star" id="star" />
				<?php
				$href_star = "' + document.getElementById('star').value";
			}
			else $href_star = "0'";
			?>
			<input type="button" onclick="envoiInfo('gestion_royaume.php?direction=diplomatie&amp;action=valid&amp;race=<?php echo $_GET['race']; ?>&amp;diplo=' + document.getElementById('diplo').value + '&amp;star=<?php echo $href_star; ?>, 'conteneur')" value="Effectuer le changement diplomatique">
			<?php
		}
	}
	elseif($_GET['direction'] == 'diplomatie_demande')
	{
	    //Recherche de la demande
	    $requete = "SELECT * FROM diplomatie_demande WHERE id = ".sSQL($_GET['id_demande']);
	    $req = $db->query($requete);
	    $row = $db->read_assoc($req);
	    //Suppression de la demande
	    $requete = "DELETE FROM diplomatie_demande WHERE id = ".sSQL($_GET['id_demande']);
	    $db->query($requete);
	    //Recherche du roi
	    $requete = "SELECT ID, nom FROM perso WHERE race = '".$row['royaume_demande']."' AND rang_royaume = 6";
	    $req = $db->query($requete);
	    $row_roi = $db->read_assoc($req);
	    if($_GET['reponse'] == 'non')
	    {
	        //Envoi d'un message au roi
	        $message = 'Le roi des '.$Gtrad[$joueur['race']].' a refusé votre demande diplomatique';
	        $requete = "INSERT INTO message VALUES('', ".$row_roi['ID'].", 0,'Mess. Auto', '".$row_roi['nom']."', 'Refus de diplomatie', '".$message."', '', '".time()."', 0)";
	        $db->query($requete);
	        //On redonne les stars
	        $requete = "UPDATE royaume SET star = star + ".$row['stars']." WHERE race = '".$row['royaume_demande']."'";
	        $db->query($requete);
	        echo 'Demande refusée<br />';
	    }
	    else
	    {
	        $diplo = $row['diplo'];
	        $duree = (pow(2, abs(5 - $diplo)) * 60 * 60 * 24);
	        $prochain_changement = time() + $duree;
	        //Requète de changement pour ce royaume
	        $requete = "UPDATE diplomatie SET ".$row['royaume_demande']." = ".$diplo." WHERE race = '".$joueur['race']."'";
	        $db->query($requete);
	        //On donne les stars au royaume qui recoit
	        $requete = "UPDATE royaume SET star = star + ".$row['stars']." WHERE race = '".$row['royaume_recois']."'";
	        $db->query($requete);
	        //Requète de changement pour l'autre royaume
	        $requete = "UPDATE diplomatie SET ".$joueur['race']." = ".$diplo." WHERE race = '".$row['royaume_demande']."'";
	        $db->query($requete);
	        $requete = "SELECT diplo_time FROM royaume WHERE race = '".$row['royaume_demande']."'";
	        $req = $db->query($requete);
	        $row2 = $db->read_assoc($req);
	        $row2['diplo_time'] = unserialize($row2['diplo_time']);
	        $row2['diplo_time'][$joueur['race']] = $prochain_changement;
	        $row2['diplo_time'] = serialize($row2['diplo_time']);
	        $row3['diplo_time'] = $R['diplo_time'];
	        $row3['diplo_time'][$row['royaume_demande']] = $prochain_changement;
	        $row3['diplo_time'] = serialize($row3['diplo_time']);
	        $requete = "UPDATE royaume SET diplo_time = '".$row2['diplo_time']."' WHERE race = '".$row['royaume_demande']."'";
	        $db->query($requete);
	        $requete = "UPDATE royaume SET diplo_time = '".$row3['diplo_time']."' WHERE race = '".$R['race']."'";
	        $db->query($requete);
	        echo 'Vous êtes maintenant en '.$Gtrad['diplo'.$diplo].' avec les '.$Gtrad[$row['royaume_demande']].'<br /><br />';
	        //Envoi d'un message au roi
	        $message = 'Le roi des '.$Gtrad[$joueur['race']].' a accepté votre demande diplomatique'.(empty($row['stars']) ? '' : '.'.$row['stars'].' ont été versés à ce royaume.');
	        $requete = "INSERT INTO message VALUES('', ".$row_roi['ID'].", 0,'Mess. Auto', '".$row_roi['nom']."', 'Accord diplomatique', '".$message."', '', '".time()."', 0)";
	        $db->query($requete);
	    }
	}
	elseif($_GET['direction'] == 'construction')
	{
	    $requete = "SELECT *, construction_ville.id as id_const FROM construction_ville LEFT JOIN batiment_ville ON construction_ville.id_batiment = batiment_ville.id WHERE id_royaume = ".$R['ID'];
	    $req = $db->query($requete);
	    echo '
	    <h3>Liste des batiments de la ville :</h3>
	    <ul class="ville">';
	    while($row = $db->read_assoc($req))
	    {
	        if($row['statut'] == 'actif')
	        {
	        ?>
	        <li><?php echo $row['nom']; ?><span class="small">, entretien : <?php echo $row['entretien']; ?> <a href="gestion_royaume.php?direction=amelioration&amp;action=list&amp;batiment=<?php echo $row['type']; ?>" onclick="return envoiInfo(this.href, 'conteneur')">Améliorer</a></li>
	        <?php
	    	}
	    	else
	    	{
	        ?>
	        <li><?php echo $row['nom']; ?><span class="small">, inactif <a href="gestion_royaume.php?direction=reactif&amp;action=list&amp;batiment=<?php echo $row['id_const']; ?>" onclick="if(confirm('Voulez vous vraiment réactiver cette construction ?')) return envoiInfo(this.href, 'conteneur'); else return false;">Réactiver pour <?php echo $row['dette']; ?> stars</a></li>
	        <?php
	    	}
	    }
	    echo '</ul>';
	}
	elseif($_GET['direction'] == 'reactif')
	{
	    $id_batiment = $_GET['batiment'];
	    $requete = "SELECT * FROM construction_ville WHERE id = ".$id_batiment;
	    $req = $db->query($requete);
	    $row = $db->read_assoc($req);
	    if($R['star'] >= $row['dette'])
	    {
	        $requete = "UPDATE construction_ville SET statut = 'actif', dette = 0 WHERE id = ".$id_batiment;
	        $db->query($requete);
	        $requete = "UPDATE royaume SET star = star - ".$row['dette']." WHERE ID = ".$R['ID'];
	        if($db->query($requete)) echo 'Batiment bien réactivé.';
	    }
	    else
	    {
	        echo 'Vous n\'avez pas assez de stars pour réactiver cette construction !';
	    }
	}
	elseif($_GET['direction'] == 'amelioration')
	{
	    $type = $_GET['batiment'];
	    $action = $_GET['action'];
	    $requete = "SELECT *, construction_ville.id AS id_batiment_ville FROM construction_ville LEFT JOIN batiment_ville ON construction_ville.id_batiment = batiment_ville.id WHERE id_royaume = ".$R['ID']." AND batiment_ville.type = '".$type."'";
	    $req = $db->query($requete);
	    $row = $db->read_assoc($req);
	    $id_batiment_ville = $row['id_batiment_ville'];
	    switch($action)
	    {
	        case 'list' :
	            ?>
	            Actuellement vous possédez : <?php echo $row['nom']; ?><br />
	            Vous pouvez l'améliorer en :
	            <ul class="ville">
	            <?php
	            $requete = "SELECT * FROM batiment_ville WHERE level > ".$row['level']." AND type = '".$type."'";
	            $req = $db->query($requete);
	            while($row = $db->read_assoc($req))
	            {
	                ?>
	                <li><?php echo $row['nom']; ?>, coût : <?php echo $row['cout']; ?>, entretien par jour : <?php echo $row['entretien']; ?> <a href="gestion_royaume.php?direction=amelioration&amp;action=ameliore&amp;batiment=<?php echo $row['type']; ?>&amp;id_batiment=<?php echo $row['id']; ?>" onclick="return envoiInfo(this.href, 'conteneur')">Améliorer</a></li>
	                <?php
	            }
	            ?>
	            </ul>
	            <?php
	        break;
	        case 'ameliore' :
	            $id_batiment = $_GET['id_batiment'];
	            $requete = "SELECT * FROM batiment_ville WHERE id = ".$id_batiment;
	            $req = $db->query($requete);
	            $row = $db->read_assoc($req);
	            //Si le royaume a assez de stars on achète le batiment
	            if($R['star'] >= $row['cout'])
	            {
	                //On paye
	                $R['star'] = $R['star'] - $row['cout'];
	                $requete = "UPDATE royaume SET star = ".$R['star']." WHERE ID = ".$R['ID'];
	                $db->query($requete);
	                //On ajoute le batiment et on supprime l'ancien
	                $requete = "DELETE FROM construction_ville WHERE id = ".$id_batiment_ville;
	                $db->query($requete);
	                $requete = "INSERT INTO construction_ville VALUES ('', ".$R['ID'].", ".$id_batiment.", 'actif', '')";
	                if($db->query($requete))
	                {
	                    echo $row['nom'].' bien acheté.';
	                }
	            }
	            else
	            {
	                echo 'Le royaume ne possède pas assez de stars';
	            }
	        break;
	    }
	}
	elseif($_GET['direction'] == 'carte')
	{
		echo '<img src="carte_roy2.php?url='.$joueur['race'].'" />';
	
	}
	elseif($_GET['direction'] == 'stats')
	{
	    //Statistiques du royaume
	    $requete = "SELECT *, COUNT(*) as tot FROM perso WHERE race = '".$joueur['race']."' AND statut = 'actif' GROUP BY classe ORDER BY tot DESC, classe ASC";
	    $req = $db->query($requete);
	    ?>
	    <h3>Nombre de joueurs de votre race par classe</h3>
	    <table>
	    <tr>
	    	<td>
	    		Classe
	    	</td>
	    	<td>
	    		Nombre
	    	</td>
	    </tr>
	    <?php
	    while($row = $db->read_array($req))
	    {
	        echo '
	        <tr>
	        	<td>
	        	'.$row['classe'].'
	        	</td>
	        	<td>
	        	'.$row['tot'].'
	        	</td>
	        </tr>'; 
	    }
	    ?>
	    </table>
	    <?php
	    $requete = "SELECT nom, melee FROM perso WHERE race = '".$joueur['race']."' AND statut = 'actif' ORDER BY melee DESC LIMIT 0, 5";
	    $req = $db->query($requete);
	    ?>
	    <h3>Meilleurs guerriers</h3>
	    <table>
	    <tr>
	    	<td>
	    		Nom
	    	</td>
	    	<td>
	    		Mélée
	    	</td>
	    </tr>
	    <?php
	    while($row = $db->read_array($req))
	    {
	        echo '
	        <tr>
	        	<td>
	        	'.$row['nom'].'
	        	</td>
	        	<td>
	        	'.$row['melee'].'
	        	</td>
	        </tr>'; 
	    }
	    ?>
	    </table>
	    <?php
	    $requete = "SELECT nom, distance FROM perso WHERE race = '".$joueur['race']."' AND statut = 'actif' ORDER BY distance DESC LIMIT 0, 5";
	    $req = $db->query($requete);
	    ?>
	    <h3>Meilleurs Archers</h3>
	    <table>
	    <tr>
	    	<td>
	    		Nom
	    	</td>
	    	<td>
	    		Tir à distance
	    	</td>
	    </tr>
	    <?php
	    while($row = $db->read_array($req))
	    {
	        echo '
	        <tr>
	        	<td>
	        	'.$row['nom'].'
	        	</td>
	        	<td>
	        	'.$row['distance'].'
	        	</td>
	        </tr>'; 
	    }
	    ?>
	    </table>
	    <?php
	    $requete = "SELECT nom, esquive FROM perso WHERE race = '".$joueur['race']."' AND statut = 'actif' ORDER BY esquive DESC LIMIT 0, 5";
	    $req = $db->query($requete);
	    ?>
	    <h3>Meilleurs esquiveurs</h3>
	    <table>
	    <tr>
	    	<td>
	    		Nom
	    	</td>
	    	<td>
	    		Esquive
	    	</td>
	    </tr>
	    <?php
	    while($row = $db->read_array($req))
	    {
	        echo '
	        <tr>
	        	<td>
	        	'.$row['nom'].'
	        	</td>
	        	<td>
	        	'.$row['esquive'].'
	        	</td>
	        </tr>'; 
	    }
	    ?>
	    </table>
	    <?php
	    $requete = "SELECT nom, incantation FROM perso WHERE race = '".$joueur['race']."' AND statut = 'actif' ORDER BY incantation DESC LIMIT 0, 5";
	    $req = $db->query($requete);
	    ?>
	    <h3>Meilleurs mages</h3>
	    <table>
	    <tr>
	    	<td>
	    		Nom
	    	</td>
	    	<td>
	    		Incantation
	    	</td>
	    </tr>
	    <?php
	    while($row = $db->read_array($req))
	    {
	        echo '
	        <tr>
	        	<td>
	        	'.$row['nom'].'
	        	</td>
	        	<td>
	        	'.$row['incantation'].'
	        	</td>
	        </tr>'; 
	    }
	    ?>
	    </table>
	    <?php
	}
	elseif($_GET['direction'] == 'criminel')
	{
	    //Sélection de tous les joueurs ayant des points de crime
	    $requete = "SELECT * FROM perso WHERE crime > 0 AND race = '".$R['race']."' AND statut = 'actif' ORDER BY crime DESC";
	    $req = $db->query($requete);
	    ?>
	    <table>
	    <tr>
	    	<td>
	    		Nom
	    	</td>
	    	<td>
	    		Pts de crime
	    	</td>
	    	<td>
	    		Amende
	    	</td>
	    	<td>
	    	</td>
	    </tr>
	    <?php
	    while($row = $db->read_assoc($req))
	    {
	        if($row['amende'] > 0)
	        {
	            $requete = "SELECT montant FROM amende WHERE id = ".$row['amende'];
	            $req_a = $db->query($requete);
	            $row_a = $db->read_row($req_a);
	            $amende = $row_a[0];
	        }
	        else $amende = 0;
	        ?>
	    <tr>
	    	<td>
	    		<?php echo $row['nom']; ?>
	    	</td>
	    	<td>
	    		<?php echo $row['crime']; ?>
	    	</td>
	    	<td>
	    		<?php echo $amende; ?>
	    	</td>
	    	<td>
	    		<a href="gestion_royaume.php?direction=gestion_criminel&amp;id=<?php echo $row['ID']; ?>" onclick="return envoiInfo(this.href, 'conteneur')">Gérer</a>
	    		<?php
	    		if($amende != 0)
	    		{
	        		?>
	        		/ <a href="gestion_royaume.php?direction=suppr_criminel&amp;id=<?php echo $row['ID']; ?>" onclick="return envoiInfo(this.href, 'conteneur')">Supprimer</a>
	        		<?php
	    		}
	    		?>
	    	</td>
	    </tr>
	    	<?php
	    }
	    ?>
	    </table>
	    <?php
	}
	elseif($_GET['direction'] == 'suppr_criminel')
	{
	    $amende = recup_amende($_GET['id']);
		//On supprime l'amende du joueur
		$requete = "UPDATE perso SET amende = 0 WHERE ID = ".sSQL($_GET['id']);
		$db->query($requete);
		$requete = "DELETE FROM amende WHERE id = ".$amende['id'];
		$db->query($requete);
		echo 'Amende bien supprimée.';
	}
	elseif($_GET['direction'] == 'gestion_criminel')
	{
	    $joueur = recupperso($_GET['id']);
	    //Récupère l'amende
	    $amende = recup_amende($_GET['id']);
	    $amende_max = ($joueur['crime'] * $joueur['crime']) * 10;
	    $etats = array('normal');
	    if($joueur['crime'] > 30) $etats[] = 'bandit';
	    if($joueur['crime'] > 60) $etats[] = 'criminel';
	    //Si il en a pas
	    if(!$amende)
	    {
	        ?>
	        <form method="post" action="javascript:envoiInfoPost('gestion_royaume.php?direction=gestion_criminel2&amp;id=<?php echo $joueur['ID']; ?>&amp;acces_ville=' + document.getElementById('acces_ville').checked + '&amp;spawn_ville=' + document.getElementById('spawn_ville').checked + '&amp;statut=' + document.getElementById('statut').value + '&amp;montant=' + document.getElementById('montant').value, 'conteneur');">
	        	<input type="checkbox" name="acces_ville" id="acces_ville" /> Empèche le joueur d'accéder à la ville<br />
	        	<input type="checkbox" name="spawn_ville" id="spawn_ville" <?php if($joueur['crime'] > 30) echo 'disabled'; ?> /> Empèche de renaître à la ville<br />
	        	<br />
	        	Statut du personnage <select name="statut" id="statut">
	        	<?php
	        	foreach($etats as $etat)
	        	{
	            	?>
	            	<option value="<?php echo $etat; ?>"><?php echo $etat; ?></option>
	            	<?php
	        	}
	        	?>
	        	</select><br />
	        	<br />
	        	 Montant de l'amende (max : <?php echo $amende_max; ?>) <input type="text" name="montant" id="montant" /><br />
	        	 <br />
	        	 <input type="submit" value="Valider cette amende" />
	        </form>
	        <?php
	    }
	}
	elseif($_GET['direction'] == 'gestion_criminel2')
	{
	    $joueur = recupperso($_GET['id']);
	    //Récupère l'amende
	    $amende = recup_amende($_GET['id']);
	    $amende_max = ($joueur['crime'] * $joueur['crime']) * 10;
	    //Vérification d'usage
	    if($_GET['montant'] > 0)
	    {
	        if($_GET['montant'] <= $amende_max)
	        { 
	        	if($_GET['spawn_ville'] == 'true') $spawn_ville = 'y'; else $spawn_ville = 'n';
	        	if($_GET['acces_ville'] == 'true') $acces_ville = 'y'; else $acces_ville = 'n';
	        	//Inscription de l'amende dans la bdd
	        	$requete = "INSERT INTO amende(id, id_joueur, id_royaume, montant, acces_ville, respawn_ville, statut) VALUES ('', ".$joueur['ID'].", ".$Trace[$joueur['race']]['numrace'].", ".sSQL($_GET['montant']).", '".$acces_ville."', '".$spawn_ville."', '".sSQL($_GET['statut'])."')";
	        	if($db->query($requete))
	        	{
	            	$amende = recup_amende($joueur['ID']);
	            	$requete = "UPDATE perso SET amende = ".$amende['id']." WHERE ID = ".$joueur['ID'];
	            	if($db->query($requete)) echo 'Amende bien prise en compte !';
	        	}
	    	}
	    	else
	    	{
	        	echo 'Le montant de l\'amende est trop élevé';
	    	}
	    }
	}
	elseif($_GET['direction'] == 'achat_militaire')
	{
		$requete = "SELECT * FROM objet_royaume WHERE id = ".sSQL($_GET['id']);
		$nombre = $_GET['nbr'];
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		$check = true;
		//Si c'est pour une bourgade on vérifie combien il y en a déjà
		if($row['type'] == 'bourg')
		{
			$nb_bourg = nb_bourg($R['ID']);
			$nb_case = nb_case($R['ID']);
			if(($nb_bourg + $nombre - 1) >= ceil($nb_case / 250)) $check = false;
		}
		//On vérifie les stars
		if($R['star'] >= ($row['prix'] * $nombre) && $check)
		{
			//On vérifie les ressources
			if(($R['pierre'] >= $row['pierre'] * $nombre) && ($R['bois'] >= $row['bois'] * $nombre) && ($R['eau'] >= $row['eau'] * $nombre) && ($R['charbon'] >= $row['charbon'] * $nombre) && ($R['sable'] >= $row['sable'] * $nombre) && ($R['essence'] >= $row['essence'] * $nombre))
			{
				$i = 0;
				while($i < $nombre)
				{
					//Achat
					$requete = "INSERT INTO depot_royaume VALUES ('', ".$row['id'].", ".$R['ID'].")";
					$db->query($requete);
					//On rajoute un bourg au compteur
					if($row['type'] == 'bourg')
					{
						$requete = "UPDATE royaume SET bourg = bourg + 1 WHERE ID = ".$R['ID'];
						$db->query($requete);
					}
					//On enlève les stars au royaume
					$requete = "UPDATE royaume SET star = star - ".$row['prix'].", bois = bois - ".$row['bois'].", pierre = pierre - ".$row['pierre'].", eau = eau - ".$row['eau'].", charbon = charbon - ".$row['charbon'].", sable = sable - ".$row['sable'].", essence = essence - ".$row['essence']." WHERE ID = ".$R['ID'];
					if($db->query($requete))
					{
						echo '<h6>'.$row['nom'].' bien acheté.</h6><br />';
					}
					$i++;
				}
			}
			else echo '<h5>Il vous manque des ressources !</h5>';
		}
		elseif(!$check)
		{
			echo '<h5>Il y a déjà trop de bourg sur votre royaume.</h5><br />
			Actuellement : '.$nb_bourg.'<br />
			Maximum : '.ceil($nb_case / 250);
		}
		else
		{
			echo '<h5>Le royaume n\'a pas assez de stars</h5>';
		}
	}
	elseif($_GET['direction'] == 'boutique')
	{
		?>
		<table>
		<tr>
			<td>
				Nom
			</td>
			<td>
				Stars
			</td>
			<td>
				Pierre
			</td>
			<td>
				Bois
			</td>
			<td>
				Eau
			</td>
			<td>
				Sable
			</td>
			<td>
				Charbon
			</td>
			<td>
				Essence Magique
			</td>
			<td>
				Nombre
			</td>
			<td>
				Achat
			</td>
		</tr>
		<?php
		$requete = "SELECT *, objet_royaume.id as oid FROM objet_royaume LEFT JOIN batiment ON batiment.id = objet_royaume.id_batiment";
		$req = $db->query($requete);
		$i = 0;
		while($row = $db->read_assoc($req))
		{
			$overlib = $row['description'];
		?>
		<tr>
			<td onmouseover="return <?php echo make_overlib($overlib); ?>" onmouseout="return nd();">
				<?php echo $row['nom']; ?>
			</td>
			<td>
				<?php echo $row['prix']; ?>
			</td>
			<td>
				<?php echo $row['pierre']; ?>
			</td>
			<td>
				<?php echo $row['bois']; ?>
			</td>
			<td>
				<?php echo $row['eau']; ?>
			</td>
			<td>
				<?php echo $row['sable']; ?>
			</td>
			<td>
				<?php echo $row['charbon']; ?>
			</td>
			<td>
				<?php echo $row['essence']; ?>
			</td>
			<td>
				<input type="text" id="nbr<?php echo $i; ?>" value="1" />
			</td>
			<td>
				<a href="#" onclick="return envoiInfo('gestion_royaume.php?direction=achat_militaire&amp;id=<?php echo $row['oid']; ?>&amp;nbr=' + $('nbr<?php echo $i; ?>').value, 'conteneur')">Acheter</a>
			</td>
		</tr>
		<?php
			$i++;
		}
		?>
		</table>
		<?php
	}
	elseif($_GET['direction'] == 'bourse_enchere')
	{
		require_once('../class/bourse_royaume.class.php');
		require_once('../class/bourse.class.php');
		$enchere = new bourse_royaume($_GET['id_enchere']);
		//On vérifie que c'est un royaume possible
		if($R['ID'] != $enchere->id_royaume AND $R['ID'] != $enchere->id_royaume_acheteur)
		{
			$prix = ceil($enchere->prix * 1.1);
			//On vérifie que le royaume a assez de stars
			if($R['star'] >= $prix)
			{
				//On rend les stars à l'autre royaume (si l'id est différent de 0)
				if($enchere->id_royaume_acheteur)
				{
					$requete = "UPDATE royaume SET star = star + ".$enchere->prix." WHERE ID = ".$enchere->id_royaume_acheteur;
					$db->query($requete);
				}
				//On prend les stars de notre royaume
				$requete = "UPDATE royaume SET star = star - ".$prix." WHERE ID = ".$R['ID'];
				$db->query($requete);
				//On met à jour l'enchère
				$enchere->id_royaume_acheteur = $R['ID'];
				$enchere->prix = $prix;
				//Si enchère faite 6h avant la fin, on décale l'enchère de 6h
				$decalage = 6 * 60 * 60;
				if(time() > ($enchere->fin_vente - $decalage))
				{
					$enchere->fin_vente += $decalage;
				}
				$enchere->sauver();
				?>
				<h6>Enchère prise en compte !</h6>
				<a href="gestion_royaume.php?direction=bourse" onclick="return envoiInfo(this.href, 'conteneur');">Revenir à la bourse</a>
				<?php
			}
			else
			{
				?>
				<h5>Vous n'avez pas assez de stars pour enchérir !</h5>
				<?php
			}
		}
	}
	elseif($_GET['direction'] == 'bourse_ressource')
	{
		require_once('../class/bourse_royaume.class.php');
		require_once('../class/bourse.class.php');
		$enchere = new bourse_royaume();
		$ressource = $_GET['ressource'];
		$nombre = $_GET['nombre'];
		$prix = $_GET['prix'];
		//On vérifie que le royaume a assez de cette ressource
		if($R[$ressource] >= $nombre)
		{
			$enchere->id_royaume = $R['ID'];
			$enchere->ressource = $ressource;
			$enchere->nombre = $nombre;
			$enchere->prix = $prix;
			//7 jours plus tard
			$time = time() + 7 * (24 * 60 * 60);
			$enchere->fin_vente = date("Y-m-d H:i:s", $time);
			$enchere->sauver();
			//On enlève les ressources au royaume
			$requete = "UPDATE royaume SET ".$ressource." = ".$ressource." - ".$nombre." WHERE ID = ".$R['ID'];
			$db->query($requete);
		}
		else
		{
			?>
			<h5>Vous n'avez pas assez de <?php echo $ressource; ?> !</h5>
			<?php
		}
	}
	elseif($_GET['direction'] == 'bourse')
	{
		require_once('../class/bourse_royaume.class.php');
		require_once('../class/bourse.class.php');
		$bourse = new bourse($R['ID']);
		$bourse->check_encheres();
		$bourse->get_encheres('DESC', 'actif = 1 AND id_royaume != '.$R['ID'].' AND id_royaume_acheteur != '.$R['ID']);
			//
		?>
		<div style="position : absolute; left : 800px; background-color : grey; padding : 5px 10px 5px 10px;">
			<div id="ajout_ressource" style="position : relative; right : 0px; display : none; z-index : 10;">
				Ressource : <select name="ressource" id="ressource">
					<option value="pierre">pierre</option>
					<option value="bois">bois</option>
					<option value="eau">eau</option>
					<option value="sable">sable</option>
					<option value="charbon">charbon</option>
					<option value="essence">essence</option>
					<option value="food">nourriture</option>
				</select><br />
				Nombre <input type="text" name="nbr" id="nbr" value="0" /><br />
				Prix total : <input type="text" name="prix" id="prix" value="0" /><br />
				<input type="button" onclick="if(confirm('Voullez vous mettre ' + $(nbr).value + ' ' + $(ressource).value + ' en vente à ' + $(prix).value + ' stars ?')) return envoiInfo('gestion_royaume.php?direction=bourse_ressource&amp;ressource=' + $(ressource).value + '&amp;prix=' + $(prix).value + '&amp;nombre=' + $(nbr).value, 'conteneur'); else return false;" value="Valider" /><br />
			</div>
			<a href="" onclick="Effect.toggle('ajout_ressource', 'slide'); return false;">Mettre des ressources aux enchères</a>
		</div>
		<div style="position : absolute; left : 600px; background-color : grey; padding : 5px 10px 5px 10px;">
			<div id="cout_ressource" style="position : relative; right : 0px; display : none; z-index : 10;">
				<ul>
				<?php
				$yearmonth = date("Ym");
				$requete = "SELECT *, (SUM(nombre / prix) / COUNT(*)) as moyenne, COUNT(*) as tot FROM `bourse_royaume` WHERE `id_royaume_acheteur` != 0 AND `actif` = 0 AND EXTRACT(YEAR_MONTH FROM fin_vente) >= '".$yearmonth."' GROUP BY ressource";
				$req = $db->query($requete);
				while($row = $db->read_assoc($req))
				{
					echo '<li>'.$row['ressource'].' : '.round($row['moyenne'], 2).' ('.$row['tot'].' ventes)</li>';
				}
				?>
				</ul>
			</div>
			<a href="" onclick="Effect.toggle('cout_ressource', 'slide'); return false;">Cours des ressources</a>
		</div>
		<h3>Enchères en cours</h3>
		<table style="width : 100%;">
		<tr>
			<td>
				Ressource
			</td>
			<td>
				Nombre
			</td>
			<td>
				Prix actuel
			</td>
			<td>
				Fin vente
			</td>
			<td>
			</td>
		</tr>
		<?php
		foreach($bourse->encheres as $enchere)
		{
			$time = strtotime($enchere->fin_vente);
			$restant = transform_sec_temp(($time - time()));
			$prix = ceil($enchere->prix * 1.1);
			?>
		<tr>
			<td>
				<?php echo $enchere->ressource; ?>
			</td>
			<td>
				<?php echo $enchere->nombre; ?>
			</td>
			<td>
				<?php echo $enchere->prix.' ('.round(($enchere->prix / $enchere->nombre), 2).' / u)'; ?>
			</td>
			<td>
				<?php echo $restant; ?>
			</td>
			<td>
				<a href="gestion_royaume.php?direction=bourse_enchere&amp;id_enchere=<?php echo $enchere->id_bourse_royaume; ?>" onclick="return envoiInfo(this.href, 'conteneur');">Enchérir pour <?php echo $prix; ?> stars (<?php echo ($prix / $enchere->nombre); ?> / u)</a>
			</td>
		</tr>
			<?php
		}
		?>
		</table>
		<?php
		$bourse->encheres = array();
		$bourse->get_encheres('DESC', 'actif = 1 AND id_royaume_acheteur = '.$R['ID']);
		?>
		<h3>Vos mises</h3>
		<table style="width : 100%;">
		<tr>
			<td>
				Ressource
			</td>
			<td>
				Nombre
			</td>
			<td>
				Prix actuel
			</td>
			<td>
				Fin vente
			</td>
		</tr>
		<?php
		foreach($bourse->encheres as $enchere)
		{
			$time = strtotime($enchere->fin_vente);
			$restant = transform_sec_temp(($time - time()));
			?>
		<tr>
			<td>
				<?php echo $enchere->ressource; ?>
			</td>
			<td>
				<?php echo $enchere->nombre; ?>
			</td>
			<td>
				<?php echo $enchere->prix.' ('.round(($enchere->prix / $enchere->nombre), 2).' / u)'; ?>
			</td>
			<td>
				<?php echo $restant; ?>
			</td>
		</tr>
			<?php
		}
		?>
		</table>
		<?php
		$bourse->encheres = array();
		$bourse->get_encheres('DESC', 'actif = 1 AND id_royaume = '.$R['ID']);
		?>
		<h3>Vos ressources en vente</h3>
		<table style="width : 100%;">
		<tr>
			<td>
				Ressource
			</td>
			<td>
				Nombre
			</td>
			<td>
				Prix actuel
			</td>
			<td>
				Fin vente
			</td>
			<td>
			</td>
		</tr>
		<?php
		foreach($bourse->encheres as $enchere)
		{
			$time = strtotime($enchere->fin_vente);
			$restant = transform_sec_temp(($time - time()));
			if($enchere->id_royaume_acheteur != 0) $acheteur = 'Acheteur';
			else $acheteur = '';
			?>
		<tr>
			<td>
				<?php echo $enchere->ressource; ?>
			</td>
			<td>
				<?php echo $enchere->nombre; ?>
			</td>
			<td>
				<?php echo $enchere->prix.' ('.round(($enchere->prix / $enchere->nombre), 2).' / u)'; ?>
			</td>
			<td>
				<?php echo $restant; ?>
			</td>
			<td>
				<?php echo $acheteur; ?>
			</td>
		</tr>
			<?php
		}
		?>
		</table>
		<?php
		$bourse->encheres = array();
		$time = time() - 7 * (24 * 60 * 60);
		$date = date("Y-m-d H:i:s", $time);
		$bourse->get_encheres('DESC', 'actif = 0 AND fin_vente > "'.$date.'" AND id_royaume_acheteur = '.$R['ID']);
		?>
		<h3>Enchères remportées les 7 derniers jours</h3>
		<table style="width : 100%;">
		<tr>
			<td>
				Ressource
			</td>
			<td>
				Nombre
			</td>
			<td>
				Prix actuel
			</td>
			<td>
				Fin vente
			</td>
		</tr>
		<?php
		foreach($bourse->encheres as $enchere)
		{
			?>
		<tr>
			<td>
				<?php echo $enchere->ressource; ?>
			</td>
			<td>
				<?php echo $enchere->nombre; ?>
			</td>
			<td>
				<?php echo $enchere->prix.' ('.round(($enchere->prix / $enchere->nombre), 2).' / u)'; ?>
			</td>
			<td>
				<?php echo $enchere->fin_vente; ?>
			</td>
		</tr>
			<?php
		}
		?>
		</table>
		<?php
		$bourse->encheres = array();
		$time = time() - 7 * (24 * 60 * 60);
		$date = date("Y-m-d H:i:s", $time);
		$bourse->get_encheres('DESC', 'actif = 0 AND fin_vente > "'.$date.'" AND id_royaume_acheteur != 0 AND id_royaume = '.$R['ID']);
		?>
		<h3>Ressources vendues les 7 derniers jours</h3>
		<table style="width : 100%;">
		<tr>
			<td>
				Ressource
			</td>
			<td>
				Nombre
			</td>
			<td>
				Prix actuel
			</td>
			<td>
				Fin vente
			</td>
		</tr>
		<?php
		foreach($bourse->encheres as $enchere)
		{
			?>
		<tr>
			<td>
				<?php echo $enchere->ressource; ?>
			</td>
			<td>
				<?php echo $enchere->nombre; ?>
			</td>
			<td>
				<?php echo $enchere->prix.' ('.round(($enchere->prix / $enchere->nombre), 2).' / u)'; ?>
			</td>
			<td>
				<?php echo $enchere->fin_vente; ?>
			</td>
		</tr>
			<?php
		}
		?>
		</table>
		<?php
	}
	$requete = "SELECT * FROM diplomatie_demande WHERE royaume_recois = '".$joueur['race']."'";
	$req = $db->query($requete);
	if($db->num_rows > 0)
	{
	    echo '<h3>Demande(s) diplomatiques</h3>
	    <ul>';
	    while($row = $db->read_assoc($req))
	    {
	        echo '
	        <li>
	            Le roi '.$Gtrad[$row['royaume_demande']].' vous demande de passer en diplomatie et vous donne '.$row['stars'].' stars : '.$Gtrad['diplo'.$row['diplo']].'<br />
	            Accépter ? <a href="gestion_royaume.php?direction=diplomatie_demande&amp;reponse=oui&amp;id_demande='.$row['id'].';" onclick="return envoiInfo(this.href, \'conteneur\');">Oui</a> / <a href="gestion_royaume.php?direction=diplomatie_demande&amp;reponse=non&amp;id_demande='.$row['id'].';" onclick="return envoiInfo(this.href, \'conteneur\');">Non</a>
	        </li>';
	    }
	    ?>
	    </ul>
	    <?php
	}
?>
</div>
</div>
