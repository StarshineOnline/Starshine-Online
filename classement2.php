<?php
include('haut.php');
include('haut_site.php');
if ($maintenance)
{
	echo 'Starshine-online est actuellement en refonte complète, l\'expérience acquérie grâce à l\'alpha m\'a permis de voir les gros problèmes qui pourraient se poser.<br />
	Je vais donc travailler sur la béta.<br />';
}
else
{
	include('menu.php');
	?>
<div id="contenu">
	<div id="centre2">
	<script language="Javascript" type="text/javascript">
	function adresse(tri, i, race)
	{
		if(tri == '') tri = document.getElementById('tri').value;
		if(i == '') i = document.getElementById('i').value;
		if(race == '') race = document.getElementById('race').value;
		envoiInfo('classement_ajax.php?tri=' + tri + '&i=' + i + '&race=' + race + '&javascript=true', 'table_classement');
	}
	</script>
		<a href="javascript:adresse('exp', '', '')">Expérience</a> | <a href="javascript:adresse('honneur', '', '')">Honneur</a> | <a href="javascript:adresse('star', '', '')">Stars</a> | <a href="javascript:adresse('melee', '', '')">Mélée</a> | <a href="javascript:adresse('esquive', '', '')">Esquive</a> | <a href="javascript:adresse('distance', '', '')">Distance</a> | <a href="javascript:adresse('incantation', '', '')">Incantation</a> | <a href="javascript:adresse('frag', '', '')">PvP</a> | <a href="javascript:adresse('mort', '', '')">Suicide</a><br />
		<a href="javascript:adresse('', '', 'tous')">Tous</a><?php if(array_key_exists('ID', $_SESSION)) {?> | <a href="javascript:adresse('', '', 'race')">Votre race</a> | <a href="javascript:adresse('', 'moi', '')">Moi</a><?php } ?><br />
	<?php
	include('classement_ajax.php');
	?>
</div>

<?php
}
include('bas.php');

?>