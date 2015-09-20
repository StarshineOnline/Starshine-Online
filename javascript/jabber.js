
ssoApp.filter('html', function($sce)
{
  return function(txt)
	{
    return $sce.trustAsHtml(txt);
  };
});

ssoApp.directive("ssoJabberEnvoi", function()
{
		return function postLink(scope, elt, attr)
		{
			elt.find('.editeur-texte').wysiwyg();
		};
});

ssoApp.controller('ssoJabber', ['$scope', function($scope)
{
	$scope.jabber = {erreur:"", debug:[], statut:"", dbg_niv_min:"0", connecte:false, style:true, aff_debug:true};
	$scope.jabber.salons = sso_jabber.salons;
	for(var i=0; i<$scope.jabber.salons.length; i++)
	{
		$scope.jabber.salons[i].index = i;
		$scope.jabber.salons[i].statut = 0;
	}
	
	var BOSH_SERVICE = 'http://jabber.starshine-online.com:5280/http-bind';
	var connection = null;

	// filtre des logs de debugs
  $scope.dbgSupeq = function()
	{
	  return function( dbg, niv_min )
		{
			return dbg.niveau >= niv_min;
	  };
  };
  
  $scope.entrer_salon = function(i)
  {
  	$scope.jabber.salons[i].objet.entrer();
	}
  
  $scope.envoi_salon = function(i)
  {
  	$scope.jabber.salons[i].objet.envoi();
	}
  
  $scope.quitter_salon = function(i)
  {
  	$scope.jabber.salons[i].objet.quitter();
	}
  
  function parse_msg(stanza, elt)
  {
  	var from = stanza.getAttribute("from");
  	var ind = from.indexOf("/");
  	if( ind >= 0 )
  	{
  		var msg = {auteur:from.substr(ind+1), classe:"msg_perso"};
		}
		else
  		var msg = {auteur:"", classe:"msg_serveur"};
  	var body = stanza.getElementsByTagName("body");
  	if( body.length == 0 )
			return null
  	if( $scope.jabber.style && body.length > 1 )
  	{
  		msg.texte = body[1].innerHTML;
		}
  	else
  		msg.texte = body[0].innerHTML;
  	var delay = stanza.getElementsByTagName("delay");
  	if( delay.length > 0 )
  		var date = new Date(delay[0].getAttribute("stamp"));
  	else
  		var date = new Date();
  	msg.date = date.toLocaleString();
  	if( elt && elt[0] )
  	{
  		window.setTimeout(function()
			{
				elt[0].scrollTop = elt[0].scrollHeight;
			}, 100);
		}
  	return msg;
	}
	
	function parse_presence(stanza)
	{
  	var from = stanza.getAttribute("from");
  	var ind = from.indexOf("/");
  	if( ind >= 0 )
  	{
  		var part = {nom:from.substr(ind+1), icone:"", type:stanza.getAttribute("type")};
		}
		else
		{
			Strophe.log("Participant inconnu : "+from, Strophe.LogLevel.WARN);
			return null;
		}
		var items = stanza.getElementsByTagName("item");
		if( items.length )
		{
			part.affiliation = items[0].getAttribute("affiliation");
			part.role = items[0].getAttribute("role");
			switch( part.affiliation )
			{
			case 'owner':
				part.ordre = 1;
				break;
			case 'admin':
				part.ordre = 2;
				break;
			case 'member':
				part.ordre = 3;
				break;
			default:
				part.ordre = 2;
			}
		}
		else
		{
			part.affiliation = "member";
			part.role = "participant";
			part.ordre = 3;
		}
		if( part.nom == sso_jabber.nom )
			part.ordre = 0;
		return part;
	}
  
  // objets gérant les salons
  function salon(i)
  {
  	this.index = i;
		this.room = $scope.jabber.salons[i].id + "@" + sso_jabber.domaine_salons;
		this.id_editeur = '#jabber_envoi_'+$scope.jabber.salons[i].id;
		$scope.jabber.salons[i].messages = [];
		$scope.jabber.salons[i].participants = {};
		
		var id_salon = '#jabber_salon_'+$scope.jabber.salons[i].id;
		var connecte = false;
		function handle_message(stanza, room)
		{
	    $scope.$apply(function()
			{
				var msg = parse_msg(stanza, $(id_salon).find(".jabber_discussion"));
				if( msg )
					$scope.jabber.salons[i].messages.push(msg);
		    if( !connecte )
		    {
		    	connecte = true;
					$scope.jabber.salons[i].statut = 2;
				}
			});
			return true;
		}
		
		function handle_pres(stanza, room)
		{
	    $scope.$apply(function()
			{
				var part = parse_presence(stanza);
				if( part )
				{
					if( part.type == "unavailable" )
						delete  $scope.jabber.salons[i].participants[part.nom];
					else
						$scope.jabber.salons[i].participants[part.nom] = part;
				}
			});
			return true;
		}
		
		function handle_roster(stanza, room)
		{
	    /*$scope.$apply(function()
			{
		    sso_log("roster : "+stanza.tagName);
			});*/
			return true;
		}
		
		this.entrer = function()
		{
			var room = this.room;
			setTimeout(function()
			{
				var ext = creer_element("priority", null, null, null, "10");
				connection.muc.join(room, sso_jabber.nom, handle_message, handle_pres, handle_roster, null, null, null);
				//connection.muc.queryOccupants(room, null, null);
			}, 0);
			$scope.jabber.salons[this.index].statut = 1;
		}
		this.quitter = function()
		{
			if($scope.jabber.salons[this.index].statut)
				connection.muc.leave(this.room, sso_jabber.nom);
			$scope.jabber.salons[this.index].statut = 0;
		}
		this.envoi = function()
		{
			var elt = $(this.id_editeur);
			var xhtml = elt.html();
			xhtml = xhtml.replace("<b>", "<strong>");
	    xhtml = xhtml.replace("</b>", "</strong>");
	    xhtml = xhtml.replace("<i>", "<em>");
	    xhtml = xhtml.replace("</i>", "</em>");
	    xhtml = xhtml.replace("<u>", '<span style="text-decoration: underline;">');
	    xhtml = xhtml.replace("</u>", "</span>");
	    xhtml = xhtml.replace("<strike>", '<span style="text-decoration: line-through;">');
	    xhtml = xhtml.replace("</strike>", "</span>");
			connection.muc.groupchat(this.room, elt.text(), xhtml, null);
			elt.html("");
		}
		
		if( $scope.jabber.salons[i].auto )
		{
			this.entrer();
		}
	}
  
  function handle_connect(statut)
  {
  	$scope.$apply(function()
		{
	  	switch(statut)
	  	{
	  	case Strophe.Status.ERROR:
	  		$scope.jabber.statut = 'erreur';
	  		break;
	  	case Strophe.Status.CONNECTING:
	  		$scope.jabber.statut = 'connexion en cours';
	  		break;
	  	case Strophe.Status.CONNFAIL:
	  		$scope.jabber.statut = 'connection ratée';
	  		break;
	  	case Strophe.Status.AUTHENTICATING:
	  		$scope.jabber.statut = 'autentification en cours';
	  		break;
	  	case Strophe.Status.AUTHFAIL:
	  		$scope.jabber.statut = 'autentification ratée';
	  		break;
	  	case Strophe.Status.DISCONNECTING:
	  		$scope.jabber.statut = 'déconexion en cours';
	  		break;
	  	case Strophe.Status.DISCONNECTED:
	  		$scope.jabber.statut = 'déconnecté';
				$scope.jabber.connecte = false;
	  		break;
	  	case Strophe.Status.CONNECTED:
	  		$scope.jabber.statut = 'connecté';
				sso_log("Connecté", true);
				connection.send($pres());
				//connection.muc.init();
				for(var i=0; i<$scope.jabber.salons.length; i++)
				{
					$scope.jabber.salons[i].objet = new salon(i);
				}
				$scope.jabber.connecte = true;
	  		break;
	  	case Strophe.Status.ATTACHED:
	  		$scope.jabber.statut = 'attaché';
	  		break;
	  	case Strophe.Status.REDIRECT:
	  		$scope.jabber.statut = 'redirigé';
	  		break;
			}
	  });
	}
	
	function raw_input(data)
	{
    $scope.$apply(function()
		{
	    $scope.jabber.debug.push( {niveau:Strophe.LogLevel.DEBUG, message:data, classe:"input"} );
		});
	}
	
	function raw_output(data)
	{
    $scope.$apply(function()
		{
	    $scope.jabber.debug.push( {niveau:Strophe.LogLevel.DEBUG, message:data, classe:"output"} );
		});
	}
	function dbg_log(level, msg)
	{
  	window.setTimeout(function()
		{
	    $scope.$apply(function()
			{
				switch(level)
				{
				case Strophe.LogLevel.DEBUG:
					var classe = "text-muted";
		    	break;
				case Strophe.LogLevel.INFO:
					var classe = "text-info";
		    	break;
				case Strophe.LogLevel.WARN:
					var classe = "text-warning";
		    	break;
				case Strophe.LogLevel.ERROR:
					var classe = "text-danger";
					$scope.jabber.erreur = msg;
		    	break;
				case Strophe.LogLevel.FATAL:
					var classe = "text-danger fatal";
					$scope.jabber.erreur = msg;
		    	break;
				}
		    $scope.jabber.debug.push( {niveau:level, message:msg, classe:classe} );
			});
		}, 1);
	}
	function sso_log(msg, succes)
	{
		if( /*succes == undefined ||*/ !succes )
			var classe = 'text-primary';
		else
			var classe = 'text-success';
	  $scope.jabber.debug.push( {niveau:Strophe.LogLevel.INFO, message:msg, classe:classe} );
	}
	
	// débogage
	// certains hagndle sont appelés dans ce contexte de la connexion et d'autres non, ce qui pose problème avec $apply.
	// => on fait tout dans des contextes différents et on appèle $apply manuellement.
	$().ready(function()
	{
		try
		{
			Strophe.log = dbg_log;
			// Connexion
	    connection = new Strophe.Connection(BOSH_SERVICE);
	    connection.rawInput = raw_input;
	    connection.rawOutput = raw_output;
	    var jid = sso_jabber.nom + "@" + sso_jabber.serveur + "/" + sso_jabber.ressource;
	    connection.connect(jid, sso_jabber.mdp, handle_connect);
		}
		catch(e)
		{
			$scope.$apply(function()
			{
				$scope.jabber.erreur = 'Erreur connexion : ' + e.toString();
			});
		}
	});
	$( window ).unload(function()
	{
		for(var i=0; i<$scope.jabber.salons.length; i++)
		{
			$scope.jabber.salons[i].objet.quitter();
		}
	  connection.disconnect();
	});
}]);

