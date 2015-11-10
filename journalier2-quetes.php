<?php
if (file_exists('root.php'))
  include_once('root.php');
if (isset($_SERVER['REMOTE_ADDR'])) die('Forbidden connection from '.$_SERVER['REMOTE_ADDR']);

include_once('journalier2-head.php');


if(date("N") == 1)
{
	// Pas de nouvelle quête de royaume s'il y en a une en cours
	$req = $db->query('SELECT valeur FROM variable WHERE nom = "quete_royaume"');
	$row = $db->read_array($req);
	if( $row[0] )
	{
		$req = $db->query('SELECT 1 FROM quete_perso WHERE id_quete = '.$row[0].' LIMIT 1');
		$en_cours = $db->read_array($req);
		echo 'Quête de royaume actuelle : #'.$row[0].".\n";
	}
	else
		$en_cours = false;
	if( !$en_cours )
	{
		echo 'Test de nouvelle quête de royaume : ';
		// Une chance sur trois d'avoir une quête de royaume
		if( rand(1,3) == 3 )
		{
			$niv = ref_niveau();
			$id = quete::nouv_quete_royaume($niv);
			if( $id )
			{
				$db->query('REPLACE INTO variable (nom, valeur) VALUES ("quete_royaume", "'.$id.'")');
				echo '#'.$id.' (niveau '.$niv.").\n";
			}
			else
				echo 'non trouvée (niveau '.$niv.').';
		}
		else
			echo "aucune.\n";
	} 
}

?>