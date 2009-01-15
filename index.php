<?php 
$site = true;
include('haut.php');

?>
<script type="text/javascript">

function menu_change(input_name)
{
	if ($('menu_encours').value=='')
	{
		$('menu_encours').value= input_name;
		$(input_name+'_menu').addClassName('selected');
		$(input_name+'_box').show();
	}
	else
	{
		var tmp = $('menu_encours').value;
		$(tmp+'_box').hide();
		$(tmp+'_menu').removeClassName('selected');
		$('menu_encours').value= input_name;
		$(input_name+'_menu').addClassName('selected');
		$(input_name+'_box').show();
		if ($('perso_selected_id').value != '')
		{
			$($('perso_selected_id').value).className ='';
			$('personnage').hide();			
		}		
	}
}
function Chargement()
{
	$('loading_sso').show();
	$('accueil').setAttribute('style','cursor:progress !important;')
}
function race(input_race,input_classe)
{
	function Affiche(requete)
	{
		$('personnage').show();
		$('personnage').innerHTML = requete.responseText;
		$('loading_sso').hide();
		$('accueil').setAttribute('style','cursor:normal;')
		
	}
	if ($('perso_selected_id').value != '')
	{
		$($('perso_selected_id').value).className ='';			
	}
	$(input_race+'_'+input_classe).className = 'perso_selected';
	$('perso_selected_id').value = input_race+'_'+input_classe;
	new Ajax.Request('./site_accueil_personnage.php',{method:'get',parameters:'race='+input_race+'&classe='+input_classe,onLoading:Chargement,onComplete:Affiche});
}
function validation_perso()
{
	function Afficheperso(requete)
	{
		$('personnage').show();
		$('personnage').innerHTML = requete.responseText;
		$('loading').hide();
		$('accueil').setAttribute('style','cursor:normal;')
		
	}

	if ($('creat_nom').value == '')
	{
		$('creat_erreur').innerHTML = 'Vous avez laisser un champ libre, ou vos mots de passe ne correspondent pas';
		$('creat_nom').setAttribute('style','border: 1px solid #CC0033;');
	}
	
	if ($('creat_email').value == '')
	{
		$('creat_erreur').innerHTML = 'Vous avez laisser un champ libre, ou vos mots de passe ne correspondent pas';
		$('creat_email').setAttribute('style','border: 1px solid #CC0033;');	
	}
	if (($('creat_pass').value != $('creat_pass2').value) || ($('creat_pass2').value=='') || ($('creat_pass2').value==''))
	{
		$('creat_erreur').innerHTML = 'Vous avez laisser un champ libre, ou vos mots de passe ne correspondent pas';
		$('creat_pass').setAttribute('style','border: 1px solid #CC0033;');	
		$('creat_pass2').setAttribute('style','border: 1px solid #CC0033;');			
	}
	if ($('perso_selected_id').value == '')
	{
		$('creat_erreur').innerHTML = "Vous n'avez pas sélectionnez de personnage.";
	}	
	if (($('perso_selected_id').value != '') && ($('creat_pass').value == $('creat_pass2').value) && ($('creat_pass2').value!='') && ($('creat_pass2').value!='') && ($('creat_nom').value != ''))
	{
		var perso = $('perso_selected_id').value.split('_');
		new Ajax.Request('./site_accueil_creation.php',{method:'get',parameters:'race='+perso[0]+'&classe='+perso[1]+'&pseudo='+$('creat_nom').value+'&mdp='+$('creat_pass').value,onLoading:Chargement,onComplete:Afficheperso});
	}
	
}
</script>

<div id='accueil'>
	<div class='logo'></div>
	<div id='loading_sso' style='display:none;'></div>	
	<div id='test'>
	<div id='menu_accueil'>
	<ul>
		<li id='presentation_menu' class='selected' onclick="menu_change('presentation');">Présentation</li>
		<li id='screenshot_menu' onclick="menu_change('screenshot');">ScreenShot</li>
		<li id='news_menu' onclick="menu_change('news');">News</li>
		<li id='creation_menu' onclick="menu_change('creation');">Création d'un compte</li>

	</ul>
	</div>
	<div class='box'>
		<input type='hidden' id='menu_encours' value='presentation'>
		<div id='presentation_box'>
			<p>Bienvenue dans le monde de Starshine-Online.<br />
Pour l'instant au stade de la béta (c'est à dire en phase d'équilibrage et d'amélioration du monde), starshine-online sera un jeu de rôle massivement mutijoueur (mmorpg) en tour par tour.<br /><br />
Il vous permettra d'entrer dans la peau d'un grand héros de l'univers Starshine peuplé de nombreuses créatures et d'autres héros ennemis près a tout pour détruire votre peuple.
<br /><br />
Il est recommandé d'avoir un navigateur dernière génération pour jouer à Starshine, nous vous conseillons Firefox, un navigateur libre.<br />
N'oubliez pas de reporter les bugs et problèmes, et de suggérer de nouvelles choses sur le forum.</p>

		</div>
		<div id='screenshot_box' style='display:none;'>
			<a href="image/screenshots/screenshot01.jpg" rel="lightbox[screens]" title="Screenshot 1" /><img src="image/screenshots/mini_screenshot01.jpg" /></a>
			<a href="image/screenshots/screenshot02.jpg" rel="lightbox[screens]" title="Screenshot 2" /><img src="image/screenshots/mini_screenshot01.jpg" /></a>
			<a href="image/screenshots/screenshot03.jpg" rel="lightbox[screens]" title="Screenshot 3" /><img src="image/screenshots/mini_screenshot01.jpg" /></a><br />
			<a href="image/screenshots/screenshot04.jpg" rel="lightbox[screens]" title="Screenshot 4" /><img src="image/screenshots/mini_screenshot01.jpg" /></a>
			<a href="image/screenshots/screenshot05.jpg" rel="lightbox[screens]" title="Screenshot 5" /><img src="image/screenshots/mini_screenshot01.jpg" /></a>
			<a href="image/screenshots/screenshot06.jpg" rel="lightbox[screens]" title="Screenshot 6" /><img src="image/screenshots/mini_screenshot01.jpg" /></a><br />
			<a href="image/screenshots/screenshot07.jpg" rel="lightbox[screens]" title="Screenshot 7" /><img src="image/screenshots/mini_screenshot01.jpg" /></a>
			<a href="image/screenshots/screenshot08.jpg" rel="lightbox[screens]" title="Screenshot 8" /><img src="image/screenshots/mini_screenshot01.jpg" /></a>
			<a href="image/screenshots/screenshot09.jpg" rel="lightbox[screens]" title="Screenshot 9" /><img src="image/screenshots/mini_screenshot01.jpg" /></a>
		</div>
		<div id='news_box' style='display:none;'>
		<?php
				require('connect_forum.php');
	$requete = "SELECT id, subject, num_replies FROM punbbtopics WHERE (forum_id = 5) ORDER BY posted DESC";
	$req = $db_forum->query($requete);

	$i = 0;
	while($row = $db_forum->read_array($req) AND $i < 5)
	{
		$regs = '';
		echo '<h2><a href="http://forum.starshine-online.com/viewtopic.php?id='.$row['id'].'">'.($row['subject']).'</a></h2>';
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
		$message .= '<br /><a href="http://forum.starshine-online.com/viewtopic.php?id='.$row['id'].'">Lire la suite</a> '.$row['num_replies'].' commentaire(s)';
		echo $message;
		$i++;
	}

		?>
		</div>
		<div id='creation_box' style='display:none;'>
		<?php
	$RqRace = $db->query("SELECT race FROM royaume WHERE race != '' ORDER BY star_nouveau_joueur DESC, race ASC");
			?>

<form action="" method="POST">
		<p id='creat_erreur' style='color:#CC0033;'></p>
		<div style='width:35%;float:left;'>
			<span class='creation_text'>Quel sera votre nom ?</span><input type="text" name="nom" id='creat_nom' /><br />
			<span class='creation_text'>Email :</span>
			<input type="text" name="email" id='creat_email' /><br />			
			<span class='creation_text'>Indiquer un mot de passe :</span><input type="password" name="password" id='creat_pass' /><br />
			<span class='creation_text'>Confirmer votre mot de passe :</span>
			<input type="password" name="password2" id='creat_pass2' />
			<span onclick="validation_perso();" id="bouton_creer">Créer</span>
		</div>
		<div style='width:65%;float:left;'>
		<?php
		$i=0;
		while($objRace = $db->read_object($RqRace))
		{
			if ($i=='0'){echo "<p style='clear:both;'>";}
			echo "<img src='./image/personnage/".$objRace->race."/".$objRace->race."_guerrier.png' id='".$objRace->race."_guerrier' onclick=\"race('".$objRace->race."','guerrier');\" style='width:35px;float:left;cursor:pointer;' />";
			echo "<img src='./image/personnage/".$objRace->race."/".$objRace->race."_mage.png' id='".$objRace->race."_mage' onclick=\"race('".$objRace->race."','mage');\" style='width:35px;float:left;cursor:pointer;' /><span style='width:17px;float:left;height:1px;'></span>";
			$i++;
			if ($i=='4'){echo '</p>';$i=0;}

		}			
		?>
		<input type='hidden' id='perso_selected_id' />
		</div>
		</form>
			<div id="aide_inscription">
			Avant de créer un personnage, vous pouvez consulter <a href="http://wiki.starshine-online.com">l'aide de jeu</a>, pour mieux choisir votre personnage<br />
			N'hésitez pas à faire le tour des races pour en voir toutes les différences, et à passer votre curseur sur les attributs (force, dextérité, etc) pour avoir des détails sur leur fonctionnement.<br />
			Pour un équilibrage du jeu, les peuples ayant le moins de joueurs recoivent plus de stars à la création du personnage.<br />
			<strong>Un compte sur le forum sera créé automatiquement avec vos informations du jeu.</strong>
			</div>
		</div>
		
	</div>
	</div>
	<div id='login'>
			<?php
			if (!isset($_SESSION['nom']))
			{
			?>
			<form action="index.php" method="post">
			ID : <input type="text" name="nom" size="10" class="input" />
			Pass : <input type="password" name="password" size="10" class="input" />
			Auto Login <input type="checkbox" name="auto_login" value="Ok" />
			<input type="submit" name="log" value="Connexion" class="input" />
			</form>
			<?php
			}
			else
			{
				echo "<a href='jeu2.php'>Entrez dans Starshine-Online</a> <div id='deco'><div id='joueur_onglets_exit' title='Se déconnecter' onclick=\"if(confirm('Voulez vous déconnecter ?')) { document.location.href='index.php?deco=ok'; };\">X</div></div>";
			}
			?>	
	</div>		

	<div id='personnage' style='display:none'>

	
	</div>
	<div id='accueil_pub'>
	<script type="text/javascript"><!--
google_ad_client = "pub-7541997421837440";
/* 468x60, date de création 06/01/09 */
google_ad_slot = "3202928182";
google_ad_width = 468;
google_ad_height = 60;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
	
	</div>
</div>