<?php 
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
	}
}
function switch_classe()
{
	classe = document.getElementById("classe").options.selectedIndex;
	classe = document.getElementById("classe").options[classe].value;
	if(classe == 'combattant')
	{
		classe = 'guerrier';
		document.getElementById('magicien').style.display = 'none';
		document.getElementById('combattant').style.display = 'block';
	}
	else
	{
		classe = 'mage';
		document.getElementById('magicien').style.display = 'block';
		document.getElementById('combattant').style.display = 'none';
	}
	race = document.getElementById("race").options.selectedIndex;
	race = document.getElementById("race").options[race].value;
	envoiInfo('switch_classe.php?race=' + race + '&classe=' + classe, 'img' + race);
}
</script>

<div id='accueil'>
	<div class='logo'></div>
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
Il est recommandé d'avoir un navigateur dernière génération pour jouer à Starshine, nous vous conseillons Firefox.<br />
N'oubliez pas de reporter les bugs et problèmes, et de suggérer de nouvelles choses sur le forum.</p>

		</div>
		<div id='screenshot_box' style='display:none;'>
			<p>Voici quelques screenshots</p>
		
		</div>
		<div id='news_box' style='display:none;'>
		<?php
				require('connect_forum.php');
	$requete = "SELECT * FROM punbbtopics WHERE (forum_id = 5) ORDER BY posted DESC";
	$req = $db_forum->query($requete);

	$i = 0;
	while($row = $db_forum->read_array($req) AND $i < 7)
	{
		echo '<h2><a href="http://forum.starshine-online.com/viewtopic.php?id='.$row['id'].'">'.($row['subject']).'</a></h2>';
		if ($i < 2)
		{
			$requete_post = "SELECT * FROM punbbposts WHERE (topic_id = ".$row['id'].") ORDER BY id ASC";
			$req_post = $db_forum->query($requete_post);
			$row_post = $db_forum->read_array($req_post);
			$message = /*utf8_encode*/(nl2br($row_post['message']));
			$message = eregi_replace("\[img\]([^[]*)\[/img\]", '<img src=\\1 title="\\1">', $message );
			$message = eregi_replace("\[b\]([^[]*)\[/b\]", '<strong>\\1</strong>', $message );
			$message = eregi_replace("\[i\]([^[]*)\[/i\]", '<i>\\1</i>', $message );
			$message = eregi_replace("\[url\]([^[]*)\[/url\]", '<a href="\\1">\\1</a>', $message );
			if(strlen($message) > 600)
			{
				$message = mb_substr($message, 0, 600);
				$message .= '<br /><a href="http://forum.starshine-online.com/viewtopic.php?id='.$row['id'].'">Lire la suite</a>';
			}
			echo $message;
		}
		$i++;
	}

		?>
		</div>
		<div id='creation_box' style='display:none;'>
		<?php
		$races = array('barbare', 'elfebois', 'elfehaut', 'humain', 'humainnoir', 'mortvivant', 'nain', 'orc', 'scavenger', 'troll', 'vampire');
foreach($races as $race)
{
	$requete = "SELECT star_nouveau_joueur FROM royaume WHERE ID = ".$Trace[$race]['numrace'];
	$req = $db->query($requete);
	$row = $db->read_row($req);
	$stars[$race] = $row[0];
	$requete = "SELECT propagande FROM motk WHERE id_royaume = ".$Trace[$race]['numrace'];
	$req = $db->query($requete);
	$row = $db->read_row($req);
	$propa = $row[0];
	$propa = htmlspecialchars(stripslashes($propa));
	$propa = str_replace('[br]', '<br />', $propa);
	$propagande[$race] = $propa;
}
			?>
			Avant de créer un personnage, vous pouvez consulter <a href="wiki.starshine-online.com">l'aide de jeu</a>, pour mieux choisir votre personnage<br />
			N'hésitez pas à faire le tour des races pour en voir toutes les différences, et à passer votre curseur sur les attributs (force, dextérité, etc) pour avoir des détails sur leur fonctionnement.<br />
			Pour un équilibrage du jeu, les peuples ayant le moins de joueurs recoivent plus de stars à la création du personnage.<br />
			<br />
			<strong>Un compte sur le forum sera créé automatiquement avec vos informations du jeu.</strong>
<form action="create.php" method="POST" style="margin : 10px; padding : 5px; border : 2px solid white; -moz-border-radius : 13px; font-size : 0.9em;">
		<p>
		<span class='creation_text'>Quel sera votre nom ?</span><input type="text" name="nom" /><br />
		<span class='creation_text'>Indiquer un mot de passe :</span><input type="password" name="password" /><br />
		<span class='creation_text'>Confirmer votre mot de passe :</span>
		<input type="password" name="password2" /><br />
		<br />
		<span class='creation_text'>Choisissez une race :</span>
		<select name="race" id="race" onChange="switch_race();">
			<?php
			$true = true;
			$requete = "SELECT race FROM royaume WHERE race != '' ORDER BY star_nouveau_joueur DESC, race ASC";
			$req = $db->query($requete);
			while($row = $db->read_row($req))
			{
				if($true)
				{
					$race_1 = $row[0];
					$true = false;
				}
				echo '<option value="'.$row[0].'">'.$Gtrad[$row[0]].'</option>';
			}
			?>
		</select>
		<br />
		<span class='creation_text'>Choisissez une classe :</span>
		<select name="classe" id="classe" onchange="switch_classe();">
			<option value="combattant">Combattant</option>
			<option value="magicien">Magicien</option>
		</select><br />
		<br />
		<input type="hidden" name="direction" value="phase2" />
		<input type="submit" value="Créer ce personnage" />
		</form>

		
		</div>
		
	</div>
	</div>
	<div id='login'>
			<?php
			if (!isset($_SESSION['nom']))
			{
			?>
			<form action="" method="post">
			ID : <input type="text" name="nom" size="10" class="input" />
			Pass : <input type="password" name="password" size="10" class="input" />
			Auto Login <input type="checkbox" name="auto_login" value="Ok" />
			<input type="submit" name="log" value="Connexion" class="input" />
			</form>
			<?php
			}
			else
			{
				echo "<a href='site_index.php'>jouer</a>";
			}
			?>	
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