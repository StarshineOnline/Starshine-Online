<a href="royaume.php?carte=royaume" onclick="return envoiInfo(this.href, 'popup_content');">Royaumes</a> |
<a href="royaume.php?carte=3d" onclick="return envoiInfo(this.href, 'popup_content');">Carte 3D</a><br />
<?php
if(array_key_exists('carte', $_GET)) $carte = $_GET['carte']; else $carte = '3d';
if($carte == 'royaume')
{
    ?>
<img src="image/carte_royaume.png" />
    <?php
}
elseif($carte == 'ou')
{
    ?>
<img src="image/carte3d.jpg" />
    <?php
}
elseif($carte == '3d')
{
    ?>
<img src="image/carte3d.jpg" />
    <?php
}
?>