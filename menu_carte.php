	<div id="deplacement">
		<div id="carteville">
		<?php
		require_once('menu_carteville.php');
		?>
		</div>

<?php
	echo '
		<a href="livre.php" onclick="return envoiInfo(this.href, \'information\');montre(\'\');"><img src="image/icone/livre.png" onmouseover="document.getElementById(\'image_livre\').src = \'image/icone/livre_over.png\';" onmouseout="document.getElementById(\'image_livre\').src = \'image/icone/livre.png\';" id="image_livre" alt="Livre" title="Livre" /></a>
		<a href="quete.php" onclick="return envoiInfo(this.href, \'information\');montre(\'\');"><img src="image/icone/quete_icone.png" onmouseover="document.getElementById(\'image_quete\').src = \'image/icone/quete_icone_over.png\';" onmouseout="document.getElementById(\'image_quete\').src = \'image/icone/quete_icone.png\';" id="image_quete" alt="Quêtes" title="Quêtes" /></a>
		<a href="journal.php" onclick="return envoiInfo(this.href, \'information\');montre(\'\');"><img src="image/icone/journal-des-actions.png" onmouseover="document.getElementById(\'image_journal\').src = \'image/icone/journal-des-actions_over.png\';" onmouseout="document.getElementById(\'image_journal\').src = \'image/icone/journal-des-actions.png\';" id="image_journal" alt="Journal des actions" title="Journal des actions" /></a>
		<a href="actions.php" onclick="return envoiInfo(this.href, \'information\');montre(\'\');"><img src="image/icone/script_combat.png" onmouseover="document.getElementById(\'image_script\').src = \'image/icone/script_combat_over.png\';" onmouseout="document.getElementById(\'image_script\').src = \'image/icone/script_combat.png\';" id="image_script" alt="Script de combat" title="Script de combat" /></a>
		<a href="inventaire.php" onclick="return envoiInfo(this.href, \'information\');montre(\'\');"><img src="image/icone/inventaire.png" onmouseover="document.getElementById(\'image_inventaire\').src = \'image/icone/inventaire_over.png\';" onmouseout="document.getElementById(\'image_inventaire\').src = \'image/icone/inventaire.png\';" id="image_inventaire" alt="Inventaire" title="Inventaire" /></a>
		<a href="liste_echange.php" onclick="return envoiInfo(this.href, \'information\');montre(\'\');"><img src="image/icone/liste_echange.png" onmouseover="document.getElementById(\'image_echange\').src = \'image/icone/liste_echange_over.png\';" onmouseout="document.getElementById(\'image_echange\').src = \'image/icone/liste_echange.png\';" id="image_echange" alt="Liste des échanges" title="Liste des échanges" /></a>
';

?>
<br />		
	</div>	
			
		
