<?php
if (file_exists('root.php'))
  include_once('root.php');

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'haut_ajax.php');

$joueur = new perso($_SESSION['ID']);
$joueur->check_perso();

//Vérifie si le perso est mort
verif_mort($joueur, 1);

$W_case = $_GET['poscase'];
$W_requete = 'SELECT * FROM map WHERE ID =\''.sSQL($W_case).'\'';
$W_req = $db->query($W_requete);
$W_row = $db->read_array($W_req);
$R = new royaume($W_row['royaume']);
$R->get_diplo($joueur->get_race());
?>
		<h2><?php if(!array_key_exists('fort', $_GET)) return_ville('<img src="image/ville.gif" alt="Retour en ville" title="Retour en ville" />', $W_case); ?> Candidature</h2>
<?php
$W_distance = detection_distance($W_case, $joueur->get_pos());
$W_coord = convert_in_coord($W_case);
if($W_distance == 0)
{
	if($joueur->get_honneur() >= $R->honneur_candidat())
	{
		if(isset($_GET['action']))
		{
			switch ($_GET['action'])
			{
				case 'oui' :
					$date = date_prochain_mandat();
					$requete = "SELECT * FROM candidat WHERE id_perso = ".$joueur->get_id()." AND date = '".$date."'";
					$db->query($requete);
					if($db->num_rows > 0)
					{
						echo 'Vous êtes déjà candidat !';
					}
					else
					{
						$requete = "INSERT INTO candidat ( `id` , `id_perso` , `date` , `royaume` , `programme`, `nom` ) VALUES('', ".$joueur->get_id().", '".$date."', ".$R['ID'].", '', '".$joueur['nom']."')";
						if($db->query($requete))
						{
							echo 'Votre candidature pour le poste de roi a bien été prise en compte';
						}
					}
				break;
			}
		}
		else
		{
	?>
	Voulez vous devenir roi ?<br />
	Si oui, cliquez sur oui et votre candidature pour le poste de roi sera pris en compte.<br />
	<a href="candidature.php?action=oui&amp;poscase=<?php echo $W_case; ?>" onclick="return envoiInfo(this.href, 'carte')">Oui</a>, <?php return_ville('Non', $W_case); ?>
	<?php
		}
	}	
}
?>
