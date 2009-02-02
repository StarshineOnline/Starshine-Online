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

?>