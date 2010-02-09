<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
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
	$texte = nl2br($texte);
	$texte = preg_replace("`\[b\]([^[]*)\[/b\]`i", '<strong>\\1</strong>', $texte);
	$texte = preg_replace("`\[i\]([^[]*)\[/i\]`i", '<i>\\1</i>', $texte);
	$texte = preg_replace("`\[url\]([^[]*)\[/url\]`i", '<a href="\\1">\\1</a>', $texte);
	$texte = str_replace("[/color]", "</span>", $texte);
	//Lien vers échange
	$texte = preg_replace("`\[echange:([^[]*)\]`i", "<a href=\"echange.php?id_echange=\\1\" onclick=\"return envoiInfo(this.href, 'information')\">Echange ID : \\1</a>", $texte);
	return $texte;
}

function message_affiche($message, $joueur_id, $thread_title = '')
{
	$date =	date("d-m H:i", strtotime($message->date));
//	$date = $message->date;
	if($message->titre != $thread_title) $titre = htmlspecialchars(stripslashes($message->titre));
	else $titre = '';
	$message_texte = transform_texte($message->message);
	$bonus = recup_bonus($message->id_auteur);
	$bonus_total = recup_bonus_total($message->id_auteur);
/*	if(array_key_exists(19, $bonus))
	{
		$fichier = 'image/avatar/'.$bonus_total[19]['valeur'];
		if(is_file($fichier))
		{
			$style = 'style="background: transparent url('.$fichier.') no-repeat left;padding-left:42px;"';
		}
	}
	/*
		/*
		if($message->etat != 'important' && $message->etat != 'masque') 
		{
			$fav = '<a href="message_change_etat.php?id_message='.$message->id_message.'&amp;etat=important" onclick="return envoiInfo(this.href, \'message'.$message->id_message.'\')" title="Mettre en favoris"><span class="fav_off"></span></a><a href="message_change_etat.php?id_message='.$message->id_message.'&amp;etat=masque" onclick="return envoiInfo(this.href, \'message'.$message->id_message.'\')" title="Masquer"><span class="msg_cache"></span></a>';
		}
		elseif($message->etat == 'masque')
		{
			$fav = '<a href="message_change_etat.php?id_message='.$message->id_message.'&amp;etat=lu" onclick="return envoiInfo(this.href, \'message'.$message->id_message.'\')" title="Afficher"><span class="msg_voir"></span></a>';
			$masque = 'display:none;';
		}
		else 
		{
			$fav = '<a href="message_change_etat.php?id_message='.$message->id_message.'&amp;etat=lu" onclick="return envoiInfo(this.href, \'message'.$message->id_message.'\')" title="Mettre en favoris"><span class="fav_on"></span></a>';
		}
		*/	
		$bulle_haut = 'haut_bulle_gauche';
		if($joueur_id == $message->id_auteur) {$del =  '<a href="message_change_etat.php?id_message='.$message->id_message.'&amp;etat=del" onclick="if(confirm(\'Voulez vous supprimer votre message ?\')) return envoiInfo(this.href, \'message'.$message->id_message.'\'); else return false;" title="Supprimer"><span class="del"></span></a>';$style_me='style="float:right !important;"';}
		if($joueur_id == $message->id_auteur) {$bulle_haut = 'haut_bulle_droite';$class_message = 'bulle_message';}elseif($message->etat=='non_lu'){$class_message = 'bulle_message_new';} else{$class_message = 'bulle_message';}

		$message_affiche = '
		<span class="'.$bulle_haut.'"></span><span class="messagerie" '.$style_me.'>
			<span class="auteur" '.$style.'>'.$message->nom_auteur.'</span>
			<span class="date">'.$date.'</span>
		</span>
		<span style="float:right">'.$fav.'</span>
		<p id="mess'.$message->id_message.'" class="'.$class_message.'" style="clear:both;padding:2px;'.$masque.'" >'.$message_texte.'</p>
		<div class="message_action">
		'.$del.'
	</div>
	<div class="spacer"></div>';
	
	return $message_affiche;
}