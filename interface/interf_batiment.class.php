<?php
/**
 * @file interf_batiment.class.php
 * Permet l'accès aux bâtiments externes.
 */

class interf_batiment extends interf_gauche
{
	protected $batiment;
	function __construct(&$construction/*, $url*/)
	{
		parent::__construct('carte');
		$this->batiment = &$construction->get_def();
		
		// Icone,titre & jauge extérieure
		$icone = $this->set_img_centre($construction->get_image()/*, $url.'?id_construction='.$construction->get_id(), $this->batiment->get_nom()*/);
		$icone->set_tooltip( $this->batiment->get_nom() );
		$this->barre_haut->add( new interf_txt($construction->get_nom()) );
		$this->set_jauge_ext($construction->get_hp(), $this->batiment->get_hp(), 'hp', 'HP : ');
	}
}

class interf_bourg_fort extends interf_batiment
{
	function __construct(&$construction/*, $url*/)
	{
		parent::__construct($construction/*, $url*/);
		$type = $this->batiment->get_type();
		/// @todo à améliorer
		$bats = batiment::create('type', $type, 'cout ASC');
		$i=0;
		foreach($bats as $b)
		{
			$i++;
			if( $b->get_id() == $this->batiment->get_id() )
				$niv = $i;
		}
		$this->set_jauge_int($niv, count($bats), 'avance', 'Niveau : ');
		
		
		// Cadre principal
		$cadre = $this->centre->add( new interf_bal_cont('div', $this->batiment->get_type()) );
		$div = $cadre->add( new interf_panneau(false, false, false, false, false, 'default', false) );
		$menu = $div->add( new interf_bal_cont('div', false, 'list-group') );
		//  Liens vers les bâtiments
		if($this->batiment->has_bonus('taverne'))
		{
			$menu->add(  new interf_lien('Taverne', 'taverne.php', false, 'list-group-item') );
		}
		if($this->batiment->has_bonus('quete'))
			$menu->add(  new interf_lien('Bureau des quêtes', 'bureau_quete.php', false, 'list-group-item') );
		if($this->batiment->has_bonus('ecurie'))
		{
			$menu->add(  new interf_lien('Ecurie', 'ecurie.php', false, 'list-group-item') );
		}
		if($this->batiment->has_bonus('teleport'))
		{
			$menu->add(  new interf_lien('Pierre de téléportation', 'teleport.php', false, 'list-group-item') );
		}
		if($this->batiment->has_bonus('alchimiste'))
		{
			$menu->add(  new interf_lien('Alchimiste', 'alchimiste.php', false, 'list-group-item') );
		}
	}
}
?>