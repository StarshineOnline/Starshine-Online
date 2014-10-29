<?php
if (file_exists('root.php'))
  include_once('root.php');
  
include_once(root.'inc/fp.php');

$interf_princ = $G_interf->creer_jeu();
  
$histoire = array_key_exists('histoire', $_GET) ? $_GET['histoire'] : null;
/// @todo passer à l'objet
if( $histoire )
{
	$requete = 'SELECT titre, texte FROM texte_rp WHERE id = '.$histoire;
	$req = $db->query($requete);
	$rp = $db->read_array($req);
}
$requete = 'SELECT id, titre FROM texte_rp WHERE type = "background" ORDER BY id';
$req = $db->query($requete);
$dlg = $interf_princ->set_dialogue( new interf_dialogBS('Histoire de Starshine', true, 'dlg_rp') );
$liste = $dlg->add( new interf_bal_cont('ul', 'liste_rp', 'nav nav-pills nav-stacked') );
while($row = $db->read_array($req))
{
	if( $row['id']==$histoire )
		$liste->add( new interf_elt_menu($row['titre'], '#', false, false, 'active') );
	else
		$liste->add( new interf_elt_menu($row['titre'], 'background.php?histoire='.$row['id'], 'return charger(this.href);') );
}
if( $histoire )
{
	$texte = new texte($rp['texte'], texte::msg_monde);
	$dlg->add( new interf_bal_smpl('div', $texte->parse(), 'texte_rp') );
}
$dlg->add( new interf_bal_smpl('div', false, 'rp_fin') );