<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
include_once(root.'inc/fp.php');
?>
	<script language="Javascript" type="text/javascript">
	</script>
		<a href="classement.php" onclick="return envoiInfo(this.href, 'popup_content');">Classement personnages</a><br />
		<a href="javascript:adresse_groupe('honneur', '', '')">Honneur</a> | <a href="javascript:adresse_groupe('exp', '', '')">Expérience</a> | <a href="javascript:adresse_groupe('star', '', '')">Stars</a> | <a href="javascript:adresse_groupe('frag', '', '')">PvP</a> | <a href="javascript:adresse_groupe('mort', '', '')">Suicide</a> | <a href="javascript:adresse_groupe('crime', '', '')">Crime</a> | <a href="javascript:adresse_groupe('survie', '', '')">Survie</a><br />
		<a href="javascript:adresse_groupe('craft', '', '')">Fabrication d'objets</a> | <a href="javascript:adresse_groupe('hp_max', '', '')">HP</a> | <a href="javascript:adresse_groupe('mp_max', '', '')">MP</a><br />
	<?php
	include_once(root.'classement_groupe_ajax.php');
	?>