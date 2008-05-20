<?php
$requete = "SELECT COUNT(id) FROM message WHERE id_dest = '".$_SESSION['ID']."' AND FIND_IN_SET('lu', type) = 0";
if($req = $db->query($requete)) $row = $db->read_row($req);
$nb_mess = $row[0];
if ($nb_mess > 10) $nb_mess='10+';
echo '<a href="javascript:envoiInfo(\'messagerie.php\', \'information\');montre(\'\');"><img src="image/icone/messagerie'.$nb_mess.'.png" onmouseover="document.getElementById(\'image_messagerie\').src = \'image/icone/messagerie'.$nb_mess.'_over.png\';" onmouseout="document.getElementById(\'image_messagerie\').src = \'image/icone/messagerie'.$nb_mess.'.png\';" id="image_messagerie" alt="Messagerie" title="Messagerie"/></a>';
	

?>
