// -*- tab-width: 2 -*-


function affiche_ajax(data, status, jqXHR)
{
	aff_ico_sso();
  $("[data-toggle='tooltip']").tooltip('hide');
  $("[data-toggle='popover']").tooltip('hide');
  var maj_tooltip = false;
  $(data).find('section').each( function()
  {
  	switch(this.id)
  	{
  	case "modal":
		  var modal = document.getElementById("modal");
		  if( !modal )
		  {
		    var cont = document.getElementById("contenu");
		    modal = document.createElement("div");
		    modal.id = "modal";
		    modal.className = "modal fade";
		    modal.setAttribute("role", "dialog");
		    modal.tabIndex = "-1";
		    modal.setAttribute("aria-labelledby", "modalLabel");
		    cont.appendChild(modal);
		  }
    	modal.innerHTML =  this.innerHTML;
			$("#modal").modal('show');
			maj_tooltip = true;
  		break;
  	case 'recharger':
  		document.location=this.innerHTML.trim();
  		document.location.reload();
  	case 'maj_tooltips':
			maj_tooltip = true;
  		break;
  	case 'erreur':
  		aff_erreur(this.innerHTML, data);
  		break;
  	case 'javascript':
  		var script = creer_element("script", false, false, document.getElementsByTagName("body")[0], this.innerHTML);
  		script.setAttribute('type', 'text/javascript');
  		//alert(this.innerHTML);
  	default:
    	$('#'+this.id).html( this.innerHTML );
			maj_tooltip = true;
		}
  });
  if( maj_tooltip )
  	maj_tooltips();
}

function aff_erreur(contenu, donnees, icone/*='bug'*/)
{
	if( icone == undefined )
		icone = 'bug';
		var cont = document.getElementById('contenu_jeu');
		var alerte = document.createElement('div');
		alerte.className = 'alert alert-danger alert-dismissable';
		cont.insertBefore(alerte, cont.firstChild);
		alerte.style = 'margin-top: 5px;'
		var btn = document.createElement('button');
		btn.className = 'close';
		btn.setAttribute('aria-hidden', 'true');
		btn.setAttribute('data-dismiss', 'alert');
		btn.type = 'button';
		btn.innerHTML = '&times;';
		alerte.appendChild(btn);
		var ico = document.createElement('a');
		ico.className = 'icone icone-'+icone;
		ico.setAttribute('onclick', '$("#erreur_recu").toggle();');
		ico.style = 'margin-right: 5px;'
		alerte.appendChild(ico);
		var txt = document.createElement('span');
		txt.innerHTML = contenu;
		alerte.appendChild(txt);
		var recept = document.createElement('div');
		recept.innerHTML = donnees;
		recept.id = 'erreur_recu';
		recept.style = 'display: none; border: dashed 1px; margin-top: 5px;';
		alerte.appendChild(recept);
}

function charger(page)
{
	//alert('charger:'+page);
	aff_ico_charger();
  $.get(page, "ajax=1", affiche_ajax);
	return false;
}

function charger_formulaire(id)
{
  var formul = $('#' + id);
  $.ajax({type:formul.attr("method"),url:formul.attr("action")+"&ajax=1",data:formul.serialize(),success:affiche_ajax});
	return false;
}

function verif_charger(url, texte)
{
	if( confirm(texte) )
		charger(url);
	return false;
}

function envoie_texte(dest, id)
{
	var texte = $("#"+id).html().trim();
	$.ajax({type:"post",url:dest+"&ajax=1",data:"texte="+decode_texte(texte),success:affiche_ajax});
	return false;
}

function charger_formulaire_texte(id, id_texte)
{
  var formul = $('#' + id);
	var texte = $("#"+id_texte).html().trim();
  var donnees = formul.serializeArray();
  donnees[donnees.length] = {name:"texte", value:decode_texte(texte)};
  $.ajax({type:"post",url:formul.attr("action")+"&ajax=1",data:donnees,success:affiche_ajax});
	return false;
}

function charger_formulaire_fichier(id_form, id_input)
{
  formul = $('#' + id_form);
  // Chargement du script permettant l'envoi de fichier
  jQuery.getScript("./javascript/jquery/fileupload.js", function()
  { 
		jQuery.ajaxFileUpload({url:formul.attr("action")+"&ajax=1",data:formul.serialize(), fileElementId:id_input, dataType:"html",secureuri:false,success:affiche_ajax});
	});
  return false;
}

function decode_texte(texte)
{
	texte = bbcodeParser.htmlToBBCode(texte);
	if( texte.substring(texte.length-4) ==  '<br>' )
		texte = texte.substr(0, texte.length-4);
	while( texte.indexOf('<br>') >= 0 )
	{
		texte = texte.replace('<br>', '[br]');
	}
	return texte;
}

function charge_tab(elt, id)
{
  var e = $("#"+id);
  if( !e.html().trim().length )
  {
    e.html( getWait() );
    e.load(elt.getAttribute("data-url"));
  }
}

function temps_serveur()
{
	var date = new Date();
	return Math.ceil(date.getTime()/1000);
}

function maj_tooltips()
{
	// on active les tooltip déjà définis
	$("[data-toggle='tooltip']:not(#modal *)").tooltip({container: 'body'});
	$("#modal *[data-toggle='tooltip']").tooltip({container: '#modal'});
	// On crée ceux des buffs ainsi que les popvers
	$(".buff").each( function()
	{
		var li = $(this);
		var img = li.children();
		var nom = img[0].alt;
		li.tooltip({title:function()
		{
			return nom + " − Durée : " + formate_duree(li.attr('data-fin') - temps_serveur(), true);
		},placement:"left",container:"#contenu"});
		$(img[0]).popover({html:true,placement:"bottom",title:nom,container:"#contenu",content:function()
		{
			//var date = new Date();
			var txt = "<table><tbody>";
			txt += "<tr><th>Decription</th><td>"+li.attr("data-description")+"</td></tr>";
			txt += "<tr><th>Durée totale</th><td>"+formate_duree(li.attr("data-duree"), false)+"</td></tr>";
			txt += "<tr><th>Durée restante</th><td>"+formate_duree(li.attr('data-fin') - temps_serveur(), true)+"</td></tr>";
			txt += "<tr><th>Fin</th><td>"+formate_date(li.attr('data-fin'))+"</td></tr>";
			txt += "</tbody></table>";
			if( li.attr("data-suppr") )
				txt += "<a class='suppr_buff' href='suppbuff.php?id="+li.attr("data-suppr")+"' onclick='return charger(this.href);'>Supprimer le buff</a>";
			return txt;
		}});
	});
}

function aff_ico_charger()
{
	document.getElementById("icone-sso").className = "navbar-brand icone icone-charger";
}

function aff_ico_sso()
{
	document.getElementById("icone-sso").className = "navbar-brand icone icone-sso";
}

function formate_duree(duree, detail)
{
	var txt = "";
	var jours = Math.floor(duree / (3600*24));
	var heures = Math.floor(duree/3600) % 24;
	var mins = Math.floor(duree/60) % 60;
	var sec = duree % 60;
	if( jours )
		txt = jours+"j ";
	if( heures || (txt.length && detail) )
		txt += heures+"h ";
	if( mins || (txt.length && detail) )
		txt += mins+"min ";
	if(sec || detail)
		txt += sec+"s";
	return txt;
}

function formate_date(date)
{
	var d = new Date(date*1000);
	var jours = ["dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi"];
	var mois = ["janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre"];
	var txt = jours[d.getDay()] + " " + d.getDate();
	if( d.getDate() == 1 )
		txt += "<sup>er</sup>";
	txt += " " + mois[d.getMonth()] + " " + d.getFullYear() + " ";
	txt += d.getHours()+"h ";
	txt += d.getMinutes()+"min ";
	return txt + d.getSeconds()+"s";
}


function suppr_buff(elt)
{
	var li = $(elt);
	var img = li.children();
	var nom = img[0].alt;
	if(confirm('Voulez vous supprimer '+ nom +' ?'))
	{
		charger('suppbuff.php?id=' + li.attr('data-suppr'));
	}
}

var filtre_ecole_mag = false;
$.fn.dataTable.ext.search.push( function( settings, data, dataIndex )
{
	//alert(dataIndex + " : " + data);
	return false;
});
$.fn.dataTable.ext.type.detect.unshift( function ( d )
{
  return d.indexOf("<img") >= 0 ? 'aptitude' : null;
} );
$.fn.dataTable.ext.type.order['aptitude-pre'] = function ( d )
{
	var deb = d.indexOf('src=') + 17;
	var fin = d.indexOf('"', deb) - 4;
	var aptitude = d.substring(deb, fin);
	deb = d.indexOf('>', d.indexOf('<span')) + 1;
	fin = d.indexOf('</span>', deb);
	var valeur = parseInt(d.substring(deb, fin));
	switch(aptitude)
	{
	case 'sort_elem':
	case 'melee':
		return 1000+valeur;
	case 'sort_mort':
	case 'esquive':
		return 2000+valeur;
	case 'sort_vie':
	case 'distance':
		return 3000+valeur;
	case 'blocage':
	case 'dressage':
		return 4000+valeur;
	}
  return 0;
};
function filtre_table(filtre)
{
	filtre_ecole_mag = filtre;
	alert('Ne fonctionne pas');
	//tbl_sort_jeu.draw();
}

function toggle(id)
{
	$('#'+id).slideToggle();
	return false;
}

function init_bbcode()
{
	bbcodeParser.addBBCode('[b]{TEXT}[/b]', '<b>{TEXT}</b>');
	bbcodeParser.addBBCode('[i]{TEXT}[/i]', '<i>{TEXT}</i>');
	bbcodeParser.addBBCode('[u]{TEXT}[/u]', '<u>{TEXT}</u>');
	bbcodeParser.addBBCode('[s]{TEXT}[/s]', '<strike>{TEXT}</strike>');
}

function creer_element(tag, id, classe, pere, contenu, before)
{
  var elt = document.createElement(tag);
  if(id)
    elt.id = id;
  if(classe)
    elt.className = classe;
  if( contenu )
    elt.innerHTML = contenu;
  if( before )
    pere.insertBefore(elt, before)
  else
    pere.appendChild(elt);
  return elt;
}

function creer_bouton(texte, pere, code, style)
{
  var btn = document.createElement("button");
  btn.type = "button";
  btn.className = "btn";
  btn.innerHTML = texte;
  btn.setAttribute("onclick", code);
  if( style )
    btn.className += " btn-"+style;
  pere.appendChild(btn);
  return btn;
}

function creer_chp_form(type_chp, nom, valeur, pere)
{
	var chp = creer_element("input", null, "form-control", pere);
	chp.type = type_chp;
	if( nom )
		chp.name = nom;
	if( valeur )
		chp.value = valeur;
	return chp;
}

function modif_nom_creature(id)
{
	var div = $('#creat_'+id);
	var lien = div.find('.icone-modifier');
	var nom = div.find('.nom_creature');
	nom[0].contentEditable = true;
	nom.addClass("modifie");
	nom.keypress( function(evt)
	{
		if( evt.which == 13 )
			valide_nom_creature(id);
	});
	lien.removeClass('icone-modifier');
	lien.addClass('icone-ok');
	lien.attr("onclick", "return valide_nom_creature("+id+");");
}

function valide_nom_creature(id)
{
	var div = $('#creat_'+id);
	var nom = div.find('.nom_creature');
	charger("gestion_monstre.php?action=modifier&id="+id+"&nom="+nom.html());
}

function debugs()
{
	$('.debug').toggle();
	return false;
}

function carte_royaume(id)
{
	var url = '';
	if( document.URL.search('/roi/') >= 0 )
		url = '../'
	var id_img = 'carte_'+id;
	var svg = document.getElementById('carte_monde');
	var img = document.getElementById(id_img);
	if(img)
	{
		svg.removeChild(img);
	}
	else
	{
		var rect = svg.getElementsByTagName('rect');
		img = document.createElementNS('http://www.w3.org/2000/svg','image');
		img.id = id_img;
		img.setAttribute('width', '450px');
		img.setAttribute('height', '450px');
		if( id == 'monstres' )
			img.setAttributeNS('http://www.w3.org/1999/xlink','href', url+'image/carte_densite_mob.png');
		else
			img.setAttributeNS('http://www.w3.org/1999/xlink','href', url+'image/carte_royaume.png');
		img.setAttribute('filter', 'url(#filtre_'+id+')');
		svg.insertBefore(img, rect[0]);
	}
	$('#opt_'+id).toggleClass('active');
	return false;
}

// Aide
var elts_aide = null;
var elt_aide_actif = null;
var rect_aide_actif = null;
var elt_aide_aff = null;
function bascule_aide()
{
	var body = document.getElementsByTagName("body")[0];
	var voile = document.getElementById("voile");
	if(voile)
	{
		body.removeChild(voile);
		$("#navbar").show();
		$("#info_aide").remove();
		if( elt_aide_aff )
		{
			elt_aide_aff.popover('hide');
			elt_aide_aff.removeClass('aide-actif');
			elt_aide_actif = null;
			elt_aide_aff = null;
		}
	}
	else
	{
		voile = creer_element("div", "voile", false, body, false);
		voile.setAttribute("onclick", "clique_aide();");
		voile.setAttribute("onmousemove", "mouv_aide(event);");
		$("#navbar")[0].style = "display:none !important";
		$("#barre_menu .container").append("<div id='info_aide'><b>Description de l'interface : </b>Les élements qui s'affiche en surbrillance quand vous passez la souris dessus proposent une aide. Cliquez dessus pour affichez celle-ci. Cliquez ailleurs pour revenir au jeu.</div>");
		elts_aide = document.getElementsByClassName("aide");
	}
	$(body).toggleClass("aide-active");
	return false;
}

function mouv_aide(evt)
{
	if(elt_aide_aff)
		return;
	if( elt_aide_actif )
	{
		$(elt_aide_actif).removeClass('aide-actif');
		elt_aide_actif = null;
	}
	for(var i=0; i<elts_aide.length; i++)
	{
		var rect = elts_aide[i].getBoundingClientRect();
		if( evt.clientX >= rect.left && evt.clientX <= rect.right && evt.clientY >= rect.top && evt.clientY <= rect.bottom )
		{
			$(elts_aide[i]).addClass('aide-actif');
			elt_aide_actif = elts_aide[i];
			break;
		}
	}
}

function clique_aide()
{
	if(elt_aide_actif)
	{
		charger("aide.php?id="+elt_aide_actif.id);
		elt_aide_aff = $(elt_aide_actif);
		elt_aide_actif = null;
	}
	else
		bascule_aide();
}

function aide(id)
{
	var body = document.getElementsByTagName("body")[0];
	voile = creer_element("div", "voile", false, body, false);
	voile.setAttribute("onclick", "fin_aide();");
	$("#"+id).addClass('aide-actif');
	charger("aide.php?tuto=1&id="+id);
	elt_aide_aff = $("#"+id);
}

function fin_aide()
{
	$("#voile").remove();
	elt_aide_aff.removeClass('aide-actif');
	elt_aide_aff.popover('hide');
	elt_aide_aff = null;
}

// anciennes fonctions (tri à faire)

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

function chargerPopover(elt, id, pos, url, titre)
{
  var e = $('#' + elt);
  e.popover({html:true, placement:pos, title: titre, content:"<div id="+id+" class=\"infos_obj\">"+getWait()+"</div>", container:'body'});
  $.get(url, function(d)
  {
    e.data('bs.popover').options.content = d;
    e.popover('show');
  });
  document.getElementById(elt).onclick = "";
}

function getWait()
{
  var html = '<div class="wait">\n';
  html += '<div class="wait_circle wait_circle_1"></div>\n';
  html += '<div class="wait_circle wait_circle_2"></div>\n';
  html += '<div class="wait_circle wait_circle_3"></div>\n';
  html += '<div class="wait_circle wait_circle_4"></div>\n';
  html += '<div class="wait_circle wait_circle_5"></div>\n';
  html += '<div class="wait_circle wait_circle_6"></div>\n';
  html += '<div class="wait_circle wait_circle_7"></div>\n';
  html += '<div class="wait_circle wait_circle_8"></div>\n';
  html += '</div>\n';
  return html;
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
	/*$(".login_nom").focus();
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

$(function () {
        $("[rel='tooltip']").tooltip();
    });*/


		//$("#debug_log_button").hide();

		$(document).ajaxError(function(e, jqxhr, settings, exception)
		{
			aff_ico_sso();
			if (jqxhr.status == 403) // Sans doute un security_block
				aff_erreur('Accès interdit !', jqxhr.responseText, 'stop');
			else
				aff_erreur('Erreur : '+jqxhr.statusText+' (statut : '+jqxhr.status+')', jqxhr.responseText);
		});
		
		init_bbcode();
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

