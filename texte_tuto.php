<?php
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');
$perso = new perso($_SESSION['ID']);
$classe = $perso->get_classe_id();
// Si message global
$requete = "SELECT * FROM texte_tutoriel WHERE etape = ".$perso->get_tuto()." AND race = '".$perso->get_race()."' AND classe ='".$classe."'" ;
$req_tuto = $db->query($requete);
if ($db->num_rows > 0)
{
	echo '<h3>Tutoriel</h3>';
	while ($row_tuto = $db->read_assoc($req_tuto))
	{
		$texte = new texte($row_tuto['text'], texte::tutoriel);
    $texte->set_perso($perso);
		echo '<h4>'.$row_tuto['titre'].'</h4>'.$texte->parse().'<hr/>';
  }
}

?>
