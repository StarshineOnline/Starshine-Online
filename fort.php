<?php
if (file_exists('root.php'))
  include_once('root.php');


//Inclusion du haut du document html
include_once(root.'haut_ajax.php');

$joueur = new perso($_SESSION['ID']);

$joueur->check_perso();

//Vérifie si le perso est mort
verif_mort($joueur, 1);

$W_requete = 'SELECT * FROM map WHERE x = '.$joueur->get_x().' and y = '.$joueur->get_y();
$W_req = $db->query($W_requete);
$W_row = $db->read_array($W_req);
$R = new royaume($W_row['royaume']);
$R->get_diplo($joueur->get_race());

$construction = new construction(sSQL($_GET['id_construction']));
$batiment = new batiment($construction->get_id_batiment());

?>
	<div id="carte">
		<fieldset>
<?php
//Distance + royaume
if($joueur->get_x() == $construction->get_x() AND $joueur->get_y() == $construction->get_y() AND $joueur->get_race() == $R->get_race())
{
	
	 echo "<legend>".$batiment->get_nom()."</legend>"; 
	echo "<ul>";

	if($batiment->get_bonus7() == 1)
	{
	?>
		<li>
			<a href="alchimiste.php" onclick="return envoiInfo(this.href, 'carte')">Alchimiste</a>
		</li>
		<li>
			<a href="poste.php" onclick="return envoiInfo(this.href, 'carte')">La Poste</a>
		</li>
	<?php
	}
	if($batiment->get_bonus6() == 1)
	{
	?>
		<li>
			<a href="teleport.php" onclick="return envoiInfo(this.href, 'carte')">Pierre de Téléportation</a>
		</li>
	<?php
	}
	?>
		<li>
			<a href="taverne.php" onclick="return envoiInfo(this.href, 'carte')">Taverne</a>
		</li>
<?php
	//Si il est roi
	if(($joueur->get_rang_royaume() == 6 ||
			 $R->get_ministre_economie() == $joueur->get_id() ||
			 $R->get_ministre_militaire() == $joueur->get_id()) AND  
			 $batiment->get_id() != 1 AND
			 $batiment->get_bonus7() == 1)
	{
?>
		<li>
			<a href="roi/?fort=ok">Gestion du royaume</a>
		</li>
<?php
	}
}
?>
	</ul>
	</fieldset>
	</div>