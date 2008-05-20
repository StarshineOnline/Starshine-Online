<?php
function print_head()
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<title>StarShine, le jeu qu'il s'efface tout seul !</title>
	<link rel="stylesheet" type="text/css" media="screen,projection" title="Normal" href="<?php echo $root; ?>css/interface.css" />
	<link rel="stylesheet" type="text/css" media="screen,projection" title="Normal" href="<?php echo $root; ?>css/texture.css" />
	<script language="Javascript" type="text/javascript" src="<?php echo $root; ?>javascript/fonction.js"></script>
	<script language="Javascript" type="text/javascript" src="<?php echo $root; ?>javascript/overlib/overlib.js"></script>
</head>

<body>

<?php
}

function make_overlib($message)
{
	return "overlib('<ul><li class=\'overlib_titres\'>".$message."</li></ul>', BGCLASS, 'overlib', BGCOLOR, '', FGCOLOR, '', VAUTO);";
}