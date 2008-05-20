<?php
//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include('haut_ajax.php');

$joueur = recupperso($_SESSION['ID']);
check_perso($joueur);

?>
	<div id="carte">
		<h2><a href="javascript:envoiInfo('actions.php', 'information');">Script de combat</a> - Choix de la création</h2>
		<div class="information_case">
		Vous avez deux modes pour créer votre scripts de combat.<br />
		<br />
		<ul>
			<li><a href="javascript:envoiInfo('action.php?mode=s', 'information');">Créer un script de combat en mode simplifié</a> : <span class="small">Vous configurez vos rounds les uns après les autres.</span></li>
			<li><a href="javascript:envoiInfo('action.php?mode=a', 'information');">Créer un script de combat en mode avancé</a> : <span class="small">Vous configurez les actions en fonction de vos hp ou d'autres paramètres.</span></li>
		</ul>
		</div>
		<p><strong>Exemple du simplifié : </strong>
		<ul>
		<li>round1 : coup puissant (4 réserve de mana utilisée)</li>
		<li>round2 : coup puissant (4 réserve de mana utilisée)</li>
		<li>round3 : coup puissant (4 réserve de mana utilisée)</li>
		<li>round4 : attaque (0 réserve utilisée)</li>
		<li>round5 : attaque (0 réserve utilisée)</li>
		</ul>
		Il est important de mettre les compétences qui font le plus mal en 1er pour tenter de tuer votre adversaire le plus vite possible. D'où le fait que l'on trouve "coup puissant" dans les trois premiers rounds.</p>
		<p><a href="http://wiki.starshine-online.com/index.php?n=PmWiki.ScriptsDeCombat">Pour avoir plus d'information sur le script de combat</a></p>

	</div>