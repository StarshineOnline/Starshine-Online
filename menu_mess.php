<?php
/// @deprecated
if (file_exists('root.php'))
  include_once('root.php');

$messagerie = new messagerie($_SESSION['ID']);
$nb_mess = $messagerie->get_non_lu();
$nb_mess = $nb_mess['total'];
if ($nb_mess > 10) $nb_mess='10+';
?>
<a href="messagerie.php" onclick="return envoiInfo(this.href, 'information');montre('');"><img src="image/icone/messagerie<?php echo $nb_mess; ?>.png" onmouseover="document.getElementById('image_messagerie').src = 'image/icone/messagerie<?php echo $nb_mess; ?>_over.png';" onmouseout="document.getElementById('image_messagerie').src = 'image/icone/messagerie<?php echo $nb_mess; ?>.png';" id="image_messagerie" alt="Messagerie" title="Messagerie"/></a>