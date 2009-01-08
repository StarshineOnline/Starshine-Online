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
		<h2><a href="/roi/gestion_bataille.php?refresh_bataille=<?php echo $bataille->id; ?>" onclick="return envoiInfo(this.href, 'centre');"><?php echo $bataille->nom; ?></a></h2>
		<div>
			<?php echo transform_texte($bataille->description); ?>
		</div>
	<?php
	if($bataille->is_groupe_in($joueur['groupe']))
	{
		$bataille->get_reperes();
		echo 'Vous participez à cette bataille<br />';
		foreach($bataille->reperes as $repere)
		{
			if($repere_groupe = $repere->get_groupe($joueur['groupe']))
			{
				$repere->get_type();
				if($repere_groupe->accepter == 0)
				{
					$accepter = 'V';
				}
				else
				{
					$accepter = '';
				}
				echo $repere->repere_type->nom.' en '.$repere->x.' / '.$repere->y.' - '.$accepter;
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

$groupe = recupgroupe($joueur['groupe'], '');
//Si c'est le chef de groupe
if($groupe['id_leader'] == $joueur['ID'])
{
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
}
?>