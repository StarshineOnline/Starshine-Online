<?php
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');
$joueur = new perso($_SESSION['ID']);

// Si message global
$requete = "SELECT * FROM texte_tutoriel WHERE etape = ".$joueur->get_tuto()." AND race = '".$joueur->get_race()."'" ;
$req_tuto = $db->query($requete);
if ($db->num_rows > 0)
{
	echo '<h3>Tutoriel</h3>';
	while ($row_tuto = $db->read_assoc($req_tuto)) 
		echo '<h4>'.$row_tuto['titre'].'</h4>'.nl2br($row_tuto['text']).'<hr/>';
}

?>
