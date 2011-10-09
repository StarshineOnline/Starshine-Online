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
  $d1 = mt_rand(1, 3);
	do {
		$d2 = mt_rand(1, 3);
	} while ($d2 == $d1);
	$msg = "Vous avez choisi le gobelet numéro $d1.<br>Le jeton était dans le gobelet $d2, vous avez perdu !";
  showMessage($msg, $titre);
  $joueur->sauver();
}

function peche($joueur, $recompense = null, $diff = 30, $mise = 0)
{
	$titre = 'Pêche';
	if ($joueur->get_pa() < 10) {
		showMessage('<h5>Vous n\'avez pas assez de pa</h5>', $titre);
		return;
	}
	if ($joueur->get_star() < $mise) {
		showMessage('<h5>Vous n\'avez pas assez de stars</h5>', $titre);
		return;
	}
  $joueur->add_star($mise * -1);
  $joueur->add_pa(-10);
  $d1 = mt_rand(1, $joueur->get_force()) + 
		mt_rand(1, $joueur->get_dexterite());
	$d2 = mt_rand(1, $diff);
	if ($d1 > $d2) {
		$msg = "Vous avez réussi à attraper le poisson !";
		if ($recompense) $joueur->prend_objet($recompense);
		if ($mise) $joueur->add_star($mise * 2);
		$ok = true;
	} else {
		$msg = 'Vous échouez à attraper le poisson';
		$ok = false;
	}
  showMessage($msg, $titre);
  $joueur->sauver();
	return $ok;
}
