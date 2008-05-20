<?php
//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include('haut_ajax.php');
//L'ID du du joueur attaqué
$W_ID = $_GET['ID'];

$joueur = recupperso($_SESSION['ID']);

$ennemi = 'batiment';
$defenseur = recupbatiment($_GET['id_batiment'], $_GET['table']);
$defenseur['type2'] = 'batiment';
$attaquant = recupbatiment($_GET['id_arme_de_siege'], 'construction');
$attaquant['type2'] = 'batiment';
$attaquant['melee'] = $attaquant['melee'] * (1 + ($joueur['craft'] / 100));
if($defenseur['type'] == 'arme_de_siege') $attaquant['arme_degat'] = $attaquant['arme_degat2'];
$attaquant['action_a'] = '!';
$defenseur['action_d'] = $defenseur['action_d'];

//Case du monstre
$W_case = $_GET['poscase'];
$W_requete = 'SELECT * FROM map WHERE ID =\''.sSQL($W_case).'\'';
$W_req = $db->query($W_requete);
$W_row = $db->read_array($W_req);
$R = get_royaume_info($joueur['race'], $W_row['royaume']);
$W_distance = detection_distance($W_case,$_SESSION["position"]);

?>
<h2>COMBAT</h2>
<?php
if($W_distance > $attaquant['arme_distance'])
{
	echo 'Vous êtes trop loin pour l\'attaquer !';
}
else
{
	$round_total = 1;
	$round = 1;
	$debugs = 0;
	$attaquant['etat'] = array();
	$defenseur['etat'] = array();
	if ($attaquant['rechargement'] <= time())
	{
		if($attaquant['hp'] > 0)
		{
			//Boucle principale qui fait durer le combat $round_total round
			while(($round < ($round_total + 1)) AND ($attaquant['hp'] > 0) AND ($defenseur['hp'] > 0))
			{
				$attaquant['comp'] = 'melee';
				$defenseur['comp'] = 'melee';
				//Calcul du potentiel de toucher et parer
				$attaquant['potentiel_toucher'] = round($attaquant[$attaquant['comp']] + ($attaquant[$attaquant['comp']] * ((pow($attaquant['dexterite'], 2)) / 1000)));
				$defenseur['potentiel_toucher'] = round($defenseur[$defenseur['comp']] + ($defenseur[$defenseur['comp']] * ((pow($defenseur['dexterite'], 2)) / 1000)));
				$attaquant['potentiel_parer'] = round($attaquant['esquive'] + ($attaquant['esquive'] * ((pow($attaquant['dexterite'], 2)) / 1000)));
				$defenseur['potentiel_parer'] = round($defenseur['esquive'] + ($defenseur['esquive'] * ((pow($defenseur['dexterite'], 2)) / 1000)));
				$actif['degat_sup'] = 0;
				$actif['degat_moins'] = 0;
				$passif['degat_sup'] = 0;
				$passif['degat_moins'] = 0;
				
				if ($mode == 'attaquant') $mode = 'defenseur';
				else ($mode = 'attaquant');
			
				if($mode == 'attaquant')
				{
					echo '<strong>Round '.$round.'</strong><br />';
					$etati = 0;
					$etatkeys = array_keys($defenseur['etat']);
					while($etati < count($defenseur['etat']))
					{
						$defenseur['etat'][$etatkeys[$etati]]['duree'] -= 1;
						if($defenseur['etat'][$etatkeys[$etati]]['duree'] <= 0) array_splice($defenseur['etat'], $etati, 1);
						else echo $defenseur['nom'].' est '.$etatkeys[$etati].' pour '.$defenseur['etat'][$etatkeys[$etati]]['duree'].'<br />';
						$etati++;
					}
				}
				else
				{
					$etati = 0;
					$etatkeys = array_keys($attaquant['etat']);
					while($etati < count($attaquant['etat']))
					{
						$attaquant['etat'][$etatkeys[$etati]]['duree'] -= 1;
						if($attaquant['etat'][$etatkeys[$etati]]['duree'] <= 0) array_splice($attaquant['etat'], $etati, 1);
						else echo $attaquant['nom'].' est '.$etatkeys[$etati].' pour '.$attaquant['etat'][$etatkeys[$etati]]['duree'].'<br />';
						$etati++;
					}
				}

					//Résolution des actions
					if($mode == 'attaquant') $mode_def = 'defenseur'; else $mode_def = 'attaquant';
					if($ennemi == 'monstre' OR $mode == 'attaquant') $action = script_action(${$mode}, ${$mode_def}, $mode);
					else $action[0] = '';
					$args = array();
					$args_def = array();
					$args_comp = array();
					//echo $action[0];
					switch($action[0])
					{
						//Attaque
						case 'attaque' :
							attaque($mode, ${$mode}['comp']);
						break;
					}
					$args[] = 'hp = '.$attaquant['hp'];
					$args_def[] = 'hp = '.$defenseur['hp'];
					
					//Update de la base de donnée.
					//Attaquant
					$time = time() + $attaquant['reload'];
					$requete = 'UPDATE construction SET rechargement = '.$time.' WHERE id = '.sSQL($_GET['id_arme_de_siege']);
					$req = $db->query($requete);
					//Defenseur
					$requete = 'UPDATE '.sSQL($_GET['table']).' SET hp = '.$defenseur['hp'].' WHERE id = '.sSQL($_GET['id_batiment']);
					$req = $db->query($requete);
				if($mode == 'defenseur') $round++;			
			}
			echo '<br /><table><tr style="text-align : center;"><td>'.$attaquant['nom'].'</td><td>'.$attaquant['hp'].' HP</td></tr><tr style="text-align : center;"><td>'.$defenseur['nom'].'</td><td>'.$defenseur['hp'].' HP</td></tr></table><br />';
			
			//Le défenseur est mort !
			if ($defenseur['hp'] <= 0)
			{
				if($ennemi == 'batiment')
				{
                   	//On supprime un bourg au compteur
	            	if($defenseur['type'] == 'bourg')
       		    	{
        		    	$requete = "UPDATE royaume SET bourg = bourg - 1 WHERE ID = ".$defenseur['ID'];
        		    	$db->query($requete);
       		    	}
					//On efface le batiment
					$requete = "DELETE FROM ".sSQL($_GET['table'])." WHERE ID = ".sSQL($_GET['id_batiment']);
					$req = $db->query($requete);
				}
			}
		}
		else
		{
			echo 'Vous êtes mort !<img src="image/pixel.gif" onload="window.location.reload();" />';
		}
	}
	else
	{
		echo 'Attendre avant rechargement de l\'arme de siège<br />';
	}
}
?>
<br />
<a onclick="for (i=0; i<<?php echo $debugs; ?>; i++) {if(document.getElementById('debug' + i).style.display == 'inline') document.getElementById('debug' + i).style.display = 'none'; else document.getElementById('debug' + i).style.display = 'inline';}">Debug</a><br />
<a href="javascript:envoiInfo('informationcase.php?case=<?php echo $W_case; ?>', 'information')">Retour aux informations de la case</a><br />
<img src="image/pixel.gif" onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />