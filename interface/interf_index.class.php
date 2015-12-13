<?php
include_once(root.'fonction/forum.inc.php');

class interf_index extends interf_sso
{
  const page = 'index.php';
  protected $contenu;
  const droite_prem = true;
	function __construct($theme)
	{
		global $estConnexionReussie, $erreur_login, $G_url;
		parent::__construct($theme);
    $this->meta('viewport', 'width=device-width, initial-scale=1.0');
    $this->css(static::prefixe_fichiers.'css/site.css');
    $this->css(static::prefixe_fichiers.'css/site-'.$theme.'.css');
    $this->css(static::prefixe_fichiers.'css/jquery.lightbox-0.5.css');
    $this->javascript(static::prefixe_fichiers.'javascript/jquery/jquery.lightbox-0.5.min.js', true);
		
    // Barre de menu
    $joueur = joueur::factory();
    $this->menu->add_elt( new interf_elt_menu('Infos', self::page.'?page=infos', 'return charger(this.href);') );
    $this->menu->add_elt( new interf_elt_menu('Captures d\'écran', self::page.'?page=captures', 'return charger(this.href);') );
    //$this->menu->add_elt( new interf_elt_menu('Charte', self::page.'page=charte', 'return charger(this.href);') );
    $autres = $this->menu->add_elt( new interf_nav_deroul('Autres') );
    $autres->add( new interf_elt_menu('Histoire de Starshine', 'background.php', 'return charger(this.href);') );
    $autres->add( new interf_elt_menu('Bestiaire', 'liste_monstre.php', 'return charger(this.href);') );
    $autres->add( new interf_elt_menu('Statistiques', 'stats2.php?graph=carte_royaume.php', 'return charger(this.href);') );
    $autres->add( new interf_elt_menu('Classement', 'classement.php', 'return charger(this.href);') );
    $autres->add( new interf_elt_menu('Diplomatie', 'diplomatie.php', 'return charger(this.href);') );
    //$autres->add( new interf_elt_menu('Cartes', 'carte.php', 'return charger(this.href);') );
    
    
    if( $joueur )
    {
	    $nbr_perso = count( perso::create('id_joueur', $joueur->get_id()) );
	    if( $nbr_perso )
    		$this->menu->add_elt(new interf_elt_menu('Jeu', 'interface.php', false, 'lien_jeu'), false);
		}
    if( $joueur )
    {
	    $this->menu->add_elt(new interf_elt_menu('Aide', 'http://wiki.starshine-online.com/'), false);
	    $forum = $this->menu->add_elt(new interf_elt_menu('Forum', 'http://forum.starshine-online.com/'), false);
	    if( joueur::get_perso() )
	    {
		    $nbr_posts = get_nbr_posts_forum(joueur::get_perso());
		    $forum->get_lien()->add( new interf_bal_smpl('span', $nbr_posts ? $nbr_posts : '', 'nbr_posts', 'badge') );
			}
	    /// @todo à améliorer
	    //$persos = (array_key_exists('nbr_perso', $_SESSION) && $_SESSION['nbr_perso'] > 1) || $joueur->get_droits() & joueur::droit_pnj;
	    if( $nbr_perso < $G_nbr_max_persos || joueur::factory()->get_droits() & joueur::droit_staf )
    		$this->menu->add_elt(new interf_elt_menu('Créer un personnage', self::page.'?page=creer_perso', 'return charger(this.href);'/*, null, 'navbar-right'*/), false);
    	$this->aff_menu_joueur();
		}
		else
		{
    	$connex = $this->menu->add_elt(new interf_menu_div('Connexion', 'menu_connexion', 'navbar-right min-exp'), false);
    	$form = $connex->add( new interf_form(self::page, 'connexion', 'post', 'form-horizontal') );
    	$div_nom = $form->add( new interf_bal_cont('div', false, 'form-group') );
    	$nom = $div_nom->add( new interf_chp_form('text', 'nom', false, false, 'connex_nom', 'form-control') );
			$nom->set_attribut('placeholder', 'identifiant');
			$nom->set_attribut('tabindex', '1');
    	$div_mdp = $form->add( new interf_bal_cont('div', false, 'form-group') );
    	$mdp = $div_mdp->add( new interf_chp_form('password', 'password', false, false, false, 'form-control') );
			$mdp->set_attribut('placeholder', 'mot de passe');
			$mdp->set_attribut('tabindex', '2');
    	$div_auto = $form->add( new interf_bal_cont('div', false, 'form-group') );
    	$div_auto2 = $div_auto->add( new interf_bal_cont('div', false, 'checkbox') );
    	$auto = $div_auto2->add( new interf_chp_form('checkbox', 'auto_login', false, 'Ok') );
			$auto->set_attribut('tabindex', '3');
			$div_auto2->add( new interf_bal_smpl('label', 'Connexion automatique') );
    	$div_btn = $form->add( new interf_bal_cont('div', 'btn-connex', 'form-group') );
    	$btn = $div_btn->add( new interf_chp_form('submit', false, false, 'Connexion', false, 'btn btn-default') );
			$btn->set_attribut('tabindex', '4');
			$form->add( new interf_chp_form('hidden', 'log') );
			$script = $connex->add( new interf_bal_smpl('script', '') );
			$script->set_attribut('type', 'text/javascript');
			$script->set_attribut('src', 'javascript/emp/emp-min.js');
			$liens = $connex->add( new interf_bal_cont('div', 'liens_connexion') );
			$liens->add( new interf_lien('Mot de passe oublié&nbsp;?', self::page.'?page=oubli_mdp') );
			$div_autres = $connex->add( new interf_bal_cont('div', 'connex-autres', 'btn-group') );
			$btn_autres = $div_autres->add( new interf_bal_smpl('button', 'Autres <span class="caret"></span>', false, 'btn btn-default dropdown-toggle') );
			$btn_autres->set_attribut('type', 'button');
			$btn_autres->set_attribut('data-toggle', 'dropdown');
			$btn_autres->set_attribut('aria-haspopup', 'true');
			$btn_autres->set_attribut('aria-expanded', 'false');
			$ul_autres = $div_autres->add( new interf_bal_cont('ul', false, 'dropdown-menu') );
			$ul_autres->add( new interf_elt_menu('Mot de passe oublié&nbsp;?', self::page.'?page=creer_perso', 'return charger(this.href);') );
			//self::code_js('$(".dropdown input, .dropdown label").click(function(e) { e.stopPropagation();});');
    	$this->menu->add_elt(new interf_elt_menu('Créer un compte', self::page.'?page=creer_compte', 'return charger(this.href);', false, 'navbar-right'), false);
    	self::code_js('document.getElementById("connex_nom").focus();');
	    $this->menu->add_elt(new interf_elt_menu('Aide', 'http://wiki.starshine-online.com/'), false);
	    $forum = $this->menu->add_elt(new interf_elt_menu('Forum', 'http://forum.starshine-online.com/'), false);
		}
		
		$a_pub = file_exists(root.'pub.php');
		$main = $this->add( new interf_bal_cont('main', 'princ', $a_pub?false:'plein') );
		$this->contenu = $main->add( new interf_bal_cont('section', 'contenu') );
		
		// pub
	  if( $a_pub )
	  {
	  	$pub = $this->add( new interf_bal_smpl('iframe', '', 'pub') );
	  	$pub->set_attribut('src', 'pub.php');
	  	$pub->set_attribut('scrolling', 'no');
		}
		// erreur de connexion
		if( $estConnexionReussie === false )
		{
			$dlg = $this->set_dialogue( new interf_dialogBS('Erreur', true) );
			$dlg->add( new interf_alerte(interf_alerte::msg_erreur, false, false, $erreur_login) );
			$dlg->ajout_btn('Ok', 'ferme');
		}
		// Partenaires
		$part = $this->add( new interf_bal_cont('div', 'partenaires') );
		$part->add( new interf_bal_smpl('span', 'Partenaires :') );
		$lien1 = $part->add( new interf_bal_smpl('a', 'Jeux sur navigateur en ligne') );
		$lien1->set_attribut('href', 'http://www.jeux-sociaux.org/navigateur/');
		
    $this->aff_fin();
	}
	
  function set_dialogue($fils)
  {
  	$dlg = $this->add( new interf_bal_cont('div', 'modal', 'modal fade') );
  	$dlg->set_attribut('role', 'dialog');
  	$dlg->set_attribut('tabindex', '-1');
  	$dlg->set_attribut('aria-labelledby', 'modalLabel');
  	$dlg->code_js('$("#modal").modal("show");');
  	return $dlg->add( $fils );
  }
	
	function set_contenu($fils)
	{
		return $this->contenu->add( $fils );
	}
	
	function add_section($id, $fils)
	{
		global $G_interf;
		switch($id)
		{
		case 'infos_perso':
			$this->set_contenu( $G_interf->creer_index_perso($fils) );
			break;
		default:
			log_admin::log('erreur', 'Section inconnue : '.$id);
			return null;
		}
	}
	
  function affiche($tab = 0)
	{
		global $G_interf;
		// On remplie les parties gauche et droites si elles sont vides
		if( !$this->contenu->get_fils() )
			$this->contenu->add( $G_interf->creer_index_infos() );
		parent::affiche($tab);
	}
}

class interf_index_infos extends interf_cont
{
	function __construct()
	{
		$this->add( new interf_bal_smpl('span', false, false, 'logo') );
		$pres = $this->add( new interf_bal_cont('div', 'presentation') );
		$pres->add( new interf_bal_smpl('h4', 'Présentation') );
		$txt = "Bienvenue dans le monde de Starshine-Online.
Pour l'instant au stade de la bêta (c'est à dire en phase d'équilibrage et d'amélioration du monde), Starshine-Online sera un jeu de rôle massivement multijoueur (MMORPG) en tour par tour.<br />
<br />
Il vous permettra d'incarner un grand héros de l'univers Starshine, peuplé de nombreuses créatures et d'autres héros ennemis prêts à tout pour détruire votre peuple.<br />
<br /><br />
Il est recommandé d'utiliser <strong>un navigateur dernière génération (évitez Internet Explorer)</strong> pour jouer à Starshine, nous vous conseillons <a href='http://www.mozilla-europe.org/'>Firefox</a> ou bien <a href='http://www.google.fr/chrome'>Google Chrome</a>.
N'oubliez pas de reporter les bugs et problèmes, et d'apporter vos suggestions sur le forum.";
		$pres->add( new interf_bal_smpl('p', $txt) );
		
		$ancienne = annonce::derniere(10);
		if( $ancienne )
		{
			$nouv = $this->add( new interf_bal_cont('div', 'nouvelles') );
			$nouv->add( new interf_bal_smpl('h4', 'Nouveautés') );
			foreach($ancienne as $a)
			{
				$art = $nouv->add( new interf_bal_cont('article') );
				$art->add( new interf_bal_smpl('span', date('d/m/Y H:i', strtotime($a->get_date())), false, 'xsmall') );
				$art->add( new interf_bal_smpl('p', texte::parse_url($a->get_message())) );
			}
		}
	}
}

class interf_index_captures extends interf_bal_cont
{
	function __construct()
	{
		parent::__construct('div', 'captures'/*, 'row'*/);
		$minis = glob(root.'/image/screenshots/*-mini.png');
		foreach($minis as $m)
		{
			$p = pathinfo($m);
			$titre = substr($p['filename'], 0, strlen($p['filename'])-5);
			$div = $this->add( new interf_bal_cont('div', false, 'col-sm-6 col-md-4') );
			$lien = $div->add( new interf_bal_cont('a', false, 'thumbnail') );
			$lien->set_attribut('href', 'image/screenshots/'.$titre.'.png');
			$lien->set_attribut('title', $titre);
			$img = $lien->add( new interf_img('image/screenshots/'.$p['basename'], $titre) );
			$leg = $lien->add( new interf_bal_smpl('div', $titre, false, 'caption') );
		}
		self::code_js('$(function()
			{
				$("#captures a").lightBox({
					fixedNavigation:true,
					overlayBgColor: "#000000",
					overlayOpacity: 0.6,
					imageLoading: "image/jquery-lightbox/loading.gif",
					imageBtnClose: "image/jquery-lightbox/btn-close.gif",
					imageBtnPrev: "image/jquery-lightbox/btn-prev.gif",
					imageBtnNext: "image/jquery-lightbox/btn-next.gif",
					imageBlank: "image/jquery-lightbox/blank.gif",
					containerResizeSpeed: 350,
					txtImage: "Image",
					txtOf: "sur"
				});
			});');
	}
}

class interf_index_compte extends interf_bal_cont
{
	function __construct()
	{
		global $G_url;
		parent::__construct('div', false, 'container-fluid');
		$div_row0 = $this->add( new interf_bal_cont('div', false, 'row') );
		$div_row0->add( new interf_bal_smpl('h3', 'Création du compte joueur', false, 'row') );
		interf_alerte::aff_enregistres($div_row0);
		$div_row1 = $this->add( new interf_bal_cont('div', false, 'row') );
		$div_form = $div_row1->add( new interf_bal_cont('div', false, 'col-md-8') );
  	$form = $div_form->add( new interf_form($G_url->get('action', 'creer_joueur'), 'creer_compte', 'post', 'form-horizontal') );
  	$div_nom = $form->add( new interf_bal_cont('div', false, 'form-group') );
  	$div_nom->add( new interf_bal_smpl('label', 'Quel sera votre nom&nbsp;?', false, 'col-sm-4 control-label') );
  	$div_in_nom = $div_nom->add(new interf_bal_cont('div', false, 'col-sm-8')  );
  	$nom = $div_in_nom->add( new interf_chp_form('text', 'nom', false, false, false, 'form-control') );
		$nom->set_attribut('placeholder', 'nom');
		$nom->set_attribut('tabindex', '5');
  	$div_mdp1 = $form->add( new interf_bal_cont('div', false, 'form-group') );
  	$div_mdp1->add( new interf_bal_smpl('label', 'Indiquer un mot de passe&nbsp;:', false, 'col-sm-4 control-label') );
  	$div_in_mdp1 = $div_mdp1->add(new interf_bal_cont('div', false, 'col-sm-8')  );
  	$mdp1 = $div_in_mdp1->add( new interf_chp_form('password', 'password', false, false, false, 'form-control') );
		$mdp1->set_attribut('placeholder', 'mot de passe');
		$mdp1->set_attribut('tabindex', '6');
  	$div_mdp2 = $form->add( new interf_bal_cont('div', false, 'form-group') );
  	$div_mdp2->add( new interf_bal_smpl('label', 'Confirmer votre mot de passe&nbsp;:', false, 'col-sm-4 control-label') );
  	$div_in_mdp2 = $div_mdp2->add(new interf_bal_cont('div', false, 'col-sm-8')  );
  	$mdp2 = $div_in_mdp2->add( new interf_chp_form('password', 'password2', false, false, false, 'form-control') );
		$mdp2->set_attribut('placeholder', 'mot de passe');
		$mdp2->set_attribut('tabindex', '7');
  	$div_email = $form->add( new interf_bal_cont('div', false, 'form-group') );
  	$div_email->add( new interf_bal_smpl('label', 'Indiquer un email&nbsp;:', false, 'col-sm-4 control-label') );
  	$div_in_email = $div_email->add(new interf_bal_cont('div', false, 'col-sm-8')  );
  	$email = $div_in_email->add( new interf_chp_form('email', 'email', false, false, false, 'form-control') );
		$email->set_attribut('placeholder', 'email');
		$email->set_attribut('tabindex', '8');
		$div_in_email->add( new interf_bal_smpl('span', '(facultatif, mais indispensable en cas de perte mot de passe)', false, 'small') );
  	$div_btn = $form->add( new interf_bal_cont('div', false, 'form-group') );
  	$div_in_btn = $div_btn->add(new interf_bal_cont('div', false, 'col-sm-offset-4 col-sm-8')  );
  	$btn = $div_in_btn->add( new interf_chp_form('submit', false, false, 'Créer', false, 'btn btn-default') );
		$btn->set_attribut('tabindex', '9');
		
		$div_regles = $div_row1->add( new interf_bal_cont('div', false, 'col-md-4') );
		$txt = 'Ce compte vous identifie en tant que joueur et est différent de votre personnage, vous le garderez pour les différentes parties, y compris lorsque vous changez de personnage.
        <ul>
          <li>Il est unique : il est interdit de posséder plusieurs compte joueur. Tout changement sans accord des administrateurs sera considérés comme du mutlicompte (si vous voulez changer de personnage, il vous suffit de le supprimer pour pouvoir en créer un nouveau).</li>
          <li>Il est personnel : il est interdit de prêter ou donner un compte joueur à quelqu\'un d\'autre.</li>
        </ul>
        Tout non respect des règles pourra entrainer un bannissement temporaire ou définitif du compte.';
		$div_regles->add( new interf_bal_smpl('span', $txt) );
		$this->add( new interf_bal_smpl('div', 'NB : Vous pourrez donner à votre personnage le même nom que celui utilisé pour votre compte.', false, 'row') );
	}
}

class interf_index_perso extends interf_bal_cont
{
	function __construct($infos=null)
	{
		global $db, $Gtrad, $G_url;
		parent::__construct('div', false, 'container-fluid');
		$div_row0 = $this->add( new interf_bal_cont('div', false, 'row') );
		$div_row0->add( new interf_bal_smpl('h3', 'Création du pesonnage', false, 'row') );
		interf_alerte::aff_enregistres($div_row0);
		$div_row1 = $this->add( new interf_bal_cont('div', false, 'row') );
		$div_form = $div_row1->add( new interf_bal_cont('div', false, 'col-sm-6') );
  	$form = $div_form->add( new interf_form($G_url->get('action', 'creer_perso'), 'creer_perso', 'post') );
  	$div_nom = $form->add( new interf_bal_cont('div', false, 'form-group') );
  	$div_nom->add( new interf_bal_smpl('label', 'Quel sera votre nom&nbsp;?', false, 'control-label') );
  	$nom = $div_nom->add( new interf_chp_form('text', 'nom', false, false, false, 'form-control') );
		$nom->set_attribut('placeholder', 'nom');
		$nom->set_attribut('tabindex', '5');
		$div_nom->add( new interf_bal_smpl('span', '(peut etre identique au nom de votre compte joueur)', false, 'small') );
  	$div_btn = $form->add( new interf_bal_cont('div', false, 'form-group') );
  	$btn = $div_btn->add( new interf_chp_form('submit', false, false, 'Créer', false, 'btn btn-default') );
		$btn->set_attribut('tabindex', '6');
		$form->add( new interf_chp_form('hidden', 'race', false, false, 'race') );
		$form->add( new interf_chp_form('hidden', 'classe', false, false, 'classe') );
		$div_races = $div_row1->add( new interf_bal_cont('div', 'races', 'col-sm-6') );
		/// @todo passer à l'objet
		$requete = 'SELECT race FROM royaume WHERE race != "" ORDER BY star_nouveau_joueur DESC, race ASC';
		$req = $db->query($requete);
		$i=0;
		while($row = $db->read_object($req))
		{
			$div = $div_races->add( new interf_bal_cont('div', $row->race, 'race') );
			$guerrier = $div->add( new interf_lien('', $G_url->get( array('page'=>'infos_perso', 'race'=>$row->race, 'classe'=>'combattant') ), $row->race.'_guerrier', $row->race.'_guerrier') );
			$guerrier->set_tooltip($Gtrad[$row->race].' Combattant');
			$mage = $div->add( new interf_lien('', $G_url->get( array('page'=>'infos_perso', 'race'=>$row->race, 'classe'=>'magicien') ), $row->race.'_mage', $row->race.'_mage') );
			$mage->set_tooltip($Gtrad[$row->race].' Magicien');
		}
		$inf = $this->add( new interf_bal_cont('div', 'infos_perso', 'row') );
		/*$div_row2->add( new interf_bal_cont('div', 'description', 'col-md-6') );
		$div_row2->add( new interf_bal_cont('div', 'propagande', 'col-md-6') );*/
		if($infos)
			$inf->add($infos);
		$texte = 'Avant de créer un personnage, vous pouvez consulter <a href="http://wiki.starshine-online.com">l\'aide de jeu</a>, pour mieux choisir votre personnage.<br />
  			N\'hésitez pas à faire le tour des races pour en voir toutes les différences, et à passer votre curseur sur les attributs (force, dextérité, etc) pour avoir des détails sur leur fonctionnement.<br />
  			Pour un équilibrage du jeu, les peuples ayant le moins de joueurs recoivent plus de stars à la création du personnage.<br />
  			<strong>Un compte sur le forum sera créé automatiquement avec vos informations du jeu.</strong>';
		$this->add( new interf_bal_smpl('div', $texte, false, 'row') );
	}
}

class interf_index_infos_perso extends interf_cont
{
	function __construct($race, $classe)
	{
		global $Gtrad, $Trace, $db;
		
		if ($classe == 'combattant')
		{
			$Trace[$race]['vie']++;
			$Trace[$race]['force']++;
			$Trace[$race]['dexterite']++;
		}
		else
		{
			$Trace[$race]['puissance']++;
			$Trace[$race]['volonte']++;	
			$Trace[$race]['energie']++;
		}
		
		$div_descr = $this->add( new interf_bal_cont('div', false, 'col-sm-6') );
		$descr = $div_descr->add( new interf_bal_cont('div', 'description') );
		$descr->add( new interf_bal_smpl('h3', $Gtrad[$race].' - '.$classe) );
		$descr->add( new interf_bal_smpl('strong', 'Stars au début du jeu : ') );
		/// @todo passer à l'objet
		$requete = "SELECT star_nouveau_joueur FROM royaume WHERE ID = ".$Trace[$race]['numrace'];
		$req = $db->query($requete);
		$row = $db->read_row($req);
		$descr->add( new interf_bal_smpl('span', $row[0], 'stars') );
		$descr->add( new interf_bal_smpl('br') );
		$descr->add( new interf_bal_smpl('strong', 'Passif : ') );
		$descr->add( new interf_bal_smpl('span', $Trace[$race]['passif'], 'passif') );
		$descr->add( new interf_bal_smpl('br') );
		$descr->add( new interf_bal_smpl('strong', 'Caractéristiques :') );
		$dl = $descr->add( new interf_descr() );
		$dl->nouv_elt('Constitution', $Trace[$race]['vie'], 'Caractérise vos points de vie.');
		$dl->nouv_elt('Force', $Trace[$race]['force'], 'Augmente vos dégâts physiques, permet de porter de plus grosses armes ou armures.');
		$dl->nouv_elt('Dextérité', $Trace[$race]['dexterite'], 'Augmente vos chances de toucher, d\'esquiver et de porter des coups critiques.');
		$dl->nouv_elt('Puissance', $Trace[$race]['puissance'], 'Augmente vos dégâts magiques.');
		$dl->nouv_elt('Volonté', $Trace[$race]['volonte'], 'Augmente vos chances de lancer un sort, d\'esquiver un sort, ou de toucher une cible avec un sort.');
		$dl->nouv_elt('Énergie', $Trace[$race]['energie'], 'Caractérise vos points de mana.');
		$descr->add( new interf_bal_smpl('strong', 'Affinités magiques :') );
		$dl2 = $descr->add( new interf_descr() );
		$dl2->nouv_elt('Magie de la Vie', $Gtrad['affinite'.$Trace[$race]['affinite_sort_vie']]);
		$dl2->nouv_elt('Magie de la Mort', $Gtrad['affinite'.$Trace[$race]['affinite_sort_mort']]);
		$dl2->nouv_elt('Magie Élémentaire', $Gtrad['affinite'.$Trace[$race]['affinite_sort_element']]);
		
		$div_propa = $this->add( new interf_bal_cont('div', false, 'col-sm-6') );
		$propa = $div_propa->add( new interf_bal_cont('div', 'propagande') );
		$propa->add( new interf_bal_smpl('h3', 'Propagande royale') );
		/// @todo passer à l'objet
		$requete = "SELECT propagande FROM motk WHERE id_royaume = ".$Trace[$race]['numrace'];
		$req = $db->query($requete);
		$row = $db->read_row($req);
		$texte = new texte($row[0], texte::msg_propagande);
		$propa->add( new interf_bal_smpl('p', $texte->parse()) );
		
		self::code_js('$("#race").attr("value", "'.$race.'");');
		self::code_js('$("#classe").attr("value", "'.$classe.'");');
	}
}


?>
