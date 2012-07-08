<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'haut_ajax.php');

$joueur = new perso($_SESSION['ID']);
?>
	<fieldset>
		<legend>Script de combat - Créatures</legend>
<ul id="messagerie_onglet">
	<li><a href="actions.php" onclick="return envoiInfo(this.href, 'information');">Personnage</a></li>
	<li><a href="actions_pet.php" onclick="return envoiInfo(this.href, 'information');">Créature</a></li>
</ul>
<br /><br />
<?php
if (isset($_GET['id_pet']))
{
$pet = new pet($_GET['id_pet']);
		//Suppression du script
		if(array_key_exists('action', $_GET) && $_GET['action'] == 'suppr_action')
		{
			$requete = "DELETE FROM action_pet WHERE id = ".sSQL($_GET['id_action']);
			if($db->query($requete))
			{
				echo '<h6>Script effacé avec succès !</h6>';
			}
			$requete = "SELECT * FROM action_pet WHERE id_joueur = ".$joueur->get_id()." AND type_monstre = ".$pet->get_id_monstre()." ORDER BY nom ASC";
			$req = $db->query($requete);
			if($db->num_rows($req) == 0)
			{
				$requete = "UPDATE pet SET action_a = '0', action_d = '0' WHERE ID = ".$pet->get_id();
				$req = $db->query($requete);
			}
		}
		if(array_key_exists('valid', $_GET))
		{
			//Si ya pas d'id_action alors création
			if($_GET['id_action'] == '')
			{
				$requete = "INSERT INTO action_pet VALUES('', ".$joueur->get_id().", ".$pet->get_id_monstre().", '".sSQL($_POST['action_nom'])."', '', '".sSQL($_POST['mode'])."')";
				$req = $db->query($requete);
				$id_action = $db->last_insert_id();
			}
			else
			{
				$id_action = $_GET['id_action'];
			}
			$actionexplode = explode(';', recupaction($id_action));
			$i = 1;
			while($i <= $_GET['r'])
			{
				$final = $_GET['final'.$i];
				$action = '#09='.$i.'@';
				$typefinal = $final[0];
				$action_nom = sSQL($_GET['action_nom']);
				$numfinal = mb_substr($final, 1, strlen($final));
				if($final == 'attaque') $a_final = '!';
				elseif($typefinal == 's') $a_final = '~'.$numfinal;
				else $a_final = '_'.$numfinal;
				$action .= $a_final;
				$actionexplode[($i - 1)] = $action;
				$i++;
			}
			$requete = "UPDATE action_pet SET action = '".implode(';', $actionexplode)."', nom = '".$action_nom."' WHERE id = ".$id_action;
			//echo $requete;
			$db->query($requete);
			echo '<h6>Le script a bien été ajouté / modifié</h6>';
		}

		if(array_key_exists('action', $_GET) && $_GET['action'] == 'select' && $_GET['id_action'] != '')
		{
			if($_GET['type'] == 'attaque') $t = 'a';
			else $t = 'd';
			$id_action = sSQL($_GET['id_action'], SSQL_INTEGER);
			$requete = "UPDATE pet SET action_".$t." = '".sSQL($id_action)."' WHERE ID = ".$pet->get_id();
			if($db->query($requete))
			{
				$set = 'set_action_'.$t;
				$pet->$set($_GET['id_action']);
				$pet->sauver();
				echo '<h6>Script '.$_GET['type'].' bien séléctionné.</h6>';
			}
		}
		if(array_key_exists('action', $_GET) && $_GET['action'] == 'dupliq' && $_GET['id_action'] != '')
		{
			$requete = "SELECT action, mode FROM action_pet WHERE id = ".sSQL($_GET['id_action']);
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			$requete = "INSERT INTO action_pet VALUES(null, ".$joueur->get_id().", ".$pet->get_id_monstre().", '".sSQL($_GET['nom_copie'])."', '".$row['action']."', '".$row['mode']."')";
			//echo $requete;
			if($db->query($requete))
			{
				echo '<h6>Script dupliqué.</h6>';
			}
		}
		$joueur->check_perso();
		$script_attaque = recupaction_all($pet->get_action_a(), true);
		$script_defense = recupaction_all($pet->get_action_d(), true);
		?>
			Voici l'interface du script de combat, grâce à celui-ci vous pourrez attaquer avec des sorts ou des compétences.<br />
			<fieldset>
				<legend>Scripts de combat de <?php echo $pet->get_nom(); ?></legend>
				<ul id="liste_script">
				<?php
				$requete = "SELECT * FROM action_pet WHERE id_joueur = ".$joueur->get_id()." AND type_monstre = ".$pet->get_id_monstre()." ORDER BY nom ASC";
				$req = $db->query($requete);
				while($row = $db->read_assoc($req))
				{
					$type = '';
					if($row['nom']==$script_attaque['nom']) $type .= '<a><span class="attaque" title="Script d\'attaque"> </span></a> ';
					else $type .= '<a><span class="space"></span></a> ';
					if($row['nom']==$script_defense['nom']) $type .= '<a><span class="shield" title="Script de défense"> </span></a> ';
					else $type .= '<a><span class="space"></span></a> ';
					?>
					<li><?php echo $type; ?><span class="nom_script" onclick="envoiInfo('action.php?from=modif&id_pet=<?php echo $pet->get_id(); ?>&amp;id_action=<?php echo $row['id']; ?>', 'information');" title="Modifier le script"><?php echo $row['nom']; ?></span>
						<span class="options">
							<a title="Définir comme script d'attaque" onclick="envoiInfo('actions_pet.php?action=select&id_pet=<?php echo $pet->get_id(); ?>&amp;type=attaque&amp;id_action=<?php echo $row['id']; ?>', 'information'); return false;"><span class="attaque hover"></span></a>
							<a title="Définir comme script de défense" onclick="envoiInfo('actions_pet.php?action=select&id_pet=<?php echo $pet->get_id(); ?>&amp;type=defense&amp;id_action=<?php echo $row['id']; ?>', 'information'); return false;"><span class="shield hover"></span></a>
							<a title="Copier" onclick="envoiInfo('actions_pet.php?action=dupliq&id_pet=<?php echo $pet->get_id(); ?>&amp;id_action=<?php echo $row['id']; ?>&amp;nom_copie=copie', 'information');"><span class="copy hover"></span></a>
							<a title="Supprimer ce script de combat" style="float : left;" onclick="if(confirm('Voulez-vous vraiment supprimer ce script ?')) envoiInfo('actions_pet.php?action=suppr_action&id_pet=<?php echo $pet->get_id(); ?>&amp;id_action=<?php echo $row['id']; ?>', 'information');"><span class="del hover"></span></a>
						</span>
					</li>
					<?php
				}
				?>
				</ul>
				<br />
				<a href="action.php?mode=s&id_pet=<?php echo $pet->get_id(); ?>" onclick="return envoiInfo(this.href, 'information');">Créer un script de combat simple</a> - <a href="action.php?mode=a&id_pet=<?php echo $pet->get_id(); ?>" onclick="return envoiInfo(this.href, 'information');">Créer un script de combat avancé</a>
		</fieldset>
		</div>
<?php
}
//On affiche la lsite des créatures poru choisir celle que l'on modif son script
else
{
$pets = $joueur->get_pets(true);
if(count($pets) > 0)
{
	foreach($pets as $pet)
	{
		$pet->get_monstre();
		$script_attaque = recupaction_all($pet->get_action_a(), true);
		$script_defense = recupaction_all($pet->get_action_d(), true);
		?>
	<div class="monstre combat" onclick="envoiInfo('actions_pet.php?id_pet=<?php echo $pet->get_id(); ?>', 'information');">
		<h3><?php if($pet->get_principale() == 1) echo '<img src="image/icone/couronne.png">'; ?><?php echo $pet->get_nom(); ?></h3>
			<img src="image/monstre/<?php echo $pet->monstre->get_lib(); ?>.png">
			<div class="monstre_infos">Script : [<?php echo $script_attaque['nom']; ?>]</div><br />
	</div>
		<?php
	}
}
else
{
	echo '<h5>Vous n\'avez pas de monstre</h5>';
}
}
?>
	</fieldset>
