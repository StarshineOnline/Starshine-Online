<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'inc/fp.php');

$joueur = new perso($_SESSION['ID']);

//V�rifie si le perso est mort
verif_mort($joueur, 1);

$joueur->check_perso();
$groupe = new groupe($_GET['id_groupe']);
?>
INFOS :
Nom : <?php echo $groupe->get_nom(); ?>
<ul>
<?php
$groupe->get_membre_joueur();
foreach($groupe->membre_joueur as $membre)
{
	$image = "../image/personnage/".$membre->get_race()."/".$membre->get_race()."_".$Tclasse[$membre->get_classe()]["type"].".png";
	if($membre->get_id() == $groupe->get_id_leader()) $nom = $membre->get_nom().'*'; else $nom = $membre->get_nom();
	$membre->get_grade();
	?>
	<li id="membre_<?php echo $membre->get_id(); ?>"><img src="<?php echo $image; ?>" alt="<?php echo $membre->get_classe(); ?>" title="<?php echo $membre->get_classe(); ?>" style="width : 27px; height : 27px; vertical-align: middle;"> <span style="font-weight : bold;"><?php echo $nom.'</span> - '.$membre->get_race().' - '.$membre->grade->get_nom(); ?></li>
	<?php
}
?>
</ul>
