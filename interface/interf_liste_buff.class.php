<?php
class interf_liste_buff extends interf_bal_cont
{
	const buffs = 1;
	const debuffs = 2;
	const tous = 3;
  function __construct(&$perso, $debuffs=false, $aff_vide=false)
  {
    global $db;
    interf_bal_cont::__construct('ul');
    $buffs = $perso->get_buff();
		if(is_array($buffs))
		{
			foreach($buffs as $buff)
			{//-- Listing des buffs
				if($buff->get_debuff() == $debuffs) // ($buff->get_debuff() && $type & self::debuffs) || (!$buff->get_debuff() && $type & self::buffs)
				{
          $li = $this->add( new interf_bal_cont('li', null, 'buff') );
          $img = $li->add( new interf_bal_smpl('img') );
          $fich = 'image/buff/'.$buff->get_type().'_p.png';
          if( !file_exists($fich) )
          	$fich = 'image/buff/'.($buff->get_debuff()?'debuff':'buff').'.png';
          $img->set_attribut('src', $fich);
          $img->set_attribut('alt', $buff->get_nom());
          $li->set_attribut('data-duree', $buff->get_duree());
          $li->set_attribut('data-fin', $buff->get_fin());
          $li->set_attribut('data-description', $buff->get_description());
          if( !$buff->get_debuff() )
					{
          	$li->set_attribut('data-suppr', $buff->get_id());
          	$li->set_attribut('ondblclick', 'suppr_buff(this);');
					}       
          $this->creer_buff_duree($li, $buff);
				}
			}
    }
    if( !$debuffs && $aff_vide )
    {
      $grade = $perso->get_grade();
      $case_buff_dispo = $grade->get_nb_buff() - $perso->get_nb_buff();
			for($b = 0; $b < $case_buff_dispo; $b++)
			{
        $li = $this->add( new interf_bal_smpl('li', '&nbsp;', null, 'buff_dispo icone icone-cadenas-ouvert') );
        $li->set_tooltip('Vous pouvez encore recevoir '.$case_buff_dispo.' buffs.', 'bottom');
			}
      if( $grade->get_nb_buff(true) < 10 )
  		{
        $grades = grade::create(null, null, 'rang ASC', false, 'rang > '.$grade->get_rang());
        foreach( $grades as $g )
        {
          $texte = 'Il faut être '.strtolower($g->get_nom()).' pour avoir cette case';
          if( $g->get_honneur() > 0 )
          {
            if( $g->get_honneur() >= $perso->get_honneur() )
              $texte .= ' (gardez votre honneur et vous l\'aurez bientôt)';
            else
              $texte .= ' (encore '.($g->get_honneur() - $perso->get_honneur()).')';
          }
          $li = $this->add( new interf_bal_smpl('li', '&nbsp;', null, 'buff_nondispo icone icone-cadenas-ferme') );
          $li->set_tooltip($texte.'.', 'bottom');
        }
  		}
    }
    //$this->code_js("init_buffs_infos();");
  }
  protected function creer_buff_duree(&$elt, &$buff)
  {
    $ratio = floor(100 * (($buff->get_fin() - time()) / ($buff->get_duree())));
    $pere = $elt->add( new interf_bal_cont('div', null, 'progress barre_buff jauge_buff') );
    $fils = $pere->add( new interf_bal_cont('div', null, 'progress-bar progress-bar-info') );
    $fils->set_attribut('style', 'width:'.$ratio);
  }
}



/**
 * Interface pour afficher les information sur un buff
 */
class interf_infos_buff extends interf_infos_popover
{
  /**
   * Constructeur
   * @param $buff    $buff dont veut afficher les informations
   */
  function __construct($buff)
  {
    parent::__construct();
    $this->nouv_info('Description', $buff->get_description());
    $this->nouv_info('Durée totale',transform_sec_temp($buff->get_duree()));
    $this->nouv_info('Durée restante',transform_sec_temp($buff->get_fin()-time()));
    $this->nouv_info('Fin', formate_date($buff->get_fin()));
  }
}
?>