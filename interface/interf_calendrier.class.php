<?php
/**
 * @file interf_calendrier.class.php
 * Affichage des prochains moments de la journée
 */

class interf_calendrier extends interf_tableau
{
	function __construct($nbr)
	{
		parent::__construct('calendrier', 'table table-striped table-condensed');
		$this->nouv_cell('Moment');
		$this->nouv_cell('Date');
		$this->nouv_cell('Durée');
		
	  $moments = array("Matin", "Journee", "Soir", "Nuit");
	  $durees = array(4, 6, 4, 10);
	  $heure_sso = time() / 18 * 24;
	  $date = new DateTime();
	  $date->setTimestamp($heure_sso);
	
		switch( moment_jour() )
		{
		case 'Matin':
			$h = 6;
			$k = 0;
			break;
		case 'Journee':
			$h = 10;
			$k = 1;
			break;
		case 'Soir':
			$h = 16;
			$k = 2;
			break;
		case 'Nuit':
			$h = 21;
			$k = 3;
			if( date('h', $heure_sso) < 6 )
				$date->modify('-1 day');
			break;
		}
		$m = $date->format('m');
		$j = $date->format('j');
		$a = $date->format('Y');
		$heure = mktime($h, 0, 0, $m, $j, $a); 
	
		for($i=0; $i<$nbr; $i++)
		{
			$heure += $durees[$k] * 3600;
			$k++;
			$k %= 4;
			$this->nouv_ligne();
			$this->nouv_cell( $moments[$k] );
			
			$jours = array('dim.', 'lun.', 'mar.', 'mer.', 'jeu.', 'ven.', 'sam.');
			$date = $heure / 24 * 18;
			if( date('d') == date('d', $date) )
				$this->nouv_cell( date('H:i:s', $date) );
			else if( date('d') == date('d', $date-24*3600) )
				$this->nouv_cell( 'demain '.date('H:i:s', $date) );
			else
				$this->nouv_cell( $jours[date('w', $date)].' '.date('H:i:s', $date) );
			
				$this->nouv_cell( transform_sec_temp($date - time()) );
		}
	}
}

?>