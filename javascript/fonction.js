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
function envoiInfo(page, div)
{
    sendData('null', page, div, 'GET')
}

function envoiInfoPost(page, div)
{
    sendData('null', page, div, 'POST')
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

function switch_map()
{
	for (i=0; i < 50; i++)
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