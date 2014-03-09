// -*- tab-width: 2 -*-
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

function affiche_test(x)
{
	var doc;
	doc = document.getElementById('test'+x);
	x++;
	doc.innerHTML ="Perso : <input type=\"text\" name=\"id"+x+"\" onkeydown = \"affiche_test("+x+")\" /><br />";
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


function deplacement(direction, type, show_only)
{
		
	if(show_only != 'undefined' && show_only)
		show_only = '&show_only=' + show_only;
	else
		show_only = '';

	if (type == 'troisd')
	{
		$('#centre').load('./deplacement3D.php?deplacement='+direction+show_only);
	}
	else
	{
		$('#centre').load('./deplacement.php?deplacement='+direction+show_only);
	}
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
	nd();
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
  formul = $('#' + formulaire);
  $.ajax({type:formul.attr("method"),url:formul.attr("action"),data:formul.serialize(),success:function(html){
    $('#'+position).html(html);}});
  return false;
}

// La balise input qui sert à choisir le fichier doit avoir l'id "fileUpload"
function envoiFichier(formulaire, position)
{
  formul = $('#' + formulaire);
  // Chargement du script permettant l'envoi de fichier
  jQuery.getScript("./javascript/jquery/fileupload.js", function()
    { jQuery.ajaxFileUpload({url:formul.attr("action"),data:formul.serialize(), fileElementId:"fileUpload", dataType:"html",secureuri:false,success:function(html){
        $('#'+position).html(html); }}); });
  return false;
}

function affichePopUp(input_name)
{
	$('#popup').show();
	$('#popup_content').load(input_name);
}
function fermePopUp()
{
	$('#popup').hide("fold", {}, 1000);
	$('#popup_content').text('');
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

function adresse(typeClassement, raceClassement)
{
	if(typeClassement == '')
		typeClassement = document.getElementById('classement').value;
	if(raceClassement == '')
		raceClassement = document.getElementById('race').value;
	
	var sSearch = $('#classement_table_filter input').val();
	var iDisplayLength = parseInt( $('#classement_table_length select').val() );
	
	var url = 'classement_ajax.php' + '?' + 'ajax=true';
	if(typeClassement != '')
		url += '&' + 'classement=' + encodeURIComponent(typeClassement);
	if(raceClassement != '')
		url += '&' + 'race=' + encodeURIComponent(raceClassement);
	if(sSearch != undefined)
		url += '&' + 'sSearch=' + encodeURIComponent(sSearch);
	if( !isNaN(iDisplayLength) )
		url += '&' + 'iDisplayLength=' + encodeURIComponent(iDisplayLength);
	envoiInfo(url, 'table_classement');
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

function adresse_royaume(tri, i, race)
{
	if(i == '') i = document.getElementById('i').value;
	if(tri == '') tri = document.getElementById('tri').value;
	else
	{
		if(i != 'moi') i = 0;
	}
	if(race == '') race = document.getElementById('race').value;
	envoiInfo('classement_royaume_ajax.php?tri=' + tri + '&javascript=true', 'table_classement');
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
var show_only = '';

$(document).ready(function()
{
	$(".login_nom").focus();
	$("#popup").draggable({ handle: '#popup_menu'});
	$(".3D").draggable({handle: '#info_case'});
	$("#loading").ajaxStart(function()
	{
		$(this).show();
	});

	$("#loading").ajaxStop(function()
	{
		$(this).hide();
	});

		$("#debug_log_button").hide();

		$("#popup").ajaxError(function(e, jqxhr, settings, exception) {
				if (jqxhr.status == 403) {
						// Sans doute un security_block, pop erreur
						$('#popup').show();
						$('#popup_content').html(jqxhr.responseText);
						$('#popup_content h1').css('color', 'red');
				} else {
						// On loggue dans un cadre caché
						$('#debug_log').append('<p>status : '+jqxhr.status+' - '+jqxhr.statusText+'<br/>url: ' + settings.url + '</p>');
						$('#debug_log').append(jqxhr.responseText);
						$('#debug_log').append('<hr/>');
						// On fait apparaître le bouton de debug
						$('#debug_log_button').show();
				}
		});
});

function show_debug_log()
{
		$('#popup').show();
		$('#popup_content').html($('#debug_log').html() + '<a href="javascript:clear_debug_log()">clear</a>');
}

function clear_debug_log() { $('#debug_log').text(''); $('#popup_content').text(''); }

function remplir(destination, valeur, source)
{
	$('#'+destination).val(valeur);
	$('#'+source).hide();
}

function findPos(obj)
{
	var curleft = obj.offsetLeft || 0;
	var curtop = obj.offsetTop || 0;
	while (obj = obj.offsetParent)
	{
		curleft += obj.offsetLeft
		curtop += obj.offsetTop
	}
	return {x:curleft,y:curtop};
}

function suggestion(valeur, cible, origine)
{
	if(valeur.length == 0)
	{
		$('#'+cible).hide();
	}
	else
	{
		$.post("poste_pseudo.php", {queryString: ""+escape(valeur)+"", origine: ""+origine+"", cible: ""+cible+""}, 
		function(data)
		{
     	 	if(data.length >0) 
        	{
        		var tmp = document.getElementById(cible);
	        	tmp.innerHTML = data;
				$('#'+cible).show();
			}
        });
		pos = findPos(document.getElementById(origine));
		$('#'+cible).css({top: (pos.y + 20) + "px", left: pos.x + "px"});
	}
}

var Sound = {
	m_current_audio: null
};
function doLoop() {
	document.getElementById('audio_1').addEventListener('ended', function(){
			this.currentTime = 0;
			this.pause();
			document.getElementById('audio_2').play();
		}, false);

	document.getElementById('audio_2').addEventListener('ended', function(){
			this.currentTime = 0;
			this.pause();
			document.getElementById('audio_1').play();
		}, false);
}
function setAmbianceAudio(file) {
	if (Sound.m_current_audio == file || Sound.m_current_audio == null && file == '')
		return;
	if (file == '') {
		Sound.m_current_audio = null;
	}
	else
		Sound.m_current_audio = file;
	c = document.getElementById('ambiance_sound_container');
	var a = [ 'audio_1', 'audio_2' ];
	var e = [ 'ogg', 'mp3' ];
	for (var i in a) {
		var aa = document.getElementById(a[i]);
		if (aa) 
			aa.pause();
	}
	if (Sound.m_current_audio == null)
		return;
	while (c.hasChildNodes())
		c.removeChild(c.firstChild);
	for (var i in a) {
		var aa = document.createElement('audio');
		aa.setAttribute('id', a[i]);
		aa.setAttribute('controls', 'controls');
		for (var j in e) {
			var x = document.createElement('source');
			x.setAttribute('src', 'image/son/' + file + '.' + e[j]);
			aa.appendChild(x);
		}
		c.appendChild(aa);
	}
	document.getElementById(a[0]).play();
	doLoop();
}
function stopAmbiance() {
	var a = [ document.getElementById('audio_1'),
						document.getElementById('audio_2') ];
	for (var i in a) 
		a[i].pause();
}
function showSoundPanel() {
	var p = $('#ambiance_sound').dialog('open');
}

