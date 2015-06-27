<?php // -*- mode: php; tab-width:2 -*-

/**
 * @file texte.class.php
 * COntient la classe qui permet la mise en forme du texte en fon©tion des balises
 */

/// Classe géant la mise en forme du texte
class texte
{
  protected $texte;  ///< Texte à formater
  protected $options;  ///< Options indiquant s'il y a plusieurs textes
  protected $url;  ///< URL pour les liens s'il y a plusieurs parties au texte.
  protected $lien_li;  ///< true si les liens doivent être dans des listes.
  protected $case;  ///< Case pour les lien de retour à celle-ci
  protected $perso;   ///< Personnage actif pour les balsises basées sur celui-ci
  protected $id;  ///<  id de l'objet d'où vient le texte.
  const plrs_txt = 0x1;  ///< Il y a plusieurs textes possibles, séparés par 5 '*'.
  const bbcode = 0x2;   ///< bbcode.
  const grade = 0x4;   ///< balises sélectionnant ce qui doit être affiché en fonction du grade.
  const quetes = 0x8;   ///< balises gérant les intéraction avec les quêtes.
  const navig = 0x10;   ///< balises gérant la navigation (par ex. [retour]).
  const progr_pnj = 0x20;   ///< balises permettant de mettre d'éxécuter les fonctions spéciales pour les PNJ.
  const html = 0x40;   ///< indique qu'il faut supprimer les balises html.
  const perso = 0x80;   ///< balises sélectionnant ce qui doit être affiché en fonction du personnage.
  const actions = 0x100;   ///< balises effectuant certaines actions.
  const tuto = 0x200;   ///< balises liées au tutoriel.

  const messagerie = 0x42;   ///< textes de la messagerie
  const msg_monde = 0x86;   ///< message du monde
  const msg_roi = 0x46;   ///< message du roi
  const msg_roi_modif = 0x42;   ///< message du roi sans les balises non gérées par l'éditeur
  const msg_propagande = 0x42;   ///< propagande
  const pnj = 0x3bf;   ///< textes des PNJ
  const cases = 0x3bf;   ///< textes des cases
  const tutoriel = 0x3b8;   ///< textes des tutoriels
  const batailles = 0x42;   ///< textes des batailles
  const descr_quetes = 0x86;   ///< textes des quêtes
  
  /**
   * Constructeur
   * @param  $texte     Texte à formater
   * @param  $options   Options indiquant s'il y a plusieurs textes possibles et les balises a rechercher.
   */
  function __construct($texte, $options)
  {
    $this->texte = $texte;
    $this->options = $options;
    $this->perso = &joueur::get_perso();
  }
  
  /**
   * Défini les options pour les liens
   * @param  $url     URL pour les liens s'il y a plusieurs parties au texte.
   * @param  $liste   true si les liens doivent être dans des listes.
   */
  function set_liens($url, $case=null, $liste=true)
  {
    $this->url = $url;
    $this->liste = $liste;
    $this->case = $case;
  }
  
  /// Défini le personnage actif
  function set_perso(&$perso)
  {
    $this->perso = &$perso;
  }

  /// Défini l'id de l'objet d'où vient le texte.
  function set_id_objet($id)
  {
    $this->id = $id;
  }
  
  /**
   * Fonction formattant le texte
   * @param  $index   Index du texte s'il y en a plusieurs
   */
  function parse($index=0)
  {
    if( $this->options & self::plrs_txt )
    {
      $texte = explode('*****', $this->texte);
      $texte = $texte[$index];
    }
    else
    	$texte = $this->texte;
    $texte = preg_replace("/(\r\n|\r|\n)/", '', $texte);
    if( $this->options & self::html )
      $texte = htmlspecialchars( stripslashes($texte) );
    $texte = nl2br($texte);
    if( $this->options & self::bbcode )
      $texte = $this->parse_bbcode($texte);
    if( $this->options & self::plrs_txt )
      $texte = $this->parse_plrs_txt($texte);
    if( $this->options & self::grade )
      $texte = $this->parse_grade($texte);
    if( $this->options & self::quetes )
      $texte = $this->parse_quetes($texte);
    if( $this->options & self::navig )
      $texte = $this->parse_navig($texte);
    if( $this->options & self::progr_pnj )
      $texte = $this->parse_progr_pnj($texte);
    if( $this->options & self::progr_pnj )
      $texte = $this->parse_progr_pnj($texte);
    if( $this->options & self::perso )
      $texte = $this->parse_perso($texte);
    if( $this->options & self::actions )
      $texte = $this->parse_actions($texte);
    if( $this->options & self::tuto )
      $texte = $this->parse_tuto($texte);
      
    return $texte;//preg_replace('`\[[/]?(.*)\]`','', $texte);
  }
  
  /// Fonction formattant les balises relatives au quêtes
  protected function parse_quetes($texte)
  {
    global $db;
    $trouve = false;
    // on regarde les quêtes du personnage
    $quetes_actives = array();
    if($this->perso->get_quete() != '')
    {
    	foreach(unserialize($this->perso->get_quete()) as $quete)
    	{
    		if (!is_numeric($quete['id_quete'])) continue;
    		$requete = 'SELECT * FROM quete WHERE id = '.$quete['id_quete'];
    		$req = $db->query($requete);
    		$row = $db->read_assoc($req);
    		$objectif = unserialize($row['objectif']);
    		$i = 0;
    		$quetes_actives[] = $row['id'];
    		foreach($quete['objectif'] as $objectif_fait)
    		{
    			$total_fait = $objectif_fait->nombre;
    			$total = $objectif[$i]->nombre;
    			//On vérifie si il peut accéder à cette partie de la quête
    			if($total_fait >= $total)
            $objectif[$i]->termine = true;
    			else
            $objectif[$i]->termine = false;
    			if (($objectif_fait->requis == '' OR $objectif[$objectif_fait->requis]->termine) AND !$objectif[$i]->termine)
    			{
    				$cible = $objectif_fait->cible;
    			}
    			$i++;
    		}
    	}
    }
    // quêtes finies
    $quete_fini = explode(';', $this->perso->get_quete_fini());
    while( preg_match('`\[quete_finie:([0-9]*)(-e[0-9]*)?\](.*)\[/quete_finie:(\g1)(\g2)?\]`i', $texte, $regs) )
    {
    	$qp = $this->get_quete_perso($regs);
    	$debut = '[quete_finie:'.$regs[1].']'; 
    	$fin = '[/'.mb_substr($debut, 1);
    	if( in_array($regs[1], $quete_fini) || ($qp && $regs[2] && $qp->get_etape()->get_etape() > $regs[2]))
    		$texte = preg_replace('`\\'.$debut.'(.*)\\'.$fin.'`i', $regs[3], $texte);
    	else
    		$texte = preg_replace('`\\'.$debut.'(.*)\\'.$fin.'`i', '', $texte);
      $trouve = true;
		}
    if($this->perso->get_quete_fini() != '')
    {
    	foreach($quete_fini as $quetef)
    	{
    		//On affiche le lien pour la discussion
        if( $this->liste )
        {
          $debut = '<li>';
          $fin = '</li>';
        }
        else
        {
          $debut = '';
          $fin = '';
        }
    		$texte = preg_replace('`\[QUETEFINI'.$quetef.':([0-9]*)\](.*)\[/QUETEFINI'.$quetef.':(\g1)\]`i', $debut.'<a href="'.$this->url.'&amp;reponse=\\1" onclick="return charger(this.href);">\\2</a>'.$fin, $texte, -1, $nbr);
        $trouve |= $nbr > 0;
    	}
    }
    $texte = preg_replace('`\[QUETEFINI([0-9]*):([0-9]*)\](.*)\[/QUETEFINI(\g1):\g2\]`i', '', $texte);
    // quêtes non prises
    while( preg_match('`\[non_quete:([0-9]*)(-e[0-9]*)?(-v[0-9]*)?\](.*)\[/non_quete:(\g1)(\g2)?(\g3)?\]`i', $texte, $regs) )
    {
    	$qp = $this->get_quete_perso($regs);
    	$debut = '[non_quete:'.$regs[1].']'; 
    	$fin = '[/'.mb_substr($debut, 1);
    	if( (!$qp && !in_array($regs[1], $quete_fini)) || ($qp && ($qp->get_etape()->get_etape() < $regs[2] || $qp->get_etape()->get_variante() != $regs[3])) )
    		$texte = preg_replace('`\\'.$debut.'(.*)\\'.$fin.'`i', $regs[4], $texte);
    	else
    		$texte = preg_replace('`\\'.$debut.'(.*)\\'.$fin.'`i', '', $texte);
      $trouve = true;
    }
    // quête prises
    while( preg_match('`\[isquete:([0-9]*)(-e[0-9]*)?(-v[0-9]*)?\](.*)\[/isquete:(\g1)(\g2)?(\g3)?\]`i', $texte, $regs) )
    {
    	$qp = $this->get_quete_perso($regs);
    	$debut = '[isquete:'.$regs[1].']'; 
    	$fin = '[/'.mb_substr($debut, 1);
    	if ($qp)
    		$texte = preg_replace('`\\'.$debut.'(.*)\\'.$fin.'`i', $regs[4], $texte);
    	else
    		$texte = preg_replace('`\\'.$debut.'(.*)\\'.$fin.'`i', '', $texte);
    	$trouve = true;
    }
    //Validation de la quête
    if(preg_match('`\[quete(:[[:alnum:]]+)?]`i', $texte, $regs))
    {
      if( $regs[1] == ':silencieux' )
        echo '<span class="debug">';
    	quete_perso::verif_action($this->id, $this->perso, 's', $regs[1]);
      if( $regs[1] == ':silencieux' )
        echo '</span>';
    	$texte = preg_replace('`\[quete(:[[:alpha:]]+)?]`i', '', $texte);
    }
    //Validation de la quête de groupe
    if(preg_match('`\[quetegroupe(:[[:alnum:]]+)?]`i', $texte))
    {
      if( $regs[1] == ':silencieux' )
        echo '<span class="debug">';
      if ($this->perso->get_groupe() > 0)
      {
        $groupe = new groupe($this->perso->get_groupe());
        foreach($groupe->get_membre_joueur() as $pj)
          quete_perso::verif_action($this->id, $pj, 'g', $regs[1]);
      }
      else
        quete_perso::verif_action($this->id, $this->perso, 's', $regs[1]);
      if( $regs[1] == ':silencieux' )
        echo '<span class="debug">';
    	$texte = preg_replace('`\[quetegroupe(:[[:alpha:]]+)?]`i', '', $texte);
    }
    //Prise d'une quête
    if(preg_match('`\[prendquete:([0-9]*)\]`i', $texte, $regs))
    {
    	$this->perso->prend_quete($regs[1]);
    	$texte = str_ireplace('[prendquete:'.$regs[1].']', '', $texte);
    }
    //Donne un item
    if(preg_match('`\[donneitem:([a-zA-Z][0-9]*)\]`', $texte, $regs))
    {
    	$this->perso->prend_objet($regs[1]);
    	$texte = str_ireplace('[donneitem:'.$regs[1].']', '', $texte);
    	quete_perso::verif_action($regs[1], $this->perso, 's');
    	//$trouve = true;
    }
    //Vends un item
    if(preg_match('`\[vendsitem:([a-zA-Z][0-9]*):([0-9]*)\]`i', $texte, $regs))
    {
    	if ($this->perso->get_star() < $regs[2])
    	{
    		$replace = 'Vous n\'avez pas assez de stars !!<br/>';
    	}
    	else
    	{
    		$this->perso->set_star($this->perso->get_star() - $regs[2]);
    		$this->perso->prend_objet($regs[1]);
    		$this->perso->sauver();
    		$replace = 'Vous recevez un objet.<br/>';
    		quete_perso::verif_action($regs[1], $this->perso, 's');
    	}
    	$texte = str_ireplace('[vendsitem:'.$regs[1].':'.$regs[2].']', $replace, $texte);
    }
    //validation inventaire
    if(preg_match('`\[verifinventaire:([0-9]*)(-e[0-9]*)?(-v[0-9]*)?\]`i', $texte, $regs))
    {
    	$qp = $this->get_quete_perso($regs);
    	if ($qp && $qp->verif_inventaire() == false)
    		$texte = "Tu te moques de moi, mon bonhomme ?";
    	else
    		$texte = str_ireplace($regs[0], '', $texte);
    }
    
    if( $trouve )
      return $this->parse_quetes($texte);
    else
      return $texte;
  }
  
  protected function get_quete_perso($regs)
  {
    $qp = new quete_etape(array('id_perso', 'id_quete'), array($this->perso->get_id(), $regs[1]));
    if( !$qp )
    	return null;
  	if( count($regs) > 1 && $regs[2][1] == 'e' )
  	{
			if( $qp->get_id_etape() !=  mb_substr($regs[2], 3) )
				return null;
		}
		if( count($regs) > 2 && $regs[3][1] == 'v' )
		{
			if( $qp->get_etape()->get_variante() !=  mb_substr($regs[3], 3) )
				return null;
		}
	}
  
  /// Fonction formattant les balises gérant la navigation (par ex. [retour])
  protected function parse_navig($texte)
  {
    if( $this->liste )
    {
      $debut = '<li>';
      $fin = '</li>';
    }
    else
    {
      $debut = '';
      $fin = '';
    }
    return str_ireplace('[retour]', $debut.'<a href="informationcase.php?case='.$this->case.'" onclick="return charger(this.href);">Retour aux informations de la case</a>'.$fin, $texte);
  }

  /// Fonction formattant les balises permettant de naviguer entre les différentes parties d'un texte.
  protected function parse_plrs_txt($texte)
  {
    if( $this->liste )
    {
      $debut = '<li>';
      $fin = '</li>';
    }
    else
    {
      $debut = '';
      $fin = '';
    }
    $texte = preg_replace('`\[/id:([0-9,]+)\]`i', '[/£id:\\1]', $texte);
    return preg_replace('`\[id:([0-9]*)\]([^£]*)\[/£id:\g1\]`iu', $debut.'<a href="'.$this->url.'&amp;reponse=\\1" onclick="return charger(this.href);">\\2</a>'.$fin, $texte);
  }
  
  /// Fonction formattant les balises permettant de naviguet entre les différentes parties d'un textes.
  protected function parse_progr_pnj($texte)
  {
    //lancement fonction personalisée (cf. fonction/pnj.inc.php)
    while( preg_match("`\[run:([a-z0-9_]+)(\(([a-z0-9_]+)\))?\]`i",
											$texte, $regs) )
    {
      include_once('fonction/pnj.inc.php');
      $run = 'pnj_run_'.$regs[1];
			if (array_key_exists(3, $regs))
				$replace = $run($this->perso, $regs[3]);
			else
				$replace = $run($this->perso);
      $texte = str_ireplace($regs[0], $replace, $texte);
    }
    //IF fonction personalisée (cf. fonction/pnj.inc.php)
    while( preg_match('`\[if:([a-z0-9_]+)(\(([a-z0-9_]+)\))?\]`i',
											$texte, $regs) )
    {
			$markup = 'if:'.$regs[1];
      include_once('fonction/pnj.inc.php');
      $run = 'pnj_if_'.$regs[1];
			if (array_key_exists(3, $regs)) {
				$ok = $run($this->perso, $regs[3]);
				$markup .= $regs[2];
			}
			else
				$ok = $run($this->perso);
      if ($ok)
      {
        $texte = str_ireplace('['.$markup.']', '', $texte);
        $texte = str_ireplace('[/'.$markup.']', '', $texte);
      }
      else
      {
    		// NE PAS OUBLIER le modificateur 's' pour PCRE_DOTALL
    		$s = preg_match('`\['.$markup.'\]`i', $texte);
    		$e = preg_match('`\[/'.$markup.'\]`i', $texte);
        $texte = preg_replace('`\['.$markup.'\].*\[/'.$markup.'\]`si', '', $texte);
    		if (!$s || !$e) die("Erreur de dialogue pnj: id = $this->id, reponse = $reponse, s = $s, e = $e");
      }
    }
    //IFNOT fonction personalisée (cf. fonction/pnj.inc.php)
    while( preg_match("`\[ifnot:([a-z0-9_]+)(\(([a-z0-9_]+)\))?\]`i",
											$texte, $regs) )
    {
    	$markup = 'ifnot:'.$regs[1];
      include_once('fonction/pnj.inc.php');
      $run = 'pnj_if_'.$regs[1];
			if (array_key_exists(3, $regs)) {
				$ok = $run($this->perso, $regs[3]);
				$markup .= $regs[2];
			}
			else
				$ok = $run($this->perso);
      if (!$ok)
      {
        $texte = str_ireplace('['.$markup.']', '', $texte);
        $texte = str_ireplace('[/'.$markup.']', '', $texte);
      }
      else
      {
    		// NE PAS OUBLIER le modificateur 's' pour PCRE_DOTALL
    		$s = preg_match('`\[$markup\]`i', $texte);
    		$e = preg_match('`\[/$markup\]`i', $texte);
        $texte = preg_replace('`\['.$markup.'\].*\[/'.$markup.'\]`si', '', $texte);
    		if (!$s || !$e) die("Erreur de dialogue pnj: id = $this->id, reponse = $reponse, s = $s, e = $e");
      }
    }
    //lancement fonction personalisée (cf. fonction/pnj.inc.php)
    while( preg_match("`\[runafter:([a-z0-9_]+)(\(([a-z0-9_]+)\))?\]`i",
											$texte, $regs) )
    {
      include_once('fonction/pnj.inc.php');
      $run = 'pnj_run_'.$regs[1];
			if (array_key_exists(3, $regs))
				$replace = $run($this->perso, $regs[3]);
			else
				$replace = $run($this->perso);
      $texte = str_ireplace($regs[0], $replace, $texte);
    }
    return $texte;
  }

  /// Fonction formattant les balises permettant de naviguet entre les différentes parties d'un textes.
  protected function parse_grade($texte)
  {
    global $G_autorisations;
  	foreach ($G_autorisations as $balise => $grades)
    {
  		if (!in_array($this->perso->get_rang_royaume(), $grades))
  			$texte = preg_replace('/\['.$balise.'\].*?\[\\/'.$balise.'\]/i', '', $texte);
  	  else
  	  	$texte = preg_replace('/\['.$balise.'\](.+?)\[\\/'.$balise.'\]/i', '<small class="confidentiel">R&eacute;serv&eacute; aux '.$balise.' : \\1 </small>', $texte);
  	}
  	return $texte;
  }

  /// Fonction formattant les balises permettant de naviguet entre les différentes parties d'un textes.
  protected function parse_bbcode($texte)
  {
    $trouve = false;
    $texte = str_ireplace('[br]', '<br />', $texte);
  	$texte = preg_replace('#\[img\]([^[]*)\[/img\]#i', '<img src="\\1" title="\\1" />', $texte);
  	$texte = preg_replace('`\[/b\]`i', '[/£b]', $texte);
  	$texte = preg_replace('#\[b\]([^£]*)\[/£b\]#i', '<strong>\\1</strong>', $texte, -1, $nbr);
  	$trouve |= $nbr > 0;
  	$texte = preg_replace('`\[/i\]`i', '[/£i]', $texte);
  	$texte = preg_replace('#\[i\]([^£]*)\[/£i\]#i', '<i>\\1</i>', $texte, -1, $nbr);
  	$trouve |= $nbr > 0;
  	$texte = preg_replace('`\[/u\]`i', '[/£u]', $texte);
  	$texte = preg_replace('#\[u\]([^£]*)\[/£u\]#i', '<u>\\1</u>', $texte, -1, $nbr);
  	$trouve |= $nbr > 0;
  	$texte = preg_replace('`\[/s\]`i', '[/£s]', $texte);
  	$texte = preg_replace('#\[s\]([^£]*)\[/£s\]#i', '<strike>\\1</strike>', $texte, -1, $nbr);
  	$trouve |= $nbr > 0;
  	$texte = preg_replace('`\[/url\]`i', '[/£url]', $texte);
  	$texte = preg_replace('#\[url\]([^£]*)\[/£url\]#i', '<a href="\\1">\\1</a>', $texte);
  	$texte = preg_replace('#\[url=([^[\]]*)\]([^£]*)\[/£url\]#i', '<a href="\\1">\\2</a>', $texte, -1, $nbr);
  	$trouve |= $nbr > 0;
  	$texte = str_ireplace("[/color]", "</span>", $texte);
  	$regCouleur = "`\[color= ?(([[:alpha:]]+)|(#[[:digit:][:alpha:]]{6})) ?\]`i";
  	$texte = preg_replace($regCouleur, "<span style=\"color: \\1\">", $texte);
  	
    if( $trouve )
      return $this->parse_bbcode($texte);
    else
      return $texte;
  }

  /// Fonction formattant les balises sélectionnant ce qui doit être affiché en fonction de la personnage.
  protected function parse_perso($texte)
  {
    $trouve = false;
    // classes
    $texte = preg_replace('`\[/classe:([0-9,]+)\]`i', '[/£classe:\\1]', $texte);
    while( preg_match('`\[classe:([0-9,]+)\]([^£]*)\[/£classe:\g1\]`i', $texte, $regs) )
    {
      $classes = explode(',', $regs[1]);
      if( in_array($this->perso->get_classe_id(), $classes) )
        $texte = preg_replace('`\[classe:'.$regs[1].'\]([^£]*)\[/£classe:'.$regs[1].'\]`i', '\\1', $texte);
      else
        $texte = preg_replace('`\[classe:'.$regs[1].'\]([^£]*)\[/£classe:'.$regs[1].'\]`i', '', $texte);
      $trouve = true;
    }
    // race
    $texte = preg_replace('`\[/race:([a-z,]+)\]`i', '[/£race:\\1]', $texte);
    while( preg_match('`\[race:([a-z,]+)\]([^£]*)\[/£race:\g1\]`i', $texte, $regs) )
    {
      $classes = explode(',', $regs[1]);
      if( in_array($this->perso->get_race(), $classes) )
        $texte = preg_replace('`\[race:'.$regs[1].'\]([^£]*)\[/£race:'.$regs[1].'\]`i', '\\1', $texte);
      else
        $texte = preg_replace('`\[race:'.$regs[1].'\]([^£]*)\[/£race:'.$regs[1].'\]`i', '', $texte);
      $trouve = true;
    }
    // compétences
    while( preg_match('`\[comp:([a-z_]*)([=<>])([0-9]*)\].*\[/comp:\g1\g2\g3\]`i', $texte, $regs) )
    {
      $get = 'get_'.$regs[1];
      switch($regs[2])
      {
      case '=':
        $ok = $this->perso->$get() == $regs[3];
        break;
      case '<':
        $ok = $this->perso->$get() < $regs[3];
        break;
      case '>':
        $ok = $this->perso->$get() > $regs[3];
        break;
      default:
        $ok = false;
      }
      $cond = $regs[1].$regs[2].$regs[3];
      if( $ok )
        $texte = preg_replace('`\[comp:'.$cond.'\](.*)\[/comp:'.$cond.'\]`i', '\\1', $texte);
      else
        $texte = preg_replace('`\[comp:'.$cond.'\].*\[/comp:'.$cond.'\]`i', '', $texte);
      $trouve = true;
    }

    if( $trouve )
      return $this->parse_perso($texte);
    else
      return $texte;
  }

  /// Fonction formattant les balises sélectionnant ce qui doit être affiché en fonction des compétences.
  protected function parse_actions($texte)
  {
    if( preg_match('`\[tp:([0-9]+)-([0-9]+)\]`i', $texte, $regs) )
    {
      $pos = convert_in_coord($this->case);
      if( $this->perso->get_x() == $pos['x'] && $this->perso->get_y() == $pos['y'] )
      {
        $this->perso->set_x( $regs[1] );
        $this->perso->set_y( $regs[2] );
        $this->perso->sauver();
      }
      $texte = str_ireplace('[tp:'.$regs[1].'-'.$regs[2].']', '<script type="text/javascript">charger("deplacement.php?deplacement=centre");</script>', $texte);
    }
    return $texte;
  }

  /// Fonction formattant les balises liées au tutoriel.
  protected function parse_tuto($texte)
  {
    $trouve = false;
    if( preg_match('`\[aide:([a-z_-]*)\](.*)\[/aide:(\g1)\]`i', $texte, $regs) )
    {
      //interf_base::code_js('aide("'.$regs[1].'");');
      //$texte = str_ireplace('[aide:'.$regs[1].']', '<script type="text/javascript">aide("'.$regs[1].'");</script>', $texte);
      $texte = preg_replace('`\[aide:([a-z_-]*)\](.*)\[/aide:(\g1)\]`i', '<a onclick="aide(\''.$regs[1].'\');">'.$regs[2].'</a>', $texte);
    }
    else if( preg_match('`\[aide:([a-z_-]*)\]`i', $texte, $regs) )
    {
      //interf_base::code_js('aide("'.$regs[1].'");');
      $texte = str_ireplace('[aide:'.$regs[1].']', '<script type="text/javascript">aide("'.$regs[1].'");</script>', $texte);
    }
    if( preg_match('`\[tuto:([0-9+]+)\]`i', $texte, $regs) )
    {
      if( $regs[1] == '+' )
        $tuto = $this->perso->get_tuto() + 1;
      else
        $tuto = $regs[1];
      $this->perso->set_tuto( $tuto );
      $this->perso->sauver();
      $texte = str_ireplace('[tuto:'.$regs[1].']', '', $texte);
    }
    // champ tutoriel égal à une certaine valeur
    while( preg_match('`\[tuto_egal:([0-9]*)\](.*)\[/tuto_egal:(\g1)\]`i', $texte, $regs) )
    {
    	$num = $regs[1];
    	if( $this->perso->get_tuto() == $num )
    		$texte = preg_replace('`\[tuto_egal:'.$num.'\](.*)\[/tuto_egal:'.$num.'\]`i', $regs[2], $texte);
    	else
    		$texte = preg_replace('`\[tuto_egal:'.$num.'\](.*)\[/tuto_egal:'.$num.'\]`i', '', $texte);
    	$trouve = true;
    }
    // champ tutoriel inférieur à une certaine valeur, mais non-nul
    while( preg_match('`\[tuto_inf:([0-9]*)\](.*)\[/tuto_inf:(\g1)\]`i', $texte, $regs) )
    {
    	$num = $regs[1];
    	if( $this->perso->get_tuto() < $num && $this->perso->get_tuto() > 0 )
    		$texte = preg_replace('`\[tuto_inf:'.$num.'\](.*)\[/tuto_inf:'.$num.'\]`i', $regs[2], $texte);
    	else
    		$texte = preg_replace('`\[tuto_inf:'.$num.'\](.*)\[/tuto_inf:'.$num.'\]`i', '', $texte);
    	$trouve = true;
    }
    // champ tutoriel supérieur à une certaine valeur
    while( preg_match('`\[tuto_sup:([0-9]*)\](.*)\[/tuto_sup:(\g1)\]`i', $texte, $regs) )
    {
    	$num = $regs[1];
    	if( $this->perso->get_tuto() > $num )
    		$texte = preg_replace('`\[tuto_sup:'.$num.'\](.*)\[/tuto_sup:'.$num.'\]`i', $regs[2], $texte);
    	else
    		$texte = preg_replace('`\[tuto_sup:'.$num.'\](.*)\[/tuto_sup:'.$num.'\]`i', '', $texte);
    	$trouve = true;
    }

    if( $trouve )
      return $this->parse_perso($texte);
    else
      return $texte;
  }
  
  static function parse_url($texte)
  {  
  	return preg_replace('`((?:https?|ftp)://S+)(s|z)`', '<a href="$1">$1</a>',$url);
	}
}
?>
