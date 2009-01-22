<?php
//Connexion obligatoire
$connexion = true;
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

$_SESSION['position'] = convert_in_pos($joueur['x'], $joueur['y']);

//Informations sur le batiment
$requete = "SELECT * FROM batiment WHERE id = ".sSQL($_GET['id_batiment']);
$req = $db->query($requete);
$row_b = $db->read_assoc($req);
?>
	<div id="carte">
		<h2><?php echo $row_b['nom']; ?></h2>
<?php

$W_distance = detection_distance($W_case, $_SESSION["position"]);

$W_coord = convert_in_coord($W_case);
if($W_distance == 0 AND $joueur['race'] == $R['race'])
{
	?>
	<ul class="ville">
	<?php
	if($row_b['bonus7'] == 1)
	{
	?>
		<li>
			<a href="taverne.php?poscase=<?php echo $W_case; ?>&amp;fort=ok" onclick="return envoiInfo(this.href, 'carte')">Taverne</a>
		</li>
		<li>
			<a href="poste.php?poscase=<?php echo $W_case; ?>&amp;fort=ok" onclick="return envoiInfo(this.href, 'carte')">La Poste</a>
		</li>
	<?php
	}
	?>
		<li>
			<a href="bureau_quete.php?poscase=<?php echo $W_case; ?>" onclick="return envoiInfo(this.href, 'carte')">Bureau des quètes</a>
		</li>
<?php
	if(date("d") >= 15 AND date("d") < 20)
	{
?>
		<li>
			<a href="candidature.php?poscase=<?php echo $W_case; ?>&amp;fort=ok" onclick="return envoiInfo(this.href, 'carte')">Candidature</a>
		</li>
<?php
	}
	if(date("d") >= 20)
	{
?>
		<li>
			<a href="vote_roi.php?poscase=<?php echo $W_case; ?>&amp;fort=ok" onclick="return envoiInfo(this.href, 'carte')">Vote</a>
		</li>
<?php
	}
	if($row_b['bonus5'] == 1 AND $joueur['rang_royaume'] == 6)
	{
?>
		<li>
			<a href="roi/">Gestion du royaume</a>
		</li>
<?php
	}
}
?>
	</ul>
	</div>