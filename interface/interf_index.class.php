<?php

class interf_index extends interf_sso
{
	function __construct($theme)
	{
		parent::__construct($theme);
		
    // Barre de menu
    $joueur = joueur::factory();
    $this->menu = $this->add( new interf_navbar('', 'barre_menu', 'navbar-inverse', 'icone-sso', 'icone icone-sso', root_url.'index.php') );
    $this->menu->add_elt( new interf_elt_menu('Infos', 'index.php', 'return charger(this.href);') );
    $this->menu->add_elt( new interf_elt_menu('Captures d\'écran', 'index.php', 'return charger(this.href);') );
    
    
    $this->menu->add_elt(new interf_elt_menu('Aide', 'http://wiki.starshine-online.com/'), false);
    $forum = $this->menu->add_elt(new interf_elt_menu('Forum', 'http://forum.starshine-online.com/'), false);
    if( $joueur )
    {
	    $nbr_posts = get_nbr_posts_forum(joueur::get_perso());
	    $forum->get_lien()->add( new interf_bal_smpl('span', $nbr_posts ? $nbr_posts : '', 'nbr_posts', 'badge') );
		}
		else
    	$this->menu->add_elt(new interf_elt_menu('Créer un compte', ''), false);
	}
}


?>