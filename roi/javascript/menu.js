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
function menu_change(input_name)
{
	if ($('#menu_encours').val() =='')
	{
		$('#menu_encours').val(input_name) ;
		$('#'+input_name).addClass('select');
		$('#'+input_name+'_menu').show();
	}
	else
	{
		var tmp = $('#menu_encours').val();
		$('#'+tmp+'_menu').hide();
		$('#'+tmp).removeClass('select');
		$('#menu_encours').val(input_name);
		$('#'+input_name).addClass('select');
		$('#'+input_name+'_menu').show();
	}
	$('#message_confirm').innerHTML = '';

}
function affiche_page(page)
{
	$('#contenu_jeu').load(page);
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
	$('#message_confirm').load('./ajax/gestion_royaume_update.php?id='+id+'&nbr='+nbr+'&action='+action);
	refresh('gestion_royaume.php?direction=boutique','contenu_jeu');
	refresh('perso_contenu.php','perso_contenu');			

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
function select_groupe(groupeid)
{
	if ($('groupe_'+groupeid).value == 0)
	{
		$('ligroupe_'+groupeid).addClassName('select');
		$('groupe_'+groupeid).value = 1;
	}
	else
	{
		$('ligroupe_'+groupeid).removeClassName('select');
	
		$('groupe_'+groupeid).value = 0;
	}
}
$(document).ready(function()
{
	$("#loading").ajaxStart(function()
	{
		$(this).show();
	});

	$("#loading").ajaxStop(function()
	{
		$(this).hide();
	});
});

