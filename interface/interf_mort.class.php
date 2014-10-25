<?php

class interf_mort extends interf_gauche
{
	function __construct()
	{
		global $db, $Trace;
		parent::__construct('mort');
		$perso = joueur::get_perso();	
		
		// Icone et titre
		$this->set_icone_centre('rafraichir', 'mort.php?rafraichir=tout');
		$this->barre_haut->add( new interf_txt('Vous êtes mort') );
		
		// Personnage dans une arène ?
	  $arene = false;
	  if( $perso->in_arene() )
	  {
	    $event = event::create_from_arenes_joueur($perso);
	    if( $event )
	      $perso_ar = $event->get_arenes_joueur('id_perso='.$perso->get_id().' AND statut='.arenes_joueur::en_cours);
	    else
	      $perso_ar = arenes_joueur::creer(0, 'arenes_joueur', 'id_perso='.$perso->get_id().' AND statut='.arenes_joueur::en_cours);
	    // Si a on trouvé les infos sur son TP, alors traitement spécial
	    if( $perso_ar )
	    {
	      // rez non autorisée ou choix de sortir de l'arène
	      if( ( $event !== null && !$event->rez_possible($perso->get_id()) ) || $var == 2 )
	      {
	        // renvoie hors de l'arène
	        $perso_ar[0]->teleporte( $perso->get_nom() );
	        return;
	      }
	      $arene = true;
	    }
	  }
	  
		//Bonus mort-vivant
		$bonus = $perso->get_race() == 'mortvivant' ? 10 : 0;
		
		// Centre
		$this->centre->add( new interf_bal_smpl('p', 'Votre dernier souvenir est l\'endroit où vous êtes mort x : '.$perso->get_x().' / y : '.$perso->get_y()) );
		$rez = $this->centre->add( new interf_bal_cont('p') );
		// Liste des rez
		$requete = "SELECT * FROM rez WHERE id_perso = ".$perso->get_id();
		$req = $db->query($requete);
		$liste = $rez->add( new interf_menu('Que voulez vous faire ?', 'rez', null) );
		$max_pourcent = 0;
		if($db->num_rows > 0)
		{
			while($row = $db->read_assoc($req))
			{
				$pourcent = $row['pourcent'] + $bonus;
				$elt = $liste->add( new interf_elt_menu('', 'carte.php', 'return charger(this.href);') );
				$elt->get_lien()->set_attribut('class', 'icone icone-carte2');
				$rez = $elt->add( new interf_lien('Vous faire ressusciter par '.$row['nom_rez'].' ('.$pourcent.'% HP / '.$pourcent.' MP)', 'mort.php?choix=2&amp;rez='.$row['id']) );
				if( $pourcent > $max_pourcent )
					$max_pourcent = $pourcent;
			}
		}
		if( $arene )
		{
			$liste->add( new interf_elt_menu('Sortir de l\'arène', 'mort.php?choix=1', 'charger(this.href);') );
		}
		else
		{
			// Recherche du fort le plus proche
			$requete = "SELECT *, (ABS(".$perso->get_x()." - cast(x as signed integer)) + ABS(".$perso->get_y()." - cast(y as signed integer))) AS plop FROM `construction` WHERE rez > 0 AND type = 'fort' AND royaume = ".$Trace[$perso->get_race()]['numrace']." ORDER BY plop ASC";
			$req = $db->query($requete);
			if( $row = $db->read_assoc($req) )
			{
				$pourcent = $row['rez'] + $bonus;
				$elt = $liste->add( new interf_elt_menu('', 'carte.php?x='.$row['x'].'&y='.$row['y'], 'return charger(this.ref);') );
				$elt->get_lien()->set_attribut('class', 'icone icone-carte2');
				$rez = $elt->add( new interf_lien('Revenir dans le fort le plus proche, en x : '.$row['x'].' / y : '.$row['y'].' ('.$pourcent.'% HP / '.$pourcent.' MP)', 'mort.php?choix=3&amp;rez='.$row['id']) );
				if( $pourcent > $max_pourcent )
					$max_pourcent = $pourcent;
			}
			//Vérifie s'il y a une amende qui empêche le spawn en ville
			$amende = recup_amende($perso->get_id());
			$race = &$Trace[$perso->get_race()];
			if($amende && $amende['respawn_ville'] == 'n')
			{
				// Refuge des criminels
				$pourcent = 5 + $bonus;
				$elt = $liste->add( new interf_elt_menu('', 'carte.php?x='.$race['spawn_c_x'].'&y='.$race['spawn_c_y'], 'return charger(this.href);') );
				$elt->get_lien()->set_attribut('class', 'icone icone-carte2');
				$rez = $elt->add( new interf_lien('Revenir dans le refuge des criminels ('.$pourcent.'% HP / '.$pourcent.' MP)', 'mort.php?choix=1') );
				if( $pourcent > $max_pourcent )
					$max_pourcent = $pourcent;
			}
			else
			{
				// Capitale
				$R = new royaume($Trace[$perso->get_race()]['numrace']);
				$pourcent = ($R->is_raz() ? 5 : 20) + $bonus;
				$elt = $liste->add( new interf_elt_menu('', 'carte.php?x='.$race['spawn_x'].'&y='.$race['spawn_y'], 'return charger(this.href);') );
				$elt->get_lien()->set_attribut('class', 'icone icone-carte2');
				$rez = $elt->add( new interf_lien('Revenir dans votre ville natale ('.$pourcent.'% HP / '.$pourcent.' MP)', 'mort.php?choix=1') );
				if( $pourcent > $max_pourcent )
					$max_pourcent = $pourcent;
			}
		}

		// Jauges
		$this->set_jauge_ext(round($perso->get_hp_max()*$max_pourcent/100), $perso->get_hp_max(), 'hp', 'La meilleure résurection remettra vos points de vie à ');
		$this->set_jauge_int(round($perso->get_mp_max()*$max_pourcent/100), $perso->get_mp_max(), 'mp', 'La meilleure résurection remettra vos points de mana à ');
	}
}
?>