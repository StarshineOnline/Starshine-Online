	<div id="deplacement">
		<div id="carteville">
		<?php
		require_once('menu_carteville.php');
		?>
		</div>

<?php
	echo '
		<a href="javascript:envoiInfo(\'livre.php\', \'information\');montre(\'\');"><img src="image/icone/livre.png" onmouseover="document.getElementById(\'image_livre\').src = \'image/icone/livre_over.png\';" onmouseout="document.getElementById(\'image_livre\').src = \'image/icone/livre.png\';" id="image_livre" alt="Livre" title="Livre" /></a>
		<a href="javascript:envoiInfo(\'quete.php\', \'information\');montre(\'\');"><img src="image/quete_icone.png" onmouseover="document.getElementById(\'image_quete\').src = \'image/quete_icone_over.png\';" onmouseout="document.getElementById(\'image_quete\').src = \'image/quete_icone.png\';" id="image_quete" alt="Qu�tes" title="Qu�tes" /></a>
		<a href="javascript:envoiInfo(\'journal.php\', \'information\');montre(\'\');"><img src="image/icone/journal-des-actions.png" onmouseover="document.getElementById(\'image_journal\').src = \'image/icone/journal-des-actions_over.png\';" onmouseout="document.getElementById(\'image_journal\').src = \'image/icone/journal-des-actions.png\';" id="image_journal" alt="Journal des actions" title="Journal des actions" /></a>
		<a href="javascript:envoiInfo(\'actions.php\', \'information\');montre(\'\');"><img src="image/icone/script_combat.png" onmouseover="document.getElementById(\'image_script\').src = \'image/icone/script_combat_over.png\';" onmouseout="document.getElementById(\'image_script\').src = \'image/icone/script_combat.png\';" id="image_script" alt="Script de combat" title="Script de combat" /></a>
		<a href="javascript:envoiInfo(\'inventaire.php\', \'information\');montre(\'\');"><img src="image/icone/inventaire.png" onmouseover="document.getElementById(\'image_inventaire\').src = \'image/icone/inventaire_over.png\';" onmouseout="document.getElementById(\'image_inventaire\').src = \'image/icone/inventaire.png\';" id="image_inventaire" alt="Inventaire" title="Inventaire" /></a>
		<a href="javascript:envoiInfo(\'liste_echange.php\', \'information\');montre(\'\');"><img src="image/icone/liste_echange.png" onmouseover="document.getElementById(\'image_echange\').src = \'image/icone/liste_echange_over.png\';" onmouseout="document.getElementById(\'image_echange\').src = \'image/icone/liste_echange.png\';" id="image_echange" alt="Liste des �changes" title="Liste des �changes" /></a>
';

?>
<br />		
	</div>	
			
		
