<?php
if(!isset($root)) $root = '';
?>
<link rel="stylesheet" type="text/css" media="screen,projection" title="Normal" href="<?php echo $root; ?>css/index.css" />
<div id="site">
<?php
	if (!isset($_SESSION['nom']))  echo 'Login';
	else
	{
	
	include ('infopersoindex.php');
	}

	if (!isset($_SESSION['nom']))
	{
	?>
	<div id="haut">
		<img src="image/monstre/lapin.png" /><a href="<?php echo $root; ?>index.php"><img src="<?php echo $root; ?>image/logossot.png" /></a>
	</div>
	<?php 
	
	}
	?>