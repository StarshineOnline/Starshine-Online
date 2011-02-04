<?php
if (file_exists('root.php'))
  include_once('root.php');
$max = 250;
$actuel = 84.94;
$ratio_don = floor(10 * ($actuel / $max));
if($ratio_don > 10) $ratio_don = 10;
if($ratio_don < 0) $ratio_don = 0;
$barre_don = './image/barre/pa'.$ratio_don.'.png';
?>
Avancement pour paiement hébergement : <img src="<?php echo $barre_don; ?>" title="<?php echo $actuel.'€ / 250 €'; ?>" /><br />
<h3>Faire un don via paypal</h3>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="FR3RDRQTGWJEE">
<input type="image" src="https://www.paypal.com/fr_FR/FR/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - la solution de paiement en ligne la plus simple et la plus sécurisée !">
<img alt="" border="0" src="https://www.paypal.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
</form>
<?php
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
?>