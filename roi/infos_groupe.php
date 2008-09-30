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
	$image = "../image/personnage/".$joueur['race']."/".$joueur['race']."_".$Tclasse[$membre_info['classe']]["type"].".png";
	?>
	<li><img src="<?php echo $image; ?>" alt="" style="width : 27px; height : 27px; vertical-align: middle;"> <span style="font-weight : bold;"><?php echo $membre['nom'].'</span> - '.$membre_info['classe'].' - '.$membre_info['grade']; ?></li>
	<?php
}
?>
</ul>