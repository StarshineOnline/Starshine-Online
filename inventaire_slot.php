<?php // -*- tab-width:	 2 -*-
if (file_exists('root.php'))
  include_once('root.php');
  

	/*if (isset($_GET['javascript']))
	{*/
		include_once(root.'inc/fp.php');
		$joueur = new perso($_SESSION['ID']);
		$W_requete = 'SELECT * FROM map WHERE x ='.$joueur->get_x()
			.' and y = '.$joueur->get_y();
		$W_req = $db->query($W_requete);
		$W_row = $db->read_array($W_req);
		$R = get_royaume_info($joueur->get_race(), $W_row['royaume']);
//	}
if(array_key_exists('filtre', $_GET)) $filtre = $_GET['filtre'];
else $filtre = 'utile';

$princ = new interf_princ_cont();
$invent = $G_interf->creer_inventaire_slot($joueur, 'inventaire.php?filtre='.$filtre.'&amp;', $filtre, true);
$princ->add($invent);
?>
