<?php
/**
 * @file actions.php
 * Gestion des scripts
 */  
if (file_exists('root.php'))
  include_once('root.php');

//Inclusion des fichiers indispensables
include_once(root.'inc/fp.php');

//Récupération des informations du personnage
$perso = joueur::get_perso();
$perso->check_perso();


$interf_princ = $G_interf->creer_jeu();

$action = array_key_exists('action', $_GET) ? $_GET['action'] : null;
if( $pet = array_key_exists('id', $_GET) )
	$id = $_GET['id'];
else if( $pet = array_key_exists('creature', $_GET) )
	$id = $_GET['creature'];

if($pet && $action != 'perso')
{
	$entite = new pet($id);
	$G_url->add('id', $id);
}
else
	$entite = &$perso;
if( array_key_exists('script', $_GET) )
{
	if( $pet )
		$script = new action_pet($_GET['script']);
	else
		$script = new action_perso($_GET['script']);
	$script->decode();
}
else
	$script = null;
	
if( array_key_exists('ajax', $_GET) && $_GET['ajax'] == 2 )
{
	$interf_princ->add( $G_interf->creer_liste_scripts($entite) );
	exit;
}


$change_script = false;
switch($action)
{
case 'change_ordre':
	$script->change_position($_GET['ligne'], $_GET['pos']);
	$change_script = true;
	break;
case 'ajout_cond':
	$script->ajout_condition($_GET['ligne']);
	$change_script = true;
	break;
case 'suppr_cond':
	$script->suppr_condition($_GET['ligne'], $_GET['cond']);
	$change_script = true;
	break;
case 'copie_action':
	$script->copie_action($_GET['ligne']);
	$change_script = true;
	break;
case 'suppr_action':
	$script->suppr_action($_GET['ligne']);
	$change_script = true;
	break;
case 'change_cond':
	$script->change_condition($_GET['ligne'], $_GET['cond'], $_GET['valeur']);
	$change_script = true;
	break;
case 'change_op':
	$ops = array('p'=>'<', 'e'=>'=', 'g'=>'>');
	$script->change_operateur($_GET['ligne'], $_GET['cond'], $ops[$_GET['valeur']]);
	$change_script = true;
	break;
case 'change_param':
	$script->change_parametre($_GET['ligne'], $_GET['cond'], $_GET['valeur']);
	$change_script = true;
	break;
case 'modif_action':
	$script->modifie_action($_GET['ligne'], $_GET['type'], $_GET['valeur']);
	$change_script = true;
	break;
case 'ajout_action':
	$script->ajout_action( action::creer($_GET['type'], $_GET['valeur'], true) );
	$change_script = true;
	break;
case 'modifier_nom':
	$script->set_nom($_GET['nom']);
	$change_script = true;
	break;
case 'attaque':
	$id_script = $script ? $script->get_id() : $_GET['id_script'];
	$entite->set_action_a($id_script);
	$entite->sauver();
	break;
case 'defense':
	$id_script = $script ? $script->get_id() : $_GET['id_script'];
	$entite->set_action_d($id_script);
	$entite->sauver();
	break;
case 'supprimer':
	$script->supprimer();
	unset($script);
	break;
case 'copier':
	$script->set_id(0);
	$script->set_nom( 'Copie de '.$script->get_nom() );
	$script->sauver();
	break;
case 'nouveau':
	if( $pet )
		$script = new action_pet('Nouveau script', $_GET['type'], $perso->get_id(), $entite->get_id_monstre());
	else
		$script = new action_perso('Nouveau script', $_GET['type'], $perso->get_id());
	if( $_GET['type'] == 's' )
	{
		$rounds = $G_round_total;
		/// @todo à améliorer
		if( $perso->get_race() == 'orc' )
			$rounds++;
		$script->init_simple($rounds);
		$change_script = true;
	}
	else
		$script->sauver();
	break;
case 'transf_avance':
	$script->set_mode( 'a' );
	$script->sauver();
	break;
}

if( $change_script )
{
	$script->encode();
	$script->sauver();
}

$cadre = $interf_princ->set_droite( $G_interf->creer_droite('Scripts de combat') );
if( $script )
{
	$cadre->add( $G_interf->creer_script($script, $entite) );
}
else
{
	//$cadre->add( new interf_bal_smpl('p', 'Voici l\'interface du script de combat, grâce à celui-ci vous pourrez attaquer avec des sorts ou des compétences.') );
	$onglets = $cadre->add( new interf_onglets('ongl_scripts', 'scripts') );
	$url = $G_url->copie('ajax', 2);
	$onglets->add_onglet('Perso', $url->get('action', 'perso'), 'ongl_perso', 'invent', !$id);
	$pets = $perso->get_pets(true);
	foreach($pets as $pet)
	{
		$onglets->add_onglet($pet->get_nom(), $url->get(array('id'=>$pet->get_id(), 'ajax'=>2)), 'ongl_'.$pet->get_id(), 'invent', $id==$pet->get_id());
	}
	if( $id )
		$onglets->get_onglet('ongl_'.$id)->add( $G_interf->creer_liste_scripts($entite) );
	else
		$onglets->get_onglet('ongl_perso')->add( $G_interf->creer_liste_scripts($entite) );
	/*$aide = $cadre->add( new interf_bal_cont('div') );
	$aide->add( new interf_bal_smpl('h4', 'Aide') );
	$aide->add( new interf_bal_smpl('p', 'Une attaque sur un monstre ou un joueur se fait généralement en 10 rounds (11 si l\'un des deux est un Orc, 9 si l\'attaquant a le buff Sacrifice sur lui). Vous pouvez paramétrer les 10 actions que vous allez faire dans le script de combat, afin de les réaliser à chaque attaque. Il est donc conseillé de créer un script d\'attaque, et de créer vos 10 actions en ajoutant les compétences que vous voulez utiliser. Vous pouvez aussi créer un script de défense qui s\'exécutera automatiquement si vous êtes attaqué par d\'autres joueurs. (les compétences que vous pourrez utiliser dans votre script sont limitées par votre réserve de mana)') );
	$aide->add( new interf_lien('Pour avoir plus d\'informations sur le script de combat', 'http://wiki.starshine-online.com/doku.php?id=combat:scripts') );*/
}
$interf_princ->maj_tooltips();

