function envoiInfoPost(page,position)
{
	function Affiche(requete){$(position).innerHTML = requete.responseText; Hidechargement();}
	new Ajax.Request(page,{method:'get',onLoading:Loadchargement,onComplete:Affiche});
}


function montre(id)
{
	var d = document.getElementById(id);
	for (var i = 1; i<=10; i++)
	{
		if (document.getElementById('smenu'+i))
		{
			document.getElementById('smenu'+i).style.display='none';
		}
	}
	if (d)
	{
		d.style.display='block';
	}
}

function hide_op()
{
	document.getElementById('op').style.visibility = 'hidden';
	document.getElementById('valeur').style.visibility = 'hidden';
}

function show_op()
{
	document.getElementById('op').style.visibility = 'visible';
	document.getElementById('valeur').style.visibility = 'visible';
}

function opii(id)
{
	selection = id.options[id.selectedIndex].value;
	switch(selection)
	{
		case '00' :
			show_op();
		break;
		case '01' :
			show_op();
		break;
		case '09' :
			show_op();
		break;
		case '10' :
			hide_op();
		break;
		case '14' :
			show_op();
		break;
		default :
			hide_op();
		break;
	}
}

function switch_map(cases)
{
	for (i=0; i < cases; i++)
	{
		if(document.getElementById('marq' + i))
		{
			//alert(document.getElementById('marq' + i).style.borderWidth);
			if(document.all) // IE
			{
				if(document.getElementById('marq' + i).style.borderWidth == '1px') document.getElementById('marq' + i).style.borderWidth = '0px';
				else document.getElementById('marq' + i).style.borderWidth = '1px';
			}
			else
			{
				if(document.getElementById('marq' + i).style.borderWidth == '1px 1px 1px 1px') document.getElementById('marq' + i).style.borderWidth = '0px 0px 0px 0px';
				else document.getElementById('marq' + i).style.borderWidth = '1px 1px 1px 1px';
			}
		}
	}
}

function checkCase()
{
	var chaine = '';
	for (i=0; i < 25; i++)
	{
		if(document.getElementById('mess' + i) && document.getElementById('mess' + i).checked)
		{
			if(chaine != '') chaine = chaine + '|';
			chaine = chaine + document.getElementById('mess' + i).value;
		}
	}
	if(chaine != '') envoiInfo('messagerie_ajax.php?javascript=ok&action=delc&ID=' + chaine, 'liste_message');
}


// Déplacement sur la carte


function deplacement(direction, cache, royaume)
{
	if(cache != 'undefined' && cache)
		cache = '&cache_monstre';
	else
		cache = '';
		
	if(royaume != 'undefined' && royaume)
		royaume = '&affiche_royaume';
	else
		royaume = '';
	
	$('#centre').load('./deplacement.php?deplacement='+direction+cache+royaume);
}

function affiche_info(id_case)
{	
  $('information').load('./informationcase.php?case='+id_case);

}

function refresh(page,position)
{	
	$('#' + position).load(page);
	return false;
}

function envoiInfo(page,position)
{
	$('#' + position).load(page);
	return false;
}

function envoiInfoJS(page, position)
{
	function Affiche(requete){$(position).innerHTML = requete.responseText.evalJSON(); Hidechargement();}
	new Ajax.Request(page,{method:'get',onLoading:Loadchargement,onComplete:Affiche});
	return false;
}

function envoiFormulaire(formulaire, position)
{
	function Affiche(requete){$(position).innerHTML = requete.responseText; Hidechargement(); nd();}
	$(formulaire).request({method:'post',onLoading:Loadchargement,onComplete:Affiche});
	return false;
}

function envoiFormulaireGET(formulaire, position)
{
	function Affiche(requete){$(position).innerHTML = requete.responseText; Hidechargement(); nd();}
	$(formulaire).request({method:'get',onLoading:Loadchargement,onComplete:Affiche});
	return false;
}

function affichePopUp(input_name)
{
	$('#popup').show();
	$('#popup_content').load(input_name);
}
function fermePopUp()
{
	$('#popup').hide();
	$('#popup_content').innerHTML = '';
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

function adresse(tri, i, race)
{
	if(i == '') i = document.getElementById('i').value;
	if(tri == '') tri = document.getElementById('tri').value;
	else
	{
		if(i != 'moi') i = 0;
	}
	if(race == '') race = document.getElementById('race').value;
	envoiInfo('classement_ajax.php?tri=' + tri + '&i=' + i + '&race=' + race + '&javascript=true', 'table_classement');
}

function adresse_groupe(tri, i, race)
{
	if(i == '') i = document.getElementById('i').value;
	if(tri == '') tri = document.getElementById('tri').value;
	else
	{
		if(i != 'moi') i = 0;
	}
	if(race == '') race = document.getElementById('race').value;
	envoiInfo('classement_groupe_ajax.php?tri=' + tri + '&javascript=true', 'table_classement');
}

function clicMessage(id)
{
	message = document.getElementById("mess"+id);
	titremess = event.srcElement;
	if(message.style.display == 'none')
	{
		message.style.display = 'block';
		titremess.onmouseout = '';
	}
	else
	{
		message.style.display = 'none';
		titremess.onmouseout = 'masqueMessage(id)';
	}
}

function afficheMessage(id)
{
	element = document.getElementById("mess"+id);
	element.style.display = 'block';
}

function masqueMessage(id)
{
	element = document.getElementById("mess"+id);
	element.style.display = 'none';
}

function showChat(url) {
	lnk = document.getElementById('chatlink');
	lnk.style.display="none";
	frm = document.getElementById('chatframe');
	frm.style.display="block";
	frm.src = url;
	return false;
}

var cache_monstre;
var affiche_royaume = false;

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
