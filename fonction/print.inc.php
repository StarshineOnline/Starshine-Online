<?php

function print_mess_bar()
{
?>
<div id="mess_bar">
<input type="button" onclick="javascript:mess_button('b')" value="Gras" id="button_b" />
<input type="button" onclick="javascript:mess_button('i')" value="Italique" id="button_i" />
</div>
<?php
}
//=======================================================================================================================================//
//== print_head : Fonction qui écrit l'entête HTML
//==-----------------------------------------------------------------------------------------------------------------------------------==//
//==	- Entrée : 	- title 		:	titre de la page 				(par défaut : Sans titre)
//==				- bgcolor 		:	couleur de fond de la page 		(par défaut : 0xFFFFFF)
//==				- background 	: 	image de fond de la page 		(par défaut : aucune)
//==				- textcolor 	:	couleur du texte de la page 	(par défaut : 0x000000)
//==-----------------------------------------------------------------------------------------------------------------------------------==//
//==	- Sortie : 	RIEN
//=======================================================================================================================================/
function print_head($Option_List = "")
{
	{//-- Initialisation & valeurs par defaut
		$style = "";
		$titre = "StarShine, le jeu qu'il s'efface tout seul !";
	}
	echo "<?xml version='1.1' encoding='iso-8859-15'?>
		  <!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.1//EN' 'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd'>
		  <html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en'>
           <head>
		    <meta http-equiv='content-type' content='text/html; charset=utf-8' />";
	{//-- OPTIONS
		$Option_List = trim($Option_List);
		$opt_tmp = explode(";", $Option_List);

		foreach($opt_tmp as $opt)
		{//-- On parcour toutes les options présente dans Option_List
			$opt_value = explode(":", $opt);	//-- On re-explose l'option afin d'en determiner le type et la valeur
			
			switch(trim($opt_value[0]))
			{//-- suivant le résultat obtenu
				case "identifier-url"	:	echo "  <meta name='identifier-url' content='".trim($opt_value[1])."'/>\n";
											break;

				case "content-language"	:	echo "  <meta http-equiv='content-language' content='".trim($opt_value[1])."'/>\n";
											break;							

				case "description"		:	echo "  <meta name='description' content='".htmlentities(trim($opt_value[1]), ENT_QUOTES)."'/>\n";
											break;

				case "keywords"			:	echo "  <meta name='keywords' content='".htmlentities(trim($opt_value[1]), ENT_QUOTES)."'/>\n";
											break;

				case "copyright"		:	echo "  <meta name='copyright' content='".trim($opt_value[1])."'/>\n";
											break;

				case "robots"			:	echo "  <meta name='robots' content='INDEX, FOLLOW, NOYDIR, NOODP, ALL'/>\n";
											break;

				case "title"			:	echo "	<title>".trim($opt_value[1])."</title>\n";
											break;
											
				case "css"				:	$tmp = split("~", trim($opt_value[1]));
											for($i = 0; $i < count($tmp); $i++)
												echo "<link href='".trim($tmp[$i])."' rel='stylesheet' type='text/css' />\n";
											break;

				case "script"			:	$tmp = split("~", trim($opt_value[1]));
											for($i = 0; $i < count($tmp); $i++)
												echo "<script type='text/javascript' src='".trim($tmp[$i])."'></script>\n";
											break;

				case "style"			:	$style = $opt_value[1];
											break;
			}
		}
	}
	echo "    <link rel=\"icon\" type=\"image/png\" href=\"http://www.starshine-online.com/image/favicon.png\" />
			</head>
		   <body"; if(!empty($style)) { echo "style='$style'"; }; echo ">
		    <div id='overDiv' style='position:absolute; visibility:hidden; z-index:1000;'></div>\n";
}
function print_foot()
{
	echo "\n</body>\n</html>";
}
function print_messbar()
{
?>
	<input type="button" value="b" class="msgbar" style="font-weight:bold" onclick="storeCaret('b')">
	<input type="button" value="i" class="msgbar" style="font-style:italic" onclick="storeCaret('i')">
	<input type="button" value="u" class="msgbar" style="text-decoration:underline" onclick="storeCaret('u')">
	<input type="button" value="quote" class="msgbar" onclick="storeCaret('quote')">
	<input type="button" value="code" class="msgbar" onclick="storeCaret('code')">
	<input type="button" value="url" class="msgbar" onclick="storeCaret('url')">
	<input type="button" value="img" class="msgbar" onclick="storeCaret('img')"><br/>
<?php
}

function make_overlib($message)
{
	$print = ereg_replace('([^\\])\'', '\\1\\\'', $message);
	$print = ereg_replace("[\n\r]", '', $print);
	return "overlib('<ul><li class=\'overlib_titres\'>".$print."</li></ul>', BGCLASS, 'overlib', BGCOLOR, '', FGCOLOR, '', VAUTO);";
}

function affiche_perso_visu($joueur, $W_row, $position="")
{
	global $db;
	global $Tclasse;

	$mybonus = recup_bonus($_SESSION['ID']);
	echo '<li style="clear:both;">
	';
	$W_nom = $W_row['nom'];
	$W_race = $W_row['race'];
	$W_ID = $W_row['ID'];
	$W_hp = $W_row['hp'];
	$bonus = recup_bonus($W_ID);
	// on envois dans infojoueur.php -> ID du joueur et La position de la case ou il se trouve
	
	$requete = "SELECT ".$W_race." FROM diplomatie WHERE race = '".$joueur->get_race()."'";
	$req_diplo = $db->query($requete);
	$row_diplo = $db->read_array($req_diplo);
	
	$statut_joueur = 'normal';
	$diplo = $row_diplo[0];
	if ($row_diplo[0] == 127) {
		$amende = recup_amende($W_ID);
		$row_diplo[0] = 0;
		if($amende)	{
			switch($amende['statut']) {
			case 'normal' :
				break;
			case 'bandit' :
				$row_diplo[0] = 5;
				$statut_joueur = 'Bandit';
				break;
			case 'criminel' :
				$row_diplo[0] = 10;
				$statut_joueur = 'Criminel';
				break;
			}
		}
	}
	$facteur_xp = $row_diplo[0] * 0.2;
	$facteur_honneur = ($row_diplo[0] * 0.2) - 0.8;
	if ($facteur_honneur < 0) $facteur_honneur = 0;
	if(array_key_exists(6, $bonus) AND !check_affiche_bonus($bonus[6], $joueur, $W_row)) $chaine_nom = $W_nom;
	else $chaine_nom = $W_row['gnom'].' '.$W_nom;
	$echo = $Gtrad['diplo'.$diplo].' => XP : '.($facteur_xp * 100).'% - Honneur : '.($facteur_honneur * 100).'%';
	echo '<img src="image/personnage/'.$W_race.'/'.$W_race.'_'.$Tclasse[$W_row['classe']]["type"].'.png" alt="'.$W_race.'" title="'.$W_race.'" style="vertical-align: middle;height:21px;float:left;width:21px;" /><span style="font-weight : bold;float:left;width:325px;margin-left:15px;"><a href="infojoueur.php?ID='.$W_ID.'&poscase='.$W_case.'" onclick="return envoiInfo(this.href, \'information\');" onclick="return nd();" onmouseover="return '.make_overlib($echo).'" onmouseout="return nd();">';
			
	if ($W_hp <= 0)
		{
			echo '<span class="mort">'.$chaine_nom.'</span> ';
		}
	else
		{
			echo $chaine_nom;
		}

	echo '</a>'.$position.'</span>';
	echo '<span style="float:left;">';
	if ($W_ID != $_SESSION['ID'])
	{
		echo '
		<a href="envoimessage.php?id_type=p'.$W_ID.'" onclick="return envoiInfo(this.href, \'information\')"><img src="image/interface/message.png" title="Envoyer un message" /></a>';
		if ($joueur->get_sort_jeu() != '') echo '<a href="sort_joueur.php?poscase='.$W_case.'&amp;id_joueur='.$W_ID.'" onclick="return envoiInfo(this.href, \'information\')"><img src="image/sort_hc_icone.png" title="Lancer un sort" alt="Lancer un sort" /></a>';
		if ($row_diplo[0] <= 5 OR array_key_exists(5, $mybonus)) echo '<a href="echange.php?poscase='.$W_case.'&amp;id_joueur='.$W_ID.'" onclick="return envoiInfo(this.href, \'information\')"><img src="image/icone/echanger.png" alt="Echanger" title="Echanger" /></a>';
	}
	else
	{
		if ($joueur->get_sort_jeu() != '') echo '<a href="sort.php" onclick="return envoiInfo(this.href, \'information\')"><img src="image/sort_hc_icone.png" title="Lancer un sort" alt="Lancer un sort" /></a>';
	}
	if ($statut_joueur != 'normal') echo ' ('.$statut_joueur.')';
	echo '</span>';
	
	echo '</li>';	
}

?>