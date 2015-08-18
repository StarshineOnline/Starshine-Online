<?php
/// @deprecated
if (file_exists('root.php'))
  include_once('root.php');
?><?php
if(!isset($root)) $root = '';
?>
<link rel="stylesheet" type="text/css" media="screen,projection" title="Normal" href="<?php echo $root; ?>css/index.css" />
<link rel="alternate" type="application/rss+xml" title="News Starshine-Online" href="http://forum.starshine-online.com/extern.php?action=feed&fid=5&type=rss"/>
<div id="site">

	<div id="haut">
		<a href="<?php echo $root; ?>index.php"><img src="<?php echo $root; ?>image/logossot.png" /></a>
	</div>