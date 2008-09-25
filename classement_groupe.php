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
			<div class="titre">
				Classement des groupes
			</div>
	<script language="Javascript" type="text/javascript">
	function adresse(tri, i, race)
	{
		if(i == '') i = document.getElementById('i').value;
		if(tri == '') tri = document.getElementById('tri').value;
		else
		{
			if(i != 'moi') i = 0;
		}
		if(race == '') race = document.getElementById('race').value;
		envoiInfo('classement_groupe_ajax.php?tri=' + tri + '&javascript=true', 'table_classement');
	}
	</script>
		<a href="classement.php">Classement personnages</a><br />
		<a href="javascript:adresse('honneur', '', '')">Honneur</a> | <a href="javascript:adresse('exp', '', '')">Expérience</a> | <a href="javascript:adresse('star', '', '')">Stars</a> | <a href="javascript:adresse('frag', '', '')">PvP</a> | <a href="javascript:adresse('mort', '', '')">Suicide</a> | <a href="javascript:adresse('crime', '', '')">Crime</a> | <a href="javascript:adresse('survie', '', '')">Survie</a><br />
		<a href="javascript:adresse('craft', '', '')">Fabrication d'objets</a> | <a href="javascript:adresse('hp_max', '', '')">HP</a> | <a href="javascript:adresse('mp_max', '', '')">MP</a><br />
	<?php
	include('classement_groupe_ajax.php');
	?>
		</div>
	</div>

<?php
	include('menu_d.php');
}
?>
</div>
<?php
include('bas.php');

?>