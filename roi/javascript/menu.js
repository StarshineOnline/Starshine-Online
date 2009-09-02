function hideMenu()
{
	for(i = 1; i < 5; i++)
	{
		$('smenu' + i).fade({ duration: 0.2 })
	}
}
	
function showMenu(id)
{
	new Effect.toggle('smenu' + id, 'appear', { duration: 0.2 })
	for(i = 1; i < 5; i++)
	{
		if(i != id)
		{
			$('smenu' + i).fade({ duration: 0.2 })
		}
	}
}
function enchere()
{
	if(confirm('Voullez vous mettre ' + $('nbr').value + ' ' + $('ressource_vente').value + ' en vente à ' + $('prix').value + ' stars ?')) {envoiInfo('gestion_royaume.php?direction=bourse_ressource&ressource=' + $('ressource_vente').value + '&prix=' + $('prix').value + '&nombre=' + $('nbr').value, 'message_confirm'); envoiInfo('gestion_royaume.php?direction=bourse', 'contenu_jeu'); } else {return false;}
}
function clickMenu(el)
{
	refresh(el.href, 'conteneur');
	hideMenu();
	return false;
}

// Déplacement sur la carte
function Loadchargement()
{
	$('loading').show();
}
function Hidechargement()
{
	$('loading').hide();
}

function deplacement(direction)
{
	function AfficheCarte(map)
	{
		$('loading').hide();
		$('centre').innerHTML = map.responseText;
	}

	new Ajax.Request('./deplacement.php',{method:'get',parameters:'deplacement='+direction,onLoading:Loadchargement,onComplete:AfficheCarte});
}
function refresh(page, position)
{
	function Affiche(requete){$(position).innerHTML = requete.responseText; Hidechargement()}
	new Ajax.Request(page,{method:'get',onLoading:Loadchargement,onComplete:Affiche});
}
function menu_change(input_name)
{
	if ($('menu_encours').value=='')
	{
		$('menu_encours').value= input_name;
		$(input_name).addClassName('select');
		$(input_name+'_menu').show();
	}
	else
	{
		var tmp = $('menu_encours').value;
		$(tmp+'_menu').hide();
		$(tmp).removeClassName('select');
		$('menu_encours').value= input_name;
		$(input_name).addClassName('select');
		$(input_name+'_menu').show();
	}
	$('message_confirm').innerHTML = '';

}
function affiche_page(page)
{
	function affiche(contenu)
	{
		$('loading').hide();
		$('contenu_jeu').innerHTML = contenu.responseText;
	}
	new Ajax.Request(page,{method:'get',onLoading:Loadchargement,onComplete:affiche});
	$('message_confirm').innerHTML = '';
	
}

function affiche_bataille(page,action)
{
	function affiche(contenu)
	{
		$('loading').hide();
		$('contenu_jeu').innerHTML = contenu.responseText;
	}
	new Ajax.Request(page,{method:'get',parameters:action,onLoading:Loadchargement,onComplete:affiche});
	$('message_confirm').innerHTML = '';
	
}


function royaume_update(id,nbr,action)
{
	function Affiche_Royaume(text)
	{
		$('loading').hide();	
		refresh('gestion_royaume.php?direction=boutique','contenu_jeu');
		refresh('perso_contenu.php','perso_contenu');			
		$('message_confirm').innerHTML = text.responseText;
	}
	new Ajax.Request('./ajax/gestion_royaume_update.php',{method:'post',parameters:'id='+id+'&nbr='+nbr+'&action='+action,onLoading:Loadchargement,onComplete:Affiche_Royaume});
}

function minimap(x,y)
{
	function Affiche_minimap(text)
	{
		$('loading').hide();	
		$('affiche_minimap').innerHTML = text.responseText;
	}
	new Ajax.Request('./mini_map.php',{method:'post',parameters:'x='+x+'&y='+y,onLoading:Loadchargement,onComplete:Affiche_minimap});
}

function texte_update(message,action)
{
	function Affiche_texte(text)
	{
		$('loading').hide();	
		refresh('propagande.php','contenu_jeu');
		$('message_confirm').innerHTML = text.responseText;
	}
	new Ajax.Request('./ajax/gestion_royaume_update.php',{method:'post',parameters:'message='+message+'&action='+action,onLoading:Loadchargement,onComplete:Affiche_texte});
}

