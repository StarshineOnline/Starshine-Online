<?php

if ($joueur['exp'] > prochain_level($joueur['level']))
{
	$requete = "UPDATE perso SET level = level + 1, point_sso = point_sso + 1 WHERE ID = ".$_SESSION['ID'];
	$db->query($requete);
	$joueur = recupperso($_SESSION['ID']);
}

?>