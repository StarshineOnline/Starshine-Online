<?php
//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include('haut_ajax.php');

$joueur = recupperso($_SESSION['ID']);
check_perso($joueur);

?>
	<div id="carte">
		<h2><a href="javascript:envoiInfo('actions.php', 'information');">Script de combat</a> - Choix de la cr�ation</h2>
		<div class="information_case">
		Vous avez deux modes pour cr�er votre scripts de combat.<br />
		<br />
		<ul>
			<li><a href="javascript:envoiInfo('action.php?mode=s', 'information');">Cr�er un script de combat en mode simplifi�</a> : <span class="small">Vous configurez vos rounds les uns apr�s les autres.</span></li>
			<li><a href="javascript:envoiInfo('action.php?mode=a', 'information');">Cr�er un script de combat en mode avanc�</a> : <span class="small">Vous configurez les actions en fonction de vos hp ou d'autres param�tres.</span></li>
		</ul>
		</div>
		<p><strong>Exemple du simplifi� : </strong>
		<ul>
		<li>round1 : coup puissant (4 r�serve de mana utilis�e)</li>
		<li>round2 : coup puissant (4 r�serve de mana utilis�e)</li>
		<li>round3 : coup puissant (4 r�serve de mana utilis�e)</li>
		<li>round4 : attaque (0 r�serve utilis�e)</li>
		<li>round5 : attaque (0 r�serve utilis�e)</li>
		</ul>
		Il est important de mettre les comp�tences qui font le plus mal en 1er pour tenter de tuer votre adversaire le plus vite possible. D'o� le fait que l'on trouve "coup puissant" dans les trois premiers rounds.</p>
		<p><a href="http://wiki.starshine-online.com/index.php?n=PmWiki.ScriptsDeCombat">Pour avoir plus d'information sur le script de combat</a></p>

	</div>