 <?php
/// @deprecated
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
require_once('haut_roi.php');

	?>
	<fieldset>
		<legend>Effectuer un échange</legend>
		<?php
		//Si un identifiant d'echange est passé alors on récupère les infos sur cet échange
		if(array_key_exists('id_echange', $_GET))
		{
			$echange = recup_echange(sSQL($_GET['id_echange'], SSQL_INTEGER), true);
			$receveur = new royaume($echange['id_r2']);
			//Vérification si le joueur fait parti du donneur ou receveur
			if($Trace[$joueur->get_race()]['numrace'] != $echange['id_r1'] AND $Trace[$joueur->get_race()]['numrace'] != $echange['id_r2'])
			{
				?>
				Vous ne faîtes pas parti de cet échange...
				<?php
				exit();
			}
			else
			{
				$r1 = new royaume($echange['id_r1']);
				$r2 = new royaume($echange['id_r2']);
			}
		}
		//Sinon c'est le début d'un echange
		else
		{
			$W_ID = sSQL($_GET['id_race'], SSQL_INTEGER);
			$receveur = new royaume($W_ID);
			$r1 = new royaume($Trace[$joueur->get_race()]['numrace']);
			$r2 = new royaume($W_ID);
		}

		//Si on commence un nouvel échange
		if(array_key_exists('nouvel_echange', $_GET))
		{
			$requete = "INSERT INTO echange_royaume(id_r1, id_r2, statut, date_fin) VALUES(".$r1->get_id().", ".$r2->get_id().", 'creation', ".time().")";
			$db->query($requete);
			$echange = recup_echange($db->last_insert_id(), true);
			//On créé l'échange
		}
		elseif(array_key_exists('annule', $_GET))
		{
			//On passe l'échange en mode annulé
			$requete = "UPDATE echange_royaume SET statut = 'annule' WHERE id_echange = ".sSQL($_GET['id_echange']);
			if($db->query($requete))
			{
				?>
				<h5>L'échange a été supprimé.</h5>
				<?php
			}
			unset($echange);
		}

		//Si début d'un echange
		if(!isset($echange))
		{
			$W_ID = sSQL($_GET['id_race'], SSQL_INTEGER);
			$receveur = new royaume($W_ID);
			echo '<div class="bourse">';
			//On demande au joueurs si il veut faire un échange ou en récupérer un ancien
			$echanges = recup_echange_perso($Trace[$joueur->get_race()]['numrace'], $receveur->get_id(), true);
			//Il y a déjà eu des échanges
			if(count($echanges) > 0)
			{
				//Listing des échanges
				echo "<ul>";
				foreach($echanges as $echange_liste)
				{
				?>
					<li><a href="echange_royaume.php?id_echange=<?php echo $echange_liste['id_echange']; ?>" onclick="return envoiInfo(this.href, 'popup_content');">Echange ID : <?php echo $echange_liste['id_echange']; ?> - <?php echo $echange_liste['statut']; ?></a></li>
				<?php
				}
				?>
				</ul>
				<br />
				<a href="echange_royaume.php?id_race=<?php echo $W_ID; ?>&amp;nouvel_echange=true" onclick="return envoiInfo(this.href, 'popup_content');">Débuter un nouvel échange avec ce royaume.</a>
				</div>
				<?php
			}
			//Sinon on lui demande si il veut en créer un nouveau
			else
			{
				?>
				Vous n'avez actuellement aucun échange en cours avec ce royaume.<br />
				<br />
				<a href="echange_royaume.php?id_race=<?php echo $W_ID; ?>&amp;nouvel_echange=true" onclick="return envoiInfo(this.href, 'popup_content');">Débuter un nouvel échange avec ce royaume.</a>
				</div>
				<?php
			}
		}
		
		
		
//Validation d'étapes
if(array_key_exists('valid_etape', $_GET))
{
	switch($echange['statut'])
	{
		case 'creation' :
			//Ajout des ressources dans la bdd
			if(echange_royaume_ajout(sSQL($_GET['star'], SSQL_INTEGER), 'star', $echange['id_echange'], $Trace[$joueur->get_race()]['numrace'])
			AND echange_royaume_ajout(sSQL($_GET['pierre'], SSQL_INTEGER), 'pierre', $echange['id_echange'], $Trace[$joueur->get_race()]['numrace'])
			AND echange_royaume_ajout(sSQL($_GET['bois'], SSQL_INTEGER), 'bois', $echange['id_echange'], $Trace[$joueur->get_race()]['numrace'])
			AND echange_royaume_ajout(sSQL($_GET['eau'], SSQL_INTEGER), 'eau', $echange['id_echange'], $Trace[$joueur->get_race()]['numrace'])
			AND echange_royaume_ajout(sSQL($_GET['sable'], SSQL_INTEGER), 'sable', $echange['id_echange'], $Trace[$joueur->get_race()]['numrace'])
			AND echange_royaume_ajout(sSQL($_GET['charbon'], SSQL_INTEGER), 'charbon', $echange['id_echange'], $Trace[$joueur->get_race()]['numrace'])
			AND echange_royaume_ajout(sSQL($_GET['essence'], SSQL_INTEGER), 'essence', $echange['id_echange'], $Trace[$joueur->get_race()]['numrace'])
			AND echange_royaume_ajout(sSQL($_GET['food'], SSQL_INTEGER), 'food', $echange['id_echange'], $Trace[$joueur->get_race()]['numrace']))
			{
				$echange = recup_echange($echange['id_echange'], true);
			
				//On passe l'échange en mode proposition
				$requete = "UPDATE echange_royaume SET statut = 'proposition' WHERE id_echange = '".sSQL($_GET['id_echange'], SSQL_INTEGER)."'";
				if($db->query($requete))
				{
					echo '<h6>Votre proposition a bien été envoyée</h6>';
					unset($echange);
				}
			}
			else
			{
				echo "Vous n'avez pas assez de ressources";
			}
			
		break;
		case 'proposition' :
			if($r1->get_id() == $Trace[$joueur->get_race()]['numrace'])
				break;
			//Ajout des ressources dans la bdd
			if(echange_royaume_ajout(sSQL($_GET['star'], SSQL_INTEGER), 'star', $echange['id_echange'], $Trace[$joueur->get_race()]['numrace'])
			AND echange_royaume_ajout(sSQL($_GET['pierre'], SSQL_INTEGER), 'pierre', $echange['id_echange'], $Trace[$joueur->get_race()]['numrace'])
			AND echange_royaume_ajout(sSQL($_GET['bois'], SSQL_INTEGER), 'bois', $echange['id_echange'], $Trace[$joueur->get_race()]['numrace'])
			AND echange_royaume_ajout(sSQL($_GET['eau'], SSQL_INTEGER), 'eau', $echange['id_echange'], $Trace[$joueur->get_race()]['numrace'])
			AND echange_royaume_ajout(sSQL($_GET['sable'], SSQL_INTEGER), 'sable', $echange['id_echange'], $Trace[$joueur->get_race()]['numrace'])
			AND echange_royaume_ajout(sSQL($_GET['charbon'], SSQL_INTEGER), 'charbon', $echange['id_echange'], $Trace[$joueur->get_race()]['numrace'])
			AND echange_royaume_ajout(sSQL($_GET['essence'], SSQL_INTEGER), 'essence', $echange['id_echange'], $Trace[$joueur->get_race()]['numrace'])
			AND echange_royaume_ajout(sSQL($_GET['food'], SSQL_INTEGER), 'food', $echange['id_echange'], $Trace[$joueur->get_race()]['numrace']))
			{
				$echange = recup_echange($echange['id_echange'], true);
			
				//On passe l'échange en mode finalisation
				$requete = "UPDATE echange_royaume SET statut = 'finalisation' WHERE id_echange = '".sSQL($_GET['id_echange'], SSQL_INTEGER)."'";
				if($db->query($requete))
				{
					//C'est ok
					echo '<h6>Votre proposition a bien été envoyée</h6>';
					unset($echange);
				}
			}
			else
			{
				echo "Vous n'avez pas assez de ressources";
			}
		break;
		case 'finalisation' :
			//Finalisation de l'échange donc vérifications
			$req_tmp = $db->query("SELECT date_fin FROM echange_royaume WHERE statut = 'fini' AND ((id_r2 = '".$Trace[$row['race']]['numrace']."' AND id_r1 = '".$Trace[$joueur->get_race()]['numrace']."') OR (id_r1 = '".$Trace[$row['race']]['numrace']."' AND id_r2 = '".$Trace[$joueur->get_race()]['numrace']."')) ORDER BY date_fin DESC LIMIT 0,1");
			$row_tmp = $db->read_assoc($req_tmp);
			$temps = $row_tmp['date_fin'] + (60*60*24*7) - time();
				if(verif_echange_both_royaume(sSQL($_GET['id_echange'], SSQL_INTEGER), $r1->get_id(), $r2->get_id()) AND $temps < 0)
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
					$log_admin->send($joueur->get_id(), 'Echange royaume', $message_mail);
					
					//On met a jour le statut de l'échange
					//On passe l'échange en mode fini
					$requete = "UPDATE echange_royaume SET statut = 'fini', date_fin = '".time()."' WHERE id_echange = '".sSQL($_GET['id_echange'], SSQL_INTEGER)."'";
					if($db->query($requete))
					{
						//C'est ok
						echo '<h6>L\'échange s\'est déroulé avec succès</h6>';
						unset($echange);
					}
				}
				else
				{
					echo '<h5>Il manque des ressources à un royaume pour finaliser l\'échange, ou bien vous n\'avez pas attendu la fin du délai entre deux échanges</h5>';
				}
				
		break;
	}
}



if(isset($echange))
{
?>
<h3>Echange avec <?php echo $receveur->get_nom(); ?> - N° : <?php echo $echange['id_echange']; ?> - <?php echo $echange['statut']; ?></h3>
<div class="bourse">
<?php
	if(($echange['statut'] == 'proposition') OR ($echange['statut'] == 'finalisation') OR $echange['statut'] == 'fini')
	{
		?>
		Proposition de <?php echo $r1->get_nom(); ?> :
		<div>
		Ressources :
		<ul>
			<?php
			if(is_array($echange['ressource']))
			{
				$i = 0;
				$keys = array_keys($echange['ressource']);
				$count = count($echange['ressource']);
				while($i < $count)
				{
					if(array_key_exists($r1->get_id(), $echange['ressource'][$keys[$i]]))
					{
					?>
					<li><?php echo $keys[$i]." : ".$echange['ressource'][$keys[$i]][$r1->get_id()]['nombre']; ?></li>
					<?php
					}
					$i++;
				}
			}
			?>
		</ul>
		</div>
		<?php
	}
	if($echange['statut'] == 'finalisation' OR $echange['statut'] == 'fini')
	{
		?>
		<br />
		Proposition de <?php echo $r2->get_nom(); ?> :
		<div>
		Ressources :
		<ul>
			<?php
			if(is_array($echange['ressource']))
			{
				$i = 0;
				$keys = array_keys($echange['ressource']);
				$count = count($echange['ressource']);
				while($i < $count)
				{
					if(array_key_exists($r2->get_id(), $echange['ressource'][$keys[$i]]))
					{
					?>
					<li><?php echo $keys[$i]." : ".$echange['ressource'][$keys[$i]][$r2->get_id()]['nombre']; ?></li>
					<?php
					}
					$i++;
				}
			}
			?>
		</ul>
		</div>
		<?php
		if($echange['id_r1'] == $Trace[$joueur->get_race()]['numrace'] AND $echange['statut'] != 'fini')
		{
		?>
		<input type="button" value="Finir l'échange" onclick="envoiInfo('echange_royaume.php?id_echange=<?php echo $echange['id_echange']; ?>&amp;valid_etape=true', 'popup_content');" />
		<?php
		}
	}
	elseif(($echange['statut'] == 'creation' AND $echange['id_r1'] == $Trace[$joueur->get_race()]['numrace']) OR ($echange['statut'] == 'proposition' AND $echange['id_r2'] == $Trace[$joueur->get_race()]['numrace']))
	{
	?>
		Vous proposez :
		<div>
			<form method="post" action="envoiInfoPostData('echange_royaume.php?direction=echange&amp', 'information', 'message=' + message);">
				Stars : <input type="text" name="star" id="star" value="0" /><br />
				Pierre : <input type="text" name="pierre" id="pierre" value="0" /><br />
				Bois : <input type="text" name="bois" id="bois" value="0" /><br />
				Eau : <input type="text" name="eau" id="eau" value="0" /><br />
				Sable : <input type="text" name="sable" id="sable" value="0" /><br />
				Charbon : <input type="text" name="charbon" id="charbon" value="0" /><br />
				Essence Magique : <input type="text" name="essence" id="essence" value="0" /><br />
				Nourriture : <input type="text" name="food" id="food" value="0" /><br />
				<input type="button" value="Proposer ces éléments" onclick="envoiInfo('echange_royaume.php?id_echange=<?php echo $echange['id_echange']; ?>&amp;valid_etape=true&amp;star=' + document.getElementById('star').value + '&amp;pierre=' + document.getElementById('pierre').value + '&amp;bois=' + document.getElementById('bois').value + '&amp;eau=' + document.getElementById('eau').value + '&amp;sable=' + document.getElementById('sable').value + '&amp;charbon=' + document.getElementById('charbon').value + '&amp;essence=' + document.getElementById('essence').value + '&amp;food=' + document.getElementById('food').value, 'popup_content');" />
			</form>
		</div>
<?php
	}
	elseif($echange['statut'] == 'creation' AND $echange['id_j2'] == $joueur->get_id())
	{
		echo 'Un échange est en train d\'être créé';
	}
	elseif($echange['statut'] == 'proposition' AND $echange['id_j1'] == $joueur->get_id())
	{
		echo 'Votre proposition est étudiée par le joueur';
	}
}
echo '</div>';
	if($echange['statut'] != 'annule' AND isset($echange) AND $echange['statut'] != 'fini')
	{
		?>
		<input type="button" onclick="if(confirm('Voulez vous supprimer cet échange ?')) envoiInfo('echange_royaume.php?id_echange=<?php echo $echange['id_echange']; ?>&amp;annule=ok', 'popup_content');" value="Supprimer l'échange" />
		<?php
	}
?>
</legend>
