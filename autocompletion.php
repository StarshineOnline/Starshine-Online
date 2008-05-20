<?php
$textures = false;
include('haut.php');
setlocale(LC_ALL, 'fr_FR');
include('haut_site.php');
header('Content-Type: text/xml;charset=utf-8');
echo(utf8_encode("<?xml version='1.0' encoding='UTF-8' ?><options>"));
if (isset($_GET['debut'])) {
    $debut = utf8_decode($_GET['debut']);
} else {
    $debut = "";
}
$debut = strtolower($debut);
$liste = array();

$requete = "SELECT nom FROM perso WHERE LOWER(nom) LIKE '%".$debut."%' ORDER BY nom ASC";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$liste[] = $row['nom'];
}
		
function generateOptions($debut,$liste) {
    $MAX_RETURN = 20;
    $i = 0;
    foreach ($liste as $element)
    {
        if ($i < $MAX_RETURN)
        {
            echo(utf8_encode("<option>".$element."</option>"));
            $i++;
        }
    }
}

generateOptions($debut,$liste);

echo("</options>");
?>