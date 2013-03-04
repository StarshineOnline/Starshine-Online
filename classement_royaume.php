<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
include_once(root.'inc/fp.php');
?>
	<script language="Javascript" type="text/javascript">
	</script>
		<a href="classement.php" onclick="return envoiInfo(this.href, 'popup_content');">Classement des Personnages</a><br />
		<a href="classement_groupe.php" onclick="return envoiInfo(this.href, 'popup_content');">Classement des Groupes</a><br />
		<a href="javascript:adresse_royaume('victoire', '', '')">Victoires</a> | <a href="javascript:adresse_royaume('cases', '', '')">Cases</a> | <a href="javascript:adresse_royaume('level', '', '')">Niveaux</a> | <a href="javascript:adresse_royaume('survie', '', '')">Honneur</a> | <a href="javascript:adresse_groupe('frag', '', '')">PvP</a> | <a href="javascript:adresse_royaume('mort', '', '')">Suicide</a> | <a href="javascript:adresse_royaume('crime', '', '')">Crime</a><br />
	<?php
	include_once(root.'classement_royaume_ajax.php');
	?>