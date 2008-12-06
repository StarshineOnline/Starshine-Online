<?php
/**
 * @file messagerie.inc.php
 * Gestion des de la messagerie.
 */ 

/**
 * Renvoie le nombre de message non lu par type pour un perso.
 *
 * @param $ud_perso identifiant du perso.
 * 
 * @return ['total'] Nombre total de message non lu.
 * @return ['groupe'] Nombre de message de groupe non lu.
 * @return ['perso'] Nombre de message perso non lu.
 */

function transform_texte($texte)
{
	$texte = htmlspecialchars(stripslashes($texte));
	//bbcode de merde
	$texte = str_replace('[br]', '<br />', $texte);
	$texte = eregi_replace("\[b\]([^[]*)\[/b\]", '<strong>\\1</strong>', $texte);
	$texte = eregi_replace("\[i\]([^[]*)\[/i\]", '<i>\\1</i>', $texte);
	$texte = eregi_replace("\[url\]([^[]*)\[/url\]", '<a href="\\1">\\1</a>', $texte);
	$texte = str_replace("[/color]", "</span>", $texte);
	//Lien vers échange
	$texte = eregi_replace("\[echange:([^[]*)\]", "<a href=\"javascript:envoiInfo('echange.php?id_echange=\\1', 'information')\">Echange ID : \\1</a>", $texte);
	return $texte;
}

function message_affiche($message, $joueur, $thread_title = '')
{
	$date = $message->date;
	if($message->titre != $thread_title) $titre = htmlspecialchars(stripslashes($message->titre));
	else $titre = '';
	$message_texte = transform_texte($message->message);
	$bonus = recup_bonus($message->id_auteur);
	$bonus_total = recup_bonus_total($message->id_auteur);
	if(array_key_exists(19, $bonus))
	{
		$fichier = 'image/avatar/'.$bonus_total[19]['valeur'];
		if(is_file($fichier))
		{
			$avatar = '<img src="'.$fichier.'" style="float : left; margin : 5px;"/>';
		}
		else $avatar = '';
	}
	$message_affiche = '
		<head>
		<script type="text/javascript">
		//<![CDATA[
		new Event.observe(window, "load", loadTips);
		function loadTips()
		{
			new Tip("option_message_'.$message->id_message.'", "content", {title: "this tooltip has a title"});
		}
		//]]>
		</script>
		</head>
		<h3 class="messagerie">
			<span class="auteur">'.$message->nom_auteur.'</span>
			<span class="titre">'.$titre.'</span>
			<span class="date">'.$message->etat.' / '.$date.'<div id="option_message_'.$message->id_message.'">a</div></span>
		</h3>
		<p>'.$avatar.$message_texte.'</p>';
	
	//Masquer
	if($message->etat != 'masque') $message_affiche .= '<a href="message_change_etat.php?id_message='.$message->id_message.'&amp;etat=masque" onclick="return envoiInfo(this.href, \'message'.$message->id_message.'\')">Masquer</a>';
	//Important
	if($message->etat != 'important') $message_affiche .= ' / <a href="message_change_etat.php?id_message='.$message->id_message.'&amp;etat=important" onclick="return envoiInfo(this.href, \'message'.$message->id_message.'\')">Important</a>';
	//Suppression
	if($joueur_id == $message->id_auteur) $message_affiche .= ' / <a href="message_change_etat.php?id_message='.$message->id_message.'&amp;etat=del" onclick="if(confirm(\'Voulez vous supprimer votre message ?\')) return envoiInfo(this.href, \'message'.$message->id_message.'\'); else return false;">Supprimer</a>';
	return $message_affiche;
}