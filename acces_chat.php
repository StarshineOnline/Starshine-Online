<?php
include('inc/fp.php');
$joueur = new perso($_SESSION['ID']);
//Affichage du chat

$base = "http://widget.mibbit.com/?settings=a0f3506cf53bb4bdc5476642cc300fb2&server=irc.quakenet.org&noServerTab=true&autoConnect=true&nick=".$joueur->get_nom();

$url = $base.'&channel=%23starshine-online';
$url_roy = $base.'&channel=%23starshine-online-'.$joueur->get_race();

?>
<script type="text/javascript">

</script>
<a href="<?= $url ?>" target="_new">Chat (nouvelle fen&ecirc;tre)</a><br />
<a href="<?= $url_roy ?>" target="_new">Chat de royaume (nouvelle fen&ecirc;tre)</a><br />
<a href="#" onClick="javascript:showChat('<?= $url ?>');" id="chatlink">Chat (Ingame)</a><br />
<iframe id="chatframe" style="width: 800px; height: 470px; display: none;" src="" />
