<?php
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');
$joueur = recupperso($_SESSION['ID']);
//Affichage du chat

$url = 'http://www.starshine-online.com/tigase';

?>
<script type="text/javascript">

</script>
Vos login et mot de passe pour accéder au tchat sont les mêmes que ceux du jeu.<br />
<a href="<?php echo $url ?>" target="_new">Chat (nouvelle fen&ecirc;tre)</a><br />
<a href="#" onClick="javascript:showChat('<?php echo $url ?>');" id="chatlink">Chat (Ingame)</a><br />
Vous pouvez aussi vous connecter avec un client jabber (pidgin, psy ou autres), en utilisant comme nom de serveur "jabber.starshine-online.com".<br />
<iframe id="chatframe" style="width: 800px; height: 470px; display: none;" src="" />
<br />
