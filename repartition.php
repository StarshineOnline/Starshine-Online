<?php
include('haut.php');
?>
	<link rel="stylesheet" type="text/css" media="screen,projection" title="Normal" href="css/index.css" />
<div id="haut">
	<img src="image/logo.png" />
</div>	
<?php
if ($maintenance)
{
	echo 'Starshine-online est actuellement en refonte compl�te, l\'exp�rience acqu�rie gr�ce � l\'alpha m\'a permis de voir les gros probl�mes qui pourraient se poser.<br />
	Je vais donc travailler sur la b�ta.<br />';
}
else
{
	include('menu.php');
	?>

<h3>R�partition des points d'attributs :</h3>

<?php

if (isset($_GET['direction'])) $direction = $_GET['direction'];
elseif (isset($_POST['direction'])) $direction = $_POST['direction'];

//PHASE 1
if (!isset($direction))
{
?>
<form action="repartition.php" method="post">
	<input type="button" name="moinsvie" value="-" /> <input type="text" name="vie" value="<?php echo $joueur['vie']; ?>" style="width : 20px; border : 0px;" /> <input type="button" name="plusvie" value="+" /> = <input type="text" name="viefinal" value="<?php echo $joueur['vie']; ?>" style="width : 20px; border : 0px;" /><br />
	<br />
	Points d'attributs restants : <input type="text" name="restant" value="5" style="width : 20px;" /><br />
	<br />
	<input type="hidden" name="direction" value="phase2" />
	<input type="submit" value="Envoyer" />
</form>
<?php
}
//PHASE 2
elseif ($direction == 'phase2')
{
}
include('bas.php');
}
?>