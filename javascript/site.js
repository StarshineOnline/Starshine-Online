function menu_change(input_name)
{
	if ($('#menu_encours').val()=='')
	{
		$('#menu_encours').val(input_name);
		$('#'+input_name+'_menu').addClass('selected');
		$('#'+input_name+'_box').show();
	}
	else
	{
		var tmp = $('#menu_encours').val();
		$('#'+tmp+'_box').hide();
		$('#'+tmp+'_menu').removeClass('selected');
		$('#menu_encours').val(input_name);
		$('#'+input_name+'_menu').addClass('selected');
		$('#'+input_name+'_box').show();
		if ($('#perso_selected_id').val() != '')
		{
			$($('#perso_selected_id').val()).className ='';
			$('#personnage').hide();
		}		
	}
}
function Chargement()
{
	$('#loading_sso').show();
	$('#accueil').setAttribute('style','cursor:progress !important;')
}
function race(input_race,input_classe)
{
	if ($('#perso_selected_id').val() != '')
	{
		$($('#perso_selected_id').val()).className ='';			
	}
	$('#'+input_race+'_'+input_classe).className = 'perso_selected';
	$('#perso_selected_id').val(input_race+'_'+input_classe);
	$('#personnage').show();	
	$('#personnage').load('./site_accueil_personnage.php?race='+input_race+'&classe='+input_classe);
}
function validation_perso()
{
	if ($('#creat_nom').val() == '')
	{
		$('#creat_erreur').text('Vous avez laissé un champ libre, ou vos mots de passe ne correspondent pas');
		$('#creat_erreur').show();
	}

	if (($('#creat_pass').val() != $('#creat_pass2').val()) || ($('#creat_pass2').val()=='') || ($('creat_pass2').val()==''))
	{
		$('#creat_erreur').text('Vous avez laissé un champ libre, ou vos mots de passe ne correspondent pas');
		$('#creat_erreur').show();
	}
	if ($('#perso_selected_id').val() == '')
	{
		$('#creat_erreur').text("Vous n'avez pas sélectionné de type de personnage (cadre à droite).");
		$('#creat_erreur').show();
	}	
	if (($('#perso_selected_id').val() != '') && ($('#creat_pass').val() == $('#creat_pass2').val()) && ($('#creat_pass2').val()!='') && ($('#creat_pass2').val()!='') && ($('#creat_nom').val() != ''))
	{
		var tmp = $('#perso_selected_id').val();
		var perso = tmp.split('_');

		$('#personnage_box').load('./site_accueil_creation.php?race='+perso[0]+'&classe='+perso[1]+'&pseudo='+$('#creat_nom').val()+'&mdp='+$('#creat_pass').val());
	}
}
function affichePopUpErreur(erreur)
{
	$('popup_erreur').show();
	$('popup_erreur_content').innerHTML = erreur;
}
function fermePopUpErreur()
{
	Effect.DropOut('popup_erreur', { duration: 0.5, direction : top });
	$('popup_erreur_content').innerHTML = '';
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

$(function() {
		$(".combat").sortable();
		$(".combat").disableSelection();
	});

