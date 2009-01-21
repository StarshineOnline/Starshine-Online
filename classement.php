<?php
include('inc/fp.php');
?>
	<a href="classement_groupe.php" onclick="return envoiInfo(this.href, 'popup_content');">Classement des groupes</a><br />
	<a href="javascript:adresse('honneur', '', '')">Honneur</a> | <a href="javascript:adresse('exp', '', '')">Expérience</a> | <a href="javascript:adresse('star', '', '')">Stars</a> | <a href="javascript:adresse('frag', '', '')">PvP</a> | <a href="javascript:adresse('mort', '', '')">Suicide</a> | <a href="javascript:adresse('crime', '', '')">Crime</a> | <a href="javascript:adresse('survie', '', '')">Survie</a><br />
	<a href="javascript:adresse('melee', '', '')">Mélée</a> | <a href="javascript:adresse('esquive', '', '')">Esquive</a> | <a href="javascript:adresse('blocage', '', '')">Blocage</a> | <a href="javascript:adresse('distance', '', '')">Distance</a> | <a href="javascript:adresse('incantation', '', '')">Incantation</a> <span class="xsmall">(<a href="javascript:adresse('sort_element', '', '')">Magie élémentaire</a> | <a href="javascript:adresse('sort_mort', '', '')">Nécromancie</a> | <a href="javascript:adresse('sort_vie', '', '')">Magie de la vie</a>)</span> | <a href="javascript:adresse('craft', '', '')">Fabrication d'objets</a><br />
	<a href="javascript:adresse('', '', 'tous')">Tous</a><?php if(array_key_exists('ID', $_SESSION)) {?> | <a href="javascript:adresse('', '', 'race')">Votre race</a> | <a href="javascript:adresse('', 'moi', '')">Moi</a><?php } ?><br />
	<?php
	include('classement_ajax.php');
	?>
		</div>