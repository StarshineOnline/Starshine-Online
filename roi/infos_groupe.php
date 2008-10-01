<?php
$root = '../';
//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include($root.'inc/fp.php');

$joueur = recupperso($_SESSION['ID']);

//Vérifie si le perso est mort
verif_mort($joueur, 1);

check_perso($joueur);

$_SESSION['position'] = convert_in_pos($joueur['x'], $joueur['y']);
$groupe = recupgroupe($_GET['id_groupe'], 'membre');
?>
INFOS :
Nom : <?php echo $groupe['nom']; ?>
<ul>
<?php
foreach($groupe['membre'] as $membre)
{
	$membre_info = recupperso($membre['id_joueur']);
	$image = "../image/personnage/".$membre_info['race']."/".$membre_info['race']."_".$Tclasse[$membre_info['classe']]["type"].".png";
	if($membre_info['ID'] == $groupe['id_leader']) $nom = $membre_info['nom'].'*'; else $nom = $membre_info['nom'];
	?>
	<li><img src="<?php echo $image; ?>" alt="<?php echo $membre_info['classe']; ?>" style="width : 27px; height : 27px; vertical-align: middle;"> <span style="font-weight : bold;"><?php echo $nom.'</span> - '.$membre_info['race'].' - '.$membre_info['grade']; ?></li>
	<?php
}
?>
</ul>