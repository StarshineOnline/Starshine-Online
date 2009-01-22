/**
* Permet d'envoyer des données en GET ou POST en utilisant les XmlHttpRequest
*/
function sendData(data, page, div, method)
{
    if(document.all && !window.opera)
    {
        //Internet Explorer
        var XhrObj = new ActiveXObject("Microsoft.XMLHTTP") ;
    }//fin if
    else
    {
        //Mozilla
        var XhrObj = new XMLHttpRequest();
    }//fin else
    
    //définition de l'endroit d'affichage:
    var content = document.getElementById(div);
    
    //si on envoie par la méthode GET:
    if(method == "GET")
    {
        if(data == 'null')
        {
            //Ouverture du fichier sélectionné:
            XhrObj.open("GET", page);
        }//fin if
        else
        {
            //Ouverture du fichier en methode GET
            XhrObj.open("GET", page+"?"+data);
        }//fin else
    }//fin if
    else if(method == "POST")
    {
        //Ouverture du fichier en methode POST
        XhrObj.open("POST", page);
    }//fin elseif

    //Ok pour la page cible
    XhrObj.onreadystatechange = function()
    {
        if (XhrObj.readyState == 4 && XhrObj.status == 200)
            content.innerHTML = XhrObj.responseText ;
    }    

    if(method == "GET")
    {
        XhrObj.send(null);
    }//fin if
    else if(method == "POST")
    {
        XhrObj.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
        XhrObj.send(data);
    }//fin elseif
}//fin fonction SendData

/**
* Permet de récupérer les données d'un fichier via les XmlHttpRequest:
*/
/*function envoiInfo(page, div)
{
    sendData('null', page, div, 'GET')
}*/

function envoiInfoPost(page,position)
{
	function Affiche(requete){$(position).innerHTML = requete.responseText; Hidechargement();}
	new Ajax.Request(page,{method:'get',onLoading:Loadchargement,onComplete:Affiche});
}

function envoiInfoPostData(page, div, data)
{
    sendData(data, page, div, 'POST')
}
/**
* Permet d'afficher une info bulle au passage d'une case
*/
function afficheInfo2(id, display, evt, xml)
{
	if(document.all) // IE
	{
		x = evt.x + document.body.scrollLeft;
		y = evt.y + document.body.scrollTop;
		dwidth = document.body.clientWidth;
		dheight = document.body.clientHeight;
	}
	else // FF
	{
		x = evt.pageX;
		y = evt.pageY;
		dwidth = document.width;
		dheight = document.height;
	}

	if(xml == 'centre')
	{
	}

	element = document.getElementById(id);

	x += 10;
	y += 10;

	element.style.top 	= y + "px";
	element.style.left 	= x + "px";

	element.style.display = display;
}

/**
* Permet d'afficher une info bulle au passage d'une case
*/
function afficheInfo(id, display, evt, xml)
{
	if(document.all) // IE
	{
		x = evt.x + document.body.scrollLeft;
		y = evt.y + document.body.scrollTop;
		dwidth = document.body.clientWidth;
		dheight = document.body.clientHeight;
	}
	else // FF
	{
		x = evt.pageX;
		y = evt.pageY;
		dwidth = document.width;
		dheight = document.height;
	}

	if(xml == 'centre')
	{
	}

	element = document.getElementById(id);

	//ehg = parseInt(element.style.top);
	ewidth = parseInt(element.style.width);
	eheight = parseInt(element.clientHeight);
	if((x + ewidth) > dwidth)
	{
		x = x - ewidth - 15;
	}
	else x += 10;
	if(((y + eheight) > dheight) && y > 100)
	{
		y = y - eheight - 15;
	}
	else y += 10;
	element.style.top 	= y + "px";
	element.style.left 	= x + "px";

	element.style.display = display;
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
function Loadchargement()
{
	$('loading').show();
}
function Hidechargement()
{
	$('loading').hide();	
}
function AfficheCarte(map)
{
	$('centre').innerHTML = map.responseText;
	$('loading').hide();
}

function deplacement(direction)
{

	new Ajax.Request('./deplacement.php',{method:'get',parameters:'deplacement='+direction,onLoading:Loadchargement,onComplete:AfficheCarte});
}

function affiche_info(id_case)
{	
	function Chargement(){$('loading_information').show();}
	function Affiche(requete){$('information').innerHTML = requete.responseText;$('loading_information').hide();}
	new Ajax.Request('./informationcase.php',{method:'get',parameters:'case='+id_case,onLoading:Chargement,onComplete:Affiche});
}

function refresh(page,position)
{	
	function Affiche(requete){$(position).innerHTML = requete.responseText;}
	new Ajax.Request(page,{method:'get',parameters:'javascript=oui',onComplete:Affiche});
}

function envoiInfo(page,position)
{	
	function Affiche(requete){$(position).innerHTML = requete.responseText; Hidechargement(); nd();}
	new Ajax.Request(page,{method:'get',onLoading:Loadchargement,onComplete:Affiche});
	return false;
}

function envoiInfoJS(page, position)
{
	function Affiche(requete){$(position).innerHTML = requete.responseText.evalJSON(); Hidechargement();}
	new Ajax.Request(page,{method:'get',onLoading:Loadchargement,onComplete:Affiche});
	return false;
}

function affichePopUp(input_name,input_get)
{
	function AffichePopup(resultat)
	{
		$('popup').show();
		$('popup_content').innerHTML = resultat.responseText;
		Hidechargement();
	}
	new Ajax.Request(input_name,{method:'get',parameters:input_get,onLoading:Loadchargement,onComplete:AffichePopup});
}
function fermePopUp()
{
	$('popup_content').innerHTML = '';
	$('popup').hide();
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