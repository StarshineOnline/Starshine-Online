<?php
if ($joueur->get_exp() > prochain_level($joueur->get_level()))
{
	$joueur->set_level($joueur->get_level() + 1);
	$joueur->set_point_sso($joueur->get_point_sso() + 1);
	$joueur->sauver();
}

?>