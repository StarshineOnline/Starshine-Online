<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php

include_once(root.'inc/fp.php');
$joueur = new perso($_SESSION['ID']);
check_perso($joueur);
$W_case = $joueur->get_poscase();

if(array_key_exists('reponse', $_GET)) $reponse = $_GET['reponse']; else $reponse = 0;

$id = $_GET['id'];
$requete = 'SELECT * FROM pnj WHERE id = \''.sSQL($id).'\'';  
$req = $db->query($requete);
$row = $db->read_assoc($req);

if ($row['x'] != $joueur->get_x() ||
		$row['y'] != $joueur->get_y()) {
	security_block(URL_MANIPULATION, 'PNJ pas sur la même case');
}

echo '<fieldset><legend>'.$row['nom'].'</legend>';
$reponses = explode('*****', nl2br($row['texte']));
$message = eregi_replace("\[ID:([^[]*)\]([^[]*)\[/ID:([^[]*)\]", "<li><a href=\"pnj.php?id=".$id."&amp;reponse=\\1&amp;poscase=".$W_case."\" onclick=\"return envoiInfo(this.href, 'information')\">\\2</a></li>", $reponses[$reponse]);
//On vérifie si ya une quête pour ce pnj
$supp = true;
$quetes_actives = array();
if($joueur->get_quete() != '')
{
	foreach(unserialize($joueur->get_quete()) as $quete)
	{
		$requete = 'SELECT * FROM quete WHERE id = '.$quete['id_quete'];
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		$objectif = unserialize($row['objectif']);
		$i = 0;
		$quetes_actives[] = $row['id'];
		foreach($quete['objectif'] as $objectif_fait)
		{
			$total_fait = $objectif_fait->nombre;
			$total = $objectif[$i]->nombre;
			//On vérifie si il peut accéder à cette partie de la quête
			if($total_fait >= $total) $objectif[$i]->termine = true;
			else $objectif[$i]->termine = false;
			/*
			echo $i.'<br />';
			my_dump($objectif[$i]);
			my_dump($objectif_fait);
			echo '<br />';
			*/
			if (($objectif_fait->requis == '' OR $objectif[$objectif_fait->requis]->termine) AND !$objectif[$i]->termine)
			{
				$cible = $objectif_fait->cible;
			}
			$i++;
		}
	}
}
if($joueur->get_quete_fini() != '')
{
	$quete_fini = explode(';', $joueur->get_quete_fini());
	foreach($quete_fini as $quete)
	{
		//On affiche le lien pour la discussion
		$message = eregi_replace("\[QUETEFINI".$quete.":([^[]*)\]([^[]*)\[/QUETEFINI".$quete.":([^[]*)\]", "<li><a href=\"pnj.php?id=".$id."&amp;reponse=\\1&amp;poscase=".$W_case."\" onclick=\"return envoiInfo(this.href, 'information')\">\\2</a></li>", $message);
		$supp = false;
	}
}
while (eregi("\[ISQUETE:([^[]*)\]([^[]*)\[/ISQUETE:([^[]*)\]", $message, $regs))
{
	$numq = $regs[1];
	if (in_array($numq, $quetes_actives))
	{
		$message = eregi_replace("\[ISQUETE:${numq}\]([^[]*)\[/ISQUETE:$numq\]",
														 $regs[2], $message);
	}
	else
	{
		$message = eregi_replace("\[ISQUETE:${numq}\]([^[]*)\[/ISQUETE:$numq\]",
														 '', $message);
	}
}
//On supprime le lien pour la discussion
$message = eregi_replace("\[QUETE([^[]*)\]([^[]*)\[/QUETE([^[]*)\]", "", $message);
//On supprime les autres liens
$message = eregi_replace("\[QUETEFINI([^[]*)\]([^[]*)\[/QUETEFINI([^[]*)\]", "", $message);
$message = eregi_replace("\[retour]", "<li><a href=\"informationcase.php?case=".$W_case."\" onclick=\"return envoiInfo(this.href, 'information')\">Retour aux informations de la case</a></li>", $message);
//Validation de la quête
if(eregi("\[quete]", $message))
{
	verif_action('P'.$id, $joueur, 's');
	$message = eregi_replace("\[quete]", "", $message);
}
//Prise d'une quête
if(eregi("\[prendquete:([^[]*)\]", $message, $regs))
{
	prend_quete($regs[1], $joueur);
	$message = eregi_replace("\[prendquete:([^[]*)\]", "", $message);
}
//Donne un item
if(eregi("\[donneitem:([^[]*)\]", $message, $regs))
{
	$joueur->prend_objet($regs[1]);
	$message = eregi_replace("\[donneitem:([^[]*)\]", "", $message);
	verif_action($regs[1], $joueur, 's');
}
//validation inventaire
if(eregi("\[verifinventaire:([^[]*)\]", $message, $regs))
{
	if (verif_inventaire($regs[1], $joueur) == false) {
		$message = "<h5>Tu te moques de moi, mon bonhomme ?</h5>";
	}
	else {
		$message = eregi_replace("\[verifinventaire:([^[]*)\]", "", $message);
	}
}
echo '<ul>'.$message.'</ul>';
echo "</fieldset>";
?>