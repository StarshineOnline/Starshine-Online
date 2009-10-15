function enchere()
{
	if(confirm('Voullez vous mettre ' + $('#nbr').val() + ' ' + $('#ressource_vente').val() + ' en vente à ' + $('#prix').val() + ' stars ?')) {envoiInfo('gestion_royaume.php?direction=bourse_ressource&ressource=' + $('#ressource_vente').val() + '&prix=' + $('#prix').val() + '&nombre=' + $('#nbr').val(), 'message_confirm'); envoiInfo('gestion_royaume.php?direction=bourse', 'contenu_jeu'); } else {return false;}
}

// Déplacement sur la carte

function deplacement(direction)
{
	$('#centre').load('./deplacement.php?deplacement='+direction);
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
	$('#message_confirm').text('');
	

}
function affiche_page(page)
{
	$('#message_confirm').text('');
	$('#contenu_jeu').load(page);
	
}

function affiche_bataille(page,action)
{
	$('#contenu_jeu').load(page+'?'+action);
	$('#message_confirm').text('');
}

function royaume_update(id,nbr,action)
{
	$('#message_confirm').load('./ajax/gestion_royaume_update.php?id='+id+'&nbr='+nbr+'&action='+action);
	refresh('gestion_royaume.php?direction=boutique','contenu_jeu');
	refresh('perso_contenu.php','perso_contenu');			
}

function minimap(x,y)
{
	$('#affiche_minimap').load('./mini_map.php?x='+x+'&y='+y);
}

function texte_update(message,action)
{
	$('#message_confirm').load('./ajax/gestion_royaume_update.php?message='+message+'&action='+action);
	refresh('propagande.php','contenu_jeu');
	
}
function repere_bataille(input_id)
{
	if ($('#case_old').val() == '')
	{
		$('#case_old').val(input_id);
		$('#pos_'+input_id).addClass('select_repere');
	}
	else
	{
		$('#pos_'+$('#case_old').val()).removeClass('select_repere');	
		$('#case_old').val(input_id);
		$('#pos_'+input_id).addClass('select_repere');
	}
	
}

function select_groupe(groupeid)
{
	if ($('#groupe_'+groupeid).val() == 0)
	{
		$('#ligroupe_'+groupeid).addClass('select');
		$('#groupe_'+groupeid).val(1);
	}
	else
	{
		$('#ligroupe_'+groupeid).removeClass('select');
	
		$('#groupe_'+groupeid).val(0);
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

