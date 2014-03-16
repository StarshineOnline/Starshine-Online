<?php
class interf_liste_buff extends interf_bal_cont
{
  function __construct(&$perso, $debuffs=false, $aff_vide=false)
  {
    global $db;
    interf_bal_cont::__construct('ul');
    $buffs = $perso->get_buff();
		if(is_array($buffs))
		{
			foreach($buffs as $buff)
			{//-- Listing des buffs
				if($buff->get_debuff() == $debuffs)
				{
          $li = $this->add( new interf_bal_cont('li', null, 'buff') );
          $img = $li->add( new interf_bal_smpl('img') );
          $img->set_attribut('src', 'image/buff/'.$buff->get_type().'_p.png');
          $img->set_attribut('alt', $buff->get_type());
          $this->creer_buff_duree($li, $buff);
				}
			}
    }
    if( !$debuffs && $aff_vide )
    {
      $grade = $perso->get_grade();
      $case_buff_dispo = $grade->get_nb_buff() - $perso->get_nb_buff();
      echo "case vides : $case_buff_dispo<br/>";
			for($b = 0; $b < $case_buff_dispo; $b++)
			{
        $li = $this->add( new interf_bal_smpl('li', '&nbsp;', null, 'buff_dispo') );
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
          $li = $this->add( new interf_bal_smpl('li', '&nbsp;', null, 'buff_nondispo') );
          $li->set_tooltip($texte.'.', 'bottom');
        }
  		}
    }
  }
  protected function creer_buff_duree(&$elt, &$buff)
  {
    $ratio = floor(100 * (($buff->get_fin() - time()) / ($buff->get_duree())));
    $pere = $elt->add( new interf_bal_cont('div', null, 'progress barre_buff jauge_buff') );
    $fils = $pere->add( new interf_bal_cont('div', null, 'progress-bar progress-bar-info') );
    $fils->set_attribut('style', 'width:'.$ratio);
  }
}
?>