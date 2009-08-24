<?php
if (file_exists('root.php'))
  include_once('root.php');

//Inclusion du haut du document html
include_once(root.'haut_ajax.php');

$joueur = new perso($_SESSION['ID']);
$joueur->check_perso();

//Vérifie si le perso est mort
verif_mort($joueur, 1);

$W_requete = 'SELECT royaume, type FROM map WHERE ID =\''.sSQL($joueur->get_pos()).'\'';
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);
$R->get_diplo($joueur->get_race());
?>
   	<h2 class="ville_titre"><?php echo '<a href="ville.php?poscase='.$W_case.'" onclick="return envoiInfo(this.href, \'centre\')">';?><?php echo $R->get_nom();?></a> - <?php echo '<a href="universite.php?poscase='.$W_case.'" onclick="return envoiInfo(this.href, \'carte\')">';?> Université </a></h2>
		<?php include_once(root.'ville_bas.php');?>
<?php
if($W_row['type'] == 1)
{
	if(isset($_GET['action']))
	{
		$requete = "SELECT * FROM classe WHERE id = '".sSQL($_GET['id'])."'";
		$req = $db->query($requete);
		$row = $db->read_array($req);
		$nom = $row['nom'];
		$description = $row['description'];
		$requete = "SELECT * FROM classe_requis WHERE id_classe = '".sSQL($_GET['id'])."'";
		$req = $db->query($requete);
		switch ($_GET['action'])
		{
			//Description de la classe
			case 'description' :
			?>
				<div class="ville_test">
				<span class="texte_normal">
				<h3 class="ville_haut"><?php echo $nom; ?></h3>
				<?php echo nl2br($description); ?>
				<h3>Requis</h3>
				<ul>
					<?php
					while($row = $db->read_array($req))
					{
						if($row['competence'] == 'classe')
						{
							$requete = "SELECT * FROM classe WHERE id = ".$row['requis'];
							$req_classe = $db->query($requete);
							$row_classe = $db->read_array($req_classe);
							$row['requis'] = $row_classe['nom'];
						}
						echo '<li>'.$Gtrad[$row['competence']].' : '.$row['requis'].'</li>';
					}
					?>
				</ul>
				<?php
				$requete = "SELECT * FROM classe_permet WHERE id_classe = '".sSQL($_GET['id'])."'";
				$req = $db->query($requete);
				?>
				<h3>Permet</h3>
				<ul>
					<?php
					while($row = $db->read_array($req))
					{
						echo '<li>'.ucwords($Gtrad[$row['competence']]).' : '.$row['permet'].'</li>';
					}
					?>
				</ul>
				<br />
				<?php
				$requete = "SELECT * FROM classe_comp_permet WHERE id_classe = '".sSQL($_GET['id'])."'";
				$req = $db->query($requete);
				?>
				<h3>Donne ces compétences</h3>
				<ul>
					<?php
					$comps = array();
					$i = 0;
					while($row = $db->read_array($req))
					{
						if($row['type'] == 'comp_combat') $comps[0][$i] = $row['competence'];
						else $comps[1][$i] = $row['competence'];
						$i++;
					}
					$count = count($comps[0]);
					if($count > 0)
					{
						$comps0 = implode(', ', $comps[0]);
						$requete = "SELECT * FROM comp_combat WHERE id IN (".$comps0.")";
						$req = $db->query($requete);
						while($row = $db->read_array($req))
						{
							?>
							<li onmousemove="afficheInfo('infob_<?php echo $row['id']; ?>', 'block', event);" onmouseout="afficheInfo('infob_<?php echo $row['id']; ?>', 'none', event );"><?php echo ucwords($row['nom']); ?></li>
							<div style="display: none; z-index: 2; position: absolute; top: 250px; right: 150px; background-color:#ffffff; border: 1px solid #000000; font-size:12px; width: 200px; padding: 5px;" id="infob_<?php echo $row['id']; ?>">
							<?php
							$row['cible2'] = $G_cibles[$row['cible']];
							echo description('[%cible2%] '.$row['description'], $row);
							?>
							</div>
							<?php
						}
					}
					$count = count($comps[1]);
					if($count > 0)
					{
						$comps1 = implode(', ', $comps[1]);
						$requete = "SELECT * FROM comp_jeu WHERE id IN (".$comps1.")";
						$req = $db->query($requete);
						while($row = $db->read_array($req))
						{
							?>
							<li onmousemove="afficheInfo('infob_<?php echo $row['id']; ?>', 'block', event);" onmouseout="afficheInfo('infob_<?php echo $row['id']; ?>', 'none', event );"><?php echo ucwords($row['nom']); ?></li>
							<div style="display: none; z-index: 2; position: absolute; top: 250px; right: 150px; background-color:#ffffff; border: 1px solid #000000; font-size:12px; width: 200px; padding: 5px;" id="infob_<?php echo $row['id']; ?>">
							<?php
							$row['cible2'] = $G_cibles[$row['cible']];
							echo description('[%cible2%] '.$row['description'], $row);
							?>
							</div>
							<?php
						}
					}
					?>
				</ul>
				<br />
				<br />
				<a href="universite.php?action=prendre&amp;id=<?php echo $_GET['id']; ?>&amp;poscase=<?php echo $_GET['poscase']; ?>" onclick="return envoiInfo(this.href, 'carte')">Suivre la voie du <?php echo $nom; ?></a>
				</span>
				</tr></td>
				</table>
				</div>

			<?php
			break;
			//Prise de la classe
			case 'prendre' :
				$fin = false;
				$requete = "SELECT * FROM classe_requis WHERE id_classe = '".sSQL($_GET['id'])."'";
				$req = $db->query($requete);
				while($row = $db->read_array($req))
				{
					if($row['new'] == 'yes') $new[] = $row['competence'];
					if($row['competence'] == 'classe')
					{
						$requete = "SELECT * FROM classe WHERE id = ".$row['requis'];
						$req_classe = $db->query($requete);
						$row_classe = $db->read_array($req_classe);
						if(strtolower($row_classe['nom']) != strtolower($joueur->get_classe()))
						{
							echo 'Il vous faut être un '.$row_classe['nom'].'<br />';
							$fin = true;
						}
					}
					else
					{
						$get = 'get_'.$row['competence'];
						if((method_exists($joueur, $get) && $joueur->$get() < $row['requis']) OR (!method_exists($joueur, $get) && $joueur->get_competence($row['competence']) < $row['requis']))
						{
							echo 'Vous n\'avez pas assez en : '.ucwords($row['competence']).'<br />';
							$fin = true;
						}
					}
				}
				//Le joueur rempli les conditions
				if(!$fin)
				{
					$and = '';
					$requete = "SELECT * FROM classe_permet WHERE id_classe = '".sSQL($_GET['id'])."'";
					$req = $db->query($requete);
					$new = array();
					while($row = $db->read_array($req))
					{
						if($row['new'] == 'yes') $new[] = $row['competence'];
						if($row['competence'] == 'facteur_magie') $joueur->set_facteur_magie($row['permet']);
						if($row['competence'] == 'sort_vie+') $joueur->set_sort_vie($joueur->get_sort_vie() + $row['permet']);
					}
					$newi = 0;
					while($newi < count($new))
					{
						$requete = "INSERT INTO comp_perso VALUES('', '1', '".$new[$newi]."', 1, ".$_SESSION['ID'].")";
						$req = $db->query($requete);
						$newi++;
					}
					$comp_combat = explode(';', $joueur->get_comp_combat());
					if($comp_combat[0] == '') $comp_combat = array();
					$comp_jeu = explode(';', $joueur->get_comp_jeu());
					if($comp_jeu[0] == '') $comp_jeu = array();
					$requete = "SELECT * FROM classe_comp_permet WHERE id_classe = '".sSQL($_GET['id'])."'";
					$req = $db->query($requete);
					while($row = $db->read_assoc($req))
					{
						if($row['type'] == 'comp_combat') $comp_combat[] = $row['competence'];
						if($row['type'] == 'comp_jeu') $comp_jeu[] = $row['competence'];
					}
					$joueur->set_comp_combat(implode(';', $comp_combat));
					$joueur->set_comp_jeu(implode(';', $comp_jeu));
					$joueur->set_classe_id($_GET['id']);
					$joueur->set_classe(strtolower($nom));
					$requete = "UPDATE perso SET classe = '".strtolower($nom)."', classe_id = '".sSQL($_GET['id'])."', comp_combat = '".$joueur['comp_combat']."', comp_jeu = '".$joueur->get_comp_jeu()."' ".$and." WHERE ID = ".$_SESSION['ID'];
					$req = $db->query($requete);
					echo 'Félicitations vous suivez maintenant la voie du '.strtolower($nom).'<br />';
				}
			break;
			case 'quete_myriandre' :
			?>
<h3 class="ville_haut">Journal de Frankriss hawkeye :</h3>
< le journal est en très mauvais état, maculé de sang, certaines pages sont partiellement ou entièrement déchirées. ><br />
<br />
12 Dulfandal : " Au terme de plusieurs jours de voyages, nous voila enfin parvenus jusqu'aux ruines de la cité humaine de Myriandre. La cité à du être majestueuse, mais les ravages provoqués par ce maudit dragon s'aperçoivent à des kilomètres à la ronde. Les hauts remparts ont été éventres, et la noirceur des bâtiments est le signe flagrant de la puissance de souffle de flamme du dragon fou.<br />
J'ai ordonné à la compagnie de se disperser afin de me faire un rapport des plus précis. Je souhaite surtout avoir des informations sur les groupes de pillards qui auront immanquablement élu domicile dans les parages.<br />
Demetros est anormalement nerveux."<br />
13 Dulfandal : " mes éclaireurs me rapportent des faits étranges, aucun groupe de pillards à l'horizon. Je ne peux pas croire que ces charognes auraient manqué l'occasion de venir piller cette cité en ruines... ce qui demanderait des semaines... je vais envoyer quelques hommes explorer les restes de la ville.<br />
demetros m'a demandé a procéder a certains rituels afin de vérifier < Une tache de sang empêche de lire la suite >.<br />
< Plusieurs pages ont été arrachées ><br />
< l'écriture saccadée semble indiquer que les lignes on été écrites à la va-vite ><br />
... Ils ont surgit de nulle part ... ( tache de sang )<br />
Il faut prévenir Scytä ( tache de sang )<br />
que Dulfandal nous protège tous, nous sommes perdus ( tache de sang )...<br />
			<?php
			break;
		}
	}
	else
	{	
		//Affichage des classes
		?>

		<div class="ville_test">
		<ul>	
			<?php
			$requete = "SELECT * FROM classe WHERE rang > 0 ORDER BY rang ASC";
			$req = $db->query($requete);
			$rang = 0;
			while($row = $db->read_array($req))
			{
				if($row['rang'] != $rang)
				{
					$rang = $row['rang'];
					echo '<h3 class="ville_haut">Rang '.$rang.'</h3>';
				}
			?>
			<li>
				<a href="universite.php?action=description&amp;id=<?php echo $row['id']; ?>&amp;poscase=<?php echo $_GET['poscase']; ?>" onclick="return envoiInfo(this.href, 'carte')"><?php echo $row['nom']; ?></a>
			</li>
			<?php
			}
			if($R->get_id() == 7)
			{
			?>
				<h3 class="ville_haut">Bibliothèque</h3>
				<li>
					<a href="universite.php?action=quete_myriandre&amp;id=1&amp;poscase=<?php echo $_GET['poscase']; ?>" onclick="return envoiInfo(this.href, 'carte')">Journal de Frankriss hawkeye</a>
				</li>
			<?php
			}
			?>
		</ul>
		</div>

<?php
	}
}
?>
