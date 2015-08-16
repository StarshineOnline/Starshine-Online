var ssoApp = angular.module('ssoApp',[]);
 
ssoApp.controller('ssoJabber', ['$scope', function($scope)
{
	$scope.erreur = '';
	$scope.debug = [];
	$scope.statut = '';
	
	// 
  function handle_iq(iq)
	{
    dbg.log('IQ non implémenté : '+oIQ.xml().htmlEnc(), 0);
    connex.send(iq.errorReply(ERR_FEATURE_NOT_IMPLEMENTED));
  }

  function handle_message(oJSJaCPacket)
	{
    /*var html = '';
    html += '<div class="msg"><b>Received Message from ' + oJSJaCPacket.getFromJID() + ':</b><br/>';
    html += oJSJaCPacket.getBody().htmlEnc() + '</div>';
    document.getElementById('iResp').innerHTML += html;
    document.getElementById('iResp').lastChild.scrollIntoView();*/
  }

  function handle_presence(oJSJaCPacket)
	{
    /*var html = '<div class="msg">';
    if (!oJSJaCPacket.getType() && !oJSJaCPacket.getShow())
        html += '<b>' + oJSJaCPacket.getFromJID() + ' has become available.</b>';
    else {
        html += '<b>' + oJSJaCPacket.getFromJID() + ' has set his presence to ';
        if (oJSJaCPacket.getType())
            html += oJSJaCPacket.getType() + '.</b>';
        else
            html += oJSJaCPacket.getShow() + '.</b>';
        if (oJSJaCPacket.getStatus())
            html += ' (' + oJSJaCPacket.getStatus().htmlEnc() + ')';
    }
    html += '</div>';

    document.getElementById('iResp').innerHTML += html;
    document.getElementById('iResp').lastChild.scrollIntoView();*/
  }

  function handle_error(e)
	{
    $scope.erreur = "Erreur:<br />" + ("Code: " + e.getAttribute('code') + "\nType: " + e.getAttribute('type') + "\nCondition: " + e.firstChild.nodeName).htmlEnc();
    if (connex.connected())
        connex.disconnect();
  }

  function handle_status_changed(status)
	{
    dbg.log("Statut changé: " + status, 1);
    $scope.statut = status;
  }

  function handle_connected()
	{
    /*document.getElementById('login_pane').style.display = 'none';
    document.getElementById('sendmsg_pane').style.display = '';
    document.getElementById('err').innerHTML = '';*/
		//$scope.statut = 'connecté';
    dbg.log("Connecté", 1);
    connex.send(new JSJaCPresence());
  }

  function handle_disconnected()
	{
    /*document.getElementById('login_pane').style.display = '';
    document.getElementById('sendmsg_pane').style.display = 'none';*/
		//$scope.statut = 'déconnecté';
    dbg.log("Déconnecté", 1);
  }
	
	// débogage
	var dbg = new JSJaCConsoleLogger(4);
	dbg.log = function(message, level)
	{
    $scope.debug.push( {niveau:level, message:message} );
	};
	try
	{
		// Connexion
		var connex = new JSJaCHttpBindingConnection({ httpbase : '/http-bind/', oDbg : dbg });
	  connex.registerHandler('message', handle_message);
	  connex.registerHandler('presence', handle_presence);
	  connex.registerHandler('iq', handle_iq);
	  connex.registerHandler('onconnect', handle_connected);
	  connex.registerHandler('onerror', handle_error);
	  connex.registerHandler('status_changed', handle_status_changed);
	  connex.registerHandler('ondisconnect', handle_disconnected);
		// arguments
	  args = new Object();
	  args.domain = sso_jabber.serveur;
	  args.username = sso_jabber.nom;
	  args.resource = sso_jabber.ressource;
	  args.pass = sso_jabber.mdp;
	  args.register = false;
	  connex.connect(args);
	}
	catch(e)
	{
		$scope.erreur = 'Erreur connexion : ' + e.toString();;
	}
	//$scope.statut = 'connexion en cours';
}]);

