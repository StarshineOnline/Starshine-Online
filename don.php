<?php
if (file_exists('root.php'))
  include_once('root.php');
if( isset($_GET['action']) )
  $action = $_GET['action'];
else
  $action = false;
/*$max = 250;
$actuel = 184.87;
$ratio_don = floor(10 * ($actuel / $max));
if($ratio_don > 10) $ratio_don = 10;
if($ratio_don < 0) $ratio_don = 0;
$barre_don = './image/barre/pa'.$ratio_don.'.png';*/
/*Avancement pour paiement hébergement : <img src="<?php echo $barre_don; ? >" title="<?php echo $actuel.'€ / 250 €'; ? >" /><br />
<h3>Faire un don via paypal</h3>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="FR3RDRQTGWJEE">
<input type="image" src="https://www.paypal.com/fr_FR/FR/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - la solution de paiement en ligne la plus simple et la plus sécurisée !">
<img alt="" border="0" src="https://www.paypal.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
</form>*/
/*
<h3>Faire un don via allopass (SMS)</h3>
<!-- Begin Allopass Checkout-Button Code -->
<script type="text/javascript" src="https://payment.allopass.com/buy/checkout.apu?ids=244083&idd=960025&lang=fr"></script>
<noscript>
 <a href="https://payment.allopass.com/buy/buy.apu?ids=244083&idd=960025" style="border:0">
  <img src="https://payment.allopass.com/static/buy/button/fr/162x56.png" style="border:0" alt="Buy now!" />
 </a>
</noscript>
<!-- End Allopass Checkout-Button Code -->
 *
 */
if( $action == "valid" )
{
  // Identifiants de votre document
  $docId      = 118738;
  $siteId      = 399811;

  // PHP5 avec register_long_arrays désactivé?
  if (!isset($HTTP_GET_VARS)) {
      $HTTP_SESSION_VARS    = $_SESSION;
      $HTTP_SERVER_VARS     = $_SERVER;
      $HTTP_GET_VARS        = $_GET;
  }

  // Construction de la requête pour vérifier le code

  $query      = 'http://payment.rentabiliweb.com/checkcode.php?';
  $query     .= 'docId='.$docId;
  $query     .= '&siteId='.$siteId;
  $query     .= '&code='.$HTTP_GET_VARS['code'];
  $query     .= "&REMOTE_ADDR=".$HTTP_SERVER_VARS['REMOTE_ADDR'];
  $result     = @file($query);


  if(trim($result[0]) !== "OK") {
      header('Location: http://www.starshine-online.com/don.php?action=erreur');
      exit();
  }


  // Accès à votre page protégée

  echo 'Merci pour le don !';
}
else if( $action == "erreur" )
{
  echo 'Il y a visiblement une erreur, la question qui se pose est : mais comment avez vous fait pour arriver là ?';
}
else
{
?>
<h3>Faire un don par SMS</h3>
<table border="0"><tr><td>
<table border="0" cellpadding="0" cellspacing="0" style="border:5px solid #E5E5E5; margin: 5px auto;"><tr><td>
	<table cellpadding="0" cellspacing="0" style="width: 436px;  border: solid 1px #AAAAAA;">
    <tr>
        <td colspan="2">
            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #FFFFFF;">
                <tr>
                    <td>
                        <img src="http://payment.rentabiliweb.com/data/i/component/logo-form.gif" width="173" height="20" alt="Paiement sécurisé par Rentabiliweb" style="padding: 1px 0 0 5px"/>
                    </td>
                    <td>
                        <div style="text-align: right; padding: 2px; font-family: Arial, Helvetica, sans-serif; min-height:30px; ">
                            <span style="color: #3b5998; font-weight:bold; font-size: 12px;">Solutions de paiements sécurisés</span>
                            <br/>
                            <span style="color: #5c5c5c; font-size: 11px; font-style: italic;">Secure payment solution</span>
													</div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="border-top: 1px solid #AAAAAA; border-bottom: 1px solid #AAAAAA;background-color: #F7F7F7;">
            <div style="text-align: center; padding: 2px; font-family: Arial, Helvetica, sans-serif; min-height:30px;">
                <span style="color: #3b5998; font-weight:bold; font-size: 12px;">Choisissez votre pays et votre moyen de paiement pour obtenir votre code</span>
                <br/>
                <span style="color: #5c5c5c; font-size: 11px; font-style: italic;">Please choose your country and your kind of payment to obtain a code</span>
							</div>
        </td>
    </tr>
    <tr height="250">
        <td width="280"  style="background-color: #FFFFFF;">
            <iframe name="rweb_display_frame" width="280" height="250" frameborder="0" marginheight="0" marginwidth="0" scrolling="no" src="http://payment.rentabiliweb.com/form/acte/frame_display.php?docId=118738&siteId=399811&cnIso=geoip&lang=fr&skin=default">
            </iframe>
        </td>
        <td width="156" style="border-left: 1px solid #AAAAAA; background-color: #FFFFFF;">
            <iframe name="rweb_flags_frame" width="156" height="250" frameborder="0" marginheight="0" marginwidth="0" scrolling="no" src="http://payment.rentabiliweb.com/form/acte/frame_flags.php?docId=118738&siteId=399811&lang=fr&skin=default">
            </iframe>
        </td>
    </tr>
    <!--<tr>
        <td colspan="2" style="border-top: 1px solid #AAAAAA; background-color: #F7F7F7;">
            <form id="rweb_tickets_118738" method="get" action="http://payment.rentabiliweb.com/access.php" style="margin: 0px; padding: 0px;" >
                <table width="400" cellpadding="0" cellspacing="0" style=" margin: 2px auto;">
                	<tr>
                		<td style="text-align: center"><label for="code_0" style=" font-family:Arial, Helvetica, sans-serif;font-size: 12px; font-weight:bold; color:#3b5998; padding: 2px; margin: 0px;">
                        Saisissez votre code d'accès et validez :
										<br/>
										<span style="font-size: 11px; font-style: italic;color:#5c5c5c;">Please enter your access code :</span>
                    </label></td>
                	</tr>
                	<tr>
                		<td style="text-align: center">
                		<input name="code[0]" type="text" id="code_0" size="10" style="border: solid 1px #3b5998; padding: 2px; font-weight: bold; color:#3b5998; text-align: center;"/>
										<input type="hidden" name="docId" value="118738" /><input type="button"  alt="Ok" onclick="getElementById('rweb_sub_118738').disabled=true;document.getElementById('rweb_tickets_118738').submit();" id="rweb_sub_118738"  style="width: 40px; height:20px; vertical-align:middle; margin-left: 5px; border: none; background:url(http://payment.rentabiliweb.com/data/i/component/button_okdefault.gif);"/></td>
                	</tr>
                </table>
            </form>
            <div style="text-align: center; padding: 2px; font-family: Arial, Helvetica, sans-serif; clear: both;">
                <span style="font-weight:bold; font-size: 10px; color: #3b5998;">Votre navigateur doit accepter les cookies</span>
                <br/>
                <span style="font-style: italic; font-size: 10px; color: #5c5c5c;">Please check that your browser accept the cookies</span>
						</div>
			     <div style="text-align: center; padding: 2px; font-family: Arial, Helvetica, sans-serif;">
                <a href="javascript:;"  onclick="javascript:window.open('http://payment.rentabiliweb.com/support/?docId=118738&siteId=399811&lang=fr','rentabiliweb_help','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=1,copyhistory=0,menuBar=0,width=995,height=630');" style="color: #3b5998; font-weight:bold; font-size: 12px; text-decoration: none;">Support technique</a><span style="color: #AAAAAA;"> / </span><a href="javascript:;"  onclick="javascript:window.open('http://payment.rentabiliweb.com/support/?docId=118738&siteId=399811&lang=en','rentabiliweb_help','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=1,copyhistory=0,menuBar=0,width=995,height=630');" style="color: #5c5c5c; font-weight:normal; font-size: 12px; text-decoration: none;">Technical support</a>
           </div>
        </td>
    </tr>-->
	</table>
</td></tr></table>
</td>
<td>Comme on et gentils, on a décidé de vous aider à finir vos fortaits téléphoniques, c'est pas merveilleux ?<br/>
Comment ça non ?<br/>
<br/>
Sur chaque SMS envoyé, nous récupérons 20cts qui serviront à payer le serveur et le nom de domaine.<br/>
Vérifiez tout de même que le surcout du SMS est bien compris décompté de votre fortait (et qu'il vous reste assez dessus), le but est d'utiliser l'argent que vous avez déjà payé.<br/>
Lorsque que vous envoyez un SMS vous allez recevoir un code en retour, mais comme il n'y a pas de contenu payant sur Starshine-online, ce code ne vous sert à rien. Vous pouvez juste le garder en collector.</td>
</tr></table>
<?PHP } ?>