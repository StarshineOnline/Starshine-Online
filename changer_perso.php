<?PHP
	if (file_exists('root.php'))
	  include_once('root.php');
	
	include_once(root.'inc/fp.php');
    
  $joueur = joueur::factory();
  
  $interf_princ = $G_interf->creer_jeu();
  $dlg = $interf_princ->set_dialogue( new interf_dialogBS('Changement de personnage') );
  $dlg->ajout_btn('Annuler', 'fermer');

  if(array_key_exists('nom', $_GET))
  {
  	$ok = $joueur->get_droits() & joueur::droit_admin ? true : false;
  	if( !$ok )
  	{
  		$perso = new perso( $_GET['id'] );
  		$ok = $joueur->get_droits() & (joueur::droit_anim | joueur::droit_modo) && $perso->get_joueur() == 0;
  		if( !$ok )
  			$ok = $perso->get_joueur() == $joueur->get_id();
		}
		if( $ok )
		{
	    $_SESSION['nom'] = $_GET['nom'];
	    $_SESSION['ID'] = $_GET['id'];
	    //Mis à jour de la dernière connexion
			$requete = "UPDATE perso SET dernier_connexion = ".time().", statut = 'actif' WHERE ID = ".$_GET['id'];
			$db->query($requete);
			$dlg->add( new interf_txt('Rechargement de la page en cours…') );
			$interf_princ->recharger_interface();
	    exit;
		}
  }
  if(array_key_exists('info', $_GET))
    $info = $_GET['info'];
  else
    $info = false;
  if( $joueur->get_droits() & joueur::droit_admin )
  	$persos = perso::create('id_joueur', $joueur->get_id());
	else
  	$persos = perso::create(false, false, 'id ASC', false, 'id_joueur='.$joueur->get_id().' AND (statut IN ("actif", "inactif") OR statut LIKE "hibern" AND fin_ban <= '.time().')');
  $dlg->add( $G_interf->creer_liste_perso($persos, 'changer_perso.php') );
  if( $joueur->get_droits() & joueur::droit_pnj  )
  {
  	$persos = perso::create('id_joueur', 0);
  	if( $persos )
  	{
	  	$dlg->add( new interf_bal_smpl('h3', 'Personnages non joueurs') );
	  	$dlg->add( $G_interf->creer_liste_perso($persos, 'changer_perso.php') );
		}
  }
?>