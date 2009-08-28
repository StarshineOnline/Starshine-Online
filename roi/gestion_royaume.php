<?php
if (file_exists('../root.php'))
  include_once('../root.php');

require('haut_roi.php');
if($joueur->get_rang_royaume() != 6)
	echo '<p>Cheater</p>';
	else if($_GET['direction'] == 'diplomatie')
	{
		$diplo = $royaume->get_diplo_time();
		$req = $db->query("SELECT * FROM diplomatie WHERE race = '".$joueur->get_race()."'");
		$row = $db->read_assoc($req);
		if($_GET['action'] == 'valid')
		{
			if($diplo[$_GET['race']] > time())
			{
			    echo 'Vous ne pouvez pas changer votre diplomatie avec ce royaume avant : <br />'.transform_sec_temp($diplo[$_GET['race']] - time()).'<br />';
			}
			else
			{
			    //Si modification moins, on envoi la demande à l'autre royaume
			    if($_GET['diplo'] == 'm')
			    {
				$requete = 'SELECT * FROM diplomatie_demande WHERE royaume_demande = \''.$joueur->get_race().'\' AND royaume_recois = \''.sSQL($_GET['race']).'\'';
				$db->query($requete);
				if(empty($db->num_rows))
				{
					$diplo_req = $row[$_GET['race']] - 1;
					$star = $_GET['star'];
					if($star > $royaume->get_star()) $star = $royaume->get_star();
					//Suppression des stars
					$db->query("UPDATE royaume SET star = star - ".$star." WHERE ID = ".$royaume->get_id()."");
					//Envoi de la demande
					$db->query("INSERT INTO diplomatie_demande VALUES(NULL, ".$diplo_req.", '".$joueur->get_race()."', '".sSQL($_GET['race'])."',  ".$star.")");
					echo 'Une demande au royaume '.$Gtrad[$_GET['race']].' pour passer en diplomatie : '.$Gtrad['diplo'.$diplo_req].' en échange de '.$star.' stars a été envoyée.<br /><br />';
				}
				else
					echo 'Une demande au royaume '.$Gtrad[$_GET['race']].' pour passer en diplomatie : '.$Gtrad['diplo'.$diplo_req].' est déjà en cours.<br /><br />';
			    }
			    //Sinon, on change la diplomatie.
			    else
			    {
			        $diplo_req = $row[$_GET['race']] + 1;
			        $duree = (pow(2, abs(5 - $diplo_req)) * 60 * 60 * 24);
			        $prochain_changement = time() + $duree;
			        //Requète de changement pour ce royaume
			        $requete = "UPDATE diplomatie SET ".sSQL($_GET['race'])." = ".$diplo_req." WHERE race = '".$joueur->get_race()."'";
			        $db->query($requete);
			        //Requète de changement pour l'autre royaume
			        $requete = "UPDATE diplomatie SET ".$joueur->get_race()." = ".$diplo_req." WHERE race = '".sSQL($_GET['race'])."'";
			        $db->query($requete);
			        $requete = "SELECT diplo_time FROM royaume WHERE race = '".sSQL($_GET['race'])."'";
			        $req = $db->query($requete);
			        $row2 = $db->read_assoc($req);
			        $row2['diplo_time'] = unserialize($row2['diplo_time']);
			        $row2['diplo_time'][$joueur->get_race()] = $prochain_changement;
			        $row2['diplo_time'] = serialize($row2['diplo_time']);
			        $diplo[$_GET['race']] = $prochain_changement;
			        $diplo = serialize($diplo);
			        $requete = "UPDATE royaume SET diplo_time = '".$row2['diplo_time']."' WHERE race = '".sSQL($_GET['race'])."'";
			        $db->query($requete);
			        $requete = "UPDATE royaume SET diplo_time = '".$diplo."' WHERE ID = ".$royaume->get_id();
			        $db->query($requete);
			        echo 'Vous êtes maintenant en '.$Gtrad['diplo'.$diplo_req].' avec les '.$Gtrad[$_GET['race']].'<br /><br />';
			        //Recherche du roi
			        $requete = "SELECT ID, nom FROM perso WHERE race = '".sSQL($_GET['race'])."' AND rang_royaume = 6";
			        $req = $db->query($requete);
			        $row_roi = $db->read_assoc($req);
			        //Envoi d'un message au roi
			        $message = 'Le roi des '.$Gtrad[$joueur->get_race()].' a changé son attitude diplomatique envers votre royaume en : '.$Gtrad['diplo'.$diplo_req];
			        $requete = "INSERT INTO message VALUES('', ".$row_roi['ID'].", 0, 'Mess. Auto', '".$row_roi['nom']."', 'Modification de diplomatie', '".$message."', '', '".time()."', 0)";
			        $db->query($requete);
			    }
			    $requete = "SELECT * FROM diplomatie WHERE race = '".$joueur->get_race()."'";
			    $req = $db->query($requete);
			    $row = $db->read_assoc($req);
			}
		}
		$i = 0;
		$keys = array_keys($row);
		$count = count($keys);
		echo 
		'<div id="diplomatie">
		<fieldset>
		
		<ul>';
		while($i < $count)
		{
			if($keys[$i] != 'race' AND $row[$keys[$i]] != 127)
			{
				$temps = $diplo[$keys[$i]] - time();
				if($temps > 0) $show = transform_sec_temp($temps).' avant changement possible';
				else $show = 'Modif. Possible';
				switch($row[$keys[$i]])
				{
					case '0' :
					$image_diplo = '../image/icone/diplomatie_paixdurable.png';					
					break;
					case '1' :
					$image_diplo = '../image/icone/diplomatie_paixdurable.png';					
					break;
					case '2' :
					$image_diplo = '../image/icone/diplomatie_paixdurable.png';
					break;
					case '3' :
					$image_diplo = '../image/icone/diplomatie_paix.png';					
					break;
					case '4' :
					$image_diplo = '../image/icone/diplomatie_bonterme.png';					
					break;
					case '5' :
					$image_diplo = '../image/icone/diplomatie_neutre.png';					
					break;
					case '6' :
					$image_diplo = '../image/icone/diplomatie_mauvaisterme.png';					
					break;
					case '7' :
					$image_diplo = '../image/icone/diplomatie_guerre.png';					
					break;
					case '8' :
					$image_diplo = '../image/icone/diplomatie_guerredurable.png';
					break;				
					case '9' :
					$image_diplo = '../image/icone/diplomatie_guerredurable.png';
					break;
					case '10' :
					$image_diplo = '../image/icone/diplomatie_guerredurable.png';
					break;
				}
		$requete = "SELECT id FROM perso WHERE rang_royaume = 6 AND race = '".$keys[$i]."'";
		$req_roi = $db->query($requete);
		$row_roi = $db->read_assoc($req_roi);
		$roi = new perso($row_roi['id']);				
		echo '
		<li class="'.$diplo_class.'">
		<span class="drapeau"><img src="../image/g_etendard/g_etendard_'.$Trace[$keys[$i]]['numrace'].'.png" style="vertical-align : middle;height:30px;">'.$Gtrad[$keys[$i]].'</span>
		<span class="diplo"><img src="'.$image_diplo.'" style="vertical-align : middle;height:25px;"> '.$Gtrad['diplo'.$row[$keys[$i]]].' </span>
		<span class="liens" style="cursor:pointer;"><a style="font-size : 0.8em;" onclick="affichePopUp(\'gestion_royaume.php\',\'direction=diplomatie_modif&amp;race='.$keys[$i].'\');"><span class="xsmall">'.$show.'</span></a></span>
		<span class="nom"><img src="../image/personnage/'.$roi->get_race().'/'.$roi->get_race().'_'.$Tclasse[$roi->get_classe()]["type"].'.png" alt="'.$roi->get_race().'" title="'.$roi->get_race().'" style="vertical-align: middle;float:left;height:28px;padding-right:15px;" />'.$roi->get_nom().'</span>
		<span style="cursor:pointer;" onclick="affichePopUp(\'telephone.php\',\'id_dest='.$roi->get_id().'\');"><img src="../image/interface/message.png" alt="Envoyer un message" title="Envoyer un message"></span>
		
		</li>';
			}
			$i++;
			if ($diplo_class == 't1'){$diplo_class = 't2';}else{$diplo_class = 't1';}	    

		}
		?>
		</ul>
		</fieldset>
		</div>
		<?php

	}
	elseif($_GET['direction'] == 'diplomatie_modif')
	{
			?>
			<h3>Modification de la diplomatie avec <?php echo $Gtrad[$_GET['race']]; ?></h3>
			Changer votre diplomatie pour :<br />
			<select name="diplo" id="diplo">
			<?php
			$req = $db->query("SELECT * FROM diplomatie WHERE race = '".$joueur->get_race()."'");
			$row = $db->read_assoc($req);
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
				$href_star = "' + $('star').value";
			}
			else $href_star = "0'";
			?>
			<input type="button" onclick="envoiInfo('gestion_royaume.php?direction=diplomatie&amp;action=valid&amp;race=<?php echo $_GET['race']; ?>&amp;diplo=' + $('diplo').value + '&amp;star=<?php echo $href_star; ?>, 'contenu_jeu');$('popup').hide();" value="Effectuer le changement diplomatique">
			<?php
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
	        $message = 'Le roi des '.$Gtrad[$joueur->get_race()].' a refusé votre demande diplomatique';
	        $requete = "INSERT INTO message VALUES('', ".$row_roi['ID'].", 0,'Mess. Auto', '".$row_roi['nom']."', 'Refus de diplomatie', '".$message."', '', '".time()."', 0)";
	        $db->query($requete);
	        //On redonne les stars
	        $requete = "UPDATE royaume SET star = star + ".$row['stars']." WHERE race = '".$row['royaume_demande']."'";
	        $db->query($requete);
	        echo '<h5>Demande refusée</h5>';
	    }
	    else
	    {
	        $diplo = $row['diplo'];
	        $duree = (pow(2, abs(5 - $diplo)) * 60 * 60 * 24);
	        $prochain_changement = time() + $duree;
	        //Requète de changement pour ce royaume
	        $requete = "UPDATE diplomatie SET ".$row['royaume_demande']." = ".$diplo." WHERE race = '".$joueur->get_race()."'";
	        $db->query($requete);
	        //On donne les stars au royaume qui recoit
	        $requete = "UPDATE royaume SET star = star + ".$row['stars']." WHERE race = '".$row['royaume_recois']."'";
	        $db->query($requete);
	        //Requète de changement pour l'autre royaume
	        $requete = "UPDATE diplomatie SET ".$joueur->get_race()." = ".$diplo." WHERE race = '".$row['royaume_demande']."'";
	        $db->query($requete);
	        $requete = "SELECT diplo_time FROM royaume WHERE race = '".$row['royaume_demande']."'";
	        $req = $db->query($requete);
	        $row2 = $db->read_assoc($req);
	        $row2['diplo_time'] = unserialize($row2['diplo_time']);
	        $row2['diplo_time'][$joueur->get_race()] = $prochain_changement;
	        $row2['diplo_time'] = serialize($row2['diplo_time']);
	        $row3['diplo_time'] = $R['diplo_time'];
	        $row3['diplo_time'][$row['royaume_demande']] = $prochain_changement;
	        $row3['diplo_time'] = serialize($row3['diplo_time']);
	        $requete = "UPDATE royaume SET diplo_time = '".$row2['diplo_time']."' WHERE race = '".$row['royaume_demande']."'";
	        $db->query($requete);
	        $requete = "UPDATE royaume SET diplo_time = '".$row3['diplo_time']."' WHERE race = '".$royaume->get_race()."'";
	        $db->query($requete);
	        echo '<h6>Vous êtes maintenant en '.$Gtrad['diplo'.$diplo].' avec les '.$Gtrad[$row['royaume_demande']].'</h6>';
	        //Envoi d'un message au roi
	        $message = 'Le roi des '.$Gtrad[$joueur->get_race()].' a accepté votre demande diplomatique'.(empty($row['stars']) ? '' : '.'.$row['stars'].' ont été versés à ce royaume.');
	        $requete = "INSERT INTO message VALUES('', ".$row_roi['ID'].", 0,'Mess. Auto', '".$row_roi['nom']."', 'Accord diplomatique', '".$message."', '', '".time()."', 0)";
	        $db->query($requete);
	    }
	}
	elseif($_GET['direction'] == 'construction')
	{
	    $requete = "SELECT *, construction_ville.id as id_const FROM construction_ville LEFT JOIN batiment_ville ON construction_ville.id_batiment = batiment_ville.id WHERE id_royaume = ".$royaume->get_id();
	    $req = $db->query($requete);
	    echo '
	    <h3>Liste des batiments de la ville :</h3>
	    <ul class="ville">';
	    while($row = $db->read_assoc($req))
	    {
	        if($row['statut'] == 'actif')
	        {
	        ?>
	        <li><?php echo $row['nom']; ?><span class="small">, entretien : <?php echo $row['entretien']; ?> <a href="gestion_royaume.php?direction=amelioration&amp;action=list&amp;batiment=<?php echo $row['type']; ?>" onclick="return envoiInfo(this.href, 'contenu_jeu')">Améliorer</a></li>
	        <?php
	    	}
	    	else
	    	{
	        ?>
	        <li><?php echo $row['nom']; ?><span class="small">, inactif <a href="gestion_royaume.php?direction=reactif&amp;action=list&amp;batiment=<?php echo $row['id_const']; ?>" onclick="if(confirm('Voulez vous vraiment réactiver cette construction ?')) return envoiInfo(this.href, 'contenu_jeu'); else return false;">Réactiver pour <?php echo $row['dette']; ?> stars</a></li>
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
	    if($royaume->get_star() >= $row['dette'])
	    {
	        $requete = "UPDATE construction_ville SET statut = 'actif', dette = 0 WHERE id = ".$id_batiment;
	        $db->query($requete);
	        $requete = "UPDATE royaume SET star = star - ".$row['dette']." WHERE ID = ".$royaume->get_id();
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
	    $requete = "SELECT *, construction_ville.id AS id_batiment_ville FROM construction_ville LEFT JOIN batiment_ville ON construction_ville.id_batiment = batiment_ville.id WHERE id_royaume = ".$royaume->get_id()." AND batiment_ville.type = '".$type."'";
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
	                <li><?php echo $row['nom']; ?>, coût : <?php echo $row['cout']; ?>, entretien par jour : <?php echo $row['entretien']; ?> <a href="gestion_royaume.php?direction=amelioration&amp;action=ameliore&amp;batiment=<?php echo $row['type']; ?>&amp;id_batiment=<?php echo $row['id']; ?>" onclick="return envoiInfo(this.href, 'contenu_jeu')">Améliorer</a></li>
	                <?php
	            }
	            ?>
	            </ul>
	            <?php
	        break;
	        case 'ameliore' :
	            $id_batiment = $_GET['id_batiment'];
	            $requete = "SELECT cout, nom, hp FROM batiment_ville WHERE id = ".$id_batiment;
	            $req = $db->query($requete);
	            $row = $db->read_assoc($req);
	            //Si le royaume a assez de stars on achète le batiment
	            if($royaume->get_star() >= $row['cout'])
	            {
	                //On paye
	                $royaume->set_star($royaume->get_star() - $row['cout']);
	                $royaume->sauver();
	                //On ajoute le batiment et on supprime l'ancien
	                $requete = "DELETE FROM construction_ville WHERE id = ".$id_batiment_ville;
	                $db->query($requete);
	                $requete = "INSERT INTO construction_ville VALUES ('', ".$royaume->get_id().", ".$id_batiment.", 'actif', '', ".$row['hp'].")";
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
		echo '<img src="carte_roy2.php?url='.$joueur->get_race().'" style="width:600px;margin-left:170px;" />';
	
	}
	elseif($_GET['direction'] == 'stats')
	{
		echo "<div id='stats'>";
	    //Statistiques du royaume
	    $requete = "SELECT *, COUNT(*) as tot FROM perso WHERE race = '".$joueur->get_race()."' AND statut = 'actif' GROUP BY classe ORDER BY tot DESC, classe ASC";
	    $req = $db->query($requete);
		$boutique_class = 't1';
	    
	    ?>
	    <fieldset>
	    <legend>Nombre de joueurs</legend>
	    <ul>
	    <li class='haut'>
	    	<span class='nom'>Classe</span>
	    	<span class='stats'>Nombre</span>
	    </li>
	    <?php
	    while($row = $db->read_array($req))
	    {
	        echo "
	        <li class='$boutique_class'>
	        	<span class='nom'>".$row['classe']."</span>
	        	<span class='stats'>".$row['tot']."</span>
	        </li>"; 
			if ($boutique_class == 't1'){$boutique_class = 't2';}else{$boutique_class = 't1';}	        
	    }
	    ?>
	    </ul>
	    </fieldset>
	    <?php
	    $requete = "SELECT nom, melee FROM perso WHERE race = '".$joueur->get_race()."' AND statut = 'actif' ORDER BY melee DESC LIMIT 0, 5";
	    $req = $db->query($requete);
		$boutique_class = 't1';
	    
	    ?>
	    <fieldset>	    
	    <legend>Meilleurs guerriers</legend>
	    <ul>
	    <li class='haut'>
	    	<span class='nom'>Nom</span>
	    	<span class='stats'>Mélée</span>
	    </li>
	    <?php
	    while($row = $db->read_array($req))
	    {
	        echo "
	        <li class='$boutique_class'>
	        	<span class='nom'>".$row['nom']."</span>
	        	<span class='stats'>".$row['melee']."</span>
	        </li>"; 
			if ($boutique_class == 't1'){$boutique_class = 't2';}else{$boutique_class = 't1';}	    
	    }
	    ?>
	    </ul>
	    </fieldset>	    
	    <?php
	    $requete = "SELECT nom, distance FROM perso WHERE race = '".$joueur->get_race()."' AND statut = 'actif' ORDER BY distance DESC LIMIT 0, 5";
	    $req = $db->query($requete);
		$boutique_class = 't1';
	    
	    ?>
	    <fieldset>	    	    
	    <legend>Meilleurs Archers</legend>
	    <ul>
	    <li class='haut'>
	    	<span class='nom'>Nom</span>
	    	<span class='stats'>Tir</span>
	    </li>
	    <?php
	    while($row = $db->read_array($req))
	    {
	        echo "
	        <li class='$boutique_class'>
	        	<span class='nom'>".$row['nom']."</span>
	        	<span class='stats'>".$row['distance']."</span>
	        </li>"; 
			if ($boutique_class == 't1'){$boutique_class = 't2';}else{$boutique_class = 't1';}
	    }
	    ?>
	    </ul>
	    </fieldset>	    
	    
	    <?php
	    $requete = "SELECT nom, esquive FROM perso WHERE race = '".$joueur->get_race()."' AND statut = 'actif' ORDER BY esquive DESC LIMIT 0, 5";
	    $req = $db->query($requete);
		$boutique_class = 't1';
	    
	    ?>
	    <fieldset>	    	    	    
	    <legend>Meilleurs esquiveurs</legend>
	    <ul>
	    <li class='haut'>
	    	<span class='nom'>Nom</span>
	    	<span class='stats'>Esquive</span>
	    </li>
	    <?php
	    while($row = $db->read_array($req))
	    {
	        echo "
	        <li class='$boutique_class'>
	        	<span class='nom'>".$row['nom']."</span>
	        	<span class='stats'>".$row['esquive']."</span>
	        </li>"; 
			if ($boutique_class == 't1'){$boutique_class = 't2';}else{$boutique_class = 't1';}
	    }
	    ?>
	    </ul>
	    </fieldset>	    	    	    	    
	    <?php
	    $requete = "SELECT nom, incantation FROM perso WHERE race = '".$joueur->get_race()."' AND statut = 'actif' ORDER BY incantation DESC LIMIT 0, 5";
	    $req = $db->query($requete);
		$boutique_class = 't1';	    
	    ?>
	    <fieldset>	    	    	    	    
	    <legend>Meilleurs mages</legend>
	    <ul>
	    <li class='haut'>
	    	<span class='nom'>Nom</span>
	    	<span class='stats'>Incantation</span>
	    </li>
	    <?php
	    while($row = $db->read_array($req))
	    {
	        echo "
	        <li class='$boutique_class'>
	        	<span class='nom'>".$row['nom']."</span>
	        	<span class='stats'>".$row['incantation']."</span>
	        </li>"; 
			if ($boutique_class == 't1'){$boutique_class = 't2';}else{$boutique_class = 't1';}
	    }
	    ?>
	    </ul>
	    </fieldset>	
	    </div>    	    	    
	    <?php
	}
	elseif($_GET['direction'] == 'criminel')
	{
		echo "<div id='criminel'>";
	    //Sélection de tous les joueurs ayant des points de crime
	    $requete = "SELECT * FROM perso WHERE crime > 0 AND race = '".$royaume->get_race()."' AND statut = 'actif' ORDER BY crime DESC";
	    $req = $db->query($requete);
	    ?>
	    <fieldset>	    	    	    	    
	    <ul>
	    <li class='haut'>
	    	<span class='nom'>Nom</span>
	    	<span class='crime'>Pts de crime</span>
	    	<span class='amende'>Amende</span>
	    </li>
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
	    <li>
	    	<span class='nom'>
	    		<?php echo $row['nom']; ?>
	    	</span>
	    	<span class='crime'>
	    		<?php echo $row['crime']; ?>
	    	</span>
	    	<span class='amende'>
	    		<?php echo $amende; ?>
	    	</span>
	    	<span class='amende'>
	    		<span onclick="affichePopUp('gestion_royaume.php?direction=gestion_criminel&amp;id=<?php echo $row['id']; ?>')">Gérer</span>
	    		<?php
	    		if($amende != 0)
	    		{
	        		?>
	        		/ <span onclick="affichePopUp('gestion_royaume.php?direction=suppr_criminel&amp;id=<?php echo $row['id']; ?>')">Supprimer</a>
	        		<?php
	    		}
	    		?>
	    	</span>
	    </li>
	    	<?php
	    }
	    ?>
	    </ul>
	    </fieldset>
	    </div>
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
	    $joueur = new perso($_GET['id']);
	    //Récupère l'amende
	    $amende = recup_amende($_GET['id']);
	    $amende_max = ($joueur->get_crime() * $joueur->get_crime()) * 10;
	    $etats = array('normal');
	    if($joueur->get_crime() > 30) $etats[] = 'bandit';
	    if($joueur->get_crime() > 60) $etats[] = 'criminel';
        ?>
    	<input type="checkbox" name="acces_ville" id="acces_ville" /> Empèche le joueur d'accéder à la ville<br />
    	<input type="checkbox" name="spawn_ville" id="spawn_ville" <?php if($joueur->get_crime() > 30) echo 'disabled'; ?> /> Empèche de renaître à la ville<br />
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
    	 Montant de l'amende (max : <?php echo $amende_max; ?>) <input type="text" name="montant" id="montant" value='<?php echo $amende['montant'];?>' /><br />
    	 <br />
    	 <input type="submit" value="Valider cette amende" onclick="envoiInfo('gestion_royaume.php?direction=gestion_criminel2&amp;id=<?php echo $joueur->get_id(); ?>&amp;acces_ville=' + $('acces_ville').checked + '&amp;spawn_ville=' + $('spawn_ville').checked + '&amp;statut=' + $('statut').value + '&amp;montant=' + $('montant').value, 'message_confirm');$('popup').hide();envoiInfo('gestion_royaume.php?direction=criminel','contenu_jeu');" />
	       <?php
	    
	}
	elseif($_GET['direction'] == 'gestion_criminel2')
	{
	    $joueur = new perso($_GET['id']);
	    //Récupère l'amende
	    $amende = recup_amende($_GET['id']);
	    $amende_max = ($joueur->get_crime() * $joueur->get_crime()) * 10;
	    //Vérification d'usage
	    if($_GET['montant'] > 0)
	    {
	        if($_GET['montant'] <= $amende_max)
	        { 
	        	if($_GET['spawn_ville'] == 'true') $spawn_ville = 'y'; else $spawn_ville = 'n';
	        	if($_GET['acces_ville'] == 'true') $acces_ville = 'y'; else $acces_ville = 'n';
	        	//Inscription de l'amende dans la bdd
	        	$req_test = $db->query("SELECT * FROM amende WHERE id_joueur = ".$joueur->get_id()."");
	        	if ($db->num_rows>0)
	        	{
		        	$requete = "UPDATE amende SET montant = ".sSQL($_GET['montant'])."
		        								AND acces_ville = '".$acces_ville."'
		        								AND respawn_ville = '".$spawn_ville."'
		        								AND statut = '".sSQL($_GET['statut'])."
		        								WHERE id_joueur = ".$joueur->get_id()."')";
		        	if($db->query($requete))
		        	{
		            	$amende = recup_amende($joueur->get_id());
		            	$requete = "UPDATE perso SET amende = ".$amende['id']." WHERE ID = ".$joueur->get_id();
		            	if($db->query($requete)) echo '<h6>Amende bien prise en compte !</h6>';
		        	}
	        	}
	        	else
	        	{
		        	$requete = "INSERT INTO amende(id, id_joueur, id_royaume, montant, acces_ville, respawn_ville, statut) VALUES ('', ".$joueur->get_id().", ".$Trace[$joueur->get_race()]['numrace'].", ".sSQL($_GET['montant']).", '".$acces_ville."', '".$spawn_ville."', '".sSQL($_GET['statut'])."')";
		        	if($db->query($requete))
		        	{
		            	$amende = recup_amende($joueur->get_id());
		            	$requete = "UPDATE perso SET amende = ".$amende['id']." WHERE ID = ".$joueur->get_id();
		            	if($db->query($requete)) echo '<h6>Amende bien prise en compte !</h6>';
		        	}
	        	}
	        	
	    	}
	    	else
	    	{
	        	echo '<h5>Le montant de l\'amende est trop élevé</h5>';
	    	}
	    }
	}
	elseif($_GET['direction'] == 'achat_militaire')
	{
	}
	elseif($_GET['direction'] == 'boutique')
	{
		echo "
		<div id='boutique'>
		<ul>	
		<li class='haut' style='height:30px !important;line-height:30px !important;'>
			<span class='boutique_nom'>Nom</span>
			<span class='boutique_prix'><img src='../image/starsv2.png' alt='Prix' title='Prix' /></span>
			<span class='boutique_pierre'><img src='../image/icone/ressources_pierre.png' alt='pierre' title='Pierre' /></span>
			<span class='boutique_bois'><img src='../image/icone/ressources_bois.png' alt='Bois' title='Bois' /></span>
			<span class='boutique_eau'><img src='../image/icone/ressources_eau.png' alt='Eau' title='Eau'' /></span>
			<span class='boutique_sable'><img src='../image/icone/ressources_sable.png' alt='Sable' title='Sable' /></span>
			<span class='boutique_charbon'><img src='../image/icone/ressources_charbon.png' alt='Charbon' title='Charbon' /></span>
			<span class='boutique_essence'><img src='../image/icone/ressources_essence.png' alt='Essence Magique' title='Essence Magique' /></span>
			<span class='boutique_nombre'>Nombre</span>
			<span class='boutique_nom'>Achat</span>
		</li>";

		$requete = "SELECT *, objet_royaume.id as oid FROM objet_royaume LEFT JOIN batiment ON batiment.id = objet_royaume.id_batiment";
		$req = $db->query($requete);
		$i = 0;
		$boutique_class = 't1';
		while($row = $db->read_assoc($req))
		{
			$overlib = $row['description'];

			echo "<li class='$boutique_class'>
				<span class='boutique_nom'>".$row['nom']."</span>
				<span class='boutique_prix' title='Prix'"; if ($royaume->get_star()<$row['prix']){echo " style='font-style: italic;color:#BF0008;'";} echo ">".$row['prix']."</span>
				<span class='boutique_pierre' title='Cout en pierre'"; if ($royaume->get_pierre()<$row['pierre']){echo " style='font-style: italic;color:#BF0008;'";} echo ">".$row['pierre']."</span>
				<span class='boutique_bois' title='Cout en bois'"; if ($royaume->get_bois()<$row['bois']){echo " style='font-style: italic;color:#BF0008;'";} echo ">".$row['bois']."</span>
				<span class='boutique_eau' title='Cout en eau'"; if ($royaume->get_eau()<$row['eau']){echo " style='font-style: italic;color:#BF0008;'";} echo ">".$row['eau']."</span>
				<span class='boutique_sable' title='Cout en sable'"; if ($royaume->get_sable()<$row['sable']){echo " style='font-style: italic;color:#BF0008;'";} echo ">".$row['sable']."</span>
				<span class='boutique_charbon' title='Cout en charbon'"; if ($royaume->get_charbon()<$row['charbon']){echo " style='font-style: italic;color:#BF0008;'";} echo ">".$row['charbon']."</span>
				<span class='boutique_essence' title='Cout en Essence magique'"; if ($royaume->get_essence()<$row['essence']){echo " style='font-style: italic;color:#BF0008;'";} echo ">".$row['essence']."</span>
				<span class='boutique_nombre'><input type='text' id='nbr$i' value='0' /></span>
				<span class='boutique_nom'><a href='#' onclick=\"royaume_update('".$row['oid']."',$('nbr".$i."').value, 'update_objet_royaume')\">Acheter</a></span>
				</li>";
				if ($boutique_class == 't1'){$boutique_class = 't2';}else{$boutique_class = 't1';}
			$i++;
		}
		echo "
		</ul>
		</div>";
	}
	elseif($_GET['direction'] == 'bourse_enchere')
	{
		require_once(root.'class/bourse_royaume.class.php');
		require_once(root.'class/bourse.class.php');
		$enchere = new bourse_royaume($_GET['id_enchere']);
		//On vérifie que c'est un royaume possible
		if($royaume->get_id() != $enchere->id_royaume AND $royaume->get_id() != $enchere->id_royaume_acheteur)
		{
			$prix = ceil($enchere->prix * 1.1);
			//On vérifie que le royaume a assez de stars
			if($royaume->get_star() >= $prix)
			{
				//On rend les stars à l'autre royaume (si l'id est différent de 0)
				if($enchere->id_royaume_acheteur)
				{
					$requete = "UPDATE royaume SET star = star + ".$enchere->prix." WHERE ID = ".$enchere->id_royaume_acheteur;
					$db->query($requete);
				}
				//On prend les stars de notre royaume
				$requete = "UPDATE royaume SET star = star - ".$prix." WHERE ID = ".$royaume->get_id();
				$db->query($requete);
				//On met à jour l'enchère
				$enchere->id_royaume_acheteur = $royaume->get_id();
				$enchere->prix = $prix;
				//Si enchère faite 6h avant la fin, on décale l'enchère de 6h
				$decalage = 6 * 60 * 60;
				if(time() > ($enchere->get_fin_vente() - $decalage))
				{
					$enchere->set_fin_vente($enchere->get_fin_vente() + $decalage);
				}
				$enchere->sauver();
				?>
				<h6>Enchère prise en compte !</h6>
				<a href="gestion_royaume.php?direction=bourse" onclick="return envoiInfo(this.href, 'contenu_jeu');">Revenir à la bourse</a>
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
		require_once(root.'class/bourse_royaume.class.php');
		require_once(root.'class/bourse.class.php');
		$enchere = new bourse_royaume();
		$ressource = $_GET['ressource'];
		$nombre = $_GET['nombre'];
		$prix = $_GET['prix'];
		//On vérifie que le royaume a assez de cette ressource
		if($R[$ressource] >= $nombre)
		{
			$enchere->id_royaume = $royaume->get_id();
			$enchere->ressource = $ressource;
			$enchere->nombre = $nombre;
			$enchere->prix = $prix;
			//7 jours plus tard
			$time = time() + 7 * (24 * 60 * 60);
			$enchere->fin_vente = date("Y-m-d H:i:s", $time);
			$enchere->sauver();
			//On enlève les ressources au royaume
			$requete = "UPDATE royaume SET ".$ressource." = ".$ressource." - ".$nombre." WHERE ID = ".$royaume->get_id();
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
		require_once(root.'class/bourse_royaume.class.php');
		require_once(root.'class/bourse.class.php');
		$bourse = new bourse($royaume->get_id());
		$bourse->check_encheres();
		$bourse->get_encheres('DESC', 'actif = 1 AND id_royaume != '.$royaume->get_id().' AND id_royaume_acheteur != '.$royaume->get_id());
			//
		?>
		<div id='bourse'>
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
				<input type="button" onclick="if(confirm('Voullez vous mettre ' + $(nbr).value + ' ' + $(ressource).value + ' en vente à ' + $(prix).value + ' stars ?')) return envoiInfo('gestion_royaume.php?direction=bourse_ressource&amp;ressource=' + $(ressource).value + '&amp;prix=' + $(prix).value + '&amp;nombre=' + $(nbr).value, 'contenu_jeu'); else return false;" value="Valider" /><br />
			</div>
			<a href="" onclick="Effect.toggle('ajout_ressource', 'slide'); return false;">Mettre des ressources aux enchères</a>
		</div>
		<div style="position : absolute; left : 600px; background-color : grey; padding : 5px 10px 5px 10px;">
			<div id="cout_ressource" style="position : relative; right : 0px; display : none; z-index : 10;">
				<ul>
				<?php
				$requete = "SELECT *, (SUM(prix / nombre) / COUNT(*)) as moyenne, COUNT(*) as tot FROM `bourse_royaume` WHERE `id_royaume_acheteur` != 0 AND `actif` = 0 AND fin_vente > DATE_SUB(NOW(), INTERVAL 31 DAY) GROUP BY ressource";
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
		<fieldset>
		<legend>Enchères en cours</legend>
		<ul>
		<li class='haut'>
			<span class='ressourse'>Ressource</span>
			<span class='nombre'>Nombre</span>
			<span class='prix'>Prix actuel</span>
			<span class='finvente'>Fin vente</span>
		</li>
		<?php
		$class='t2';
		foreach($bourse->encheres as $enchere)
		{
			$time = strtotime($enchere->fin_vente);
			$restant = transform_sec_temp(($time - time()));
			$prix = ceil($enchere->prix * 1.1);
			
			echo "<li class='$class'>";
			?>
			<span class='ressourse'><span class='<?php echo $enchere->ressource; ?>'></span><?php echo $enchere->ressource; ?></span>
			<span class='nombre'><?php echo $enchere->nombre; ?></span>
			<span class='prix'><?php echo $enchere->prix.' ('.round(($enchere->prix / $enchere->nombre), 2).' / u)'; ?></span>
			<span class='finvente'><?php echo $restant; ?></span>
			<span><a href="gestion_royaume.php?direction=bourse_enchere&amp;id_enchere=<?php echo $enchere->id_bourse_royaume; ?>" onclick="return envoiInfo(this.href, 'message_confirm');envoiInfo('gestion_royaume.php?direction=bourse', 'contenu_jeu')">Enchérir pour <?php echo $prix; ?> stars (<?php echo ($prix / $enchere->nombre); ?> / u)</a></span>
			</li>
			<?php
			if ($class=='t1'){$class='t2';}else{$class='t1';}
		}
		?>
		</ul>
		</fieldset>
		<?php
		$bourse->encheres = array();
		$bourse->get_encheres('DESC', 'actif = 1 AND id_royaume_acheteur = '.$royaume->get_id());
		?>
		<fieldset class='moitie'>
		<legend>Vos mises</legend>
		<ul>
		<li class='haut'>
			<span class='ressourse'>Ressource</span>
			<span class='nombre'>Nombre</span>
			<span class='prix'>Prix actuel</span>
			<span class='finvente'>Fin vente</span>
		</li>
		<?php
		$class='t2';
		foreach($bourse->encheres as $enchere)
		{
			$time = strtotime($enchere->fin_vente);
			$restant = transform_sec_temp(($time - time()));
			
			echo "<li class='$class'>";
			?>			
			<span class='ressourse'><span class='<?php echo $enchere->ressource; ?>'></span><?php echo $enchere->ressource; ?></span>
			<span class='nombre'><?php echo $enchere->nombre; ?></span>
			<span class='prix'><?php echo $enchere->prix.' ('.round(($enchere->prix / $enchere->nombre), 2).' / u)'; ?></span>
			<span class='finvente'><?php echo $restant; ?></span>
			</li>
			<?php
			if ($class=='t1'){$class='t2';}else{$class='t1';}			
		}
		?>
		</ul>
		</fieldset>
		<?php
		$bourse->encheres = array();
		$bourse->get_encheres('DESC', 'actif = 1 AND id_royaume = '.$royaume->get_id());
		?>
		<fieldset class='moitie'>
		<legend>Vos ressources en vente</legend>
		<ul>
		<li class='haut'>
			<span class='ressourse'>Ressource</span>
			<span class='nombre'>Nombre</span>
			<span class='prix'>Prix actuel</span>
			<span class='finvente'>Fin vente</span>
			
		</li>
		<?php
		$class='t2';
		
		foreach($bourse->encheres as $enchere)
		{
		
			$time = strtotime($enchere->fin_vente);
			$restant = transform_sec_temp(($time - time()));
			if($enchere->id_royaume_acheteur != 0) $acheteur = true;
			else $acheteur = false;
			echo "<li class='$class'>";
			?>			
			<span class='ressourse'><span class='<?php echo $enchere->ressource; ?>'></span><?php echo $enchere->ressource; ?></span>
			<span class='nombre'><?php echo $enchere->nombre; ?></span>
			<span class='prix'><?php echo $enchere->prix.' ('.round(($enchere->prix / $enchere->nombre), 2).' / u)'; ?></span>
			<span class='finvente'><?php echo $restant; ?></span>
			<?php
			if ($acheteur){echo "<span class='acheteur' title='Il y a un acheteur sur cette offre'></span>";}
			echo "</li>";
			if ($class=='t1'){$class='t2';}else{$class='t1';}			
		
		}
		?>
		</ul>
		</fieldset>
		<?php
		$bourse->encheres = array();
		$time = time() - 7 * (24 * 60 * 60);
		$date = date("Y-m-d H:i:s", $time);
		$bourse->get_encheres('DESC', 'actif = 0 AND fin_vente > "'.$date.'" AND id_royaume_acheteur = '.$royaume->get_id());
		?>
		<fieldset class='moitie'>
		<legend>Enchères remportées les 7 derniers jours</legend>
		<ul>
		<li class='haut'>
			<span class='ressourse'>Ressource</span>
			<span class='nombre'>Nombre</span>
			<span class='prix'>Prix actuel</span>
			<span class='finvente'>Fin vente</span>
		</li>
		<?php
		$class='t2';
		
		foreach($bourse->encheres as $enchere)
		{
			echo "<li class='$class'>";
			?>			
			<span class='ressourse'><span class='<?php echo $enchere->ressource; ?>'></span><?php echo $enchere->ressource; ?></span>
			<span class='nombre'><?php echo $enchere->nombre; ?></span>
			<span class='prix'><?php echo $enchere->prix.' ('.round(($enchere->prix / $enchere->nombre), 2).' / u)'; ?></span>
			<span class='finvente'><?php echo $enchere->fin_vente; ?></span>
		</li>
		<?php
			if ($class=='t1'){$class='t2';}else{$class='t1';}			
		
		}
		?>
		</ul>
		</fieldset>
		<?php
		$bourse->encheres = array();
		$time = time() - 7 * (24 * 60 * 60);
		$date = date("Y-m-d H:i:s", $time);
		$bourse->get_encheres('DESC', 'actif = 0 AND fin_vente > "'.$date.'" AND id_royaume_acheteur != 0 AND id_royaume = '.$royaume->get_id());
		?>
		<fieldset class='moitie'>
		<legend>Ressources vendues les 7 derniers jours</legend>
		<ul>
		<li class='haut'>
			<span class='ressourse'>Ressource</span>
			<span class='nombre'>Nombre</span>
			<span class='prix'>Prix actuel</span>
			<span class='finvente'>Fin vente</span>
		</li>
		<?php
		$class='t2';
		
		foreach($bourse->encheres as $enchere)
		{
			echo "<li class='$class'>";
			?>			
			<span class='ressourse'><span class='<?php echo $enchere->ressource; ?>'></span><?php echo $enchere->ressource; ?></span>
			<span class='nombre'><?php echo $enchere->nombre; ?></span>
			<span class='prix'><?php echo $enchere->prix.' ('.round(($enchere->prix / $enchere->nombre), 2).' / u)'; ?></span>
			<span class='finvente'><?php echo $enchere->fin_vente; ?></span>
		</li>
		<?php
		if ($class=='t1'){$class='t2';}else{$class='t1';}			
		
		}
		?>
		</ul>
		</fieldset>
		<?php
	}
	$requete = "SELECT * FROM diplomatie_demande WHERE royaume_recois = '".$joueur->get_race()."'";
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
	            Accépter ? <a href="gestion_royaume.php?direction=diplomatie_demande&amp;reponse=oui&amp;id_demande='.$row['id'].';" onclick="return envoiInfo(this.href, \'contenu_jeu\');">Oui</a> / <a href="gestion_royaume.php?direction=diplomatie_demande&amp;reponse=non&amp;id_demande='.$row['id'].';" onclick="return envoiInfo(this.href, \'contenu_jeu\');">Non</a>
	        </li>';
	    }
	    ?>
	    </ul>
	    <?php
	}
?>

