<?php
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');
$site = true;
$interf_obj = true;
include_once(root.'haut.php');


if( array_key_exists('page', $_GET) )
	$page = $_GET['page'];
else if( !joueur::factory() || joueur::get_perso() )
	$page = 'infos';
else
	$page = 'creer_perso';

$action = array_key_exists('action', $_GET) ? $_GET['action'] : null;
switch($action)
{
case 'creer_joueur':
	interf_sso::change_url();
	$pseudo = $_POST['nom'];
  $mdp = $_POST['password'];
  $mdp2 = $_POST['password2'];
  $email = $_POST['email'];
	//Verification sécuritaire
	if( !$pseudo )
	{
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas saissi de nom.');
		$page = 'creer_compte';
		break;
	}
	if(!check_secu($pseudo))
	{
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Les caractères spéciaux ne sont pas autorisés.');
		$page = 'creer_compte';
		break;
	}
	if( !$mdp )
	{
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas saissi de mot de passe.');
		$page = 'creer_compte';
		break;
	}
	if( $mdp != $mdp2 )
	{
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Les mots de passes sont différents.');
		$page = 'creer_compte';
		break;
	}
  if( $email )
  {
  	/// @todo passer à l'objet
    $requete = 'SELECT id FROM joueur WHERE email LIKE "'.$email.'"';
    $req = $db->query($requete);
    if( $db->num_rows($req) )
    {
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous avez déjà un compte joueur, il est interdit d\'en avoir plusieurs ou d\'en changer sans l\'accord des administraeurs.<br/>\nSi vous voulez changer de personnage, supprimez l\'ancien (en allant dans les options).');
			$page = 'creer_compte';
			break;
    }
  }
	$login = pseudo_to_login($pseudo);
	if( check_existing_account($pseudo, true, true, true) or check_existing_account($login, true, true, true) )
	{
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Nom déjà utilisé.');
		$page = 'creer_compte';
		break;
  }
  $joueur = new joueur(0, $login, '', $pseudo, joueur::droit_jouer, $email, sha1($mdp));
  $joueur->set_mdp( md5($mdp) );
  $joueur->set_mdp_forum( sha1($mdp) );
  $joueur->set_mdp_jabber( md5($mdp) );
  $joueur->sauver();
  $identification = new identification();
  if( $identification->connexion($login, md5($mdp), false) !== 0 )
  {
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Erreur inconnue, veuiller contacter un admin.');
		$page = 'creer_compte';
		break;
	}
	$page = 'creer_perso';
	break;
case 'creer_perso':
	interf_sso::change_url();
	$pseudo = $_POST['nom'];
  $race = $_POST['race'];
  $classe = $_POST['classe'];
  
	//Config punbb groups
	$punbb['elfebois'] = 6;
	$punbb['orc'] = 12;
	$punbb['nain'] = 11;
	$punbb['troll'] = 14;
	$punbb['humain'] = 8;
	$punbb['vampire'] = 15;
	$punbb['elfehaut'] = 7;
	$punbb['humainnoir'] = 9;
	$punbb['scavenger'] = 13;
	$punbb['barbare'] = 5;
	$punbb['mortvivant'] = 10;
	
	$nombre = check_existing_account($pseudo);
	if ($nombre > 0)
	{
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Nom déjà utilisé.');
		$page = 'creer_perso';
		break;
	}
	if (!$race)
	{
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas chois de race et de classe !');
		$page = 'creer_perso';
		break;
	}
	include_once(root.'inc/race.inc.php');
	include_once(root.'inc/classe.inc.php');
	$joueur =  new joueur( $_SESSION['id_joueur'] );
	$perso = new perso();
	$caracteristiques = $Trace[$race];
	if ($classe == 'combattant')
	{
		$caracteristiques['vie'] = $caracteristiques['vie'] + 1;
		$caracteristiques['force'] = $caracteristiques['force'] + 1;
		$caracteristiques['dexterite'] = $caracteristiques['dexterite'] + 1;
		$sort_jeu = '';
		$sort_combat = '';
		$comp_combat = '';//'7;8';
		$perso->set_x($Trace[$race]['spawn_tutocx']);
		$perso->set_y($Trace[$race]['spawn_tutocy']);
	}
	else
	{
		$caracteristiques['energie'] = $caracteristiques['energie'] + 1;
		$caracteristiques['volonte'] = $caracteristiques['volonte'] + 1;
		$caracteristiques['puissance'] = $caracteristiques['puissance'] + 1;
		$sort_jeu = '1';
		$sort_combat = '1';
		$comp_combat = '';
		$perso->set_x($Trace[$race]['spawn_tutomx']);
		$perso->set_y($Trace[$race]['spawn_tutomy']);
	}
	$royaume = new royaume($caracteristiques['numrace']);

	$perso->set_nom(trim($pseudo));
	$perso->set_race($race);
	$perso->set_level(1);
	$perso->set_star($royaume->get_star_nouveau_joueur());
	$perso->set_vie($caracteristiques['vie']);
	$perso->set_force($caracteristiques['force']);
	$perso->set_dexterite($caracteristiques['dexterite']);
	$perso->set_puissance($caracteristiques['puissance']);
	$perso->set_volonte($caracteristiques['volonte']);
	$perso->set_energie($caracteristiques['energie']);
	$perso->set_sort_jeu($sort_jeu);
	$perso->set_sort_combat($sort_combat);
	$perso->set_comp_combat($comp_combat);
	$perso->set_rang_royaume(7);
	// Pas oublier les bases
	$perso->set_melee(1);
	$perso->set_distance(1);
	$perso->set_esquive(1);
	$perso->set_blocage(1);
	$perso->set_incantation(1);
	$perso->set_identification(1);
	$perso->set_craft(1);
	$perso->set_alchimie(1);
	$perso->set_architecture(1);
	$perso->set_forge(1);
	$perso->set_survie(1);
	$perso->set_dressage(1);
	$perso->set_max_pet(1);

	if($classe == 'combattant')
	{
		$perso->set_sort_vie(0);
		$perso->set_sort_element(0);
		$perso->set_sort_mort(0);
		$perso->set_facteur_magie(2);
	}
	else
	{
		$perso->set_sort_vie(1);
		$perso->set_sort_element(1);
		$perso->set_sort_mort(1);
		$perso->set_facteur_magie(1);
	}
	$requete = "SELECT id FROM classe WHERE nom = '".ucwords($classe)."'";
	$req = $db->query($requete);
	$row = $db->read_assoc($req);
	$perso->set_classe($classe);
	$perso->set_classe_id($row['id']);
	$perso->set_hp(floor(sqrt($perso->get_vie()) * 75));
	$perso->set_hp_max($perso->get_hp());
	$perso->set_mp($perso->get_energie() * $G_facteur_mana);
	$perso->set_mp_max($perso->get_mp());
	$perso->set_regen_hp(time());
	$perso->set_maj_mp(time());
	$perso->set_maj_hp(time());

	$perso->set_inventaire('O:10:"inventaire":12:{s:4:"cape";N;s:5:"mains";N;s:11:"main_droite";N;s:11:"main_gauche";N;s:5:"torse";N;s:4:"tete";N;s:8:"ceinture";N;s:6:"jambes";N;s:5:"pieds";N;s:3:"dos";N;s:5:"doigt";N;s:3:"cou";N;}');
	$perso->set_quete('');
	$perso->set_pa(180);
	$perso->set_tuto(1);
	$perso->set_date_creation(time());

	$perso->set_statut('actif');
	$perso->set_dernieraction(time());
  $perso->set_id_joueur( $_SESSION['id_joueur'] );
  $perso->set_password( $joueur->get_mdp_jabber() );

	$perso->sauver();
	$jid = replace_all($perso->get_nom()).'@jabber.starshine-online.com';
	if(is_file('connect_forum.php'))
	{
		require_once('connect_forum.php');
		//Création de l'utilisateur dans le forum
		$requete = "INSERT INTO punbbusers(`group_id`, `username`, `password`, `language`, `style`, `registered`, `jabber`, `email`) VALUES('".$punbb[$race]."', '".$perso->get_nom()."', '".$joueur->get_mdp_forum()."', 'French', 'SSO', '".time()."', '$jid', '".$joueur->get_email()."')";
		$db_forum->query($requete);	
  }

  // variables de session
	$_SESSION['nom'] = $perso->get_nom();
	$_SESSION['race'] = $perso->get_race();
	$_SESSION['grade'] = $perso->get_grade();
	$_SESSION['ID'] = $perso->get_id();
	
	$txt = '<p>Vous venez de créer un '.$Gtrad[$race].' '.$classe.' du nom de '.$pseudo.'.<p>
		<p>Vous allez commencer vos aventures dans une zone tutorielle située dans votre capitale, dans celle vous pourrez non seulement vous familiariser avec l\'interface du jeu mais aussi chasser et réaliser de petites quêtes en toute sécurité. Vous pouvez sortir de cette zone à tout moment en suivant les chemins et/ou les sorties spécifiques à l\'endroit ou vous vous trouverez. Vous pourrez alors y revenir à partir de votre capitale (aller sur une case de celle-ci, cliquez sur la case où vous êtes et sur le lien qui va bien) tant que vous aurez moins de 75 en esquive.</p>
		<p>N\'hésitez pas à aller voir régulièrement les informations fournies dans votre forum de race, et à lire le message de votre roi.</p>
		<p><em>Pour accéder au jeu cliquer sur "jeu" dans la barre de navigation en haut de la page.</em></p>
		<p>Bon jeu !</p>';
	$G_interf->creer_index()->set_contenu( $texte = new interf_bal_smpl('div', $txt, 'nouv_perso') );
	/*$G_interf->creer_index()->set_contenu( $texte = new interf_bal_cont('div', 'nouv_perso') );
	$texte->add( new interf_bal_smpl('p', 'Vous venez de créer un '.$Gtrad[$race].' '.$classe.' du nom de '.$pseudo.'.') );
	$texte->add( new interf_bal_smpl('p', 'Vous allez commencer vos aventures dans une zone tutorielle située dans votre capitale, dans celle vous pourrez non seulement vous familiariser avec l\'interface du jeu mais aussi chasser et réaliser de petites quêtes en toute sécurité. Vous pouvez sortir de cette zone à tout moment en suivant les chemins et/ou les sorties spécifiques à l\'endroit ou vous vous trouverez. Vous pourrez alors y revenir à partir de votre capitale (aller sur une case de celle-ci, cliquez sur la case où vous êtes et sur le lien qui va bien) tant que vous aurez moins de 75 en esquive.') );
	$texte->add( new interf_bal_smpl('p', 'N\'hésitez pas à aller voir régulièrement les informations fournies dans votre forum de race, et à lire le message de votre roi.') );
	$texte->add( new interf_bal_smpl('p', '<em>Pour accéder au jeu cliquer sur "jeu" dans la barre de navigation en haut de la page.</em>') );
	$texte->add( new interf_bal_smpl('p', 'Bon jeu !') );*/
	exit;
case 'oubli_mdp':
	/// @todo passer à l'objet
	$nom = $_POST['nom'];
	$requete = 'SELECT * FROM joueur WHERE login = "'.$nom.'" OR pseudo = "'.$nom.'"';
	$req = $db->query($requete);
	$row = $db->read_assoc($req);
	if( $row )
		$joueur = new joueur( $row );
	else
	{
		$requete = 'SELECT id_joueur FROM perso WHERE nom = "'.$nom.'"';
		$req = $db->query($requete);
		$row = $db->read_array($req);
		if( $row )
			$joueur = new joueur( $row[0] );
		else
		{
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Identifiant inconnu !');
			$page = 'oubli_mdp';
			break;
		}
	}
	if( !$joueur->get_email() )
	{
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas défini d\'e-mail. Impossible de réinitialiser le mot de passe !');
		$page = 'oubli_mdp';
		break;
	}
	/// @todo passer à l'objet
	$requete = 'SELECT valeur FROM variable WHERE nom = "reinit mdp"';
	$req = $db->query($requete);
	$row = $db->read_array($req);
	if( $row )
		$sel = $row[0];
	else
	{
		$sel = rand();
		$db->query('INSERT INTO variable VALUES ("reinit mdp", "'.$sel.'")');
	}
	$date = time();
	$cle = md5($sel.$joueur->get_mdp().$date);
	$lien = $G_url->get( array('page'=>'reinit_mdp', 'date'=>$date, 'cle'=>$cle, 'login'=>$joueur->get_login()) );
	$sujet = 'Starshine Online - réinitialisation du mot de passe';
	$message = '<html><head><title>'.$sujet.'</title></head><body>Bonjour,<p>Vous recevez cet e-mail car vous demandé la réinitialisation de votre mot de passe sur le site <a href="'.root_url.'">Starshine Online</a>.</p><p>Pour effectuer cette réinitialisation, veuillez cliquer <a href="'.root_url.$lien.'">sur ce lien</a>. Ce lien ne sera valable que 48h.</p><p>Si vous n\'avez pas demandé la réinitialisation de votre mot de passe, merci de contacter un admininstrateur.';
	$headers  = 'MIME-Version: 1.0'."\r\n";
  $headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
	$headers .= 'From: bot@starshine-online.com';
	mail($joueur->get_email(), $sujet, $message, $headers);
	interf_alerte::enregistre(interf_alerte::msg_succes, 'L\'e-mail a été envoyé.');
	$page = 'oubli_mdp';
	break;
case 'reinit_mdp';
	$date = $_POST['date'];
	if( $date + 3600 * 48 < time() )
	{
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Lien de réinitialisation périmé, veuillez recommencer.');
		$page = 'oubli_mdp';
		break;
	}
	$requete = 'SELECT valeur FROM variable WHERE nom = "reinit mdp"';
	$req = $db->query($requete);
	$row = $db->read_array($req);
	if( md5($row[0].$joueur->get_mdp().$date) != $_POST['cle'] )
	{
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Lien invalide, veuillez recommencer.');
		$page = 'oubli_mdp';
		break;
	}
  $mdp = $_POST['password'];
  $mdp2 = $_POST['password2'];
	if( $mdp != $mdp2 )
	{
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Les mots de passes sont différents.');
		$page = 'oubli_mdp';
		break;
	}
	$joueur = joueur::create('login', $_POST['login']);
	if( !$joueur )
	{
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Identifiant inconnu.');
		$page = 'oubli_mdp';
		break;
	}
  $joueur[0]->set_mdp( md5($mdp) );
  $joueur[0]->set_mdp_forum( sha1($mdp) );
  $joueur[0]->set_mdp_jabber( md5($mdp) );
  $joueur[0]->sauver();
	break;
}

$interf_princ = $G_interf->creer_index();

switch($page)
{
case 'infos':
	$interf_princ->set_contenu( $G_interf->creer_index_infos() );
	break;
case 'captures':
	$interf_princ->set_contenu( $G_interf->creer_index_captures() );
	break;
case 'creer_compte':
	$interf_princ->set_contenu( $G_interf->creer_index_compte() );
	break;
case 'creer_perso':
	$interf_princ->set_contenu( $G_interf->creer_index_perso() );
	break;
case 'infos_perso':
	$interf_princ->add_section('infos_perso', $G_interf->creer_index_infos_perso($_GET['race'], $_GET['classe']));
	break;
case 'oubli_mdp':
	$dlg = $interf_princ->set_dialogue( new interf_dialogBS('Oubli de mot de passe', true) );
	interf_alerte::aff_enregistres($dlg);
	$dlg->add( new interf_bal_smpl('p', 'Un lien vous sera envoyé par e-mail pour réinitialiser votre mot de passe.') );
	$form = $dlg->add( new interf_form('index.php?action=oubli_mdp', 'oubli_mdp', 'post', 'form-horizontal') );
	$div_nom = $form->add( new interf_bal_cont('div', false, 'form-group') );
	$div_nom->add( new interf_bal_smpl('label', 'Nom ou login', false, 'col-sm-4 control-label') );
	$div_in_nom = $div_nom->add(new interf_bal_cont('div', false, 'col-sm-8')  );
	$nom = $div_in_nom->add( new interf_chp_form('text', 'nom', false, false, false, 'form-control') );
	$div_btn = $form->add( new interf_bal_cont('div', false, 'form-group') );
	$div_in_btn = $div_btn->add(new interf_bal_cont('div', false, 'col-sm-offset-4 col-sm-8')  );
	$btn = $div_in_btn->add( new interf_chp_form('submit', false, false, 'Envoyer', false, 'btn btn-default') );
	$btn->set_attribut('tabindex', '9');
	break;
case 'reinit_mdp';
	$princ = $interf_princ->set_contenu( new interf_bal_cont('div', 'reinit_mdp') );
	$princ->add( new interf_bal_smpl('h4', 'Réinitialisation du mot de passe') );
  $form = $princ->add( new interf_form(self::page, 'connexion', 'post', 'form-horizontal') );
	$div_mdp1 = $form->add( new interf_bal_cont('div', false, 'form-group') );
	$div_mdp1->add( new interf_bal_smpl('label', 'Indiquer un mot de passe&nbsp;:', false, 'col-sm-4 control-label') );
	$div_in_mdp1 = $div_mdp1->add(new interf_bal_cont('div', false, 'col-sm-8')  );
	$mdp1 = $div_in_mdp1->add( new interf_chp_form('password', 'password', false, false, false, 'form-control') );
	$mdp1->set_attribut('placeholder', 'mot de passe');
	$mdp1->set_attribut('tabindex', '5');
	$div_mdp2 = $form->add( new interf_bal_cont('div', false, 'form-group') );
	$div_mdp2->add( new interf_bal_smpl('label', 'Confirmer votre mot de passe&nbsp;:', false, 'col-sm-4 control-label') );
	$div_in_mdp2 = $div_mdp2->add(new interf_bal_cont('div', false, 'col-sm-8')  );
	$mdp2 = $div_in_mdp2->add( new interf_chp_form('password', 'password2', false, false, false, 'form-control') );
	$mdp2->set_attribut('placeholder', 'mot de passe');
	$mdp2->set_attribut('tabindex', '6');
	$form->add( new interf_chp_form('hidden', 'date', false, $_GET['date']) );
	$form->add( new interf_chp_form('hidden', 'cle', false, $_GET['cle']) );
	$form->add( new interf_chp_form('hidden', 'login', false, $_GET['login']) );
	$div_btn = $form->add( new interf_bal_cont('div', false, 'form-group') );
	$div_in_btn = $div_btn->add(new interf_bal_cont('div', false, 'col-sm-offset-4 col-sm-8')  );
	$btn = $div_in_btn->add( new interf_chp_form('submit', false, false, 'Réinitialiser', false, 'btn btn-default') );
	$btn->set_attribut('tabindex', '7');
	break;
}


function replace_accents($string)
{
  return str_replace( array('à','á','â','ã','ä', 'ç', 'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö', 'ù','ú','û','ü', 'ý','ÿ', 'À','Á','Â','Ã','Ä', 'Ç', 'È','É','Ê','Ë', 'Ì','Í','Î','Ï', 'Ñ', 'Ò','Ó','Ô','Õ','Ö', 'Ù','Ú','Û','Ü', 'Ý'), array('a','a','a','a','a', 'c', 'e','e','e','e', 'i','i','i','i', 'n', 'o','o','o','o','o', 'u','u','u','u', 'y','y', 'A','A','A','A','A', 'C', 'E','E','E','E', 'I','I','I','I', 'N', 'O','O','O','O','O', 'U','U','U','U', 'Y'), $string);
}

function replace_all($string)
{
  $string = str_replace(' ', '_', $string);
  return replace_accents($string);
  //return strtolower(replace_accents($string));
}
?>