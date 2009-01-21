<?php
include('inc/fp.php');
$joueur = recupperso($_SESSION['ID']);
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
		$requete = "UPDATE perso SET craft = craft - 1, ".$up." WHERE ID = ".$joueur['ID'];
		$db->query($requete);
		echo ($joueur[$_GET['up']] + 1);
	}
	else echo ($joueur[$_GET['up']]);
}
else
{
?>
Craft actuel : <input type="text" value="<?php echo $joueur['craft']; ?>" /><br />
Mettre vos points dans :<br />
<span class="champs">Forge :</span><div id="forge"><?php echo $joueur['forge']; ?></div><div class="" onclick="envoiInfo('repartir_craft.php?up=forge', 'forge');">+</div><br />
<span class="champs">Architecture :</span><div id="architecture"><?php echo $joueur['architecture']; ?></div><div class="" onclick="envoiInfo('repartir_craft.php?up=architecture', 'architecture');">+</div><br />
<span class="champs">Alchimie :</span><div id="alchimie"><?php echo $joueur['alchimie']; ?></div><div class="" onclick="envoiInfo('repartir_craft.php?up=alchimie', 'alchimie');">+</div><br />
<input type="submit" value="Valider" />
<?php
}
?>