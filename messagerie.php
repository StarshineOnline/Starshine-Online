<?php
if (file_exists('root.php'))
	include_once('root.php');

//Inclusion des fichiers indispensables
include_once(root.'inc/fp.php');


if(!array_key_exists('ID', $_SESSION) || empty($_SESSION['ID']))
{
	echo 'Vous êtes déconnecté, veuillez vous reconnecter.';
	exit();
}

$perso = joueur::get_perso();

$interf_princ = $G_interf->creer_jeu();
$action = array_key_exists('action', $_GET) ? $_GET['action'] : null;
$type = array_key_exists('type', $_GET) ? $_GET['type'] : null;
$ajax = array_key_exists('ajax', $_GET) ? $_GET['ajax'] : 0;
$sujet = false;
$page = null;

switch($action)
{
case 'suppr_msg':
	break;
case 'ecrire':
case 'lire':
	$sujet = $_GET['sujet'];
	$page = array_key_exists('page', $_GET) ? $_GET['page'] : null;
	break;
case 'nouveau':
	break;
case 'suppr_sujet':
	break;
case 'masquer_sujet':
	break;
}

if( $ajax == 2 )
{
	$interf_princ->add( $G_interf->creer_liste_messages($perso, $type) );
}
else
{
	$cadre = $interf_princ->set_droite( $G_interf->creer_droite('Messagerie') );
	$cadre->add( $G_interf->creer_messagerie($perso, $type, $sujet, $page) );
}
$interf_princ->maj_tooltips();



exit;




$joueur = new perso($_SESSION['ID']);
$messagerie = new messagerie($joueur->get_id(), $joueur->get_groupe());
$non_lu = $messagerie->get_non_lu();
/*if (!isset($_GET['id_thread']) AND !array_key_exists('action', $_GET))

 {
$titre_messagerie = ' de groupe';
}
elseif(array_key_exists('action', $_GET))
{
switch($_GET['action'])

{
case 'groupe' :
$titre_messagerie = ' de groupe';
break;
case 'perso' :
$titre_messagerie = ' personelle';
break;
case 'echange' :
$titre_messagerie = ' des échanges';
break;

}
}*/
?>
<script>
	$(function() {
		
		$( "#tabs" ).tabs({
											ajaxOptions: {
												error: function( xhr, status, index, anchor ) {
													$( anchor.hash ).html("Chargement impossible");
												}
											},
											fx: { opacity: 'toggle', duration: 300 }
						});
		$('#tabs').bind('tabsload', function(event, ui) { 
			$("#tabs .ui-tabs-nav > li").removeClass("ui-tabs-selected");
			//alert('ok'); 
		});
		$('#tabs').bind('tabsselect', function(event, ui) { 
			$("#tabs .ui-tabs-nav > li").removeClass("ui-tabs-selected");
			//alert('ok'); 
		});
		$('#tabs').bind('tabsshow', function(event, ui) { 
			$("#tabs .ui-tabs-nav > li").removeClass("ui-tabs-selected");
			//alert('ok'); 
		});
	});
</script>
<fieldset>

	<legend>
		Messagerie
	</legend>
	<div id="tabs">
		<?php
/*		if(array_key_exists('javascript', $_GET))
		{
			include_once(root.'inc/fp.php');
			$joueur = new perso($_SESSION['ID']);
			$messagerie = new messagerie($joueur->get_id(), $joueur->get_groupe());
			$non_lu = $messagerie->get_non_lu();
		}*/
		//echo "id;".$_SESSION['ID'];
		echo "<ul>";
		if ($joueur->get_groupe()!='0')
		{
			//if ($non_lu['groupe']>0){echo "<strong>";}
			echo "<li><a href='messagerie_ajax.php?action=groupe'>Groupe (".$non_lu['groupe'].")</a></li> ";
			//if ($non_lu['groupe']>0){echo "</strong>";}

		}
		//if ($non_lu['perso']>0){echo "<strong>";}
		echo "<li><a href='messagerie_ajax.php?action=perso'>Perso (".$non_lu['perso'].")</a></li>";
		//echo "<li><a href='messagerie.php?action=perso' onclick='envoiInfo(this.href, \"information\"); return false;'>Perso (".$non_lu['perso'].")</a></li>";
		//if ($non_lu['perso']>0){echo "</strong>";}

		//if ($non_lu['echange']>0){echo "<strong>";}
		echo "<li><a href='messagerie_ajax.php?action=echange'>Echanges (".$non_lu['echange'].")</a></li>";
		//if ($non_lu['echange']>0){echo "</strong>";}
		echo "</ul>";
		?>
	</div>
	<?php /*<div id="liste_message">
		<?php
		include_once(root.'messagerie_ajax.php');
		check_undead_players();
		?>
	</div> */ ?>
</fieldset>
