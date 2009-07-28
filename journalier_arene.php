<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
include_once(root.'class/db.class.php');
//Récéption des variables de connexion ?a base et connexion ?ette base
include_once(root.'connect.php');
include_once(root.'connect_log.php');

//Inclusion du fichier contenant toutes les variables indispensablent
include_once(root.'inc/variable.inc.php');

$arene_xml = new DomDocument();
$arene_xml->load('./xml/arenes.xml');

//Récupération des arènes
$requete = "SELECT nom, xmin, xmax, ymin, ymax FROM arenes";
$req = $db->query($requete);
//$row = $db->read_assoc($req);

//Verifie que le fichier ne doit pas être crée
$nouveau = $arene_xml->getElementsByTagName("nouveau")->item(0);
$nouv_arenes = empty($nouveau);

while($row = $db->read_assoc($req))
{
	$xmin = $row['xmin'];
	$xmax = $row['xmax'];
	$ymin = $row['ymin'];
	$ymax = $row['ymax'];
	$coord['x'] = $xmin + round(($xmax - $xmin) / 2);
	$coord['y'] = $ymin + round(($ymax - $ymin) / 2);

	$requete_joueurs = 'SELECT ID, nom, level, race, x, y, classe, hp, hp_max, mp, pa FROM perso WHERE statut = \'actif\' AND (x <= '.$xmax.' AND x >= '.$xmin.') AND (y <= '.$ymax.' AND y >= '.$ymin.') ORDER BY y ASC, x ASC, dernier_connexion DESC';
	$req_joueurs = $db->query($requete_joueurs);

	$liste_joueurs = array();
	while($joueur = $db->read_assoc($req_joueur))
	{
		$liste_joueurs[$joueur['nom']]['lvl'] = $joueur['level'];
		$liste_joueurs[$joueur['nom']]['race'] = $joueur['race'];
		$liste_joueurs[$joueur['nom']]['x'] = $joueur['x'];
		$liste_joueurs[$joueur['nom']]['y'] = $joueur['y'];
		$liste_joueurs[$joueur['nom']]['classe'] = $joueur['classe'];
		$liste_joueurs[$joueur['nom']]['hp'] = $joueur['hp'];
		$liste_joueurs[$joueur['nom']]['hp_max'] = $joueur['hp_max'];
		$liste_joueurs[$joueur['nom']]['mp'] = $joueur['mp'];
		$liste_joueurs[$joueur['nom']]['pa'] = $joueur['pa'];
	}
	
	if($nouv_arenes)
	{
		$arene = $arene_xml->getElementsByTagName($row['nom'])->item(0);
	
		$xml_joueur = $arene->getElementsByTagName('joueur');
		foreach($xml_joueur as $joueur)
		{
			$nom = $joueur->getAttribute('nom');
			if(!empty($nom))
			{
				$joueur->setAttribute('level', $liste_joueurs[$nom]['lvl']);
				$joueur->setAttribute('race', $liste_joueurs[$nom]['race']);
				$joueur->setAttribute('x', $liste_joueurs[$nom]['x']);
				$joueur->setAttribute('y', $liste_joueurs[$nom]['y']);
				$joueur->setAttribute('classe', $liste_joueurs[$nom]['classe']);
				$joueur->setAttribute('hp', $liste_joueurs[$nom]['hp']);
				$joueur->setAttribute('hp_max', $liste_joueurs[$nom]['hp_max']);
				$joueur->setAttribute('mp', $liste_joueurs[$nom]['mp']);
				$joueur->setAttribute('pa', $liste_joueurs[$nom]['pa']);
			}
			else
				$joueur->parentNode->removeChild($joueur);
		}
	}
	else
	{
		$nouveau->parentNode->removeChild($nouveau);
		$racine = $arene_xml->getElementsByTagName('arenes')->item(0);
		
		$nouv_arene = $arene_xml->createElement('viewarene');
		$arene_courante = $racine->appendChild($nouv_arene);
		$arene_courante->setAttribute('type', $row['nom']);
		
		while(current($liste_joueurs))
		{
			$nom = key($liste_joueurs);
			$nouv_joueur = $arene_xml->createElement('joueur');
			$joueur = $arene_courante->appendChild($nouv_joueur);
			$joueur->setAttribute('nom', $nom);
			$joueur->setAttribute('level', $liste_joueurs[$nom]['lvl']);
			$joueur->setAttribute('race', $liste_joueurs[$nom]['race']);
			$joueur->setAttribute('x', $liste_joueurs[$nom]['x']);
			$joueur->setAttribute('y', $liste_joueurs[$nom]['y']);
			$joueur->setAttribute('classe', $liste_joueurs[$nom]['classe']);
			$joueur->setAttribute('hp', $liste_joueurs[$nom]['hp']);
			$joueur->setAttribute('hp_max', $liste_joueurs[$nom]['hp_max']);
			$joueur->setAttribute('mp', $liste_joueurs[$nom]['mp']);
			$joueur->setAttribute('pa', $liste_joueurs[$nom]['pa']);
			next($liste_joueurs);
		}
		
		//Information des cases
		$requete = 'SELECT ID, info, decor FROM map WHERE (((FLOOR(ID / '.$G_ligne.') >= '.$ymin.') AND (FLOOR(ID / '.$G_ligne.') <= '.$ymax.')) AND (((ID - (FLOOR(ID / '.$G_colonne.') * 1000)) >= '.$xmin.') AND ((ID - (FLOOR(ID / '.$G_colonne.') * 1000)) <= '.$xmax.'))) ORDER BY ID';
		$req = $db->query($requete);		

		//Création des cases
		while($case = $db->read_assoc($req))
		{
			$nouv_case = $arene_xml->createElement('case');
			$case_xml = $arene_courante->appendChild($nouv_case);
			$case_xml->setAttribute('id', $case['ID']);
			$case_xml->setAttribute('decor', $case['decor']);
		}
		
		//Taille de l'arène
		$nouv_taille = $arene_xml->createElement('taille');
		$taille = $arene_courante->appendChild($nouv_taille);
		$taille->setAttribute('xmin', $xmin);
		$taille->setAttribute('xmax', $xmax);
		$taille->setAttribute('ymin', $ymin);
		$taille->setAttribute('xmax', $xmax);
	}
}
$arene_xml->save('./xml/arenes.xml');
?>