<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
include_once(root.'inc/fp.php');
$joueur = new perso($_SESSION['ID']);
if(array_key_exists('up', $_GET))
{
	if($joueur['craft'] > 0)
	{
		switch($_GET['up'])
		{
			case 'forge' :
				$up = 'forge = forge + 1';
			break;
			case 'alchimie' :
				$up = 'alchimie = alchimie + 1';
			break;
			case 'architecture' :
				$up = 'architecture = architecture + 1';
			break;
		}
		$requete = "UPDATE perso SET craft = craft - 1, ".$up." WHERE ID = ".$joueur->get_id();
		$db->query($requete);
		echo ($joueur[$_GET['up']] + 1);
	}
	else echo ($joueur[$_GET['up']]);
}
else
{
?>
Craft actuel : <input type="text" value="<?php echo $joueur['craft']; ?>" disabled="disabled" /><br />
Mettre vos points dans :<br />
<span class="champs">Forge :</span> <span id="forge"><?php echo $joueur['forge']; ?></span> <span class="" onclick="envoiInfo('repartir_craft.php?up=forge', 'forge');">+</span><br />
<span class="champs">Architecture :</span> <span id="architecture"><?php echo $joueur['architecture']; ?></span> <span class="" onclick="envoiInfo('repartir_craft.php?up=architecture', 'architecture');">+</span><br />
<span class="champs">Alchimie :</span> <span id="alchimie"><?php echo $joueur['alchimie']; ?></span> <span class="" onclick="envoiInfo('repartir_craft.php?up=alchimie', 'alchimie');">+</span><br />
<?php
}
?>