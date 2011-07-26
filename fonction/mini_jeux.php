<?php // -*- mode: php; tab-width: 2 -*-

function joue_des($joueur, $mise = 50)
{
	$titre = 'Jeu de dés';
	if ($joueur->get_pa() < 4) {
		showMessage('<h5>Vous n\'avez pas assez de pa</h5>', $titre);
		return;
	}
	if ($joueur->get_star() < $mise) {
		showMessage('<h5>Vous n\'avez pas assez de stars</h5>', $titre);
		return;
	}
	$joueur->add_star($mise * -1);
	$joueur->add_pa(-4);
	$d1 = mt_rand(1, 6);
	$d2 = mt_rand(1, 6);
	$msg = "Vous lancez les dés et tirez $d1 et $d2.<br>";
	if ($d1 == $d2 && $d1 == 1) {
		$joueur->add_star($mise);
		$msg .= "Vous regagnez $mise stars !";
	}
	elseif ($d1 == $d2 && $d1 == 1) {
		$gains = $mise * 2;
		$joueur->add_star($gains);
		$msg = "vous gagnez $gains stars !";
	}
	else
		$msg .= 'Vous avez perdu !';
	showMessage($msg, $titre);
	$joueur->sauver();
}

function joue_gobelets($joueur, $mise = 50)
{
	$titre = 'Jeu des gobelets';
	if ($joueur->get_pa() < 4) {
		showMessage('<h5>Vous n\'avez pas assez de pa</h5>', $titre);
		return;
	}
	if ($joueur->get_star() < $mise) {
		showMessage('<h5>Vous n\'avez pas assez de stars</h5>', $titre);
		return;
	}
  $joueur->add_star($mise * -1);
  $joueur->add_pa(-4);
  $d1 = mt_rand(1, 6);
	do {
		$d2 = mt_rand(1, 6);
	} while ($d2 == $d1);
	$msg = "Vous avez choisi le gobelet numéro $d1.<br>Le jeton était dans le gebelet $d2, vous avez perdu !";
  showMessage($msg, $titre);
  $joueur->sauver();
}
