<?php
if (file_exists('root.php'))
  include_once('root.php');
?><a href="royaume.php?carte=royaume" onclick="return envoiInfo(this.href, 'popup_content');">Royaumes</a> |
<a href="royaume.php?carte=3d" onclick="return envoiInfo(this.href, 'popup_content');">Carte 3D</a> |
<a href="royaume.php?carte=3d-ro" onclick="return envoiInfo(this.href, 'popup_content');">Carte 3D des Royaumes</a> |
<a href="royaume.php?carte=densite" onclick="return envoiInfo(this.href, 'popup_content');">Densité des monstres</a><br />
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
<img src="image/carte3d.png" />
    <?php
}
elseif($carte == '3d')
{
    ?>
<img src="image/carte3d-4.png" />
    <?php
}
elseif($carte == '3d-ro')
{
    ?>
<img src="image/carte3d-royaumes.png" />
    <?php
}
elseif($carte == 'densite')
{
    ?>
<img src="image/carte_densite_mob.png" />
    <?php
}
?>