<?php

include('inc/fp.php');
$joueur = recupperso($_SESSION['ID']);
check_perso($joueur);
$W_case = $_GET['poscase'];

if(array_key_exists('reponse', $_GET)) $reponse = $_GET['reponse']; else $reponse = 0;

$id = $_GET['id'];
$requete = 'SELECT * FROM pnj WHERE id = \''.sSQL($id).'\'';  
$req = $db->query($requete);
$row = $db->read_assoc($req);

echo '<h2>'.$row['nom'].'</h2>';
$reponses = explode('*****', nl2br($row['texte']));
$message = eregi_replace("\[ID:([^[]*)\]([^[]*)\[/ID:([^[]*)\]", "<li><a href=\"pnj.php?id=".$id."&amp;reponse=\\1&amp;poscase=".$W_case."\" onclick=\"return envoiInfo(this.href, 'information')\">\\2</a></li>", $reponses[$reponse]);
//On vérifie si ya une quête pour ce pnj
$supp = true;
if($joueur['quete'] != '')
{
	foreach($joueur['quete'] as $quete)
	{
		$requete = 'SELECT * FROM quete WHERE id = '.$quete['id_quete'];
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		$objectif = unserialize($row['objectif']);
		$i = 0;
		foreach($quete['objectif'] as $objectif_fait)
		{
			$total_fait = $objectif_fait->nombre;
			$total = $objectif[$i]->nombre;
			//On vérifie si il peut accéder à cette partie de la quête
			if($total_fait >= $total) $objectif[$i]->termine = true;
			else $objectif[$i]->termine = false;
			/*echo $i.'<br />';
			print_r($objectif[$i]);
			print_r($objectif_fait);
			echo '<br />';*/
			if(($objectif_fait->requis == '' OR $objectif[$objectif_fait->requis]->termine) AND !$objectif[$i]->termine)
			{
				$cible = $objectif_fait->cible;
				/*if($cible[0] == 'P')
				{
					//Si actif pour tous les PNJ ou pour ce PNJ
					$id_cible = mb_substr($cible, 1);
					if($id_cible == 0 OR $id_cible == $id)
					{*/
						//On affiche le lien pour la discussion
						$message = eregi_replace("\[QUETE".$quete['id_quete']."-".$i.":([^[]*)\]([^[]*)\[/QUETE".$quete['id_quete']."-".$i.":([^[]*)\]", "<li><a href=\"pnj.php?id=".$id."&amp;reponse=\\1&amp;poscase=".$W_case."\" onclick=\"return envoiInfo(this.href, 'information')\">\\2</a></li>", $message);
						$supp = false;
						//On supprime les autres liens
						//$message = eregi_replace("\[QUETE([^[]*)\]([^[]*)\[/QUETE([^[]*)\]", "", $message);
					/*}
				}*/
			}
			$i++;
		}
	}
}
if($joueur['quete_fini'] != '')
{
	$quete_fini = explode(';', $joueur['quete_fini']);
	foreach($quete_fini as $quete)
	{
		//On affiche le lien pour la discussion
		$message = eregi_replace("\[QUETEFINI".$quete.":([^[]*)\]([^[]*)\[/QUETEFINI".$quete.":([^[]*)\]", "<li><a href=\"pnj.php?id=".$id."&amp;reponse=\\1&amp;poscase=".$W_case."\" onclick=\"return envoiInfo(this.href, 'information')\">\\2</a></li>", $message);
		$supp = false;
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
//validation inventaire
if(eregi("\[verifinventaire:([^[]*)\]", $message, $regs))
{
	verif_inventaire($regs[1], $joueur);
	$message = eregi_replace("\[verifinventaire:([^[]*)\]", "", $message);
}
echo '<ul>'.$message.'</ul>';
?>