<?php
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');
$joueur = new perso($_SESSION['ID']);
$joueur->check_perso();
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
$message = preg_replace("`\[ID:([^[]*)\]([^[]*)\[/ID:([^[]*)\]`", "<li><a href=\"pnj.php?id=".$id."&amp;reponse=\\1&amp;poscase=".$W_case."\" onclick=\"return envoiInfo(this.href, 'information')\">\\2</a></li>", $reponses[$reponse]);
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
$quete_fini = explode(';', $joueur->get_quete_fini());
if($joueur->get_quete_fini() != '')
{
	foreach($quete_fini as $quetef)
	{
		// Nouvelle version
		$message = preg_replace("`\[quete_finie:${quetef}\]([^[]*)\[/quete_finie:${quetef}\]`", "\\1", $message);
		//On affiche le lien pour la discussion
		$message = preg_replace("`\[QUETEFINI".$quetef.":([^[]*)\]([^[]*)\[/QUETEFINI".$quetef.":([^[]*)\]`", "<li><a href=\"pnj.php?id=".$id."&amp;reponse=\\1&amp;poscase=".$W_case."\" onclick=\"return envoiInfo(this.href, 'information')\">\\2</a></li>", $message);
		$supp = false;
	}
}
while (preg_match("`\[non_quete:([^[]*)\]([^[]*)\[/non_quete:([^[]*)\]`", $message, $regs))
{
	$numq = $regs[1];
	if (in_array($numq, $quetes_actives) == false &&
			in_array($numq, $quete_fini) == false)
	{
		$message = preg_replace("`\[non_quete:${numq}\]([^[]*)\[/non_quete:$numq\]`",
														 $regs[2], $message);
	}
	else
	{
		$message = preg_replace("`\[non_quete:${numq}\]([^[]*)\[/non_quete:$numq\]`",
														 '', $message);
	}
}
while (preg_match("`\[ISQUETE:([^[]*)\]([^[]*)\[/ISQUETE:([^[]*)\]`", $message, $regs))
{
	$numq = $regs[1];
	if (in_array($numq, $quetes_actives))
	{
		$message = preg_replace("`\[ISQUETE:${numq}\]([^[]*)\[/ISQUETE:$numq\]`",
														 $regs[2], $message);
	}
	else
	{
		$message = preg_replace("`\[ISQUETE:${numq}\]([^[]*)\[/ISQUETE:$numq\]`",
														 '', $message);
	}
}
//On supprime le lien pour la discussion
$message = preg_replace("`\[QUETE([^[]*)\]([^[]*)\[/QUETE([^[]*)\]`", "", $message);
//On supprime les autres liens
$message = preg_replace("`\[QUETEFINI([^[]*)\]([^[]*)\[/QUETEFINI([^[]*)\]`", "", $message);
$message = preg_replace("`\[quete_finie:([^[]*)\]([^[]*)\[/quete_finie:([^[]*)\]`", "", $message);
$message = preg_replace("`\[retour]`", "<li><a href=\"informationcase.php?case=".$W_case."\" onclick=\"return envoiInfo(this.href, 'information')\">Retour aux informations de la case</a></li>", $message);
//Validation de la quête
if(preg_match("`\[quete]`", $message))
{
	verif_action('P'.$id, $joueur, 's');
	$message = preg_replace("`\[quete]`", "", $message);
}
//Prise d'une quête
if(preg_match("`\[prendquete:([^[]*)\]`", $message, $regs))
{
	prend_quete($regs[1], $joueur);
	$message = preg_replace("`\[prendquete:([^[]*)\]`", "", $message);
}
//Donne un item
if(preg_match("`\[donneitem:([^[]*)\]`", $message, $regs))
{
	$joueur->prend_objet($regs[1]);
	$message = preg_replace("`\[donneitem:([^[]*)\]`", "", $message);
	verif_action($regs[1], $joueur, 's');
}
//Vends un item
if(preg_match("`\[vendsitem:([^[\:]*):([^[]*)\]`", $message, $regs))
{
	if ($joueur->get_star() < $regs[2])
	{
		$replace = "Vous n'avez pas assez de stars !!<br/>";
	}
	else
	{
		$joueur->set_star($joueur->get_star() - $regs[2]);
		$joueur->prend_objet($regs[1]);
		$replace = "Vous recevez un objet.<br/>";
		verif_action($regs[1], $joueur, 's');
	}
	$message = preg_replace("`\[vendsitem:([^[\:]*):([^[]*)\]`", $replace, $message);
}
//validation inventaire
if(preg_match("`\[verifinventaire:([^[]*)\]`", $message, $regs))
{
	if (verif_inventaire($regs[1], $joueur) == false) {
		$message = "<h5>Tu te moques de moi, mon bonhomme ?</h5>";
	}
	else {
		$message = preg_replace("`\[verifinventaire:([^[]*)\]`", "", $message);
	}
}
echo '<ul>'.$message.'</ul>';
echo "</fieldset>";
?>