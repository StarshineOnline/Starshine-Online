<?php
include('inc/fp.php');
$joueur = recupperso($_SESSION['ID']);
//Affichage du chat
?>
<script type="text/javascript">

</script>
<a href="http://widget.mibbit.com/?settings=a0f3506cf53bb4bdc5476642cc300fb2&server=irc.quakenet.org&channel=%23starshine-online&noServerTab=false&autoConnect=true" target="_new">Chat (nouvelle fen&ecirc;tre)</a><br />
<a href="#" onClick="javascript:showChat();" id="chatlink">Chat (Ingame)</a><br />
<iframe id="chatframe" style="width: 800px; height: 470px; display: none;" src="" />
