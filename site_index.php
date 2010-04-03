<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php 
$connexion = true;

include_once(root.'haut.php');

?>



	<body>
<script type="text/javascript">

function menu_change(input_name)
{
	if ($('menu_encours').value=='')
	{
		$('menu_encours').value= input_name;
		$(input_name).addClassName('select');
		$(input_name+'_menu').show();
	}
	else
	{
		var tmp = $('menu_encours').value;
		$(tmp+'_menu').hide();
		$(tmp).removeClassName('select');
		$('menu_encours').value= input_name;
		$(input_name).addClassName('select');
		$(input_name+'_menu').show();
	}
}

function switch_race()
{
	race = document.getElementById("race").options.selectedIndex;
	race = document.getElementById("race").options[race].value;
	for(i = 0; i < document.getElementById("race").options.length; i++)
	{
		irace = document.getElementById("race").options[i].value;
		document.getElementById(irace).style.display = 'none';
	}
	document.getElementById(race).style.display = 'block';
	switch_classe();
}


</script>

<div id='header'>
	<div id='header_logo'>
		<ul>
		<li class='menu'><a href='interface.php'>Jouez</a></li>		
		<li id='lejeu' class='menu' onclick="menu_change('lejeu');">Le jeu</li>
		<li id='communaute' class='menu' onclick="menu_change('communaute');">Communauté</li>
		<li id='starshine' class='menu' onclick="menu_change('starshine');">Starshine</li>
	</div>
	
</div>
<div id='menu'>
	<div id='menu_details'>
		<div id='lejeu_menu' style='display:none;'><span class='menu'>Diplomatie</span><span class='menu'>Classement</span><span class='menu'>Statistique</span></div>
		<div id='starshine_menu' style='display:none;'><span class='menu'>Bestiaire</span><span class='menu'>Background</span><span class='menu'>Carte</span></div>
		<div id='communaute_menu' style='display:none;'><span class='menu'>Forum</span><span class='menu'>Wiki</span><span class='menu'>Tchat</span></div>
	</div>
</div>
<div id='contenu_back'>
<div id='contenu'>
	<div id='news'>
	
<?php
	echo	$joueur->get_nom();
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
			echo '<div class="box">'.$message.'</div>';
		}
		$i++;
	}
	
?>
	</div>
	<div class='pub'>
					<script type='text/javascript'><!--
					google_ad_client = 'pub-7541997421837440';
					/* accueil */
					google_ad_slot = '8229796384';
					google_ad_width = 120;
					google_ad_height = 600;
					//-->
					</script>
					<script type='text/javascript'
					src='http://pagead2.googlesyndication.com/pagead/show_ads.js'>
					</script>
	</div>
</div>
</div>
<div id='bas'>
	<div id='bas_contenu'>
	</div>
</div>
	</body>
</html>
