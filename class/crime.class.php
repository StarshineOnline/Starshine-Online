<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
class crime
{
	//definition des points de crime selon l'action
	function crime_soin(&$perso, &$cible)
	{
		global $Gtrad, $db;
		
		$G_crime_soin[10] = 1;
		$G_crime_soin[9] = 0.8;
		$G_crime_soin[8] = 0.6;
		$G_crime_soin[7] = 0.4;
		$G_crime_soin[6] = 0.2;
	
		$requete = "SELECT ".$cible->get_race()." FROM diplomatie WHERE race = '".$perso->get_race()."'";
		$req = $db->query($requete);
		$row = $db->read_row($req);
		if ($row[0] > 5 AND $row[0] < 127)
		{
			$points = $G_crime_soin[$row[0]];
			$perso->set_crime($perso->get_crime() + $points);
			echo '<h5>Vous soignez un joueur en '.$Gtrad['diplo'.$row[0]].', vous recevez '.$points.' point(s) de crime</h5>';
		}
	}
	
	function crime_sort(&$perso, &$cible)
	{
		global $Gtrad, $db;
		
		$G_crime_sort[10] = 1;
		$G_crime_sort[9] = 0.8;
		$G_crime_sort[8] = 0.6;
		$G_crime_sort[7] = 0.4;
		$G_crime_sort[6] = 0.2;
	
		$requete = "SELECT ".$cible->get_race()." FROM diplomatie WHERE race = '".$perso->get_race()."'";
		$req = $db->query($requete);
		$row = $db->read_row($req);
		if ($row[0] > 5 AND $row[0] < 127)
		{
			$points = $G_crime_sort[$row[0]];
			$perso->set_crime($perso->get_crime() + $points);
			echo '<h5>Vous buffez un joueur en '.$Gtrad['diplo'.$row[0]].', vous recevez '.$points.' point(s) de crime</h5>';
		}
	}
	
	function crime_rez(&$perso, &$cible)
	{
		global $Gtrad, $db;
		
		$G_crime_rez[10] = 20;
		$G_crime_rez[9] = 16;
		$G_crime_rez[8] = 12;
		$G_crime_rez[7] = 8;
		$G_crime_rez[6] = 4;
	
		$requete = "SELECT ".$cible->get_race()." FROM diplomatie WHERE race = '".$perso->get_race()."'";
		$req = $db->query($requete);
		$row = $db->read_row($req);
		if ($row[0] > 5 AND $row[0] < 127)
		{
			$points = $G_crime_rez[$row[0]];
			$perso->set_crime($perso->get_crime() + $points);
			echo '<h5>Vous soignez un joueur en '.$Gtrad['diplo'.$row[0]].', vous recevez '.$points.' point(s) de crime</h5>';
		}
	}
	
	function crime_debuff(&$perso, &$cible)
	{
	    global $Gtrad, $db;	 
		
		$G_crime_sort[127] = 1.6;
		$G_crime_sort[0] = 1;
		$G_crime_sort[1] = 0.8;
		$G_crime_sort[2] = 0.6;
		$G_crime_sort[3] = 0.4;
		$G_crime_sort[4] = 0.2;		       
	    
	    //Gestion du crime
        $requete = "SELECT ".$cible->get_race()." FROM diplomatie WHERE race = '".$perso->get_race()."'";
		$req = $db->query($requete);
		$row = $db->read_row($req);
		if (($row[0] < 5 ) OR ( $row[0] == 127))
		{
			$points = $G_crime_sort[$row[0]];
			$perso->set_crime($perso->get_crime() + $points);
			echo '<h5>Vous debuffez un joueur en '.$Gtrad['diplo'.$row[0]].', vous recevez '.$points.' point(s) de crime</h5>';
        }
	}
	
	function crime_fin_combat(&$perso, &$cible, $type, $table)
	{
	    global $Gtrad, $db;	 
	    
	    $G_crime_kill[127] = 20;
		$G_crime_kill[0] = 16;
		$G_crime_kill[1] = 14;
		$G_crime_kill[2] = 12;
		$G_crime_kill[3] = 8;
		$G_crime_kill[4] = 4;	
		
		$G_crime_batiment[0] = 16;
		$G_crime_batiment[1] = 14;
		$G_crime_batiment[2] = 12;
		$G_crime_batiment[3] = 8;
		$G_crime_batiment[4] = 4;
		
		$G_crime[127] = 11;
		$G_crime[0] = 8;
		$G_crime[1] = 6;
		$G_crime[2] = 4;
		$G_crime[3] = 2;
		$G_crime[4] = 1;
		
		if($type == 'joueur')
		{
			$requete = "SELECT ".$cible->get_race()." FROM diplomatie WHERE race = '".$perso->get_race()."'";
			$req = $db->query($requete);
			$row = $db->read_row($req);
			$pascrime = false;
			//Vérification si crime
			if(array_key_exists($row[0], $G_crime))
			{
				if($row[0] == 127)
				{
					$amende = recup_amende($cible->get_id());
					if($amende)
					{
						if($amende['statut'] != 'normal') $pascrime = true;
					}
				}
				else if ( $row[0] > 5)
				{
					$pascrime = true;
				}
				
				if(!$pascrime && $cible->get_hp() > 0)
				{
					$points = ($G_crime_kill[$row[0]] / 4);
					$perso->set_crime($perso->get_crime() + $points);
					echo '<h5>Vous attaquez un joueur en '.$Gtrad['diplo'.$row[0]].', vous recevez '.$points.' point(s) de crime</h5>';
				}
				else if (!$pascrime && $cible->get_hp() < 0)
				{
					$points = ($G_crime_kill[$row[0]]);
					$perso->set_crime($perso->get_crime() + $points);
					echo '<h5>Vous tuez un joueur en '.$Gtrad['diplo'.$row[0]].', vous recevez '.$points.' point(s) de crime</h5>';
				}
			}
		}
		else if ( $type == 'batiment' OR $type == 'siege')
		{
			//on attaque un batiment à la main.
			if ($type == 'batiment') $facteur = 3;
			if ($type == 'siege') $facteur = 2;
			
			
			//on recherche l'id de race de la construction
			if($table == 'construction') $requete = "SELECT royaume FROM construction WHERE id = ".$cible->get_id();
			else $requete = "SELECT royaume FROM placement WHERE id = ".$cible->get_id();
			$req = $db->query($requete);
			$id = $db->read_row($req);
			
			//on traduit l'id en nom de race
			$requete = "SELECT race FROM royaume WHERE id = ".$id[0];
			$req = $db->query($requete);
			$race = $db->read_row($req);
			
			$requete = "SELECT ".$race[0]." FROM diplomatie WHERE race = '".$perso->get_race()."'";
			$req = $db->query($requete);
			$row = $db->read_row($req);
			$pascrime = false;
			if ( $row[0] > 5)
			{
				$pascrime = true;
			}
			if(!$pascrime)
			{
				$points = ceil(( $G_crime_batiment[$row[0]] / $facteur ));
				$perso->set_crime($perso->get_crime() + $points);
				echo '<h5>Vous attaquez un batiment en '.$Gtrad['diplo'.$row[0]].', vous recevez '.$points.' point(s) de crime</h5>';
			}
		}
		
	}
}
