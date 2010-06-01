<?php
if (file_exists('root.php'))
  include_once('root.php');

$site = true;
include_once(root.'haut.php');

?>

<div id="popup_erreur" style='display:none;'>
	<div id="popup_erreur_menu"><span class='fermer' title='Fermer le popup' onclick="fermePopUpErreur(); return false;">&nbsp;</span></div>
	<div id="popup_erreur_marge">
		<div id="popup_erreur_content"></div>
	</div>
</div>
<div id='accueil'>
	<div id='loading_sso' style='display:none;'></div>	
	<div id='test'>
	<div class='box'>
		<input type='hidden' id='menu_encours' value='presentation' />
		<div id='presentation_box'>
					<span class='logo'>&nbsp;</span> 
			<p>

			Bienvenue dans le monde de Starshine-Online.
Pour l'instant au stade de la bêta (c'est à dire en phase d'équilibrage et d'amélioration du monde), Starshine-Online sera un jeu de rôle massivement multijoueur (MMORPG) en tour par tour.<br />
<br />
Il vous permettra d'incarner un grand héros de l'univers Starshine, peuplé de nombreuses créatures et d'autres héros ennemis prêts à tout pour détruire votre peuple.<br />
<br /><br />
Il est recommandé d'utiliser <strong>un navigateur dernière génération (éviter les Internet Explorer)</strong> pour jouer à Starshine, nous vous conseillons <a href='http://www.mozilla-europe.org/'>Firefox</a> ou bien <a href='http://www.google.fr/chrome'>Google Chrome</a>.
N'oubliez pas de reporter les bugs et problèmes, et d'apporter vos suggestions sur le forum.</p>

		</div>
		<div id='screenshot_box' style='display:none;'>
			<ul>
				<li><a href="image/screenshots/screenshot01.png" title="Le jeu"><img src="image/screenshots/mini_screenshot01.png" alt='screenshot01' /></a></li>
				<li><a href="image/screenshots/screenshot02.png" title="Inventaire"><img src="image/screenshots/mini_screenshot02.png" alt='screenshot02' /></a></li>
				<li><a href="image/screenshots/screenshot03.png" title="Interface du roi"><img src="image/screenshots/mini_screenshot03.png" alt='screenshot03' /></a></li>
				<li><a href="image/screenshots/screenshot04.png" title="La boutique SSO"><img src="image/screenshots/mini_screenshot04.png" alt='screenshot04' /></a></li>
				<li><a href="image/screenshots/screenshot05.png" title="Screenshot 5"><img src="image/screenshots/mini_screenshot05.png" alt='screenshot05' /></a></li>
				<li><a href="image/screenshots/screenshot06.png" title=""><img src="image/screenshots/mini_screenshot06.png" alt='screenshot06' /></a></li>
				<li><a href="image/screenshots/screenshot07.jpg" title="Version alpha"><img src="image/screenshots/mini_screenshot07.png" alt='screenshot07' /></a></li>
				<li><a href="image/screenshots/screenshot08.jpg" title="Version 0.5"><img src="image/screenshots/mini_screenshot08.png" alt='screenshot08' /></a></li>
				<li><a href="image/screenshots/screenshot09.png" title="Version 0.6 (en test)"><img src="image/screenshots/mini_screenshot09.png" alt='screenshot09' /></a></li>
			</ul>
		</div>
		<div id='news_box' style='display:none;'>
		<?php
		if(!file_exists('connect_forum.php'))
		{
			echo "Le fichier de connexion au forum n'est pas présent sur le serveur";
			echo "<div class='news'>
			<h2>Qqdf qdsf qsdf  qdsf qdsf </h2>
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			</div>";
			echo "<div class='news'>
			<h2>Qqdf qdsf qsdf  qdsf qdsf </h2>
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			</div>";
			echo "<div class='news'>
			<h2>Qqdf qdsf qsdf  qdsf qdsf </h2>
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			qqdsfqdsf qsdf qsdf qsdf qsd f qdsf sdq f qsdf qsdf qs df qsdf qsdf q sdf qfsd qdsf sd f sqdf 
			</div>";
			
		}
		else
		{
			require('connect_forum.php');
			$requete = "SELECT id, subject, num_replies FROM punbbtopics WHERE (forum_id = 5) ORDER BY posted DESC";
			$req = $db_forum->query($requete);
		
			$i = 0;
			while($row = $db_forum->read_array($req) AND $i < 5)
			{
				$regs = '';
				echo '
				<div class="news">
					<h2><a href="http://forum.starshine-online.com/viewtopic.php?id='.$row['id'].'">'.($row['subject']).'</a></h2>';
				$requete_post = "SELECT message FROM punbbposts WHERE (topic_id = ".$row['id'].") ORDER BY id ASC";
				$req_post = $db_forum->query($requete_post);
				$row_post = $db_forum->read_array($req_post);
				eregi("\[chapeau\]([^[]*)\[/chapeau\]", $row_post['message'], $regs);
				if($regs[1] != '') $message = $regs[1];
				else $message = $row_post['message'];
				$message = /*utf8_encode*/(nl2br($message));
				$message = eregi_replace("\[img\]([^[]*)\[/img\]", '<img src=\\1 title="\\1">', $message );
				$message = eregi_replace("\[b\]([^[]*)\[/b\]", '<strong>\\1</strong>', $message );
				$message = eregi_replace("\[i\]([^[]*)\[/i\]", '<i>\\1</i>', $message );
				$message = eregi_replace("\[url\]([^[]*)\[/url\]", '<a href="\\1">\\1</a>', $message );
				if(strlen($message) > 600)
				{
					$message = mb_substr($message, 0, 600);
				}
				$message .= '<br /><a href="http://forum.starshine-online.com/viewtopic.php?id='.$row['id'].'">Lire la suite</a> <span class="comms">('.$row['num_replies'].' commentaire(s))</span>
				</div>';
				echo $message;
				$i++;
				
			}
		}
		?>
		</div>
		<div id='creation_box' style='display:none;'>
		<p id='creat_erreur' style='color:#FF0022; display : none;'>&nbsp;</p>
		<div style='width:165px;float:left;'>
			<span class='creation_text'>Quel sera votre nom ?</span><span class='illu'><input type="text" name="nom" id='creat_nom' /></span><br />
			<span class='creation_text'>Indiquer un mot de passe :</span><span class='illu'><input type="password" name="password" id='creat_pass' /></span><br />
			<span class='creation_text'>Confirmer votre mot de passe :</span>
			<span class='illu'><input type="password" name="password2" id='creat_pass2' /></span>
			<span class='creation_text'>Indiquer un email :</span><span class='illu'><input type="text" name="email" id='creat_email' /></span><br />
			<span onclick="validation_perso();" id="bouton_creer"> </span>
		</div>
		<div class='perso_cadre'>
		<?php
		$RqRace = $db->query("SELECT race FROM royaume WHERE race != '' AND race != 'dragon' ORDER BY star_nouveau_joueur DESC, race ASC");

		$i=0;
		while($objRace = $db->read_object($RqRace))
		{
			if ($i=='0'){echo "<p style='clear:both;'>";}
			echo "<span class='".$objRace->race."_guerrier' title='".$objRace->race." Combattant' id='".$objRace->race."_guerrier' onclick=\"race('".$objRace->race."','guerrier');\">&nbsp;</span>";
			echo "<span class='".$objRace->race."_mage' title='".$objRace->race." Magicien' id='".$objRace->race."_mage' onclick=\"race('".$objRace->race."','mage');\">&nbsp;</span>";
			echo "<span class='perso_espacement'>&nbsp;</span>";
			$i++;
			if ($i=='3'){echo '</p>';$i=0;}

		}	
		echo '</p>';		
		?>
		<input type='hidden' id='perso_selected_id' />
		</div>
			<div id="aide_inscription">
			Avant de créer un personnage, vous pouvez consulter <a href="http://wiki.starshine-online.com">l'aide de jeu</a>, pour mieux choisir votre personnage.<br />
			N'hésitez pas à faire le tour des races pour en voir toutes les différences, et à passer votre curseur sur les attributs (force, dextérité, etc) pour avoir des détails sur leur fonctionnement.<br />
			Pour un équilibrage du jeu, les peuples ayant le moins de joueurs recoivent plus de stars à la création du personnage.<br />
			<strong>Un compte sur le forum sera créé automatiquement avec vos informations du jeu.</strong>
			</div>
		</div>
		
	</div>
	</div>

			<?php
			if (!isset($_SESSION['nom']))
			{
			?>
				<div id='login'>			
				<form id='login_form' action="index.php" method="post">
				<div>
				<input type="text" name="nom" size="10" class="login_nom" tabindex="1" />
				<input type="password" name="password" size="10" class="login_mdp" tabindex="2" />
				<input type="checkbox" name="auto_login" value="Ok" class="login_auto" tabindex="3"/>
				<input type='hidden' name='log' />
				<input type='submit' class='login_connexion' onclick="$('#login_form').submit();" tabindex="4" value="" />
				</div>
				</form>
				</div>		
			
			<?php
			}
			else
			{
				echo "<div id='login_ok'>";	
				echo "<a href='interface.php'>Entrez dans le monde de Starshine-Online</a> / <span style='cursor:pointer;' onclick=\"if(confirm('Es tu sur de vouloir te déconnecter malheureux ?')) { document.location.href='index.php?deco=ok'; };\">Se deconnecter</span>";
				echo "</div>";
			}
			?>	
	<div id='menu_accueil'>
	<ul>
		<li id='presentation_menu' class='selected' onclick="menu_change('presentation');"></li>
		<li id='screenshot_menu' onclick="menu_change('screenshot');"></li>
		<li id='news_menu' onclick="menu_change('news');"></li>
		<li id='creation_menu' onclick="menu_change('creation');"></li>
	</ul>
	</div>

	<div id='personnage' style='display:none'>

	
	</div>
		<div id='liens'>

	 <p>Liens d'aide au jeu : <a href='http://wiki.starshine-online.com/'>Comprendre Starshine</a> <a href='http://bug.starshine-online.com/'>Signaler un Bug</a> <a href='http://forum.starshine-online.com/'>Le Forum
	 </a></p>
	</div>
	<!-- Piwik -->
<script type="text/javascript">
var pkBaseURL = (("https:" == document.location.protocol) ? "https://www.starshine-online.com/piwik/" : "http://www.starshine-online.com/piwik/");
document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));
</script><script type="text/javascript">
try {
var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", 1);
piwikTracker.trackPageView();
piwikTracker.enableLinkTracking();
} catch( err ) {}
</script><noscript><p><img src="http://www.starshine-online.com/piwik/piwik.php?idsite=1" style="border:0" alt="" /></p></noscript>
<!-- End Piwik Tag -->
</body>
</html>
