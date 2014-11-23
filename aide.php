<?php
/**
* @file aide.php
* Affichage de l'aide
*/
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');

if( !array_key_exists('id', $_GET) )
	exit;

/// @todo passer à l'objet 
$requete = 'SELECT * FROM aide WHERE id = "'.sSQL($_GET['id']).'"';
$req = $db->query($requete);
$row = $db->read_assoc($req);
if( !$row )
	exit;


$texte = new texte($row['texte'], texte::tutoriel);
$options = array('content:"'.$texte->parse().'"', 'placement:"'.$row['position'].'"', 'container:"body"', 'html:true');
if( array_key_exists('tuto', $_GET) && $row['total_tuto'] > 0 )
{
	$options[] = 'title:"'.$row['titre'].'"';
	$prec = $row['precedant_tuto'] ? '<a class="icone icone-precedent" href="aide.php?tuto=2&id='.$row['precedant_tuto'].'" onclick="return charger(this.href);"></a>' : '';
	$suiv = $row['suivant_tuto'] ? '<a class="icone icone-suivant" href="aide.php?tuto=2&id='.$row['suivant_tuto'].'" onclick="return charger(this.href);"></a>' : '<a class="icone icone-croix" onclick="return fin_aide();"></a>';
	$options[] = 'template:\'<div class="popover pop-aide pop-tuto" role="tooltip"><div class="arrow"></div>'.$prec.'<span class="label label-info">'.$row['index_tuto'].'/'.$row['total_tuto'].'</span><h3 class="popover-title"></h3>'.$suiv.'<div class="popover-content"></div></div>\'';
}
else
{
	$options[] = 'title:"'.$row['titre'].'"';
	$options[] = 'template:\'<div class="popover pop-aide" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>\'';
}
	
$js = '$("#'.$_GET['id'].'").popover({'.implode(',', $options).'}).popover("show");';
if( $_GET['tuto'] == 2 )
{
	$js .= 'elt_aide_aff.removeClass("aide-actif");elt_aide_aff.popover("hide");elt_aide_aff=$("#'.$_GET['id'].'");';
}

$interf_princ = $G_interf->creer_jeu();
$interf_princ->set_javascript($js);
?>