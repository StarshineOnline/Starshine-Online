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
	?>
	<li><?php echo $membre['nom'].' - '.$membre['race']; ?></li>
	<?php
}
?>
</ul>