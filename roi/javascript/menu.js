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
