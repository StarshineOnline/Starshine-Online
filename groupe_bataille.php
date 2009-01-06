<?php

include('inc/fp.php');
include('class/bataille.class.php');
include('class/bataille_royaume.class.php');
include('class/bataille_repere.class.php');
include('class/bataille_groupe.class.php');
include('class/bataille_groupe_repere.class.php');
include('class/bataille_repere_type.class.php');
include('fonction/messagerie.inc.php');
$joueur = recupperso($_SESSION['ID']);

function affiche_bataille_groupe($bataille)
{
	global $joueur;
	?>
		<h2><?php echo $bataille->nom; ?></h2>
		<div>
			<?php echo transform_texte($bataille->description); ?>
		</div>
	<?php
	if($bataille->is_groupe_in($joueur['groupe']))
	{
		$bataille->get_reperes();
		echo 'Vous participez à cette bataille';
		foreach($bataille->reperes as $repere)
		{
			if($repere_groupe = $repere->get_groupe($joueur['groupe']))
			{
				echo $repere->id.' / true';
			}
			else
			{
				echo $repere->id.' / false';
			}
		}
	}
	else
	{
		?>
		<a href="groupe_bataille.php?id_bataille=<?php echo $bataille->id; ?>&amp;participe" onclick="return envoiInfo(this.href, 'bataille_<?php echo $bataille->id; ?>');">Participer à cette bataille</a>
		<?php
	}
}

$bataille_royaume = new bataille_royaume($Trace[$joueur['race']]['numrace']);
$bataille_royaume->get_batailles();

if(array_key_exists('participe', $_GET))
{
	$bataille = new bataille($_GET['id_bataille']);
	$bataille_groupe = new bataille_groupe();
	$bataille_groupe->id_bataille = $_GET['id_bataille'];
	$bataille_groupe->id_groupe = $joueur['groupe'];
	$bataille_groupe->sauver();
	affiche_bataille_groupe($bataille);
}
else
{
	foreach($bataille_royaume->batailles as $bataille)
	{
		//il faut que ça soit des batailles "en cours"
		if($bataille->etat == 1)
		{
			?>
			<div id="bataille_<?php echo $bataille->id; ?>">
			<?php
				affiche_bataille_groupe($bataille);
			?>
			</div>
			<?php
		}
	}
}
?>