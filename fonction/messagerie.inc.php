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
	$texte = eregi_replace("\[echange:([^[]*)\]", "<a href=\"echange.php?id_echange=\\1\" onclick=\"return envoiInfo(this.href, 'information')\">Echange ID : \\1</a>", $texte);
	return $texte;
}

function message_affiche($message, $joueur_id, $thread_title = '')
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
			$style = 'style="background: transparent url('.$fichier.') no-repeat left;padding-left:42px;"';
		}
	}
	if ($message->etat=='non_lu'){$div_nonlu = '<div style="background:transparent url(./../image/v2/box_accueil2.png);">';$divfin_nonlu = '</div>';}
	
		if($message->etat != 'important' && $message->etat != 'masque') 
		{
			$fav = '<a href="message_change_etat.php?id_message='.$message->id_message.'&amp;etat=important" onclick="return envoiInfo(this.href, \'message'.$message->id_message.'\')"><span class="fav_off"></span></a><a href="message_change_etat.php?id_message='.$message->id_message.'&amp;etat=masque" onclick="return envoiInfo(this.href, \'message'.$message->id_message.'\')"><span class="msg_cache"></span></a>';
		}
		elseif($message->etat == 'masque')
		{
			$fav = '<a href="message_change_etat.php?id_message='.$message->id_message.'&amp;etat=lu" onclick="return envoiInfo(this.href, \'message'.$message->id_message.'\')"><span class="msg_voir"></span></a>';		
			$masque = 'display:none;';
		}
		else 
		{
			$fav = '<a href="message_change_etat.php?id_message='.$message->id_message.'&amp;etat=lu" onclick="return envoiInfo(this.href, \'message'.$message->id_message.'\')"><span class="fav_on"></span></a>';
		}	
		if($joueur_id == $message->id_auteur) $del =  '<a href="message_change_etat.php?id_message='.$message->id_message.'&amp;etat=del" onclick="if(confirm(\'Voulez vous supprimer votre message ?\')) return envoiInfo(this.href, \'message'.$message->id_message.'\'); else return false;"><span class="del"></span></a>';		
	$message_affiche = $div_nonlu.'
		<span class="messagerie" onclick="$(\'mess'.$message->id_message.'\').toggle();">
			<span class="auteur" '.$style.'>'.$message->nom_auteur.'</span>
			<span class="titre">'.$titre.'</span>
			<span class="date">'.$date.'</span>
		</span>
		<span style="float:right">'.$fav.'</span>
		<p id="mess'.$message->id_message.'" style="clear:both;padding:2px;'.$masque.'" >'.$message_texte.'</p>
		<div class="message_action">
		'.$del.'
	</div>
	<div class="spacer"></div>'.$divfin_nonlu;
	
	return $message_affiche;
}