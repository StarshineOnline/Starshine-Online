<?php
//Inclusion du haut du document html
include('haut_ajax.php');

$joueur = recupperso($_SESSION['ID']);

check_perso($joueur);

//Vérifie si le perso est mort
verif_mort($joueur, 1);

$W_case = $_GET['poscase'];
$W_requete = 'SELECT * FROM map WHERE ID =\''.sSQL($W_case).'\'';
$W_req = $db->query($W_requete);
$W_row = $db->read_array($W_req);
$R = get_royaume_info($joueur['race'], $W_row['royaume']);

if(!isset($_GET['type'])) $_GET['type'] = 'arme';

$_SESSION['position'] = convert_in_pos($joueur['x'], $joueur['y']);
?>
    	<h2 class="ville_titre"><?php if(verif_ville($joueur['x'], $joueur['y'])) return_ville( '<a href="javascript:envoiInfo(\'ville.php?poscase='.$W_case.'\', \'centre\')">'.$R['nom'].'</a> -', $W_case); ?> <?php echo '<a href="javascript:envoiInfo(\'taverne.php?poscase='.$W_case.'\',\'carte\')">';?> Taverne </a></h2>
		<?php include('ville_bas.php');?>	
		<div class="ville_test">
		<span class="texte_normal">
		Bien le bonjour ami voyageur !<br />
		<?php
		//Affichage des quêtes
		if($R['nom'] != 'Neutre') $return = affiche_quetes('taverne', $joueur);
		if($return[1] > 0 AND !array_key_exists('fort', $_GET))
		{
			echo 'Voici quelques petits services que j\'ai à vous proposer :';
			echo $return[0];
		}
		?></span></div><br /><?php
$W_distance = detection_distance($W_case,$_SESSION["position"]);
$W_coord = convert_in_coord($W_case);
if($W_distance == 0)
{
	if(isset($_GET['action']))
	{
		switch ($_GET['action'])
		{
			//Achat
			case 'achat' :
				$requete = "SELECT * FROM taverne WHERE id = ".sSQL($_GET['id']);
				$req = $db->query($requete);
				$row = $db->read_array($req);
				$taxe = ceil($row['star'] * $R['taxe'] / 100);
				$cout = $row['star'] + $taxe;
				if ($joueur['star'] >= $cout)
				{
					if($joueur['pa'] >= $row['pa'])
					{
						$valid = true;
						if($row['pute'] == 1)
						{
							$debuff = false;
							$buff = false;
							$honneur_need = $row['honneur'] + (($row['honneur_pc'] * $joueur['honneur']) / 100);
							if($joueur['honneur'] >= $honneur_need)
							{
								$joueur['honneur'] = $joueur['honneur'] - $honneur_need;
							}
							else $joueur['honneur'] = 0;
							
							//tirage au sort :
							$de = rand(1, 10) + rand(1, $joueur['puissance']);
							if($de <= 2)
							{
								$debuff = true;
								$texte = 'une affaire moyenne et vite expédiée. Vous avez l\'impression que vous avez fait la pire performance de votre vie. La fille de joie le pense aussi et vous chasse illico de la taverne, ou vous ne remettrez probablement pas les pieds pendant un moment. Vou sortez tout penaud en courant le plus vite possible pour ne plus entendre les rires de moqueries que s\'échangent les filles en vous montrant du doigt.';
							}
							elseif($de <= 5)
							{
								if(rand(1, 100) < 80)
								{
									$debuff = true;
								}
								$texte = 'vous avez été mauvais. Très mauvais même. Vous sortez de la chambre le moral à Zero. Vous ne reviendrez pas de sitôt, comme vous l\'a fait comprendre la fille en s\'endormant en plein milieu de l\'acte.';
							}
							elseif($de <= 10)
							{
								if(rand(1, 100) < 20) $debuff = true;
								{
									$debuff = true;
								}
								$texte = 'Une prestation moyenne. C\'est sur, vous avez connu mieux. C\'est peut être ce que vous avez mangé la veille qui vous a rendu si patraque. Une chose est sur, il faut vous changer les idées ailleurs qu\'ici, et ne plus y remettre les pieds pendant un moment.';
							}
							elseif($de <= 14)
							{
								$texte = 'une performance comme les autres. Une chose est sur, la fille de joie vous a déja oublié sitôt sortit de cet endroit.';
							}
							elseif($de <= 17)
							{
								if(rand(1, 100) < 20)
								{
									$buff = true;
								}
								$texte = 'une bonne performance. Vous sortez de la chambre un petit sourire aux lèvres. Une chose est sur, cet endroit été convivial. Vous reviendrez.';
							}
							elseif($de <= 19)
							{
								if(rand(1, 100) < 40)
								{
									$buff = true;
								}
								$texte = 'Vous sortez de la pièce heureux de votre performance. Vous étiez en forme, c\'est une certitude. Vous laisserez d\'heureux souvenirs à la fille que vous avez cotoyé, cette nuit oubliable restera gravée dans votre mémoire.';
							}
							elseif($de <= 21)
							{
								if(rand(1, 100) < 60)
								{
									$buff = true;
								}
								$texte = 'Une performance exceptionelle. Vous ne savez pas pourquoi, mais vous étiez dans une forme olympique. Ce fut toride et bestial. Vous vous sentez ragaillardi, et fermez la porte doucement, pour ne pas réveiller la fille de joie qui s\'est endormie aussitôt après l\'acte, tellement vus l\'avez épuisée. Un petit sourire orne le coin de vos lèvres quand vous l\'avez vu discrètement noter votre nom dans son carnet des personnes à recontacter à l\'avenir.';
							}
							else
							{
								if(rand(1, 100) < 80)
								{
									$buff = true;
								}
								$texte = 'Magnifique est un qualiquatif trop pauvre pour qualifier votre performance. Vous avez été tellement bon que la personne à qui vous avez fait l\'honneur de votre présence a eu une extinction de voie à force de criez votre nom. De plus, fait marquant, au milieu de l\'affaire, elle est partit et a appelée toute ses copines qui ont quitté leur chambre, laissant leurs clients sur la paille, pour venir profiter de votre journée de grâce. Ce n\'est donc pas moins que la taverne entière (tout sexe confondu) que vous avez honorées à la fois (sans payer plus cher). Une chose est sure, vous avez rendu des gens heureux aujourd\'hui.';
							}

							//lancement du buff ou debuff
							if($buff)
							{
								//Liste des buffs possibles (Identifiants dans la bdd)
								$liste_buff = array(82, 83, 80, 86, 20, 51, );
								//Tirage au sort de quel buff lancer
								$total_buff = count($liste_buff);
								$tirage = rand(0, $total_buff);
								$sort = $liste_buff[$tirage];
								echo $tirage;
								//On cherche le buff dans la bdd
								$requete = "SELECT * FROM sort_jeu WHERE id = ".$sort;
								$req = $db->query($requete);
								$row = $db->read_assoc($req);
								lance_buff($row['type'], $joueur['ID'], $row['effet'], $row['effet2'], $row['duree'], $row['nom'], description($row['description'], $row), 'perso', 0, 0, $joueur['rang_grade']);
								$texte .= '<br />En plus, vous recevez le buff : '.$row['nom'].' !!!';
							}
							elseif($debuff)
							{
								//Liste des debuff possibles (Identifiants dans la bdd)
								$liste_debuff = array(39, 35);
								//Tirage au sort de quel buff lancer
								$total_debuff = count($liste_debuff);
								$tirage = rand(0, $total_debuff);
								$sort = $liste_debuff[$tirage];
								echo $tirage;
								//On cherche le buff dans la bdd
								$requete = "SELECT * FROM sort_jeu WHERE id = ".$sort;
								$req = $db->query($requete);
								$row = $db->read_assoc($req);
								lance_buff($row['type'], $joueur['ID'], $row['effet'], $row['effet2'], $row['duree'], $row['nom'], description($row['description'], $row), 'perso', 1, 0, 0);
								$texte .= '<br />Ouch cette piètre prestation vous coupe le moral, vous recevez le debuff : '.$row['nom'].' !!!';
							}
							
							//maladie
							$pourcent_risque = 5;
							if(rand(1, 100) <= $pourcent_risque)
							{
								$maladie = true;
								//Liste des maladies possibles (Identifiants dans la bdd)
								$liste_maladie = array();
								//Tirage au sort de quel maladie lancer
								$total_maladie = count($liste_maladie);
								$tirage = rand(0, $total_maladie);
								$sort = $liste_maladie[$tirage];
								//On cherche le buff dans la bdd
								/*$requete = "SELECT * FROM sort_jeu WHERE id = ".$sort;
								$req = $db->query($requete);
								$row = $db->read_assoc($req);
								lance_buff($row['type'], $joueur['ID'], $row['effet'], $row['effet2'], $row['duree'], $row['nom'], description($row['description'], $row), 'perso', 1, 0, 0);*/
								$texte .= '<br />Phrase en fonction de la maladie : '.$row['nom'].' !!!';
							}
							if($buff) $buff_t = 'Ok'; else $buff_t = 'Nop';							
							if($debuff) $debuff_t = 'Ok'; else $debuff_t = 'Nop';							
							if($maladie) $maladie_t = 'Ok'; else $maladie_t = 'Nop';							
							echo '
							<h6>
								'.$texte.'<br />
								Buff : '.$buff_t.'<br />
								Debuff : '.$debuff_t.'<br />
								Maladie : '.$maladie_t.'
							</h6>';
						}
						if($valid)
						{
							$joueur['star'] = $joueur['star'] - $cout;
							$joueur['pa'] = $joueur['pa'] - $row['pa'];
							$joueur['hp'] = $joueur['hp'] + $row['hp'];
							if ($joueur['hp'] > $joueur['hp_max']) $joueur['hp'] = floor($joueur['hp_max']);
							$joueur['mp'] = $joueur['mp'] + $row['mp'];
							if ($joueur['mp'] > $joueur['mp_max']) $joueur['mp'] = floor($joueur['mp_max']);
							$requete = "UPDATE perso SET honneur = ".$joueur['honneur'].", star = ".$joueur['star'].", hp = ".$joueur['hp'].", mp = ".$joueur['mp'].", pa = ".$joueur['pa']." WHERE ID = ".$_SESSION['ID'];
							$req = $db->query($requete);
							//Récupération de la taxe
							if($taxe > 0)
							{
								$requete = 'UPDATE royaume SET star = star + '.$taxe.' WHERE ID = '.$R['ID'];
								$db->query($requete);
								$requete = "UPDATE argent_royaume SET taverne = taverne + ".$taxe." WHERE race = '".$R['race']."'";
								$db->query($requete);
							}
							echo '<h6>La taverne vous remercie de votre achat !</h6>';
						}
					}
					else
					{
						echo '<h5>Vous n\'avez pas assez de PA</h5>';
					}
				}
				else
				{
					echo '<h5>Vous n\'avez pas assez de Stars</h5>';
				}
			break;
		}
	}
	
	//Affichage de la taverne
	?>


	<div class="ville_test">
	<table class="marchand" cellspacing="0px">
	<tr class="header trcolor2">
		<td>
			Nom
		</td>
		<td>
			Stars
		</td>
		<td>
			Cout en PA
		</td>
		<td>
			Cout en Honneur
		</td>
		<td>
			HP gagné
		</td>
		<td>
			MP gagné
		</td>
		<td>
			Achat
		</td>
	</tr>
		
		<?php
		
		$color = 1;
		$where = "1";
		if(array_key_exists('part', $_GET))
		{
			$where .= " AND type = '".$_GET['part']."'";
		}
		$requete = "SELECT * FROM taverne";
		$req = $db->query($requete);
		while($row = $db->read_array($req))
		{
			$taxe = ceil($row['star'] * $R['taxe'] / 100);
			$cout = $row['star'] + $taxe;
			if(array_key_exists('fort', $_GET)) $fort = '&amp;fort=ok'; else $fort = '';
			//On vérifie le sexe du joueur
			$joueur['bonus'] = recup_bonus_total($joueur['ID']);
			if(array_key_exists(12, $joueur['bonus']) AND $joueur['bonus'][12]['valeur'] == 2) $champ = 'nom_f';
			else $champ = 'nom';
		?>
		<tr class="element trcolor<?php echo $color; ?>">
			<td>
				<?php echo $row[$champ]; ?>
			</td>
			<td>
				<?php echo $cout; ?>
			</td>
			<td>
				<?php echo $row['pa']; ?>
			</td>
			<td onmouseover="<?php echo make_overlib('Vous perdrez '.$row['honneur'].' + '.$row['honneur_pc'].'% points d\\\'honneur'); ?>" onmouseout="nd();">
				<?php echo ($row['honneur'] + ceil($joueur['honneur'] * $row['honneur_pc'] / 100)); ?>
			</td>
			<td onmouseover="<?php echo make_overlib('Vous regagnerez '.$row['hp'].' + '.$row['hp_pc'].'% HP'); ?>" onmouseout="nd();">
				<?php echo ($row['hp'] + ceil($joueur['hp_max'] * $row['hp_pc'] / 100)); ?>
			</td>
			<td onmouseover="<?php echo make_overlib('Vous regagnerez '.$row['mp'].' + '.$row['mp_pc'].'% MP'); ?>" onmouseout="nd();">
				<?php echo ($row['mp'] + ceil($joueur['mp_max'] * $row['mp_pc'] / 100)); ?>
			</td>
			<td>
				<a href="javascript:envoiInfo('taverne.php?action=achat&amp;id=<?php echo $row['ID']; ?>&amp;poscase=<?php echo $_GET['poscase'].$fort; ?>', 'carte')"><span class="achat">Achat</span></a>
			</td>
		</tr>
		<?php
			if($color == 1) $color = 2; else $color = 1;
		}
		
		?>
		
		</table>
		</div>


<?php
}
refresh_perso();
?>