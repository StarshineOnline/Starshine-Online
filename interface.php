<?php // -*- mode: php; tab-width: 2 -*-
if (file_exists('root.php'))
  include_once('root.php');

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
$interface_v2 = true;
include_once(root.'haut.php');
?>
<script type="text/javascript">
window.onload = function()
{
	<?php if($_COOKIE['dernier_affichage_popup'] < (time() - 3600)) echo 'affichePopUp(\'message_accueil.php\');'; ?>
}
</script>
<?php
if(array_key_exists('ID', $_SESSION) && !empty($_SESSION['ID']))
	$joueur = new perso($_SESSION['ID']);
else
{
	echo 'Vous êtes déconnecté, veuillez vous reconnecter.';
	exit();
}

//Si c'est pour entrer dans un donjon
if(array_key_exists('donjon_id', $_GET))
{
	$id = $_GET['donjon_id'];

	$requete = "SELECT x, y, x_donjon, y_donjon FROM donjon WHERE id = ".$id;
	
  if (isset($G_disallow_donjon) && $G_disallow_donjon == true) {
    $disallowed = true;
    if (isset($G_allow_donjon_for) && is_array($G_allow_donjon_for))
      foreach ($G_allow_donjon_for as $allowed)
        if ($allowed == $joueur->get_nom())
          $disallowed = false;
    if ($disallowed)
      security_block(URL_MANIPULATION);
  }
	$req = $db->query($requete);
	
	$row = $db->read_assoc($req);

	// Verification que les conditions sont reunies
	$unlock = verif_tp_donjon($row, $joueur);
	if ($unlock == false)
		security_block(URL_MANIPULATION);

	//sortie
	if(array_key_exists('type', $_GET))
	{
		if($joueur->get_x() == $row['x_donjon'] AND $joueur->get_y() == $row['y_donjon'])
		{
			$joueur->set_x($row['x']);
			$joueur->set_y($row['y']);
			$joueur->sauver();
		}
	}
	//Entrée
	else
	{
		if($joueur->get_x() == $row['x'] AND $joueur->get_y() == $row['y'])
		{
			$joueur->set_x($row['x_donjon']);
			$joueur->set_y($row['y_donjon']);
			$joueur->sauver();
		}
	}
}

//Vérifie si le perso est mort
verif_mort($joueur, 1);

$joueur->check_perso();

$_SESSION['position'] = convert_in_pos($joueur->get_x(), $joueur->get_y());
?>

<div id="conteneur_back">
<div id="conteneur">

<div id="mask" style='display:none;'></div>
<div id="popup" style='display:none;'>
	<div id="popup_menu"><span class='fermer' title='Fermer le popup' onclick="fermePopUp(); return false;">&nbsp;</span></div>
	<div id="popup_marge">
		<div id="popup_content"></div>
	</div>
</div>
<div id="loading" style='display:none'></div>
<div id="loading_information" style='display:none'></div>
	<div id="perso">
		
		<?php
		require_once('infoperso.php');
		?>
		
	</div>
	<div id='menu'>
		<?php
$arene = $joueur->in_arene();
$time = time();
if ($arene) $time += $arene->decal;
echo '<div id="menu_date"><img src="image/interface/'.moment_jour().
  '.png" alt="'.moment_jour().'" title="'.moment_jour().' - '.date_sso($time).
  '" />'.moment_jour();?>
	</div>

	<input type="hidden" id="menu_encours" value="lejeu" />
	<div id='menu_details'>
		<div id='lejeu_menu' style='display:none;'><span class='menu' onclick="affichePopUp('diplomatie.php');">Diplomatie</span><span class='menu' onclick="affichePopUp('classement.php');">Classement</span><span class='menu' onclick="affichePopUp('stats2.php?graph=carte_royaume');">Statistiques</span><span class='menu' onclick="affichePopUp('message_accueil.php?affiche=all');">Message d'Accueil</span><span class='menu' onclick="affichePopUp('option.php');">Options</span><span class='menu' onclick='showSoundPanel()'>Son</span></div>
		<div id='starshine_menu' style='display:none;'><span class='menu' onclick="affichePopUp('liste_monstre.php');">Bestiaire</span><span class='menu' onclick="affichePopUp('background.php');">Background</span><span class='menu' onclick="affichePopUp('royaume.php');">Carte</span>
		<?php //echo "<span class='menu' onclick=\"affichePopUp('beta_test.php');\">Beta</span>"; ?>
		</div>
		<div id='communaute_menu' style='display:none;'><span class='menu'><a href="http://forum.starshine-online.com">Forum</a></span><span class='menu'><a href="http://wiki.starshine-online.com/">Wiki</a></span><span class='menu'><a href="http://bug.starshine-online.com/">Signaler un bug</a></span><span class='menu' onclick="affichePopUp('acces_chat.php');">Tchat</span><span class='menu' onclick="affichePopUp('don.php');">Faire un don</span><span class="menu" style="margin : 0; padding : 0;"><a class="FlattrButton" style="display:none;" rev="flattr;button:compact;" href="http://www.starshine-online.com"></a></span></div>
<?php if ($G_no_flattr != true) { ?>
			<script type="text/javascript">
			/* <![CDATA[ */
				(function() {
					var s = document.createElement('script'), t = document.getElementsByTagName('script')[0];
					s.type = 'text/javascript';
					s.async = true;
					s.src = 'http://api.flattr.com/js/0.6/load.js?mode=auto';
					t.parentNode.insertBefore(s, t);
				})();
			/* ]]> */
			</script>
<?php } /* G_no_flattr */ ?>
	</div>
	<div id='menu_deco'>
		<span class="fermer" title='Se déconnecter' onclick="if(confirm('Voulez vous déconnecter ?')) { document.location.href='index.php?deco=ok'; };">&nbsp;</span><span class="show_debug_button" id="debug_log_button" title='Voir le debug' onclick="show_debug_log()"><img src="image/interface/debug.png" onclick="show_debug_log()"/></span>
	</div>
</div>
<div id='contenu_back'>
	<div id="contenu_jeu">
		<div id="centre">
		<?php
		//Génération de la carte apparaissant au centre.
		//Si coordonées supérieur à 100 alors c'est un donjon
		if(is_donjon($joueur->get_x(), $joueur->get_y()))
		{
			include_once(root.'donjon.php');
		}
		else include_once(root.'map2.php');
		?>
		</div>
		<?php include_once(root.'menu_carte.php');?>
		<div id="information">
				<h2>Information</h2>
		<?php
		
		$case = convert_in_pos($joueur->get_x(), $joueur->get_y());
		if(array_key_exists('page_info', $_GET)) $page_info = $_GET['page_info']; else $page_info = 'informationcase.php';
		{//-- Javascript
			echo "<script type='text/javascript'>
					// <![CDATA[\n";
			{//-- envoiInfo
				echo "envoiInfo('".$page_info."?case=".$case."', 'information');";
			}
			echo "	// ]]>
				  </script>";
		}

		echo "</div>
	</div>
	
</div>
</div>";

// Les logs de debug ajax
echo '<div id="debug_log" class="debug"></div>';

?>
<div id="ambiance_sound">
	<audio id="audio_1" controls>
	</audio>
	<audio id="audio_2" controls>
	</audio>
	<script type="text/javascript">
function doLoop() {
	document.getElementById('audio_1').addEventListener('ended', function(){
			this.currentTime = 0;
			this.pause();
			document.getElementById('audio_2').play();
		}, false);

	document.getElementById('audio_2').addEventListener('ended', function(){
			this.currentTime = 0;
			this.pause();
			document.getElementById('audio_1').play();
		}, false);
}
function setAmbianceAudio(file) {
	var a = [ document.getElementById('audio_1'),
						document.getElementById('audio_2') ];
	var e = [ 'ogg', 'mp3' ];
	for (var i in a) 
		a[i].pause();
	for (var i in a) {
		while (a[i].hasChildNodes())
			a[i].removeChild(a[i].firstChild);
		for (var j in e) {
			var x = document.createElement('source');
			x.setAttribute('src', 'image/son/' + file + '.' + e[j]);
			a[i].appendChild(x);
		}
	}
	a[0].play();
}
function stopAmbiance() {
	var a = [ document.getElementById('audio_1'),
						document.getElementById('audio_2') ];
	for (var i in a) 
		a[i].pause();
}
function showSoundPanel() {
	$('#ambiance_sound').dialog('open');
}
$(document).ready(function(){
		//setAmbianceAudio('Goutte-01-SF');
		doLoop();
		$('#ambiance_sound').dialog({ autoOpen: false });
	});
	</script>
<a href="javascript:stopAmbiance()">Stop</a>
</div>
<?php

//Inclusion du bas de la page
include_once(root.'bas.php');
?>
