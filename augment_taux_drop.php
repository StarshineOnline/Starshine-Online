<?php
if (file_exists('root.php'))
  include_once('root.php');
include('haut.php');

$monstres = monstre::create(false, false, 'level ASC', false, '1');

foreach($monstres as $monstre)
{
	$drops = explode(';', $monstre->get_drops());
	$show = '';
	foreach($drops as $key => $drop)
	{
		if($drop[0] == 'o')
		{
			$explode = explode('-', $drop);
			$id = substr($explode[0], 1);
			$requete = "SELECT nom FROM objet WHERE id = ".$id;
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			$chance = 100 / $explode[1];
			$show .= $row['nom'].' - '.$chance.'%';
			$newdrop = floor($explode[1] / 1.2);
			$chance = 100 / $newdrop;
			$show .= ' => '.$chance.'% ';
			$new = 'o'.$id.'-'.$newdrop;
			$drops[$key] = $new;
		}
	}
	$implode = implode(';', $drops);
	$monstre->set_drops($implode);
	//$monstre->sauver();
}